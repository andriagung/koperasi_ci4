<?php
namespace App\Models;
use CodeIgniter\Model;

class ShuAnggotaModel extends Model
{
    protected $table            = 'shu_anggota';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'shu_periode_id', 'anggota_id', 'dasar_jasa_modal', 'dasar_jasa_usaha',
        'shu_modal', 'shu_usaha', 'total_shu', 'status', 'disalurkan_ke'
    ];
}
