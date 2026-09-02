<?php

namespace App\Models;

use CodeIgniter\Model;

class AnggotaModel extends Model
{
    protected $table            = 'anggota';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['nip', 'nama_lengkap', 'divisi', 'no_hp', 'pin', 'limit_kasbon', 'status', 'gaji_pokok', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'email', 'pekerjaan', 'status_perkawinan', 'bendahara_id', 'nik', 'nomor_anggota', 'tanggal_masuk', 'tanggal_keluar', 'foto', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'kode_pos', 'created_by', 'updated_by'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [
        'nip'          => 'required|is_unique[anggota.nip,id,{id}]',
        'nama_lengkap' => 'required',
    ];
}
