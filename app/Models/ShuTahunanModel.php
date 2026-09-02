<?php

namespace App\Models;

use CodeIgniter\Model;

class ShuTahunanModel extends Model
{
    protected $table            = 'shu_tahunan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tahun', 'total_shu', 'cadangan', 'dana_pendidikan', 'dana_sosial', 
        'dana_pengurus', 'total_jasa_modal', 'total_jasa_usaha', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
