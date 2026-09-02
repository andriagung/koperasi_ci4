<?php
namespace App\Services;

class BankService extends BaseService {
    
    /**
     * Debit Bank (Pemasukan / Uang Masuk ke Bank)
     */
    public function debit(int $bankId, float $nominal, string $referensiType, int $referensiId, string $keterangan, int $createdBy = 0): void {
        if ($nominal <= 0) return;
        
        $bank = $this->db->table('rekening_bank')->where('id', $bankId)->get()->getRowArray();
        if (!$bank) throw new \Exception("Rekening Bank tidak ditemukan.");
        
        $saldoSebelum = (float)$bank['saldo'];
        $saldoSesudah = $saldoSebelum + $nominal;
        $noTransaksi = 'BM-' . date('YmdHis');
        
        $this->db->table('bank_transaksi')->insert([
            'rekening_bank_id' => $bankId,
            'nomor_transaksi' => $noTransaksi,
            'tanggal' => date('Y-m-d'),
            'jenis' => 'masuk',
            'nominal' => $nominal,
            'keterangan' => $keterangan,
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'created_by' => $createdBy,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->table('rekening_bank')->where('id', $bankId)->update([
            'saldo' => $saldoSesudah,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Kredit Bank (Pengeluaran / Uang Keluar dari Bank)
     */
    public function kredit(int $bankId, float $nominal, string $referensiType, int $referensiId, string $keterangan, int $createdBy = 0): void {
        if ($nominal <= 0) return;
        
        $bank = $this->db->table('rekening_bank')->where('id', $bankId)->get()->getRowArray();
        if (!$bank) throw new \Exception("Rekening Bank tidak ditemukan.");
        
        $saldoSebelum = (float)$bank['saldo'];
        $saldoSesudah = $saldoSebelum - $nominal;
        $noTransaksi = 'BK-' . date('YmdHis');
        
        $this->db->table('bank_transaksi')->insert([
            'rekening_bank_id' => $bankId,
            'nomor_transaksi' => $noTransaksi,
            'tanggal' => date('Y-m-d'),
            'jenis' => 'keluar',
            'nominal' => $nominal,
            'keterangan' => $keterangan,
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'created_by' => $createdBy,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->table('rekening_bank')->where('id', $bankId)->update([
            'saldo' => $saldoSesudah,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
