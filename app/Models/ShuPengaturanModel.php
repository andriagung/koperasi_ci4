<?php

namespace App\Models;

use CodeIgniter\Model;

class ShuPengaturanModel extends Model
{
    protected $table            = 'shu_pengaturan';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_alokasi', 'persentase', 'keterangan'];
}
