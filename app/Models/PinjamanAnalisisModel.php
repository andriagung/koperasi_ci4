<?php
namespace App\Models;
use CodeIgniter\Model;

class PinjamanAnalisisModel extends Model {
    protected $table = 'pinjaman_analisis';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['pinjaman_id', 'pendapatan_bulanan', 'pengeluaran_bulanan', 'angsuran_lain', 'dsr_score', 'catatan_analis', 'rekomendasi'];
    protected $useTimestamps = true;
}
