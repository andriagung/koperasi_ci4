<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class TestRunner extends Controller
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        $results = [];
        try {
            // 1. Buat Anggota Dummy
            $anggotaModel = new \App\Models\AnggotaModel();
            $nomorAnggota = 'QA-' . time();
            $anggotaData = [
                'nip' => 'NIP-' . time(),
                'nomor_anggota' => $nomorAnggota,
                'nama_lengkap' => 'Anggota QA Testing',
                'nik' => '1234567890123456',
                'jenis_kelamin' => 'Laki-laki',
                'no_hp' => '08123456789',
                'tanggal_masuk' => date('Y-m-d'),
                'status' => 'Aktif',
                'pin' => password_hash('123456', PASSWORD_DEFAULT)
            ];
            $anggotaId = $anggotaModel->insert($anggotaData);
            if (!$anggotaId) {
                throw new \Exception("Gagal insert Anggota: " . json_encode($anggotaModel->errors()));
            }
            $results[] = "✅ [Anggota] Berhasil membuat anggota dummy ID: $anggotaId";

            // Pastikan Akun Kas untuk testing tersedia
            $kas = $db->table('kas')->limit(1)->get()->getRow();
            if (!$kas) {
                // Insert dummy kas if not exists
                $db->table('kas')->insert(['nama_kas' => 'Kas QA', 'saldo' => 10000000, 'status' => 'aktif', 'created_at' => date('Y-m-d H:i:s')]);
                $kasId = $db->insertID();
            } else {
                $kasId = $kas->id;
            }

            // 2. Simulasi Setoran Simpanan Wajib & Pokok
            $simpananService = new \App\Services\SimpananService();
            // Simpanan Pokok (jenis = 1)
            $resPokok = $simpananService->setor([
                'anggota_id' => $anggotaId,
                'jenis_simpanan_id' => 1, 
                'tanggal' => date('Y-m-d'),
                'nominal' => 100000,
                'metode_pembayaran' => 'Tunai',
                'keterangan' => 'QA Simpanan Pokok',
                'kas_id' => $kasId
            ]);
            if(!$resPokok['success']) {
                $dbError = $db->error();
                throw new \Exception("Gagal Setor Pokok: " . $resPokok['message'] . " | DB Error: " . json_encode($dbError));
            }
            $results[] = "✅ [Simpanan] Berhasil Setor Simpanan Pokok Rp 100.000";

            // Simpanan Wajib (jenis = 2)
            $resWajib = $simpananService->setor([
                'anggota_id' => $anggotaId,
                'jenis_simpanan_id' => 2,
                'tanggal' => date('Y-m-d'),
                'nominal' => 50000,
                'metode_pembayaran' => 'Tunai',
                'keterangan' => 'QA Simpanan Wajib',
                'kas_id' => $kasId
            ]);
            if(!$resWajib['success']) throw new \Exception("Gagal Setor Wajib: " . $resWajib['message']);
            $results[] = "✅ [Simpanan] Berhasil Setor Simpanan Wajib Rp 50.000";

            // 3. Simulasi Pinjaman & Pencairan
            $pinjamanService = new \App\Services\PinjamanService();
            $resPinjam = $pinjamanService->ajukan([
                'anggota_id' => $anggotaId,
                'tanggal_pengajuan' => date('Y-m-d'),
                'nominal_pengajuan' => 1000000,
                'tenor_bulan' => 10,
                'tujuan' => 'QA Testing Pinjaman',
                'penghasilan_bulanan' => 3000000,
                'cicilan_lainnya' => 0
            ]);
            if(!$resPinjam['success']) throw new \Exception("Gagal Ajukan Pinjaman: " . $resPinjam['message']);
            $pinjamanQuery = $db->table('pinjaman')->where('anggota_id', $anggotaId)->where('nominal_pengajuan', 1000000)->get();
            if (!$pinjamanQuery) {
                throw new \Exception("DB Error get pinjaman: " . json_encode($db->error()));
            }
            $pinjamanObj = $pinjamanQuery->getRow();
            if (!$pinjamanObj) {
                throw new \Exception("Pinjaman tidak ditemukan di database.");
            }
            $pinjamanId = $pinjamanObj->id;
            
            // Pencairan Pinjaman (Simulasi dari Controller)
            $pencairanModel = new \App\Models\PinjamanPencairanModel();
            $nominalCair = 1000000;
            $pencairanModel->insert([
                'pinjaman_id' => $pinjamanId,
                'tanggal_pencairan' => date('Y-m-d'),
                'nominal_pencairan' => $nominalCair,
                'biaya_admin' => 0,
                'nominal_diterima' => $nominalCair,
                'kas_id' => $kasId
            ]);
            $pencairanId = $pencairanModel->insertID();

            // Integrasi Kas (Kredit karena Kas keluar)
            $kasService = new \App\Services\KasService();
            $kasService->kredit((int)$kasId, (float)$nominalCair, 'pinjaman_pencairan', $pencairanId, 'Pencairan Pinjaman ID: ' . $pinjamanId);

            // Update status pinjaman
            $db->table('pinjaman')->where('id', $pinjamanId)->update([
                'status_pengajuan' => 'Disetujui'
            ]);

            // Generate Jadwal Angsuran
            $pokokPerBulan = $nominalCair / 10;
            $bungaPerBulan = $nominalCair * 0.015;
            $tglMulai = new \DateTime();
            for ($i = 1; $i <= 10; $i++) {
                $tglMulai->modify('+1 month');
                $db->table('pinjaman_angsuran')->insert([
                    'pinjaman_id' => $pinjamanId,
                    'bulan_ke' => $i,
                    'jatuh_tempo' => $tglMulai->format('Y-m-d'),
                    'pokok' => $pokokPerBulan,
                    'jasa' => $bungaPerBulan,
                    'status' => 'Belum Lunas'
                ]);
            }
            $results[] = "✅ [Pinjaman] Berhasil Cairkan Pinjaman & Generate Jadwal Angsuran";

            // 4. Simulasi Angsuran (Simulasi dari Controller)
            $angsuranQuery = $db->table('pinjaman_angsuran')->where('pinjaman_id', $pinjamanId)->where('bulan_ke', 1)->get();
            if (!$angsuranQuery) throw new \Exception("DB Error get angsuran: " . json_encode($db->error()));
            $angsuran1 = $angsuranQuery->getRow();
            if (!$angsuran1) throw new \Exception("Angsuran 1 tidak ditemukan.");

            $pembayaranModel = new \App\Models\PinjamanPembayaranModel();
            $pembayaranModel->insert([
                'jadwal_angsuran_id' => $angsuran1->id,
                'pinjaman_id' => $pinjamanId,
                'tanggal_bayar' => date('Y-m-d'),
                'nominal_bayar' => $angsuran1->pokok + $angsuran1->jasa,
                'denda_dibayar' => 0,
                'metode_pembayaran' => 'Tunai'
            ]);
            $pembayaranId = $pembayaranModel->insertID();

            // Integrasi Kas (Debit karena Kas masuk)
            $kasService->debit((int)$kasId, $angsuran1->pokok + $angsuran1->jasa, 'pinjaman_pembayaran', $pembayaranId, 'Angsuran Pinjaman ID: ' . $pinjamanId);

            // Update status angsuran
            $db->table('pinjaman_angsuran')->where('id', $angsuran1->id)->update([
                'status' => 'Lunas',
                'tanggal_bayar' => date('Y-m-d')
            ]);
            $results[] = "✅ [Pinjaman] Berhasil Bayar Angsuran Pertama";

            // 5. Simulasi Penjualan Waserda (POS)
            $posService = new \App\Services\PosService();
            // Ambil 1 produk aktif
            $produk = $db->table('produk_waserda')->where('stok >', 5)->get()->getRow();
            if ($produk) {
                $qty = 2;
                $harga = $produk->harga_jual;
                $resPos = $posService->checkout([
                    'anggota_id' => $anggotaId,
                    'kas_id' => $kasId,
                    'tanggal' => date('Y-m-d'),
                    'metode_pembayaran' => 'Tunai',
                    'uang_bayar' => ($harga * $qty) + 10000,
                    'items' => [
                        [
                            'produk_id' => $produk->id,
                            'kuantitas' => $qty,
                            'harga_satuan' => $harga
                        ]
                    ]
                ]);
                if(!$resPos['success']) throw new \Exception("Gagal POS Waserda: " . $resPos['message']);
                $results[] = "✅ [Waserda] Berhasil Transaksi POS ($qty x $produk->nama_produk)";
            }

            // 6. Validasi Jurnal Akuntansi (Debit = Kredit)
            $jurnalSum = $db->query("SELECT SUM(debit) as total_debit, SUM(kredit) as total_kredit FROM jurnal_detail")->getRow();
            if ($jurnalSum->total_debit != $jurnalSum->total_kredit) {
                throw new \Exception("AKUNTANSI TIDAK SEIMBANG! Debit: {$jurnalSum->total_debit}, Kredit: {$jurnalSum->total_kredit}");
            }
            $results[] = "✅ [Akuntansi] Total Debit dan Kredit di Jurnal_Detail SEIMBANG persis (Balance: Rp ".number_format($jurnalSum->total_debit, 0, ',', '.').")";

            // Rollback agar data QA tidak mengotori database production/real
            $db->transRollback();
            $results[] = "✅ [Sistem] Semua transaksi QA di-Rollback dengan aman (Database tetap bersih).";

            return $this->response->setJSON([
                'status' => 'ALL PASSED',
                'code' => 200,
                'logs' => $results
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            $results[] = "❌ [ERROR] " . $e->getMessage();
            return $this->response->setJSON([
                'status' => 'FAILED',
                'code' => 500,
                'logs' => $results
            ]);
        }
    }
}
