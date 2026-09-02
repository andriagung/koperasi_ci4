<?php
namespace App\Models;

use CodeIgniter\Model;

class AnggotaKeluargaModel extends Model
{
    protected $table            = 'anggota_keluarga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'anggota_id', 'nama', 'hubungan', 'tanggal_lahir', 
        'no_hp', 'alamat', 'is_ahli_waris'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getKeluarga(int $anggotaId): array
    {
        return $this->where('anggota_id', $anggotaId)
                    ->orderBy('is_ahli_waris', 'DESC')
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }
}
