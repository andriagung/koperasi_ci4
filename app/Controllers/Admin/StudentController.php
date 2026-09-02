<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\SchoolModel;
use App\Models\AuditLogModel;

/**
 * StudentController: Manajemen data siswa anonim.
 * Fitur utama:
 * - CRUD individual siswa
 * - Import massal via CSV/XLSX
 * - Export template CSV
 * - TIDAK menyimpan nama asli/NIK/kontak pribadi (privacy-by-design)
 */
class StudentController extends BaseController
{
    protected StudentModel $studentModel;
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->auditModel   = new AuditLogModel();
    }

    /** Daftar siswa dengan filter sekolah & kelompok */
    public function index(): string
    {
        $schoolId  = $this->request->getGet('school_id');
        $groupType = $this->request->getGet('group_type');
        $consent   = $this->request->getGet('consent_status');

        $schoolModel = new SchoolModel();
        return $this->renderView('admin/students/index', [
            'title'     => 'Manajemen Data Siswa — SIREMAJA',
            'schools'   => $schoolModel->findAll(),
            'filters'   => compact('schoolId', 'groupType', 'consent'),
            // We pass an empty array for students as it's handled by SSP now
            'students'  => []
        ]);
    }

    /** DataTables SSP for Students */
    public function ajaxList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Direct access not allowed');
        }

        $postData = $this->request->getPost();
        
        // Pass filter parameters (if passed via ajax data)
        // Note: Students datatable will send extra params if we configure it to.
        
        $result = $this->studentModel->getDatatables($postData);

        $data = [];
        $no = $postData['start'] ?? 0;

        foreach ($result['data'] as $student) {
            $no++;
            $row = [];
            
            // 0: #
            $row[] = '<span style="color:var(--text-muted)">' . $no . '</span>';
            
            // 1: Unique Code & School
            $schoolName = $student['school_name'] ? '<div style="font-size:0.75rem;color:var(--text-muted)"><i class="bi bi-building me-1"></i>' . esc($student['school_name']) . '</div>' : '<div style="font-size:0.75rem;color:#F87171"><i class="bi bi-exclamation-circle me-1"></i>Belum terikat sekolah</div>';
            $row[] = '<div class="fw-semibold text-white d-flex align-items-center gap-2">' . esc($student['unique_code']) . '</div>' . $schoolName;
            
            // 2: Group Type
            $grpBadge = $student['group_type'] === 'intervention' 
                ? '<span class="badge badge-intervention" style="font-size:0.75rem">Intervensi</span>'
                : '<span class="badge badge-control" style="font-size:0.75rem">Kontrol</span>';
            $row[] = $grpBadge;
            
            // 3: Demographics (Age/Gender)
            $age = $student['age'] ? $student['age'] . ' thn' : '-';
            $gender = $student['gender'] === 'male' ? 'L' : ($student['gender'] === 'female' ? 'P' : '-');
            $row[] = '<div style="font-size:0.8rem;color:var(--text-muted)">' . $age . ' &bull; ' . $gender . '</div>';
            
            // 4: Guardian Consent Form
            $formBadge = $student['guardian_consent_received'] 
                ? '<span class="badge" style="background:rgba(16,185,129,0.15);color:#34D399;font-size:0.72rem"><i class="bi bi-file-check me-1"></i>Terkumpul</span>'
                : '<span class="badge" style="background:rgba(239,68,68,0.15);color:#F87171;font-size:0.72rem"><i class="bi bi-file-x me-1"></i>Belum</span>';
            $row[] = $formBadge;

            // 5: Consent Status
            $cStatus = '';
            if ($student['consent_status'] === 'approved') {
                $cStatus = '<span class="badge" style="background:rgba(16,185,129,0.15);color:#34D399;font-size:0.72rem"><i class="bi bi-check2-circle me-1"></i>Disetujui</span>';
            } elseif ($student['consent_status'] === 'declined') {
                $cStatus = '<span class="badge" style="background:rgba(239,68,68,0.15);color:#F87171;font-size:0.72rem"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';
            } else {
                $cStatus = '<span class="badge" style="background:rgba(245,158,11,0.15);color:#FBBF24;font-size:0.72rem"><i class="bi bi-hourglass me-1"></i>Pending</span>';
            }
            $row[] = $cStatus;
            
            // 6: Actions
            $editUrl = base_url("admin/students/edit/{$student['id']}");
            $deleteUrl = base_url("admin/students/delete/{$student['id']}");
            $csrf = csrf_field();
            $actionBtn = '
            <div class="d-flex gap-2">
                <a href="'.$editUrl.'" class="btn btn-sm" style="background:rgba(37,99,235,0.1);color:#60A5FA;border:1px solid rgba(37,99,235,0.2);border-radius:0.5rem" title="Edit Data">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="'.$deleteUrl.'" method="post" onsubmit="return confirm(\'Yakin hapus data siswa anonim ini?\')">
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

    /** Form tambah siswa individual */
    public function create(): string
    {
        $schoolModel = new SchoolModel();
        return $this->renderView('admin/students/form', [
            'title'   => 'Tambah Siswa — SIREMAJA',
            'student' => null,
            'schools' => $schoolModel->findAll(),
            'action'  => base_url('admin/students/store'),
        ]);
    }

    /** Simpan siswa baru */
    public function store()
    {
        $rules = [
            'unique_code' => 'required|max_length[50]|is_unique[students.unique_code]',
            'school_id'   => 'required|integer',
            'group_type'  => 'required|in_list[intervention,control]',
            'age'         => 'permit_empty|integer|greater_than[10]|less_than[25]',
            'gender'      => 'permit_empty|in_list[male,female]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->studentModel->insert([
            'unique_code'               => strtoupper(trim($this->request->getPost('unique_code'))),
            'school_id'                 => $this->request->getPost('school_id'),
            'group_type'                => $this->request->getPost('group_type'),
            'age'                       => $this->request->getPost('age') ?: null,
            'gender'                    => $this->request->getPost('gender') ?: null,
            'guardian_consent_received' => $this->request->getPost('guardian_consent_received') ? 1 : 0,
            'consent_status'            => 'pending',
        ]);

        $this->auditModel->log('CREATE', 'students', $id);
        return redirect()->to(base_url('admin/students'))->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /** Form edit siswa */
    public function edit(int $id): \CodeIgniter\HTTP\RedirectResponse|string
    {
        $student = $this->studentModel->find($id);
        if (! $student) return redirect()->to(base_url('admin/students'))->with('error', 'Siswa tidak ditemukan.');

        $schoolModel = new SchoolModel();
        return $this->renderView('admin/students/form', [
            'title'   => 'Edit Data Siswa — SIREMAJA',
            'student' => $student,
            'schools' => $schoolModel->findAll(),
            'action'  => base_url("admin/students/update/{$id}"),
        ]);
    }

    /** Update data siswa */
    public function update(int $id)
    {
        $student = $this->studentModel->find($id);
        if (! $student) return redirect()->to(base_url('admin/students'))->with('error', 'Siswa tidak ditemukan.');

        $rules = [
            'unique_code' => "required|max_length[50]|is_unique[students.unique_code,id,{$id}]",
            'school_id'   => 'required|integer',
            'group_type'  => 'required|in_list[intervention,control]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newData = [
            'unique_code'               => strtoupper(trim($this->request->getPost('unique_code'))),
            'school_id'                 => $this->request->getPost('school_id'),
            'group_type'                => $this->request->getPost('group_type'),
            'age'                       => $this->request->getPost('age') ?: null,
            'gender'                    => $this->request->getPost('gender') ?: null,
            'guardian_consent_received' => $this->request->getPost('guardian_consent_received') ? 1 : 0,
        ];

        $this->auditModel->log('UPDATE', 'students', $id, $student, $newData);
        $this->studentModel->update($id, $newData);

        return redirect()->to(base_url('admin/students'))->with('success', 'Data siswa berhasil diperbarui.');
    }

    /** Hapus siswa (hanya jika tidak punya respons kuesioner) */
    public function delete(int $id)
    {
        $student = $this->studentModel->find($id);
        if (! $student) return redirect()->to(base_url('admin/students'))->with('error', 'Siswa tidak ditemukan.');

        $responseModel = new \App\Models\ResponseModel();
        $responseCount = $responseModel->where('student_id', $id)->countAllResults();

        if ($responseCount > 0) {
            return redirect()->to(base_url('admin/students'))
                ->with('error', "Tidak dapat menghapus siswa yang sudah memiliki {$responseCount} data respons kuesioner.");
        }

        $this->auditModel->log('DELETE', 'students', $id, $student);
        $this->studentModel->delete($id);

        return redirect()->to(base_url('admin/students'))->with('success', 'Data siswa berhasil dihapus.');
    }

    // =====================================================
    //  IMPORT CSV / XLSX
    // =====================================================

    /** Halaman form import */
    public function importForm(): string
    {
        $schoolModel = new SchoolModel();
        return $this->renderView('admin/students/import', [
            'title'   => 'Import Data Siswa — SIREMAJA',
            'schools' => $schoolModel->findAll(),
        ]);
    }

    /** Proses import file CSV/XLSX */
    public function doImport()
    {
        $file = $this->request->getFile('import_file');

        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, ['csv', 'xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format file harus CSV, XLS, atau XLSX.');
        }

        $maxSize = 2 * 1024 * 1024; // 2 MB
        if ($file->getSize() > $maxSize) {
            return redirect()->back()->with('error', 'Ukuran file maksimal 2 MB.');
        }

        try {
            // Pindahkan ke folder sementara
            $tempPath = WRITEPATH . 'uploads/';
            $tempName = 'import_' . time() . '.' . $ext;
            $file->move($tempPath, $tempName);

            $rows = $this->parseFile($tempPath . $tempName, $ext);

            if (empty($rows)) {
                return redirect()->back()->with('error', 'File kosong atau format kolom tidak sesuai template.');
            }

            $imported = 0;
            $skipped  = [];
            
            $schoolModel = new \App\Models\SchoolModel();

            foreach ($rows as $i => $row) {
                $lineNum    = $i + 2; // baris 1 = header
                $uniqueCode = strtoupper(trim($row['unique_code'] ?? ''));
                $schoolId   = (int) ($row['school_id'] ?? 0);
                $groupType  = strtolower(trim($row['group_type'] ?? ''));
                $age        = isset($row['age']) && is_numeric($row['age']) ? (int) $row['age'] : null;
                $gender     = in_array(strtolower($row['gender'] ?? ''), ['male', 'female']) ? strtolower($row['gender']) : null;
                $consent    = in_array((string)($row['guardian_consent_received'] ?? ''), ['1', 'true', 'ya', 'yes']) ? 1 : 0;

                // Validasi baris
                if (empty($uniqueCode)) {
                    $skipped[] = "Baris {$lineNum}: unique_code kosong.";
                    continue;
                }

                if (! in_array($groupType, ['intervention', 'control'])) {
                    $skipped[] = "Baris {$lineNum} ({$uniqueCode}): group_type tidak valid (harus intervention/control).";
                    continue;
                }

                // Cek duplikasi
                $exists = $this->studentModel->where('unique_code', $uniqueCode)->countAllResults();
                if ($exists) {
                    $skipped[] = "Baris {$lineNum} ({$uniqueCode}): kode unik sudah terdaftar — dilewati.";
                    continue;
                }

                // Cek school_id valid
                $schoolExists = $schoolModel->where('id', $schoolId)->countAllResults();
                if (! $schoolExists) {
                    $skipped[] = "Baris {$lineNum} ({$uniqueCode}): school_id={$schoolId} tidak ditemukan.";
                    continue;
                }

                $this->studentModel->insert([
                    'unique_code'               => $uniqueCode,
                    'school_id'                 => $schoolId,
                    'group_type'                => $groupType,
                    'age'                       => $age,
                    'gender'                    => $gender,
                    'guardian_consent_received' => $consent,
                    'consent_status'            => 'pending',
                    'created_at'                => date('Y-m-d H:i:s'),
                    'updated_at'                => date('Y-m-d H:i:s'),
                ]);
                $imported++;
            }

            // Hapus file temp
            @unlink($tempPath . $tempName);

            $this->auditModel->log('IMPORT_STUDENTS', 'students', null, null, ['imported' => $imported, 'skipped' => count($skipped)]);

            $msg = "Import selesai: <strong>{$imported} siswa berhasil</strong> diimpor.";
            if ($skipped) {
                $msg .= '<br><strong>' . count($skipped) . ' baris dilewati:</strong><br>' . implode('<br>', array_map('esc', $skipped));
            }

            return redirect()->to(base_url('admin/students'))->with('success', $msg);

        } catch (\Throwable $e) {
            log_message('error', 'Import siswa error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file: ' . esc($e->getMessage()));
        }
    }

    /**
     * Parse CSV atau XLSX menjadi array asosiatif.
     * Kolom berdasarkan header baris pertama.
     */
    private function parseFile(string $path, string $ext): array
    {
        if ($ext === 'csv') {
            return $this->parseCsv($path);
        }

        // XLSX/XLS — menggunakan PhpSpreadsheet
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(strtoupper($ext === 'xlsx' ? 'Xlsx' : 'Xls'));
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet       = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        if (empty($sheet) || count($sheet) < 2) return [];

        $headers = array_map('strtolower', array_map('trim', $sheet[0]));
        $rows    = [];
        for ($i = 1; $i < count($sheet); $i++) {
            if (array_filter($sheet[$i])) { // skip baris kosong
                $rows[] = array_combine($headers, array_slice($sheet[$i], 0, count($headers)));
            }
        }
        return $rows;
    }

    private function parseCsv(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) === false) return [];

        $headers = null;
        while (($line = fgetcsv($handle, 1000, ',')) !== false) {
            if ($headers === null) {
                $headers = array_map('strtolower', array_map('trim', $line));
                continue;
            }
            if (array_filter($line)) {
                $rows[] = array_combine($headers, array_slice($line, 0, count($headers)));
            }
        }
        fclose($handle);
        return $rows;
    }

    /** Download template CSV untuk import */
    public function downloadTemplate()
    {
        $csv = "unique_code,school_id,age,gender,group_type,guardian_consent_received\n";
        $csv .= "BJS-INT-001,1,15,male,intervention,1\n";
        $csv .= "BJS-INT-002,1,14,female,intervention,1\n";
        $csv .= "BJS-CTR-001,1,15,male,control,1\n";
        $csv .= "BJS-CTR-002,1,14,female,control,1\n";

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template_import_siswa.csv"')
            ->setBody($csv);
    }
}
