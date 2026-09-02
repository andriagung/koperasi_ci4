<?php
namespace App\Services;

class KasService extends BaseService {
    
    /**
     * Debit Kas (Pemasukan / Uang Masuk)
     */
    public function debit(int $kasId, float $nominal, string $referensiType, int $referensiId, string $keterangan, int $akunLawanId = null): void {
        if ($nominal <= 0) return;
        
        $kas = $this->db->table('kas')->where('id', $kasId)->get()->getRowArray();
        if (!$kas) throw new \Exception("Kas tidak ditemukan.");
        
        $saldoSebelum = (float)$kas['saldo'];
        $saldoSesudah = $saldoSebelum + $nominal;
        
        $this->db->table('kas_transaksi')->insert([
            'kas_id' => $kasId,
            'tanggal' => date('Y-m-d'),
            'jenis' => 'Masuk',
            'nominal' => $nominal,
            'keterangan' => $keterangan,
            'akun_lawan_id' => $akunLawanId ?? 0,
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->table('kas')->where('id', $kasId)->update([
            'saldo' => $saldoSesudah,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Kredit Kas (Pengeluaran / Uang Keluar)
     */
    public function kredit(int $kasId, float $nominal, string $referensiType, int $referensiId, string $keterangan, int $akunLawanId = null): void {
        if ($nominal <= 0) return;
        
        $kas = $this->db->table('kas')->where('id', $kasId)->get()->getRowArray();
        if (!$kas) throw new \Exception("Kas tidak ditemukan.");
        
        $saldoSebelum = (float)$kas['saldo'];
        // Koperasi mungkin mengizinkan kas minus, jadi kita tidak hardblock saldo kurang
        $saldoSesudah = $saldoSebelum - $nominal;
        
        $this->db->table('kas_transaksi')->insert([
            'kas_id' => $kasId,
            'tanggal' => date('Y-m-d'),
            'jenis' => 'Keluar',
            'nominal' => $nominal,
            'keterangan' => $keterangan,
            'akun_lawan_id' => $akunLawanId ?? 0,
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->table('kas')->where('id', $kasId)->update([
            'saldo' => $saldoSesudah,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
