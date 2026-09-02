<?php
namespace App\Services;

class SimpananService extends BaseService {
    
    public function setor(array $data): array {
        $this->db->transStart();
        try {
            if (!isset($data['nominal']) || $data['nominal'] <= 0) {
                throw new \Exception('Nominal transaksi harus lebih dari 0.');
            }
            
            $nomorTransaksi = $this->generateNomor('SIM');
            $data['nomor_transaksi'] = $nomorTransaksi;
            $data['jenis_transaksi'] = 'setoran';
            
            // Insert transaksi
            $insert = $this->db->table('simpanan_transaksi')->insert($data);
            if (!$insert) throw new \Exception("DB Error Transaksi: " . json_encode($this->db->error()));
            
            // Update saldo (INSERT ON DUPLICATE KEY UPDATE)
            $dbq = $this->db->query("
                INSERT INTO simpanan_saldo (anggota_id, jenis_simpanan_id, saldo, updated_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE saldo = saldo + VALUES(saldo), updated_at = NOW()
            ", [$data['anggota_id'], $data['jenis_simpanan_id'], $data['nominal']]);
            if (!$dbq) { throw new \Exception("DB Error: " . json_encode($this->db->error())); }
            
            // Integrasi ke Kas/Bank (Phase 9)
            $insertId = $this->db->insertID(); // ID transaksi simpanan
            $kodeKasBank = '1100'; // Default Kas
            if ($data['metode_pembayaran'] === 'Tunai' && !empty($data['kas_id'])) {
                $kasService = new KasService();
                $kasService->debit((int)$data['kas_id'], (float)$data['nominal'], 'simpanan_transaksi', $insertId, 'Setoran Simpanan: ' . $nomorTransaksi);
                $kodeKasBank = '1100'; // Kas
            } elseif ($data['metode_pembayaran'] === 'Transfer Bank' && !empty($data['bank_id'])) {
                $bankService = new BankService();
                $bankService->debit((int)$data['bank_id'], (float)$data['nominal'], 'simpanan_transaksi', $insertId, 'Setoran Simpanan: ' . $nomorTransaksi);
                $kodeKasBank = '1110'; // Bank
            }
            
            // JURNAL AKUNTANSI OTOMATIS (FASE 9)
            $akuntansiService = new AkuntansiService();
            
            // Tentukan kode akun untuk simpanan (1=Pokok, 2=Wajib, 3=Sukarela)
            $kodeAkunSimpanan = '2200'; // Default Sukarela (Kewajiban)
            if ($data['jenis_simpanan_id'] == 1) $kodeAkunSimpanan = '3100'; // Pokok (Ekuitas)
            if ($data['jenis_simpanan_id'] == 2) $kodeAkunSimpanan = '3200'; // Wajib (Ekuitas)
            
            // Get ID Akun
            $akunKasBank = $this->db->table('akun_coa')->where('kode_akun', $kodeKasBank)->get()->getRow();
            $akunSimpanan = $this->db->table('akun_coa')->where('kode_akun', $kodeAkunSimpanan)->get()->getRow();
            
            if ($akunKasBank && $akunSimpanan) {
                $akuntansiService->catatJurnal(
                    $data['tanggal'] ?? date('Y-m-d'),
                    'Setoran Simpanan A/N Anggota ID ' . $data['anggota_id'],
                    [
                        ['akun_id' => $akunKasBank->id, 'posisi' => 'debit', 'nominal' => $data['nominal']],
                        ['akun_id' => $akunSimpanan->id, 'posisi' => 'kredit', 'nominal' => $data['nominal']]
                    ],
                    $nomorTransaksi
                );
            }
            
            $this->logAudit('SETORAN_SIMPANAN', 'Setoran simpanan berhasil: ' . $nomorTransaksi);
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal memproses setoran.'];
            }
            
            return ['success' => true, 'message' => 'Setoran berhasil diproses.', 'nomor_transaksi' => $nomorTransaksi];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function tarik(array $data): array {
        $this->db->transStart();
        try {
            if (!isset($data['nominal']) || $data['nominal'] <= 0) {
                throw new \Exception('Nominal penarikan harus lebih dari 0.');
            }
            
            // Cek Saldo
            $saldo = $this->getSaldo($data['anggota_id'], $data['jenis_simpanan_id']);
            if ($saldo < $data['nominal']) {
                throw new \Exception('Saldo tidak mencukupi.');
            }
            
            $nomorTransaksi = $this->generateNomor('SIM');
            $data['nomor_transaksi'] = $nomorTransaksi;
            $data['jenis_transaksi'] = 'penarikan';
            
            $this->db->table('simpanan_transaksi')->insert($data);
            
            $this->db->query("
                UPDATE simpanan_saldo 
                SET saldo = saldo - ?, updated_at = NOW() 
                WHERE anggota_id = ? AND jenis_simpanan_id = ?
            ", [$data['nominal'], $data['anggota_id'], $data['jenis_simpanan_id']]);
            
            // Integrasi ke Kas/Bank (Phase 9)
            $insertId = $this->db->insertID();
            $kodeKasBank = '1100';
            if ($data['metode_pembayaran'] === 'Tunai' && !empty($data['kas_id'])) {
                $kasService = new KasService();
                $kasService->kredit((int)$data['kas_id'], (float)$data['nominal'], 'simpanan_transaksi', $insertId, 'Penarikan Simpanan: ' . $nomorTransaksi);
                $kodeKasBank = '1100';
            } elseif ($data['metode_pembayaran'] === 'Transfer Bank' && !empty($data['bank_id'])) {
                $bankService = new BankService();
                $bankService->kredit((int)$data['bank_id'], (float)$data['nominal'], 'simpanan_transaksi', $insertId, 'Penarikan Simpanan: ' . $nomorTransaksi);
                $kodeKasBank = '1110';
            }
            
            // JURNAL AKUNTANSI OTOMATIS (FASE 9)
            $akuntansiService = new AkuntansiService();
            
            $kodeAkunSimpanan = '2200'; // Default Sukarela (Kewajiban)
            if ($data['jenis_simpanan_id'] == 1) $kodeAkunSimpanan = '3100'; // Pokok (Ekuitas)
            if ($data['jenis_simpanan_id'] == 2) $kodeAkunSimpanan = '3200'; // Wajib (Ekuitas)
            
            // Get ID Akun
            $akunKasBank = $this->db->table('akun_coa')->where('kode_akun', $kodeKasBank)->get()->getRow();
            $akunSimpanan = $this->db->table('akun_coa')->where('kode_akun', $kodeAkunSimpanan)->get()->getRow();
            
            if ($akunKasBank && $akunSimpanan) {
                $akuntansiService->catatJurnal(
                    $data['tanggal'] ?? date('Y-m-d'),
                    'Penarikan Simpanan A/N Anggota ID ' . $data['anggota_id'],
                    [
                        ['akun_id' => $akunSimpanan->id, 'posisi' => 'debit', 'nominal' => $data['nominal']],
                        ['akun_id' => $akunKasBank->id, 'posisi' => 'kredit', 'nominal' => $data['nominal']]
                    ],
                    $nomorTransaksi
                );
            }
            
            $this->logAudit('PENARIKAN_SIMPANAN', 'Penarikan simpanan berhasil: ' . $nomorTransaksi);
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal memproses penarikan.'];
            }
            
            return ['success' => true, 'message' => 'Penarikan berhasil diproses.', 'nomor_transaksi' => $nomorTransaksi];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function getSaldo(int $anggotaId, int $jenisSimpananId): float {
        $row = $this->db->table('simpanan_saldo')
            ->select('saldo')
            ->where('anggota_id', $anggotaId)
            ->where('jenis_simpanan_id', $jenisSimpananId)
            ->get()->getRow();
        return $row ? (float) $row->saldo : 0.0;
    }

    public function koreksiLegacy($anggota_id, $jenis, $nominal, $keterangan, $tipe) {
        if ($nominal <= 0) {
            throw new \Exception('Nominal koreksi harus lebih dari 0.');
        }
        $simpananModel = new \App\Models\SimpananModel();
        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        $simpanan = $simpananModel->where('anggota_id', $anggota_id)->where('jenis_simpanan', $jenis)->first();
        
        if (!$simpanan) throw new \Exception('Data simpanan tidak ditemukan.');

        $this->db->transStart();
        
        $saldo_baru = $tipe == 'Tambah' ? $simpanan['saldo'] + $nominal : $simpanan['saldo'] - $nominal;
        $simpananModel->update($simpanan['id'], ['saldo' => $saldo_baru]);
        
        $riwayatModel->insert([
            'anggota_id' => $anggota_id,
            'kategori' => 'Simpanan',
            'jenis_transaksi' => $tipe == 'Tambah' ? 'Masuk' : 'Keluar',
            'nominal' => $nominal,
            'keterangan' => 'Koreksi Simpanan ' . $jenis . ': ' . $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            throw new \Exception('Gagal memproses koreksi simpanan.');
        }
        return true;
    }

    public function transferLegacy($anggota_id, $dari, $ke, $nominal) {
        if ($nominal <= 0) {
            throw new \Exception('Nominal transfer harus lebih dari 0.');
        }
        $simpananModel = new \App\Models\SimpananModel();
        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        
        $simpananDari = $simpananModel->where('anggota_id', $anggota_id)->where('jenis_simpanan', $dari)->first();
        $simpananKe = $simpananModel->where('anggota_id', $anggota_id)->where('jenis_simpanan', $ke)->first();
        
        if (!$simpananDari || !$simpananKe) throw new \Exception('Data simpanan tidak valid.');
        if ($simpananDari['saldo'] < $nominal) throw new \Exception('Saldo tidak mencukupi.');

        $this->db->transStart();
        
        $simpananModel->update($simpananDari['id'], ['saldo' => $simpananDari['saldo'] - $nominal]);
        $simpananModel->update($simpananKe['id'], ['saldo' => $simpananKe['saldo'] + $nominal]);
        
        $riwayatModel->insert([
            'anggota_id' => $anggota_id,
            'kategori' => 'Simpanan',
            'jenis_transaksi' => 'Keluar',
            'nominal' => $nominal,
            'keterangan' => 'Transfer keluar ke ' . $ke,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $riwayatModel->insert([
            'anggota_id' => $anggota_id,
            'kategori' => 'Simpanan',
            'jenis_transaksi' => 'Masuk',
            'nominal' => $nominal,
            'keterangan' => 'Transfer masuk dari ' . $dari,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            throw new \Exception('Gagal memproses transfer simpanan.');
        }
        return true;
    }
}
