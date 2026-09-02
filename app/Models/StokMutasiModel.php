<?php

namespace App\Models;

use CodeIgniter\Model;

class StokMutasiModel extends Model
{
    protected $table            = 'stok_mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['produk_id', 'jenis', 'jumlah', 'keterangan', 'referensi_id', 'referensi_type', 'lokasi_id', 'saldo', 'harga'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getMutasiWithProduk()
    {
        return $this->select('stok_mutasi.*, produk_waserda.nama_produk')
                    ->join('produk_waserda', 'produk_waserda.id = stok_mutasi.produk_id')
                    ->orderBy('stok_mutasi.created_at', 'DESC')
                    ->findAll();
    }
}
