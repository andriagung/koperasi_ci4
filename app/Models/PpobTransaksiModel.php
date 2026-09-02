<?php

namespace App\Models;

use CodeIgniter\Model;

class PpobTransaksiModel extends Model
{
    protected $table            = 'ppob_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice', 'anggota_id', 'ppob_produk_id', 'no_pelanggan',
        'harga_beli', 'harga_jual', 'biaya_admin', 'total_bayar',
        'metode_pembayaran', 'status', 'keterangan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
