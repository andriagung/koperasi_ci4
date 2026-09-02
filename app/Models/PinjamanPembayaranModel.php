<?php
namespace App\Models;
use CodeIgniter\Model;

class PinjamanPembayaranModel extends Model {
    protected $table = 'pinjaman_pembayaran';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['jadwal_angsuran_id', 'pinjaman_id', 'tanggal_bayar', 'nominal_bayar', 'denda_dibayar', 'metode_pembayaran', 'kas_id', 'keterangan'];
    protected $useTimestamps = true;
}
