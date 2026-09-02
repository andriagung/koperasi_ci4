<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamanJaminanModel extends Model
{
    protected $table            = 'pinjaman_jaminan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pinjaman_id', 'jenis_jaminan', 'nomor_dokumen', 'deskripsi', 'nilai_taksasi', 'file_dokumen', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
