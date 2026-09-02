<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SchoolModel;
use App\Models\AuditLogModel;

/**
 * UserController: CRUD akun pengelola (admin, guru, validator, peneliti, mahasiswa).
 * Siswa dikelola di StudentController secara terpisah.
 */
class UserController extends BaseController
{
    protected UserModel $userModel;
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index(): string
    {
        $users = $this->userModel->getUsersWithSchool();

        return view('admin/users/index', [
            'title' => 'Manajemen Akun Pengelola — SIREMAJA',
            'users' => $users,
        ]);
    }

    public function create(): string
    {
        $schoolModel = new SchoolModel();
        return view('admin/users/form', [
            'title'   => 'Tambah Akun Pengelola — SIREMAJA',
            'user'    => null,
            'schools' => $schoolModel->findAll(),
            'action'  => base_url('admin/users/store'),
        ]);
    }

    public function store()
    {
        $rules = [
            'name'     => 'required|max_length[150]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'role'     => 'required|in_list[admin,guru,validator,peneliti,mahasiswa]',
            'password' => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $schoolId = $this->request->getPost('school_id') ?: null;
        // Guru wajib punya school_id
        if ($this->request->getPost('role') === 'guru' && ! $schoolId) {
            return redirect()->back()->withInput()->with('error', 'Guru harus dikaitkan dengan sekolah.');
        }

        $id = $this->userModel->insert([
            'name'          => $this->request->getPost('name'),
            'email'         => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'          => $this->request->getPost('role'),
            'school_id'     => $schoolId,
            'is_active'     => 1,
        ]);

        $this->auditModel->log('CREATE', 'users', $id);
        return redirect()->to(base_url('admin/users'))->with('success', 'Akun pengelola berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) return redirect()->to(base_url('admin/users'))->with('error', 'Akun tidak ditemukan.');

        $schoolModel = new SchoolModel();
        return view('admin/users/form', [
            'title'   => 'Edit Akun — SIREMAJA',
            'user'    => $user,
            'schools' => $schoolModel->findAll(),
            'action'  => base_url("admin/users/update/{$id}"),
        ]);
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) return redirect()->to(base_url('admin/users'))->with('error', 'Akun tidak ditemukan.');

        $rules = [
            'name'  => 'required|max_length[150]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'  => 'required|in_list[admin,guru,validator,peneliti,mahasiswa]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newData = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'role'      => $this->request->getPost('role'),
            'school_id' => $this->request->getPost('school_id') ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Update password hanya jika diisi
        $newPass = $this->request->getPost('password');
        if ($newPass) {
            if (strlen($newPass) < 8) {
                return redirect()->back()->withInput()->with('error', 'Password baru minimal 8 karakter.');
            }
            $newData['password_hash'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $this->auditModel->log('UPDATE', 'users', $id, $user, $newData);
        $this->userModel->update($id, $newData);

        return redirect()->to(base_url('admin/users'))->with('success', 'Akun berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        // Jangan bisa hapus diri sendiri
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if (! $user) return redirect()->to(base_url('admin/users'))->with('error', 'Akun tidak ditemukan.');

        $this->auditModel->log('DELETE', 'users', $id, $user);
        $this->userModel->delete($id);

        return redirect()->to(base_url('admin/users'))->with('success', 'Akun berhasil dihapus.');
    }
}
