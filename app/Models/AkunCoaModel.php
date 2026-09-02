<?php

namespace App\Models;

use CodeIgniter\Model;

class AkunCoaModel extends Model
{
    protected $table            = 'akun_coa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_akun', 'nama_akun', 'tipe_akun', 'saldo_normal'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
