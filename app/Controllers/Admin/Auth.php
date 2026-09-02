<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ── Login Form ───────────────────────────────────────────
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('admin/login');
    }



    // ── Proses Login ─────────────────────────────────────────
    public function login()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $username = trim($this->request->getPost('username'));
        $password = trim((string)$this->request->getPost('password'));

        log_message('error', 'Login attempt: ' . $username);

        // Cari user di tabel users baru
        $user = $this->userModel->findByUsername($username);
        

        
        log_message('error', 'User found: ' . ($user ? 'Yes' : 'No'));

        if (!$user) {
            // Fallback: cek tabel admin_users lama
            $db = \Config\Database::connect();
            $oldUser = $db->table('admin_users')
                ->where('username', $username)
                ->where('deleted_at IS NULL')
                ->get()->getRowArray();

            if ($oldUser && password_verify($password, $oldUser['password'])) {
                // User lama masih valid, arahkan ke users baru
                $user = $this->userModel->where('username', $username)->first();
            }
        }

        if (!$user) {
            log_message('error', 'User not found, redirecting back.');
            return redirect()->back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }

        // Verifikasi password
        if (!password_verify($password, $user['password_hash'])) {
            log_message('error', 'Password verify failed.');
            return redirect()->back()
                ->with('error', 'Username atau password salah.')
                ->withInput();
        }
        
        log_message('error', 'Login success for user: ' . $user['id']);

        // Ambil data user dengan role
        $userWithRole = $this->userModel->getWithRole($user['id']);

        // Ambil semua permission
        $permissions = $this->userModel->getPermissions($user['id']);

        // Set session
        $sessionData = [
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['name'],
            'role_id'      => $user['role_id'],
            'role'         => $userWithRole['role_name'] ?? 'Admin',
            'permissions'  => $permissions,
            'isLoggedIn'   => true,
        ];

        session()->set($sessionData);

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Log audit
        $auditModel = new \App\Models\AuditTrailModel();
        $auditModel->insert([
            'user_id'     => $user['id'],
            'user_name'   => $user['username'],
            'user_type'   => 'Admin',
            'action'      => 'LOGIN',
            'description' => 'User login: ' . $user['username'],
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/dashboard')
            ->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    // ── Logout ───────────────────────────────────────────────
    public function logout()
    {
        $userId = session()->get('user_id');

        if ($userId) {
            $auditModel = new \App\Models\AuditTrailModel();
            $auditModel->insert([
                'user_id'     => $userId,
                'user_type'   => 'Admin',
                'action'      => 'LOGOUT',
                'description' => 'User logout',
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        session()->destroy();
        return redirect()->to('/admin/login')->with('info', 'Anda telah logout.');
    }
}
