<?php

namespace App\Models;

use CodeIgniter\Model;

class ShuRiwayatModel extends Model
{
    protected $table            = 'shu_riwayat_pembagian';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['tahun', 'anggota_id', 'nominal_jasa_modal', 'nominal_jasa_anggota', 'total_shu', 'disalurkan_ke', 'created_at'];
}
