<?php
namespace App\Models;
use CodeIgniter\Model;

class PenjualanDetailModel extends Model {
    protected $table = 'penjualan_detail';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['penjualan_id', 'produk_id', 'qty', 'harga_satuan', 'hpp', 'subtotal'];
    protected $useTimestamps = true;
}
