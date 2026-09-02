<?php

namespace App\Models;

use CodeIgniter\Model;

class AnggotaDokumenModel extends Model
{
    protected $table            = 'anggota_dokumen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['anggota_id', 'jenis_dokumen', 'nomor_dokumen', 'file', 'tanggal_upload', 'uploaded_by'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
