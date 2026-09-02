<?php
namespace App\Models;
use CodeIgniter\Model;

class ProdukPinjamanModel extends Model {
    protected $table = 'produk_pinjaman';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['kode', 'nama', 'jenis_bunga', 'persentase_bunga', 'plafon_min', 'plafon_max', 'tenor_min', 'tenor_max', 'biaya_admin', 'denda_keterlambatan', 'status'];
    protected $useTimestamps = true;
}
