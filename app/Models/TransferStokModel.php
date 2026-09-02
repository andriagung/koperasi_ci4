<?php
namespace App\Models;

use CodeIgniter\Model;

class TransferStokModel extends Model
{
    protected $table            = 'transfer_stok';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nomor_transfer', 'tanggal', 'lokasi_asal_id', 'lokasi_tujuan_id', 'keterangan', 'status', 'created_by'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
