<?php
namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    public function getMutasiSimpanan($awal, $akhir)
    {
        return $this->db->table('simpanan_transaksi st')
            ->select('st.*, a.nama_lengkap, a.nomor_anggota')
            ->join('anggota a', 'a.id = st.anggota_id')
            ->where('st.tanggal >=', $awal)
            ->where('st.tanggal <=', $akhir)
            ->where('st.status', 'POSTED')
            ->orderBy('st.tanggal', 'ASC')
            ->get()->getResultArray();
    }
    
    public function getPinjamanBeredar($status)
    {
        $builder = $this->db->table('pinjaman p')
            ->select('p.*, p.nominal_pengajuan as jumlah_pinjaman, p.status_pengajuan as status, a.nama_lengkap, a.nomor_anggota, 
                (SELECT SUM(pokok + jasa) FROM pinjaman_angsuran WHERE pinjaman_id = p.id AND status = "Lunas") as total_dibayar')
            ->join('anggota a', 'a.id = p.anggota_id');
            
        if ($status != 'Semua') {
            $builder->where('p.status_pengajuan', $status);
        }
        
        return $builder->get()->getResultArray();
    }
    
    public function getPenjualanWaserda($awal, $akhir)
    {
        return $this->db->table('penjualan_detail pd')
            ->select('pw.barcode as sku, pw.nama_produk, SUM(pd.qty) as total_qty, SUM(pd.subtotal) as total_omset, SUM(pd.hpp * pd.qty) as total_hpp')
            ->join('penjualan p', 'p.id = pd.penjualan_id')
            ->join('produk_waserda pw', 'pw.id = pd.produk_id')
            ->where('p.tanggal >=', $awal)
            ->where('p.tanggal <=', $akhir)
            ->groupBy('pd.produk_id')
            ->orderBy('total_omset', 'DESC')
            ->get()->getResultArray();
    }
    
    public function getStokWaserda()
    {
        return $this->db->table('produk_waserda p')
            ->select('p.barcode as sku, p.nama_produk, p.harga_beli as kategori, p.stok, p.stok_minimum, p.harga_beli, p.harga_normal as harga_jual')
            ->orderBy('p.stok', 'ASC')
            ->get()->getResultArray();
    }

    public function getSlowMoving($tglBatas)
    {
        return $this->db->table('produk_waserda pw')
            ->select('pw.id, pw.nama_produk, pw.barcode as sku, pw.stok, pw.harga_beli, pw.tanggal_kadaluarsa, COALESCE(SUM(pd.qty), 0) as total_terjual')
            ->join('penjualan_detail pd', 'pd.produk_id = pw.id', 'left')
            ->join('penjualan p', "p.id = pd.penjualan_id AND p.tanggal >= '$tglBatas'", 'left')
            ->where('pw.stok >', 0)
            ->groupBy('pw.id')
            ->having('total_terjual <=', 0)
            ->orderBy('pw.stok', 'DESC')
            ->get()->getResultArray();
    }
}
