<?php
namespace App\Services;

class AccountingService extends BaseService {
    
    /**
     * Catat Jurnal Umum dengan Balanced Check
     * 
     * @param string $tanggal
     * @param string $nomorBukti
     * @param string $keteranganHeader
     * @param array $details Array of associative array: 
     *      [ 'akun_id' => int, 'debit' => float, 'kredit' => float, 'keterangan' => string ]
     * @param string|null $referensiType (optional)
     * @param int|null $referensiId (optional)
     * @param int $createdBy (optional)
     * 
     * @throws \Exception Jika jurnal tidak balance (debit != kredit)
     */
    public function catatJurnal(string $tanggal, string $nomorBukti, string $keteranganHeader, array $details, ?string $referensiType = null, ?int $referensiId = null, int $createdBy = 0): void {
        if (empty($details)) {
            throw new \Exception("Detail jurnal tidak boleh kosong.");
        }
        
        $totalDebit = 0;
        $totalKredit = 0;
        
        foreach ($details as $d) {
            $totalDebit += (float)($d['debit'] ?? 0);
            $totalKredit += (float)($d['kredit'] ?? 0);
        }
        
        // Balanced Check dengan toleransi pembulatan kecil
        if (abs($totalDebit - $totalKredit) > 0.01) {
            throw new \Exception("Jurnal tidak seimbang. Total Debit: $totalDebit, Total Kredit: $totalKredit");
        }
        
        $this->db->transStart();
        
        $jurnalData = [];
        
        foreach ($details as $d) {
            $debit = (float)($d['debit'] ?? 0);
            $kredit = (float)($d['kredit'] ?? 0);
            
            if ($debit > 0) {
                $jurnalData[] = [
                    'nomor_bukti' => $nomorBukti,
                    'tanggal' => $tanggal,
                    'akun_id' => $d['akun_id'],
                    'posisi' => 'Debit',
                    'nominal' => $debit,
                    'keterangan' => $d['keterangan'] ?? $keteranganHeader,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            if ($kredit > 0) {
                $jurnalData[] = [
                    'nomor_bukti' => $nomorBukti,
                    'tanggal' => $tanggal,
                    'akun_id' => $d['akun_id'],
                    'posisi' => 'Kredit',
                    'nominal' => $kredit,
                    'keterangan' => $d['keterangan'] ?? $keteranganHeader,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }
        
        if (!empty($jurnalData)) {
            $this->db->table('jurnal_transaksi')->insertBatch($jurnalData);
        }
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            throw new \Exception("Gagal menyimpan jurnal ke database.");
        }
    }
    
    /**
     * Menghitung saldo berjalan untuk laporan buku besar
     */
    public function getBukuBesar(int $akunId, string $bulan, string $tahun): array {
        // Ambil info saldo normal akun
        $akun = $this->db->table('akun_coa')->where('id', $akunId)->get()->getRowArray();
        if (!$akun) throw new \Exception("Akun tidak ditemukan");
        
        $saldoNormal = $akun['saldo_normal'];
        
        // Ambil saldo awal (sebelum periode terpilih)
        $querySaldoAwal = $this->db->query("
            SELECT 
                SUM(CASE WHEN posisi = 'Debit' THEN nominal ELSE 0 END) as total_debit, 
                SUM(CASE WHEN posisi = 'Kredit' THEN nominal ELSE 0 END) as total_kredit
            FROM jurnal_transaksi
            WHERE akun_id = ? AND tanggal < ?
        ", [$akunId, "$tahun-$bulan-01"]);
        
        $rowSaldoAwal = $querySaldoAwal->getRowArray();
        $saldoAwalDebit = (float)($rowSaldoAwal['total_debit'] ?? 0);
        $saldoAwalKredit = (float)($rowSaldoAwal['total_kredit'] ?? 0);
        
        $saldoBerjalan = 0;
        if ($saldoNormal === 'Debit') {
            $saldoBerjalan = $saldoAwalDebit - $saldoAwalKredit;
        } else {
            $saldoBerjalan = $saldoAwalKredit - $saldoAwalDebit;
        }
        
        // Ambil mutasi dalam periode
        $mutasi = $this->db->query("
            SELECT j.tanggal, j.nomor_jurnal as nomor_bukti, d.keterangan, d.debit, d.kredit
            FROM jurnal_detail d
            JOIN jurnal j ON j.id = d.jurnal_id
            WHERE d.akun_id = ? AND DATE_FORMAT(j.tanggal, '%Y-%m') = ?
            ORDER BY j.tanggal ASC, j.id ASC
        ", [$akunId, "$tahun-$bulan"])->getResultArray();
        
        // Ambil transaksi bulan ini
        $queryTransaksi = $this->db->query("
            SELECT id, nomor_bukti, tanggal, keterangan, posisi, nominal
            FROM jurnal_transaksi
            WHERE akun_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?
            ORDER BY tanggal ASC, id ASC
        ", [$akunId, $bulan, $tahun]);
        
        $transaksi = $queryTransaksi->getResultArray();
        
        $result = [];
        $result[] = [
            'tanggal' => "$tahun-$bulan-01",
            'nomor_bukti' => '-',
            'keterangan' => 'Saldo Awal',
            'debit' => 0,
            'kredit' => 0,
            'saldo' => $saldoBerjalan
        ];
        
        foreach ($transaksi as $t) {
            $debit = $t['posisi'] === 'Debit' ? (float)$t['nominal'] : 0;
            $kredit = $t['posisi'] === 'Kredit' ? (float)$t['nominal'] : 0;
            
            if ($saldoNormal === 'Debit') {
                $saldoBerjalan += ($debit - $kredit);
            } else {
                $saldoBerjalan += ($kredit - $debit);
            }
            
            $result[] = [
                'tanggal' => $t['tanggal'],
                'nomor_bukti' => $t['nomor_bukti'],
                'keterangan' => $t['keterangan'] ?? '-',
                'debit' => $debit,
                'kredit' => $kredit,
                'saldo' => $saldoBerjalan
            ];
        }
        
        return [
            'akun' => $akun,
            'mutasi' => $result,
            'saldo_akhir' => $saldoBerjalan
        ];
    }

    /**
     * Hitung Laba/Rugi (Total Pendapatan - Total Beban)
     */
    public function calculateLabaRugi(): float {
        $query = $this->db->query("
            SELECT 
                SUM(CASE WHEN c.kode_akun LIKE '4%' THEN (COALESCE(j_kredit, 0) - COALESCE(j_debit, 0)) ELSE 0 END) as total_pendapatan,
                SUM(CASE WHEN c.kode_akun LIKE '5%' OR c.kode_akun LIKE '6%' THEN (COALESCE(j_debit, 0) - COALESCE(j_kredit, 0)) ELSE 0 END) as total_beban
            FROM akun_coa c
            LEFT JOIN (
                SELECT akun_id, SUM(debit) as j_debit, SUM(kredit) as j_kredit
                FROM jurnal_detail 
                GROUP BY akun_id
            ) j ON c.id = j.akun_id
            WHERE c.kode_akun LIKE '4%' OR c.kode_akun LIKE '5%' OR c.kode_akun LIKE '6%'
        ")->getRow();

        $pendapatan = (float)($query->total_pendapatan ?? 0);
        $beban = (float)($query->total_beban ?? 0);
        
        return $pendapatan - $beban;
    }
}
