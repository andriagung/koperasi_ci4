<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kunci', 'nilai'];

    // Helper to get all settings as key-value pair
    public function getSettings()
    {
        $all = $this->findAll();
        $settings = [];
        foreach($all as $s) {
            $settings[$s['kunci']] = $s['nilai'];
        }
        return $settings;
    }
}
