<?php
namespace App\Models;

use CodeIgniter\Model;

class BankTransaksiModel extends Model
{
    protected $table            = 'bank_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nomor_transaksi', 'rekening_bank_id', 'tanggal', 'jenis', 'referensi_type', 'referensi_id', 'nominal', 'saldo_sebelum', 'saldo_sesudah', 'keterangan', 'created_by'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
