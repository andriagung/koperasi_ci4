<?php
namespace App\Models;
use CodeIgniter\Model;

class KasTransaksiModel extends Model
{
    protected $table = 'kas_transaksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'tanggal', 'jenis', 'nominal', 'keterangan', 'akun_lawan_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
