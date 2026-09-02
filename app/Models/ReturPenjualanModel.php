<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturPenjualanModel extends Model
{
    protected $table            = 'retur_penjualan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_retur', 'transaksi_id', 'tanggal', 'produk_id', 'jumlah', 
        'harga_satuan', 'total', 'alasan', 'status', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
