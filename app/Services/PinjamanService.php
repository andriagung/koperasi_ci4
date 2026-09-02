<?php
namespace App\Services;

class PinjamanService extends BaseService {
    
    public function ajukan(array $data): array {
        $this->db->transStart();
        try {
            $data['status_pengajuan'] = 'SUBMITTED';
            $data['tanggal_pengajuan'] = date('Y-m-d');
            $data['created_at'] = date('Y-m-d H:i:s');
            
            $this->db->table('pinjaman')->insert($data);
            $id = $this->db->insertID();
            
            $this->logAudit('PENGAJUAN_PINJAMAN', 'Pengajuan pinjaman baru ID: ' . $id);
            
            $this->db->transComplete();
            return ['success' => true, 'message' => 'Pengajuan berhasil disimpan.', 'id' => $id];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function hitungDSR(array $pinjaman): array {
        $gaji = $pinjaman['penghasilan_bulanan'] > 0 ? $pinjaman['penghasilan_bulanan'] : 1; 
        $pengaturanModel = new \App\Models\PengaturanModel();
        $bungaPersen = $pengaturanModel->where('kunci', 'jasa_bunga_pinjaman')->first()['nilai'] ?? 1.0;
        
        $angsuran_pokok = $pinjaman['nominal_pengajuan'] / $pinjaman['tenor_bulan'];
        $angsuran_jasa = $pinjaman['nominal_pengajuan'] * ($bungaPersen / 100);
        $total_angsuran = $angsuran_pokok + $angsuran_jasa;
        $total_beban = $total_angsuran + $pinjaman['cicilan_lainnya'];
        
        $dsr = ($total_beban / $gaji) * 100;
        
        return [
            'dsr' => $dsr,
            'total_angsuran' => $total_angsuran,
            'total_beban' => $total_beban,
            'bunga_persen' => $bungaPersen
        ];
    }
    
    public function verifikasi(int $pengajuanId, int $userId): array {
        // Implementasi
        return ['success' => true];
    }
    
    public function approve(int $pengajuanId, int $userId, string $catatan): array {
        // Implementasi
        return ['success' => true];
    }
    
    public function reject(int $pengajuanId, int $userId, string $catatan): array {
        // Implementasi
        return ['success' => true];
    }
    
    public function cairkan(int $pinjamanId, array $data, int $userId): array {
        $this->db->transStart();
        try {
            $pinjamanModel = new \App\Models\PinjamanModel();
            $pencairanModel = new \App\Models\PinjamanPencairanModel();
            
            $pinjaman = $pinjamanModel->find($pinjamanId);
            if (!$pinjaman) throw new \Exception('Pinjaman tidak ditemukan.');
            
            $nominalCair = $pinjaman['nominal_pengajuan'];
            $biayaAdmin = $data['biaya_admin'] ?? 0;
            $diterima = $nominalCair - $biayaAdmin;
            
            $pencairanModel->insert([
                'pinjaman_id' => $pinjamanId,
                'tanggal_pencairan' => $data['tanggal_pencairan'],
                'nominal_pencairan' => $nominalCair,
                'biaya_admin' => $biayaAdmin,
                'nominal_diterima' => $diterima,
                'kas_id' => $data['kas_id'] ?? null
            ]);
            $pencairanId = $pencairanModel->insertID();
            
            $kodeKasBank = '1100'; // Default Kas
            if ($data['metode_pembayaran'] === 'Tunai' && !empty($data['kas_id'])) {
                $kasService = new \App\Services\KasService();
                $kasService->kredit((int)$data['kas_id'], (float)$diterima, 'pinjaman_pencairan', $pencairanId, 'Pencairan Pinjaman ID: ' . $pinjaman['id']);
                $kodeKasBank = '1100';
            } elseif ($data['metode_pembayaran'] === 'Transfer Bank' && !empty($data['bank_id'])) {
                $bankService = new \App\Services\BankService();
                $bankService->kredit((int)$data['bank_id'], (float)$diterima, 'pinjaman_pencairan', $pencairanId, 'Pencairan Pinjaman ID: ' . $pinjaman['id']);
                $kodeKasBank = '1110';
            }
            
            $akuntansiService = new \App\Services\AkuntansiService();
            $akunKasBank = $this->db->table('akun_coa')->where('kode_akun', $kodeKasBank)->get()->getRow();
            $akunPiutang = $this->db->table('akun_coa')->where('kode_akun', '1200')->get()->getRow(); 
            $akunPendapatanAdmin = $this->db->table('akun_coa')->where('kode_akun', '4300')->get()->getRow(); 
            
            if ($akunKasBank && $akunPiutang) {
                $jurnalDetail = [
                    ['akun_id' => $akunPiutang->id, 'posisi' => 'debit', 'nominal' => $nominalCair], 
                    ['akun_id' => $akunKasBank->id, 'posisi' => 'kredit', 'nominal' => $diterima] 
                ];
                
                if ($biayaAdmin > 0 && $akunPendapatanAdmin) {
                    $jurnalDetail[] = ['akun_id' => $akunPendapatanAdmin->id, 'posisi' => 'kredit', 'nominal' => $biayaAdmin];
                }
                
                $akuntansiService->catatJurnal(
                    $data['tanggal_pencairan'],
                    'Pencairan Pinjaman ID: ' . $pinjaman['id'],
                    $jurnalDetail,
                    'CAIR-' . $pinjamanId
                );
            }
            
            $pinjamanModel->update($pinjamanId, ['status_pengajuan' => 'ACTIVE']);
            
            $angsuranService = new \App\Services\AngsuranService();
            $angsuranService->generateJadwal($pinjaman, null, $data['tanggal_pencairan']);
            
            $logModel = new \App\Models\ApprovalLogsModel();
            $logModel->insert([
                'tabel_referensi' => 'pinjaman',
                'referensi_id' => $pinjamanId,
                'user_id' => $userId,
                'action' => 'DISBURSE',
                'catatan' => 'Dana pinjaman dicairkan: Rp ' . number_format($diterima)
            ]);
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal memproses transaksi database.'];
            }
            
            return ['success' => true];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function generateJadwalAngsuran(array $pinjaman): void {
        // Implementasi
    }

    public function restrukturisasi(int $pinjamanId, array $data, int $userId): array {
        $this->db->transStart();
        try {
            $pinjamanModel = new \App\Models\PinjamanModel();
            $restrukModel = new \App\Models\PinjamanRestrukturisasiModel();
            
            $oldPinjaman = $pinjamanModel->find($pinjamanId);
            if (!$oldPinjaman) throw new \Exception('Pinjaman tidak ditemukan.');
            
            // 1. Calculate sisa_pokok
            $sisaPokok = $this->db->table('pinjaman_angsuran')
                                  ->selectSum('pokok')
                                  ->where('pinjaman_id', $pinjamanId)
                                  ->where('status', 'Belum Lunas')
                                  ->get()->getRow()->pokok ?? 0;
            
            if ($sisaPokok <= 0) {
                throw new \Exception('Tidak ada sisa pokok outstanding untuk direstrukturisasi.');
            }
            
            // 2. Insert into pinjaman_restrukturisasi
            $restrukModel->insert([
                'pinjaman_id' => $pinjamanId,
                'sisa_pokok' => $sisaPokok,
                'tenor_baru' => $data['tenor_baru'],
                'bunga_baru' => $data['bunga_baru'],
                'alasan' => $data['alasan'],
                'tanggal_efektif' => date('Y-m-d')
            ]);
            
            // 3. Delete old unpaid installments
            $this->db->table('pinjaman_angsuran')
                     ->where('pinjaman_id', $pinjamanId)
                     ->where('status', 'Belum Lunas')
                     ->delete();
            
            // 4. Update status old pinjaman
            $pinjamanModel->update($pinjamanId, ['status_pengajuan' => 'RESTRUCTURED']);
            
            // 5. Create new pinjaman
            $newPinjamanData = [
                'anggota_id' => $oldPinjaman['anggota_id'],
                'nominal_pengajuan' => $sisaPokok,
                'tenor_bulan' => $data['tenor_baru'],
                'tujuan' => 'Restrukturisasi Pinjaman ID: ' . $pinjamanId,
                'status_pengajuan' => 'ACTIVE',
                'tanggal_pengajuan' => date('Y-m-d')
            ];
            $pinjamanModel->insert($newPinjamanData);
            $newPinjamanId = $pinjamanModel->insertID();
            
            // 6. Generate new schedule for new pinjaman
            $newPinjaman = $pinjamanModel->find($newPinjamanId);
            $angsuranService = new \App\Services\AngsuranService();
            $angsuranService->generateJadwal($newPinjaman, (float)$data['bunga_baru'], date('Y-m-d'));
            
            // 7. Log restructured action
            $logModel = new \App\Models\ApprovalLogsModel();
            $logModel->insert([
                'tabel_referensi' => 'pinjaman',
                'referensi_id' => $pinjamanId,
                'user_id' => $userId,
                'action' => 'RESTRUCTURE',
                'catatan' => 'Restrukturisasi berhasil. Tenor baru: ' . $data['tenor_baru'] . ' bulan, Bunga baru: ' . $data['bunga_baru'] . '%.'
            ]);
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal memproses transaksi database.'];
            }
            
            return ['success' => true];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
