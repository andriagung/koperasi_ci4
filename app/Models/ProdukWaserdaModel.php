<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukWaserdaModel extends Model
{
    protected $table            = 'produk_waserda';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_produk', 'barcode', 'harga_normal', 'harga_member', 'ppn', 'harga_promo', 'ikon', 'is_active', 'harga_beli', 'stok', 'stok_minimum', 'tanggal_kadaluarsa'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getProdukMendekatiKadaluarsa($hari = 30)
    {
        return $this->where('tanggal_kadaluarsa IS NOT NULL')
                    ->where('tanggal_kadaluarsa <=', date('Y-m-d', strtotime("+$hari days")))
                    ->findAll();
    }
}
