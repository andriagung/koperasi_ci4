<?php
namespace App\Models;

use CodeIgniter\Model;

class AnalitikModel extends Model
{
    /**
     * Get Tren Anggota Baru
     */
    public function getTrenAnggotaBaru($dateLike)
    {
        $q = $this->db->query("SELECT COUNT(id) as total FROM anggota WHERE created_at LIKE ?", [$dateLike])->getRow();
        return $q->total ?? 0;
    }

    /**
     * Get Tren Simpanan Sukarela Masuk
     */
    public function getTrenSimpananMasuk($dateLike)
    {
        $q = $this->db->query("SELECT SUM(nominal) as total FROM riwayat_transaksi WHERE kategori = 'Simpanan' AND jenis_transaksi = 'Masuk' AND created_at LIKE ?", [$dateLike])->getRow();
        return $q->total ?? 0;
    }

    /**
     * Get Tren Pencairan Pinjaman
     */
    public function getTrenPinjaman($dateLike)
    {
        $q = $this->db->query("SELECT SUM(nominal_pengajuan) as total FROM pinjaman WHERE status_pengajuan IN ('Dicairkan','Berjalan','Lunas') AND created_at LIKE ?", [$dateLike])->getRow();
        return $q->total ?? 0;
    }

    /**
     * Get Tren Pemasukan Waserda
     */
    public function getTrenWaserda($dateLike)
    {
        $q = $this->db->query("SELECT SUM(m.jumlah * p.harga_normal) as total FROM stok_mutasi m JOIN produk_waserda p ON p.id = m.produk_id WHERE m.jenis = 'Keluar' AND m.created_at LIKE ?", [$dateLike])->getRow();
        return $q->total ?? 0;
    }

    /**
     * Get Top Penabung
     */
    public function getTopPenabung($limit = 5)
    {
        return $this->db->query("
            SELECT a.nama_lengkap, a.divisi, SUM(s.saldo) as total_saldo
            FROM simpanan s
            JOIN anggota a ON a.id = s.anggota_id
            GROUP BY s.anggota_id
            ORDER BY total_saldo DESC
            LIMIT ?
        ", [(int)$limit])->getResultArray();
    }

    /**
     * Get Anomali Penarikan
     */
    public function getAnomaliPenarikan($minNominal = 5000000, $days = 30)
    {
        return $this->db->query("
            SELECT a.nama_lengkap, r.nominal, r.created_at
            FROM riwayat_transaksi r
            JOIN anggota a ON a.id = r.anggota_id
            WHERE r.kategori = 'Simpanan' AND r.jenis_transaksi = 'Keluar' 
              AND r.nominal >= ? 
              AND r.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY r.created_at DESC
        ", [$minNominal, $days])->getResultArray();
    }

    /**
     * Get Riwayat Pinjaman
     */
    public function getRiwayatPinjaman($anggotaId)
    {
        return $this->db->table('pinjaman')
            ->select('status_pengajuan')
            ->where('anggota_id', $anggotaId)
            ->get()->getResultArray();
    }
}
