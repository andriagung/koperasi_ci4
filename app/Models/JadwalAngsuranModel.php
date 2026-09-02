<?php
namespace App\Models;
use CodeIgniter\Model;

class JadwalAngsuranModel extends Model {
    protected $table = 'jadwal_angsuran';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['pinjaman_id', 'angsuran_ke', 'jatuh_tempo', 'pokok', 'bunga', 'total_angsuran', 'denda', 'status'];
    protected $useTimestamps = true;
}
