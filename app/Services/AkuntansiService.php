<?php
namespace App\Services;

class AkuntansiService extends BaseService {
    
    /**
     * Catat jurnal akuntansi double-entry
     * 
     * @param string $tanggal Tanggal transaksi (Y-m-d)
     * @param string $keterangan Deskripsi jurnal
     * @param array $details Array of detail jurnal: 
     *      [
     *          ['akun_id' => 1, 'posisi' => 'debit', 'nominal' => 10000],
     *          ['akun_id' => 2, 'posisi' => 'kredit', 'nominal' => 10000]
     *      ]
     * @param string $nomor_bukti_referensi (Optional) Nomor transaksi terkait
     * @return array
     */
    public function catatJurnal(string $tanggal, string $keterangan, array $details, string $nomor_bukti_referensi = null): array {
        // Validasi Double Entry (Balance)
        $totalDebit = 0;
        $totalKredit = 0;
        
        foreach ($details as $d) {
            $pos = strtolower($d['posisi']);
            if ($pos === 'debit') {
                $totalDebit += (float) $d['nominal'];
            } elseif ($pos === 'kredit') {
                $totalKredit += (float) $d['nominal'];
            }
        }
        
        // Cek Balance (toleransi perbedaan desimal 0.01)
        if (abs($totalDebit - $totalKredit) > 0.01) {
            return [
                'success' => false, 
                'message' => "Jurnal tidak balance! Debit: $totalDebit, Kredit: $totalKredit"
            ];
        }
        
        // Cegah Jurnal 0
        if ($totalDebit <= 0) {
            return [
                'success' => false,
                'message' => "Nominal jurnal tidak valid (0)."
            ];
        }

        $this->db->transStart();
        try {
            // Nomor Bukti
            $nomorBukti = $nomor_bukti_referensi ?: $this->generateNomor('JUR');
            
            // Insert Detail Jurnal
            foreach ($details as $d) {
                $this->db->table('jurnal_transaksi')->insert([
                    'nomor_bukti' => $nomorBukti,
                    'tanggal' => $tanggal,
                    'akun_id' => $d['akun_id'],
                    'posisi' => ucfirst(strtolower($d['posisi'])),
                    'nominal' => $d['nominal'],
                    'keterangan' => $keterangan
                ]);
            }
            
            $this->logAudit('JURNAL_AKUNTANSI', 'Jurnal berhasil dicatat: ' . $nomorBukti);
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                $err = $this->db->error();
                return ['success' => false, 'message' => 'Gagal menyimpan jurnal akuntansi. Error: ' . json_encode($err)];
            }
            
            return ['success' => true, 'message' => 'Jurnal berhasil dicatat.', 'nomor_bukti' => $nomorBukti];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }
}
