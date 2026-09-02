<?php

namespace App\Models;

use CodeIgniter\Model;

class PpobProdukModel extends Model
{
    protected $table            = 'ppob_produk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_produk', 'nama_produk', 'kategori', 'provider',
        'harga_beli', 'harga_jual', 'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
