<?php
namespace App\Services;

class ShuService extends BaseService {
    
    /**
     * Hitung simulasi pembagian SHU untuk tahun tertentu
     */
    public function kalkulasiSimulasi(int $tahun): array {
        $cache = \Config\Services::cache();
        $cacheKey = 'shu_simulasi_' . $tahun;
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        
        // 1. Ambil Laba Bersih Tahun Tersebut
        // Pendapatan - Beban
        $pendapatan = $db->query("
            SELECT COALESCE(SUM(j.nominal), 0) as total
            FROM jurnal_transaksi j
            JOIN akun_coa a ON a.id = j.akun_id
            WHERE a.tipe_akun = 'Pendapatan' AND j.posisi = 'kredit' 
              AND YEAR(j.tanggal) = ?
        ", [$tahun])->getRow()->total;
        
        $beban = $db->query("
            SELECT COALESCE(SUM(j.nominal), 0) as total
            FROM jurnal_transaksi j
            JOIN akun_coa a ON a.id = j.akun_id
            WHERE a.tipe_akun = 'Beban' AND j.posisi = 'debit'
              AND YEAR(j.tanggal) = ?
        ", [$tahun])->getRow()->total;
        
        $labaBersih = $pendapatan - $beban;
        $hasLaba = ($labaBersih > 0);
        
        $pesan = $hasLaba ? "Kalkulasi berhasil." : "Tidak ada laba (atau rugi) untuk tahun $tahun. SHU tidak bisa dibagikan, namun proyeksi poin partisipasi tetap dihitung.";
        $labaBersihKalkulasi = $hasLaba ? $labaBersih : 0;
        
        // ASUMSI PERSENTASE DARI OPEN QUESTIONS
        $persenCadangan = 25; // 25% Dana Cadangan
        $persenJasaModal = 30; // 30% Jasa Modal (Simpanan)
        $persenJasaUsaha = 45; // 45% Jasa Usaha (Pinjaman/Belanja)
        
        $danaCadangan = $labaBersihKalkulasi * ($persenCadangan / 100);
        $danaJasaModal = $labaBersihKalkulasi * ($persenJasaModal / 100);
        $danaJasaUsaha = $labaBersihKalkulasi * ($persenJasaUsaha / 100);
        
        // 2. Hitung Total Simpanan & Total Usaha Koperasi
        $totalSimpananKoperasi = $db->query("SELECT COALESCE(SUM(saldo), 0) as total FROM simpanan_saldo")->getRow()->total;
        
        // Total Usaha = Total Pinjaman + Total Belanja Waserda
        $totalPinjamanKoperasi = $db->query("SELECT COALESCE(SUM(jumlah_dicairkan), 0) as total FROM pinjaman_pencairan WHERE YEAR(tanggal_pencairan) = ?", [$tahun])->getRow()->total;
        $totalBelanjaWaserdaKoperasi = $db->query("SELECT COALESCE(SUM(total_bayar), 0) as total FROM penjualan WHERE anggota_id IS NOT NULL AND YEAR(tanggal) = ?", [$tahun])->getRow()->total;
        $totalUsahaKoperasi = $totalPinjamanKoperasi + $totalBelanjaWaserdaKoperasi;
        
        // 3. Distribusi per Anggota
        $anggota = $db->table('anggota')->where('status', 'Aktif')->get()->getResultArray();
        $distribusi = [];
        
        foreach ($anggota as $a) {
            $id = $a['id'];
            
            // Simpanan Anggota
            $simpananAnggota = $db->query("SELECT COALESCE(SUM(saldo), 0) as total FROM simpanan_saldo WHERE anggota_id = ?", [$id])->getRow()->total;
            $jasaModalAnggota = ($totalSimpananKoperasi > 0) ? ($simpananAnggota / $totalSimpananKoperasi) * $danaJasaModal : 0;
            
            // Usaha Anggota
            $usahaPinjamanAnggota = $db->query("
                SELECT COALESCE(SUM(pp.jumlah_dicairkan), 0) as total 
                FROM pinjaman_pencairan pp
                JOIN pinjaman p ON p.id = pp.pinjaman_id
                WHERE p.anggota_id = ? AND YEAR(pp.tanggal_pencairan) = ?
            ", [$id, $tahun])->getRow()->total;
            
            $usahaBelanjaWaserdaAnggota = $db->query("
                SELECT COALESCE(SUM(total_bayar), 0) as total 
                FROM penjualan
                WHERE anggota_id = ? AND YEAR(tanggal) = ?
            ", [$id, $tahun])->getRow()->total;
            
            $usahaAnggota = $usahaPinjamanAnggota + $usahaBelanjaWaserdaAnggota;
            
            $jasaUsahaAnggota = ($totalUsahaKoperasi > 0) ? ($usahaAnggota / $totalUsahaKoperasi) * $danaJasaUsaha : 0;
            
            $totalShuAnggota = $jasaModalAnggota + $jasaUsahaAnggota;
            
            // Tampilkan semua anggota (aktif) di proyeksi meskipun SHU-nya 0, agar Poin Partisipasi terlihat
            $distribusi[] = [
                'anggota_id' => $id,
                'nama_lengkap' => $a['nama_lengkap'],
                'nip' => $a['nip'],
                'poin_modal' => round($simpananAnggota, 2),
                'poin_usaha' => round($usahaAnggota, 2),
                'jasa_modal' => round($jasaModalAnggota, 2),
                'jasa_usaha' => round($jasaUsahaAnggota, 2),
                'total_shu' => round($totalShuAnggota, 2)
            ];
        }
        
        return [
            'success' => true,
            'has_laba' => $hasLaba,
            'message' => $pesan,
            'tahun' => $tahun,
            'laba_bersih' => $labaBersih,
            'dana_cadangan' => $danaCadangan,
            'dana_jasa_modal' => $danaJasaModal,
            'dana_jasa_usaha' => $danaJasaUsaha,
            'distribusi' => $distribusi
        ];

        $cache->save($cacheKey, $result, 3600); // Cache for 1 hour

        return $result;
    }
    
    /**
     * Tutup Buku dan Distribusikan SHU ke Simpanan Sukarela Anggota
     */
    public function tutupBukuDanBagikan(int $tahun): array {
        $simulasi = $this->kalkulasiSimulasi($tahun);
        if (!$simulasi['has_laba']) {
            return ['success' => false, 'message' => "Tidak ada laba (atau rugi) untuk tahun $tahun. Proses Tutup Buku SHU tidak dapat dilakukan."];
        }
        
        $db = \Config\Database::connect();
        
        // Cek apakah sudah pernah ditutup buku
        $cek = $db->table('shu_tahunan')->where('tahun', $tahun)->countAllResults();
        if ($cek > 0) {
            return ['success' => false, 'message' => "Tahun $tahun sudah pernah tutup buku dan SHU telah dibagikan."];
        }
        
        $this->db->transStart();
        try {
            // 1. Simpan Header SHU
            $db->table('shu_tahunan')->insert([
                'tahun' => $tahun,
                'total_shu' => $simulasi['laba_bersih'],
                'cadangan' => $simulasi['dana_cadangan'],
                'total_jasa_modal' => $simulasi['dana_jasa_modal'],
                'total_jasa_usaha' => $simulasi['dana_jasa_usaha'],
                'status' => 'Dibagikan'
            ]);
            if ($this->db->transStatus() === false) throw new \Exception('Gagal insert shu_tahunan: '.json_encode($this->db->error()));
            $shuTahunanId = $this->db->insertID();
            
            // 2. Loop Distribusi & Tambahkan ke Simpanan Sukarela
            // Jenis Simpanan Sukarela ID = 3
            foreach ($simulasi['distribusi'] as $dist) {
                // Catat di tabel shu_anggota
                $db->table('shu_anggota')->insert([
                    'shu_periode_id' => $shuTahunanId,
                    'anggota_id' => $dist['anggota_id'],
                    'shu_modal' => $dist['jasa_modal'],
                    'shu_usaha' => $dist['jasa_usaha'],
                    'total_shu' => $dist['total_shu'],
                    'status' => 'Dibagikan',
                    'disalurkan_ke' => 'Sukarela'
                ]);
                if ($this->db->transStatus() === false) throw new \Exception('Gagal insert shu_anggota: '.json_encode($this->db->error()));
                
                // Tambahkan ke simpanan_saldo (Simpanan Sukarela)
                $db->query("
                    INSERT INTO simpanan_saldo (anggota_id, jenis_simpanan_id, saldo, updated_at) 
                    VALUES (?, 3, ?, NOW())
                    ON DUPLICATE KEY UPDATE saldo = saldo + VALUES(saldo), updated_at = NOW()
                ", [$dist['anggota_id'], $dist['total_shu']]);
                if ($this->db->transStatus() === false) throw new \Exception('Gagal insert simpanan_saldo: '.json_encode($this->db->error()));
                
                // Tambahkan histori transaksi simpanan
                $nomorTransaksi = $this->generateNomor('SIM');
                $db->table('simpanan_transaksi')->insert([
                    'nomor_transaksi' => $nomorTransaksi,
                    'anggota_id' => $dist['anggota_id'],
                    'jenis_simpanan_id' => 3,
                    'jenis_transaksi' => 'setoran',
                    'nominal' => $dist['total_shu'],
                    'tanggal' => date('Y-m-d'),
                    'keterangan' => "Pembagian SHU Tahun $tahun",
                    'status' => 'Selesai'
                ]);
            }
            
            // 3. Jurnal Akuntansi Penutupan
            $akuntansiService = new AkuntansiService();
            // Jurnal: Memindahkan Laba (Pendapatan & Beban) ke SHU Tahun Berjalan
            // Karena ini sistem sederhana, kita langsung jurnal 1 entry: 
            // Debit: Kas (Asumsi uang sudah ada di kas) atau Laba Ditahan? 
            // Kita jurnal: (D) SHU Tahun Berjalan (3400), (K) Simpanan Sukarela (2200) senilai total distribusi
            $totalDistribusi = array_sum(array_column($simulasi['distribusi'], 'total_shu'));
            $akunShu = $db->table('akun_coa')->where('kode_akun', '3400')->get()->getRow(); // Ekuitas
            $akunSukarela = $db->table('akun_coa')->where('kode_akun', '2200')->get()->getRow(); // Kewajiban
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi sudah gagal sebelum catatJurnal!');
            }
            if ($akunShu && $akunSukarela && $totalDistribusi > 0) {
                $resJurnal = $akuntansiService->catatJurnal(
                    date('Y-m-d'),
                    "Distribusi SHU Anggota Tahun $tahun",
                    [
                        ['akun_id' => $akunShu->id, 'posisi' => 'debit', 'nominal' => $totalDistribusi],
                        ['akun_id' => $akunSukarela->id, 'posisi' => 'kredit', 'nominal' => $totalDistribusi]
                    ],
                    'SHU-'.$tahun
                );
                if (!$resJurnal['success']) {
                    throw new \Exception('Gagal Catat Jurnal: ' . $resJurnal['message']);
                }
            }
            
            $this->logAudit('TUTUP_BUKU', "Tutup Buku dan Pembagian SHU Tahun $tahun berhasil.");
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal melakukan tutup buku. Error: ' . json_encode($this->db->error()));
            }
            
            return ['success' => true, 'message' => "SHU Tahun $tahun berhasil dibagikan ke Simpanan Sukarela."];
            
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }
}
