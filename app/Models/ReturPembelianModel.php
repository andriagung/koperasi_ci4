<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturPembelianModel extends Model
{
    protected $table            = 'retur_pembelian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_retur', 'po_id', 'supplier_id', 'tanggal', 'produk_id', 'jumlah', 
        'harga_satuan', 'total', 'alasan', 'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
