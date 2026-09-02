<?php
namespace App\Models;

use CodeIgniter\Model;

class AkuntansiModel extends Model
{
    /**
     * Neraca Saldo
     */
    public function getNeracaSaldo($bulan, $tahun)
    {
        return $this->db->query("
            SELECT a.kode_akun, a.nama_akun,
                SUM(CASE WHEN j.posisi = 'debit' THEN j.nominal ELSE 0 END) as debit,
                SUM(CASE WHEN j.posisi = 'kredit' THEN j.nominal ELSE 0 END) as kredit
            FROM akun_coa a
            LEFT JOIN jurnal_transaksi j ON a.id = j.akun_id AND MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
            GROUP BY a.id
            ORDER BY a.kode_akun ASC
        ", [$bulan, $tahun])->getResultArray();
    }

    /**
     * Pendapatan untuk Laba Rugi
     */
    public function getPendapatan($bulan, $tahun)
    {
        return $this->db->query("
            SELECT a.kode_akun, a.nama_akun, SUM(j.nominal) as total
            FROM jurnal_transaksi j
            JOIN akun_coa a ON a.id = j.akun_id
            WHERE a.tipe_akun = 'Pendapatan' AND j.posisi = 'kredit' 
              AND MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
            GROUP BY a.id
        ", [$bulan, $tahun])->getResultArray();
    }

    /**
     * Beban untuk Laba Rugi
     */
    public function getBeban($bulan, $tahun)
    {
        return $this->db->query("
            SELECT a.kode_akun, a.nama_akun, SUM(j.nominal) as total
            FROM jurnal_transaksi j
            JOIN akun_coa a ON a.id = j.akun_id
            WHERE a.tipe_akun = 'Beban' AND j.posisi = 'debit'
              AND MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
            GROUP BY a.id
        ", [$bulan, $tahun])->getResultArray();
    }

    /**
     * Saldo Aktiva (As of Date)
     */
    public function getAktiva()
    {
        return $this->db->query("
            SELECT a.kode_akun, a.nama_akun, 
                   SUM(CASE WHEN j.posisi = 'debit' THEN j.nominal ELSE 0 END) - 
                   SUM(CASE WHEN j.posisi = 'kredit' THEN j.nominal ELSE 0 END) as saldo
            FROM jurnal_transaksi j
            JOIN akun_coa a ON a.id = j.akun_id
            WHERE a.tipe_akun = 'Aktiva'
            GROUP BY a.id
        ")->getResultArray();
    }

    /**
     * Saldo Kewajiban (As of Date)
     */
    public function getKewajiban()
    {
        return $this->db->query("
            SELECT a.kode_akun, a.nama_akun, 
                   SUM(CASE WHEN j.posisi = 'kredit' THEN j.nominal ELSE 0 END) - 
                   SUM(CASE WHEN j.posisi = 'debit' THEN j.nominal ELSE 0 END) as saldo
            FROM jurnal_transaksi j
            JOIN akun_coa a ON a.id = j.akun_id
            WHERE a.tipe_akun = 'Kewajiban'
            GROUP BY a.id
        ")->getResultArray();
    }

    /**
     * Saldo Ekuitas (As of Date)
     */
    public function getEkuitas()
    {
        return $this->db->query("
            SELECT a.kode_akun, a.nama_akun, 
                   SUM(CASE WHEN j.posisi = 'kredit' THEN j.nominal ELSE 0 END) - 
                   SUM(CASE WHEN j.posisi = 'debit' THEN j.nominal ELSE 0 END) as saldo
            FROM jurnal_transaksi j
            JOIN akun_coa a ON a.id = j.akun_id
            WHERE a.tipe_akun = 'Ekuitas'
            GROUP BY a.id
        ")->getResultArray();
    }
}
