<?php
namespace App\Models;
use CodeIgniter\Model;

class SimpananTransaksiModel extends Model {
    protected $table = 'simpanan_transaksi';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nomor_transaksi', 'anggota_id', 'jenis_simpanan_id', 'tanggal', 'jenis_transaksi', 'nominal', 'metode_pembayaran', 'referensi', 'kas_id', 'keterangan', 'status', 'created_by', 'approved_by'];
    protected $useTimestamps = true;
}
