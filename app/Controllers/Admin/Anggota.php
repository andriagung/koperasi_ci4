<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;
use App\Models\ProdukWaserdaModel;
use App\Models\PenarikanSimpananModel;
use App\Models\SetoranSimpananModel;
use App\Models\PengaturanModel;
use App\Models\AdminUsersModel;
use App\Models\RiwayatTransaksiModel;
use App\Models\AkunCoaModel;
use App\Models\JurnalTransaksiModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderModel;
use App\Models\AuditTrailModel;

class Anggota extends BaseController
{
    use \App\Traits\DataTablesTrait;

    public function index()
    {

        $anggotaModel = new \App\Models\AnggotaModel();
        $data = [
            'anggota' => $anggotaModel->findAll()
        ];
        return view('admin/anggota', $data);

    }

    public function kartu($hash = null)
    {
        $data = [
            'list_anggota' => $this->anggotaModel->findAll(),
            'anggota' => null
        ];
        
        $id = $hash ? idhash_decode($hash) : $this->request->getGet('id');
        if ($id) {
            $data['anggota'] = $this->anggotaModel->find($id);
        }
        
        return view('admin/anggota/kartu', $data);
    }
    protected $anggotaModel;
    protected $simpananModel;
    protected $pinjamanModel;
    protected $waserdaModel;
    protected $transaksiModel;
    protected $pengaturanModel;
    protected $adminUsersModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $supplierModel;
    protected $poModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
        $this->pinjamanModel = new \App\Models\PinjamanModel();
        $this->waserdaModel = new ProdukWaserdaModel();
        $this->transaksiModel = new RiwayatTransaksiModel();
        $this->pengaturanModel = new PengaturanModel();
        $this->adminUsersModel = new AdminUsersModel();
        $this->coaModel = new AkunCoaModel();
        $this->jurnalModel = new JurnalTransaksiModel();
        $this->supplierModel = new SupplierModel();
        $this->poModel = new PurchaseOrderModel();
    }

    public function tambahAnggota() {
        $model = new AnggotaModel();
        
        $nip = $this->request->getPost('nip');
        
        // Cek duplikasi NIP
        $existing = $model->where('nip', $nip)->first();
        if ($existing) {
            return redirect()->to('/admin')->with('error', 'Gagal: NIP ' . esc($nip) . ' sudah terdaftar di database.');
        }

        $data = [
            'nip' => $nip,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'divisi' => $this->request->getPost('divisi'),
            'pin' => password_hash('123456', PASSWORD_DEFAULT),
            'status' => 'Aktif',
            'no_hp' => $this->request->getPost('no_hp') ?: '-',
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'email' => $this->request->getPost('email'),
            'pekerjaan' => $this->request->getPost('pekerjaan'),
            'status_perkawinan' => $this->request->getPost('status_perkawinan')
        ];

        if ($model->insert($data)) {
            // [AUDIT TRAIL]
            $auditModel = new \App\Models\AuditTrailModel();
            $auditModel->logAction('TAMBAH_ANGGOTA', 'Admin mendaftarkan anggota baru: ' . $this->request->getPost('nama_lengkap') . ' (NIP: ' . $this->request->getPost('nip') . ')');

            return redirect()->to('/admin')->with('message', 'Anggota berhasil ditambahkan');
        }
    }

    public function editAnggota($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $model = new AnggotaModel();
        $model->update($id, [
            'nip' => $this->request->getPost('nip'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'divisi' => $this->request->getPost('divisi'),
            'status' => $this->request->getPost('status'),
            'no_hp' => $this->request->getPost('no_hp') ?: '-',
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'email' => $this->request->getPost('email'),
            'pekerjaan' => $this->request->getPost('pekerjaan'),
            'status_perkawinan' => $this->request->getPost('status_perkawinan')
        ]);
        return redirect()->to('/admin')->with('message', 'Anggota berhasil diupdate.');
    }

    public function hapusAnggota($id)
    {
        $id = idhash_decode($id);
        if (!$id) return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak valid']);

        $model = new AnggotaModel();
        $anggota = $model->find($id);
        if($anggota) {
            $model->delete($id);
            // [AUDIT TRAIL]
            $auditModel = new \App\Models\AuditTrailModel();
            $auditModel->logAction('HAPUS_ANGGOTA', 'Admin menghapus anggota: ' . $anggota['nama_lengkap']);
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function templateImport()
    {
        $filename = "Template_Import_Anggota_" . date('Ymd') . ".csv";
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        
        $file = fopen('php://output', 'w');
        $header = ["NIP", "Nama Lengkap", "Divisi", "No HP"];
        fputcsv($file, $header);
        fputcsv($file, ["198001012010011001", "Dr. Budi Santoso", "IGD", "081234567890"]);
        fclose($file);
        exit;
    }

    public function import()
    {
        $file = $this->request->getFile('file_csv');
        if (!$file->isValid()) {
            return redirect()->to('/admin/anggota')->with('error', 'Gagal: File tidak valid atau tidak ditemukan.');
        }

        if ($file->getExtension() != 'csv') {
            return redirect()->to('/admin/anggota')->with('error', 'Gagal: Ekstensi file harus .csv');
        }

        $filepath = $file->getTempName();
        $handle = fopen($filepath, "r");
        
        $model = new AnggotaModel();
        $importedCount = 0;
        $skippedCount = 0;
        
        $header = true;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($header) {
                $header = false;
                continue;
            }
            
            // Format: NIP, Nama Lengkap, Divisi, No HP
            if (count($data) < 2) continue; // Skip invalid rows
            
            $nip = trim($data[0]);
            $nama = trim($data[1]);
            $divisi = isset($data[2]) ? trim($data[2]) : 'Manajemen';
            $no_hp = isset($data[3]) ? trim($data[3]) : '-';
            
            if (empty($nip) || empty($nama)) continue;
            
            // Cek NIP
            if ($model->where('nip', $nip)->first()) {
                $skippedCount++;
                continue;
            }
            
            $model->insert([
                'nip' => $nip,
                'nama_lengkap' => $nama,
                'divisi' => $divisi,
                'no_hp' => $no_hp,
                'pin' => password_hash('123456', PASSWORD_DEFAULT),
                'status' => 'Aktif'
            ]);
            $importedCount++;
        }
        fclose($handle);
        
        // Audit log
        if ($importedCount > 0) {
            $auditModel = new \App\Models\AuditTrailModel();
            $auditModel->logAction('IMPORT_ANGGOTA', "Admin mengimpor $importedCount anggota baru dari file CSV.");
        }
        
        $msg = "Import Selesai. $importedCount data berhasil ditambahkan.";
        if ($skippedCount > 0) {
            $msg .= " ($skippedCount data dilewati karena NIP sudah ada).";
        }
        
        return redirect()->to('/admin/anggota')->with('message', $msg);
    }

    public function resetPinAnggota($id) {
        $id = idhash_decode($id);
        if (!$id) return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak valid']);

        $model = new AnggotaModel();
        $model->update($id, [
            'pin' => password_hash('123456', PASSWORD_DEFAULT)
        ]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function downloadTemplateCsv()
    {
        $filename = 'Template_Import_Anggota.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        
        $file = fopen('php://output', 'w');
        fputcsv($file, ['NIP', 'Nama Lengkap', 'Divisi', 'No HP']);
        fputcsv($file, ['198501012022011001', 'Budi Santoso', 'IGD', '08123456789']);
        fclose($file);
        exit;
    }

    public function importCsv()
    {
        $file = $this->request->getFile('file_csv');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $csvData = [];
            if (($handle = fopen($file->getTempName(), 'r')) !== false) {
                $header = fgetcsv($handle, 1000, ',');
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $csvData[] = $row;
                }
                fclose($handle);
            }

            $model = new AnggotaModel();
            $count = 0;
            foreach ($csvData as $row) {
                if (count($row) >= 2) {
                    $nip = trim($row[0] ?? '');
                    if(empty($nip)) continue;
                    
                    // Cek jika NIP sudah ada
                    $exists = $model->where('nip', $nip)->first();
                    if(!$exists) {
                        $model->insert([
                            'nip' => $nip,
                            'nama_lengkap' => trim($row[1] ?? ''),
                            'divisi' => trim($row[2] ?? ''),
                            'no_hp' => trim($row[3] ?? ''),
                            'pin' => password_hash('123456', PASSWORD_DEFAULT),
                            'status' => 'Aktif',
                            'tanggal_bergabung' => date('Y-m-d')
                        ]);
                        $count++;
                    }
                }
            }
            
            // [AUDIT TRAIL]
            $auditModel = new \App\Models\AuditTrailModel();
            $auditModel->logAction('IMPORT_ANGGOTA', 'Admin mengimpor '.$count.' anggota via CSV');
            
            return redirect()->to('/admin/anggota')->with('message', $count . ' data anggota berhasil diimport.');
        }
        
        return redirect()->to('/admin/anggota')->with('error', 'Gagal mengupload file CSV.');
    }

    // --- Waserda ---
    public function ajaxAnggota()
    {
        $model = new \App\Models\AnggotaModel();
        $result = $this->processDataTables($model, ['nip', 'nama_lengkap', 'divisi', 'no_hp', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'email', 'pekerjaan', 'status_perkawinan']);
        
        $response = [
            "draw" => $result['draw'],
            "recordsTotal" => $result['recordsTotal'],
            "recordsFiltered" => $result['recordsFiltered'],
            "data" => []
        ];
        
        foreach ($result['data'] as $i => $row) {
            $statusBadge = '';
            if($row['status'] == 'Aktif') $statusBadge = '<span class="status-badge status-approved">Aktif</span>';
            else if($row['status'] == 'Nonaktif') $statusBadge = '<span class="status-badge status-rejected">Nonaktif</span>';
            else $statusBadge = '<span class="status-badge status-pending">'.$row['status'].'</span>';
            
            $idHash = idhash_encode($row['id']);
            $row['hash_id'] = $idHash;
            $rowData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
            $actionBtns = '
                <div class="action-btns">
                    <button class="btn-action edit" onclick="editAnggotaModal('.$rowData.')" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn-action btn-primary" onclick="cetakKartu('.$rowData.')" title="Cetak Kartu"><i class="fas fa-id-card"></i></button>
                    <button class="btn-action" style="background:#f59e0b;color:white" onclick="resetPinAnggota(\''.$idHash.'\')" title="Reset PIN"><i class="fas fa-key"></i></button>
                    <button class="btn-action delete" onclick="hapusAnggota(\''.$idHash.'\')" title="Hapus"><i class="fas fa-trash"></i></button>
                </div>
            ';
            
            $response['data'][] = [
                'no' => $result['offset'] + $i + 1,
                'nip' => $row['nip'],
                'nama_lengkap' => $row['nama_lengkap'],
                'divisi' => $row['divisi'],
                'no_hp' => $row['no_hp'],
                'status_badge' => $statusBadge,
                'aksi' => $actionBtns
            ];
        }
        
        return $this->response->setJSON($response);
    }
}
