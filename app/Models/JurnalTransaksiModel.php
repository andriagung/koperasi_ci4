<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalTransaksiModel extends Model
{
    protected $table            = 'jurnal_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nomor_bukti', 'tanggal', 'akun_id', 'posisi', 'nominal', 'keterangan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getJurnalWithAkun()
    {
        return $this->select('jurnal_transaksi.*, akun_coa.kode_akun, akun_coa.nama_akun')
                    ->join('akun_coa', 'akun_coa.id = jurnal_transaksi.akun_id')
                    ->orderBy('jurnal_transaksi.tanggal', 'DESC')
                    ->orderBy('jurnal_transaksi.nomor_bukti', 'DESC')
                    ->orderBy('jurnal_transaksi.id', 'ASC')
                    ->findAll();
    }
}
