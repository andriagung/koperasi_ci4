<?php
namespace App\Models;
use CodeIgniter\Model;

class PinjamanPencairanModel extends Model {
    protected $table = 'pinjaman_pencairan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['pinjaman_id', 'tanggal_pencairan', 'nominal_pencairan', 'biaya_admin', 'nominal_diterima', 'kas_id', 'bukti_transfer'];
    protected $useTimestamps = true;
}
