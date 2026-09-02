<?php
namespace App\Controllers\Api\V1;

use App\Models\AnggotaModel;

class AuthController extends BaseApiController
{
    protected $anggotaModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
    }

    public function login()
    {
        if (!$this->request->is('post')) {
            return $this->error('Metode tidak diizinkan', null, 405);
        }

        $nomor_anggota = $this->request->getPost('nomor_anggota');
        $password = $this->request->getPost('password');

        if (!$nomor_anggota || !$password) {
            return $this->error('Nomor Anggota dan Password wajib diisi.', null, 400);
        }

        $anggota = $this->anggotaModel->where('nomor_anggota', $nomor_anggota)->first();

        if (!$anggota) {
            return $this->error('Nomor Anggota tidak terdaftar.', null, 404);
        }

        // Pada sistem riil, gunakan password_verify. 
        // Di mock ini, kita cek password = NIK atau password = 123456
        if ($password !== $anggota['nik'] && $password !== '123456') {
            return $this->error('Password salah.', null, 401);
        }

        if ($anggota['status'] !== 'Aktif') {
            return $this->error('Akun Anda tidak aktif.', null, 403);
        }

        // Generate mock token
        $token = 'koperasi-token-' . $anggota['id'];

        return $this->success([
            'token' => $token,
            'anggota' => [
                'id' => $anggota['id'],
                'nomor_anggota' => $anggota['nomor_anggota'],
                'nama_lengkap' => $anggota['nama_lengkap'],
                'status' => $anggota['status']
            ]
        ], 'Login berhasil.');
    }
}
