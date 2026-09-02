<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatTransaksiModel extends Model
{
    protected $table            = 'riwayat_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['anggota_id', 'kategori', 'jenis_transaksi', 'nominal', 'keterangan', 'referensi_id', 'tanggal'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';
}
