<?php
namespace App\Models;
use CodeIgniter\Model;

class SimpananSaldoModel extends Model {
    protected $table = 'simpanan_saldo';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['anggota_id', 'jenis_simpanan_id', 'saldo', 'updated_at'];

    public function getSaldoAnggota(int $anggotaId): array {
        return $this->db->table('simpanan_saldo ss')
            ->select('ss.*, js.nama as jenis_simpanan, js.kode')
            ->join('jenis_simpanan js', 'js.id = ss.jenis_simpanan_id')
            ->where('ss.anggota_id', $anggotaId)
            ->get()->getResultArray();
    }
}
