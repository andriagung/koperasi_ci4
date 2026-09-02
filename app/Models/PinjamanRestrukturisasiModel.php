<?php
namespace App\Models;
use CodeIgniter\Model;

class PinjamanRestrukturisasiModel extends Model {
    protected $table = 'pinjaman_restrukturisasi';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['pinjaman_id', 'sisa_pokok', 'tenor_baru', 'bunga_baru', 'alasan', 'tanggal_efektif'];
    protected $useTimestamps = true;
}
