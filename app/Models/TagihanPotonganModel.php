<?php

namespace App\Models;

use CodeIgniter\Model;

class TagihanPotonganModel extends Model
{
    protected $table            = 'tagihan_potongan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'periode',
        'anggota_id',
        'nominal_simpanan_wajib',
        'nominal_angsuran',
        'total_tagihan',
        'angsuran_ids',
        'status',
        'tanggal_bayar'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
