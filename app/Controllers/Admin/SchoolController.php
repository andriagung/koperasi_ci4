<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SchoolModel;
use App\Models\AuditLogModel;

/**
 * SchoolController: CRUD sekolah mitra penelitian.
 * Hanya admin/peneliti yang dapat mengakses (via RoleAuthFilter).
 */
class SchoolController extends BaseController
{
    protected SchoolModel $schoolModel;
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->schoolModel = new SchoolModel();
        $this->auditModel  = new AuditLogModel();
    }

    /** Daftar semua sekolah + jumlah siswa per sekolah */
    public function index(): string
    {
        return $this->renderView('admin/schools/index', [
            'title'   => 'Manajemen Sekolah — SIREMAJA',
        ]);
    }

    /** DataTables SSP for Schools */
    public function ajaxList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Direct access not allowed');
        }

        $postData = $this->request->getPost();
        $result = $this->schoolModel->getDatatables($postData);

        $data = [];
        $no = $postData['start'] ?? 0;

        foreach ($result['data'] as $school) {
            $no++;
            $row = [];
            
            // 0: Number
            $row[] = '<span style="color:var(--text-muted)">' . $no . '</span>';
            
            // 1: Name & Address
            $address = $school['address'] ? '<div style="font-size:0.75rem;color:var(--text-muted)">' . esc($school['address']) . '</div>' : '';
            $row[] = '<div class="fw-semibold text-white">' . esc($school['name']) . '</div>' . $address;
            
            // 2: Area Type
            $areaBg = $school['area_type'] === 'semi_urban' ? 'rgba(14,165,233,0.15)' : 'rgba(124,58,237,0.15)';
            $areaColor = $school['area_type'] === 'semi_urban' ? '#38BDF8' : '#A78BFA';
            $areaText = $school['area_type'] === 'semi_urban' ? 'Semi Perkotaan' : 'Perkotaan';
            $row[] = '<span class="badge" style="font-size:0.72rem;background:' . $areaBg . ';color:' . $areaColor . '">' . $areaText . '</span>';
            
            // 3: Intervention Count
            $row[] = '<div class="text-center"><span class="badge badge-intervention" style="font-size:0.75rem">' . $school['intervention_count'] . '</span></div>';
            
            // 4: Control Count
            $row[] = '<div class="text-center"><span class="badge badge-control" style="font-size:0.75rem">' . $school['control_count'] . '</span></div>';
            
            // 5: Total Students
            $row[] = '<div class="text-center fw-bold text-white">' . $school['total_students'] . '</div>';
            
            // 6: Actions
            $editUrl = base_url("admin/schools/edit/{$school['id']}");
            $deleteUrl = base_url("admin/schools/delete/{$school['id']}");
            $csrf = csrf_field();
            $actionBtn = '
            <div class="d-flex gap-2 justify-content-center">
                <a href="'.$editUrl.'" class="btn btn-sm" style="background:rgba(37,99,235,0.1);color:#60A5FA;border:1px solid rgba(37,99,235,0.2);border-radius:0.5rem" title="Edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="'.$deleteUrl.'" method="post" onsubmit="return confirm(\'Yakin hapus sekolah ini?\')">
                    '.$csrf.'
                    <button type="submit" class="btn btn-sm" style="background:rgba(220,38,38,0.1);color:#F87171;border:1px solid rgba(220,38,38,0.2);border-radius:0.5rem" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>';
            $row[] = $actionBtn;

            $data[] = $row;
        }

        return $this->response->setJSON([
            'draw'            => $postData['draw'] ?? 1,
            'recordsTotal'    => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data'            => $data
        ]);
    }

    /** Form tambah sekolah baru */
    public function create(): string
    {
        return $this->renderView('admin/schools/form', [
            'title'  => 'Tambah Sekolah — SIREMAJA',
            'school' => null,
            'action' => base_url('admin/schools/store'),
        ]);
    }

    /** Simpan sekolah baru */
    public function store()
    {
        $rules = [
            'name'      => 'required|max_length[200]',
            'area_type' => 'required|in_list[semi_urban,urban]',
            'address'   => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->schoolModel->insert([
            'name'           => $this->request->getPost('name'),
            'area_type'      => $this->request->getPost('area_type'),
            'address'        => $this->request->getPost('address'),
            'contact_person' => $this->request->getPost('contact_person'),
        ]);

        $this->auditModel->log('CREATE', 'schools', $id, null, $this->schoolModel->find($id));

        return redirect()->to(base_url('admin/schools'))->with('success', 'Sekolah berhasil ditambahkan.');
    }

    /** Form edit sekolah */
    public function edit(int $id)
    {
        $school = $this->schoolModel->find($id);
        if (! $school) {
            return redirect()->to(base_url('admin/schools'))->with('error', 'Sekolah tidak ditemukan.');
        }

        return $this->renderView('admin/schools/form', [
            'title'  => 'Edit Sekolah — SIREMAJA',
            'school' => $school,
            'action' => base_url("admin/schools/update/{$id}"),
        ]);
    }

    /** Update data sekolah */
    public function update(int $id)
    {
        $school = $this->schoolModel->find($id);
        if (! $school) {
            return redirect()->to(base_url('admin/schools'))->with('error', 'Sekolah tidak ditemukan.');
        }

        $rules = [
            'name'      => 'required|max_length[200]',
            'area_type' => 'required|in_list[semi_urban,urban]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newData = [
            'name'           => $this->request->getPost('name'),
            'area_type'      => $this->request->getPost('area_type'),
            'address'        => $this->request->getPost('address'),
            'contact_person' => $this->request->getPost('contact_person'),
        ];

        $this->auditModel->log('UPDATE', 'schools', $id, $school, $newData);
        $this->schoolModel->update($id, $newData);

        return redirect()->to(base_url('admin/schools'))->with('success', 'Data sekolah berhasil diperbarui.');
    }

    /** Hapus sekolah (hanya jika tidak ada siswa terdaftar) */
    public function delete(int $id)
    {
        $school = $this->schoolModel->find($id);
        if (! $school) {
            return redirect()->to(base_url('admin/schools'))->with('error', 'Sekolah tidak ditemukan.');
        }

        $studentModel = new \App\Models\StudentModel();
        $studentCount = $studentModel->where('school_id', $id)->countAllResults();

        if ($studentCount > 0) {
            return redirect()->to(base_url('admin/schools'))
                ->with('error', "Tidak dapat menghapus sekolah yang masih memiliki {$studentCount} siswa terdaftar.");
        }

        $this->auditModel->log('DELETE', 'schools', $id, $school);
        $this->schoolModel->delete($id);

        return redirect()->to(base_url('admin/schools'))->with('success', 'Sekolah berhasil dihapus.');
    }
}
