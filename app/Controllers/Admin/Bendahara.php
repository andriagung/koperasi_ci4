<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BendaharaGajiModel;
use App\Models\UserModel;

class Bendahara extends BaseController
{
    protected $bendaharaModel;
    protected $adminModel;

    public function __construct()
    {
        $this->bendaharaModel = new BendaharaGajiModel();
        $this->adminModel = new UserModel();
    }

    public function index()
    {
        // Get all bendahara
        $bendahara = $this->bendaharaModel->select('bendahara_gaji.*, users.username as admin_username, users.name as admin_nama')
                                    ->join('users', 'users.id = bendahara_gaji.user_id', 'left')
                                    ->findAll();
        
        $data = [
            'title' => 'Manajemen Bendahara Gaji',
            'bendahara' => $bendahara,
            'admin_users' => $this->adminModel->select('users.*')->join('roles', 'roles.id = users.role_id')->where('roles.name', 'Bendahara')->findAll() // Show users with role Bendahara
        ];

        return view('admin/bendahara/index', $data);
    }

    public function simpan()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/admin/bendahara')->with('error', 'Metode tidak diizinkan');
        }

        $rules = [
            'nama_instansi' => 'required',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/bendahara')->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        $idRaw = $this->request->getPost('id');
        $id = $idRaw ? idhash_decode($idRaw) : null;

        $data = [
            'nama_instansi' => $this->request->getPost('nama_instansi'),
            'email' => $this->request->getPost('email'),
            'user_id' => $this->request->getPost('user_id') ?: null,
        ];

        if ($id) {
            $this->bendaharaModel->update($id, $data);
            return redirect()->to('/admin/bendahara')->with('message', 'Data Bendahara Gaji berhasil diupdate.');
        } else {
            $this->bendaharaModel->insert($data);
            return redirect()->to('/admin/bendahara')->with('message', 'Data Bendahara Gaji berhasil ditambahkan.');
        }
    }

    public function hapus($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/admin/bendahara')->with('error', 'Metode tidak diizinkan');
        }

        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $this->bendaharaModel->delete($id);
        return redirect()->to('/admin/bendahara')->with('message', 'Data Bendahara Gaji berhasil dihapus.');
    }

    public function ajaxBendahara()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $builder = $this->bendaharaModel
            ->select('bendahara_gaji.id, bendahara_gaji.nama_instansi, bendahara_gaji.email, bendahara_gaji.user_id, users.username as admin_username, users.name as admin_nama, COUNT(anggota.id) as jumlah_anggota')
            ->join('users', 'users.id = bendahara_gaji.user_id', 'left')
            ->join('anggota', 'anggota.bendahara_id = bendahara_gaji.id', 'left')
            ->groupBy('bendahara_gaji.id');

        return \Hermawan\DataTables\DataTable::of($builder)
            ->edit('admin_nama', function($row) {
                if ($row->admin_username) {
                    return '<span class="badge bg-success">' . esc($row->admin_nama) . ' (@' . esc($row->admin_username) . ')</span>';
                } else {
                    return '<span class="badge bg-secondary">Tidak ada akun terkait</span>';
                }
            })
            ->add('aksi', function($row) {
                $idHash = idhash_encode($row->id);
                $rowArr = (array) $row;
                $rowArr['hash_id'] = $idHash;
                $rowData = htmlspecialchars(json_encode($rowArr), ENT_QUOTES, 'UTF-8');
                
                return '
                <div class="d-flex justify-content-center gap-1">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Bendahara" onclick="editBendahara(' . $rowData . ')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="' . base_url('admin/bendahara/hapus/' . $idHash) . '" method="post" class="d-inline m-0">
                        <input type="hidden" name="' . csrf_token() . '" value="' . csrf_hash() . '">
                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Data" onclick="return confirm(\'Yakin ingin menghapus data ini?\')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                ';
            })
            ->toJson(true);
    }
}
