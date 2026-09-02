<?php
namespace App\Models;
use CodeIgniter\Model;

class PenjualanModel extends Model {
    protected $table = 'penjualan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['no_invoice', 'tanggal', 'anggota_id', 'total_harga', 'total_diskon', 'total_bayar', 'metode_pembayaran', 'status_pembayaran', 'kasir_id'];
    protected $useTimestamps = true;

    public function getFakturData($idPenjualan)
    {
        $penjualan = $this->find($idPenjualan);
        if (!$penjualan) return null;

        $db = \Config\Database::connect();
        
        $builder = $db->table('penjualan_detail pd');
        $builder->select('pd.qty, pd.harga_satuan, pd.subtotal, pw.kode_produk, pw.nama_produk, pw.satuan');
        $builder->join('produk_waserda pw', 'pw.id = pd.produk_id', 'left');
        $builder->where('pd.penjualan_id', $idPenjualan);
        $details = $builder->get()->getResultArray();

        return [
            'penjualan' => $penjualan,
            'details'   => $details
        ];
    }
}
