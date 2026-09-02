<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\AuditTrailModel;

class Auth extends BaseController
{
    protected $anggotaModel;
    protected $auditModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->auditModel = new AuditTrailModel();
    }

    public function login()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $nip = $this->request->getPost('nip');
        $pin = $this->request->getPost('pin');

        $throttler = \Config\Services::throttler();
        $ip = $this->request->getIPAddress();
        if ($throttler->check('login_member_' . md5($ip), 5, MINUTE) === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terlalu banyak percobaan login. Silakan coba lagi setelah 1 menit.']);
        }

        $anggota = $this->anggotaModel->where('nip', $nip)->first();

        if ($anggota) {
            if (password_verify($pin, $anggota['pin'])) {
                if ($anggota['status'] != 'Aktif') {
                    $this->auditModel->logAction('LOGIN_FAILED', 'Percobaan login ditolak karena status tidak aktif. NIP: ' . $nip, 'Anggota', $anggota['id']);
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Status anggota tidak aktif.']);
                }

                // Set session
                $sessionData = [
                    'id'           => $anggota['id'],
                    'anggota_id'   => $anggota['id'],
                    'nip'          => $anggota['nip'],
                    'nama_lengkap' => $anggota['nama_lengkap'],
                    'isLoggedIn'   => true,
                    'role'         => 'Anggota',
                ];
                session()->set($sessionData);

                $this->auditModel->logAction('LOGIN_SUCCESS', 'Anggota berhasil login. NIP: ' . $nip, 'Anggota', $anggota['id']);

                return $this->response->setJSON(['status' => 'success']);
            } else {
                $this->auditModel->logAction('LOGIN_FAILED', 'Percobaan login gagal (PIN salah). NIP: ' . $nip, 'Anggota', $anggota['id']);
                return $this->response->setJSON(['status' => 'error', 'message' => 'PIN salah!']);
            }
        } else {
            $this->auditModel->logAction('LOGIN_FAILED', 'Percobaan login gagal (NIP tidak ditemukan). NIP: ' . $nip, 'Unknown', null);
            return $this->response->setJSON(['status' => 'error', 'message' => 'NIP tidak ditemukan!']);
        }
    }

    public function logout()
    {
        if(session()->get('isLoggedIn')) {
            $this->auditModel->logAction('LOGOUT', 'Anggota logout. NIP: ' . session()->get('nip'), 'Anggota', session()->get('id'));
        }
        session()->destroy();
        return redirect()->to('/');
    }
}
