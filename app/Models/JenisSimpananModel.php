<?php
namespace App\Models;
use CodeIgniter\Model;

class JenisSimpananModel extends Model {
    protected $table = 'jenis_simpanan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['kode', 'nama', 'jenis', 'nominal_default', 'minimal_setoran', 'minimal_saldo', 'dapat_ditarik', 'status'];
    protected $useTimestamps = true;
}
