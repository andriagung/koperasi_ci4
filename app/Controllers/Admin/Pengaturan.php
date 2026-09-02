<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;
use App\Models\ProdukWaserdaModel;

use App\Models\PengaturanModel;
use App\Models\AdminUsersModel;
use App\Models\RiwayatTransaksiModel;
use App\Models\AkunCoaModel;
use App\Models\JurnalTransaksiModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderModel;
use App\Models\AuditTrailModel;

class Pengaturan extends BaseController
{
    use \App\Traits\DataTablesTrait;

    public function index()
    {

        $pengaturanModel = new \App\Models\PengaturanModel();
        $adminUsersModel = new \App\Models\AdminUsersModel();
        $auditTrailModel = new \App\Models\AuditTrailModel();
        
        $data = [
            'pengaturan' => $pengaturanModel->first(),
            'adminUsers' => $adminUsersModel->findAll()
        ];
        return view('admin/pengaturan', $data);
    }

    public function audit()
    {
        $auditTrailModel = new \App\Models\AuditTrailModel();
        $data = [
            'auditTrail' => $auditTrailModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('admin/audit', $data);
    }

    public function riwayat()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('riwayat_transaksi rt')
                      ->select('rt.*, a.nama_lengkap, a.nip')
                      ->join('anggota a', 'a.id = rt.anggota_id', 'left')
                      ->orderBy('rt.created_at', 'DESC');
        $data = [
            'semuaRiwayat' => $builder->get()->getResultArray()
        ];
        return view('admin/riwayat', $data);
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

    public function simpanPengaturan() {
        $pengaturanModel = new PengaturanModel();
        $post = $this->request->getPost();
        foreach ($post as $key => $value) {
            $existing = $pengaturanModel->where('kunci', $key)->first();
            if ($existing) {
                $pengaturanModel->update($existing['id'], ['nilai' => $value]);
            } else {
                $pengaturanModel->insert(['kunci' => $key, 'nilai' => $value]);
            }
        }
        cache()->delete('pengaturan_app');
        return redirect()->to('/admin')->with('message', 'Pengaturan berhasil disimpan');
    }

    // --- Admin Users CRUD ---
    public function tambahAdmin() {
        $model = new AdminUsersModel();
        $model->insert([
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username' => $this->request->getPost('username'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role')
        ]);
        return redirect()->to('/admin')->with('message', 'Admin berhasil ditambahkan.');
    }
    public function editAdmin($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $model = new AdminUsersModel();
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'username' => $this->request->getPost('username'),
            'role' => $this->request->getPost('role')
        ];
        if ($this->request->getPost('password')) {
            $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }
        $model->update($id, $data);
        return redirect()->to('/admin')->with('message', 'Admin berhasil diupdate.');
    }
    public function hapusAdmin($id) {
        $id = idhash_decode($id);
        if (!$id) return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak valid']);

        $model = new AdminUsersModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    // --- Migration & Seeding Tool ---
    public function migrateSeed() {
        $migrate = \Config\Services::migrations();
        try {
            $migrate->latest();
            
            // Seed Pengaturan
            $pengaturanModel = new PengaturanModel();
            if ($pengaturanModel->countAllResults() === 0) {
                $pengaturanModel->insertBatch([
                    ['kunci' => 'limit_pinjaman_max', 'nilai' => '15000000'],
                    ['kunci' => 'jasa_bunga_pinjaman', 'nilai' => '1.0'],
                    ['kunci' => 'limit_kasbon_waserda', 'nilai' => '1500000'],
                    ['kunci' => 'simpanan_wajib_bulan', 'nilai' => '50000'],
                ]);
            }

            // Seed Admin
            $adminModel = new AdminUsersModel();
            if ($adminModel->countAllResults() === 0) {
                $adminModel->insertBatch([
                    ['nama_lengkap' => 'Agung Andri', 'username' => 'agung', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'role' => 'Super Admin'],
                    ['nama_lengkap' => 'Kasir RSUD', 'username' => 'kasir', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'role' => 'Kasir Waserda'],
                    ['nama_lengkap' => 'Staff Keuangan', 'username' => 'keuangan', 'password' => password_hash('password123', PASSWORD_DEFAULT), 'role' => 'Keuangan'],
                ]);
            }

            echo "Migration and Seeding completed successfully.";
        } catch (\Throwable $e) {
            echo "Migration failed: " . $e->getMessage();
        }
    }

    public function backupDb()
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        $sql = "-- Koperasi CI4 Database Backup\n";
        $sql .= "-- Waktu: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            $query = $db->query("SELECT * FROM `" . $table . "`");
            $result = $query->getResultArray();
            
            $sql .= "DROP TABLE IF EXISTS `" . $table . "`;\n";
            $createTable = $db->query("SHOW CREATE TABLE `" . $table . "`")->getRowArray();
            if (isset($createTable['Create Table'])) {
                $sql .= $createTable['Create Table'] . ";\n\n";
            }
            
            if (count($result) > 0) {
                foreach ($result as $row) {
                    $keys = array_map(function($key) { return "`".$key."`"; }, array_keys($row));
                    $values = array_map(function($val) use ($db) { 
                        if ($val === null) return 'NULL';
                        return $db->escape($val); 
                    }, array_values($row));
                    
                    $sql .= "INSERT INTO `" . $table . "` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n\n";
            }
        }
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Log Audit Trail
        $auditModel = new \App\Models\AuditTrailModel();
        $auditModel->logAction('BACKUP_DB', 'Admin melakukan backup database manual.');
        
        return $this->response->download('backup_koperasi_' . date('Ymd_His') . '.sql', $sql);
    }


    public function ajaxDaftarAdmin()
    {
        $model = new \App\Models\AdminUsersModel();
        $result = $this->processDataTables($model, ['username', 'nama_lengkap', 'role']);
        
        $response = [
            'draw' => $result['draw'],
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => []
        ];
        
        foreach ($result['data'] as $i => $row) {
            $roleBadge = '<span class="status-badge status-pending">'.$row['role'].'</span>';
            if($row['role'] == 'Admin Master') $roleBadge = '<span class="status-badge status-approved" style="background:#8b5cf6">Master</span>';
            
            $idHash = idhash_encode($row['id']);
            $actionBtn = '<div class="action-btns"><button class="btn-action edit" onclick="editAdminModal(\''.$idHash.'\', \''.$row['nama_lengkap'].'\', \''.$row['username'].'\', \''.$row['role'].'\')" title="Edit"><i class="fas fa-edit"></i></button></div>';
            
            $response['data'][] = [
                $result['offset'] + $i + 1,
                $row['nama_lengkap'],
                $row['username'],
                $roleBadge,
                $actionBtn
            ];
        }
        return $this->response->setJSON($response);
    }

    public function ajaxAuditTrail()
    {
        $model = new \App\Models\AuditTrailModel();
        // Since we don't have AuditTrailModel yet, wait, we do have it. Wait, does it exist? Let me assume it exists or use DB table directly.
        // Actually earlier it was queried using Db table if I remember correctly or Model.
        $db = \Config\Database::connect();
        $request = service('request');
        $limit = $request->getPost('length') ?? 10;
        $offset = $request->getPost('start') ?? 0;
        $search = $request->getPost('search')['value'] ?? '';
        
        $builder = $db->table('audit_trail');
        $totalData = $builder->countAllResults(false);
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('user_type', $search)
                    ->orLike('user_name', $search)
                    ->orLike('action', $search)
                    ->orLike('ip_address', $search)
                    ->groupEnd();
        }
        $totalFiltered = $builder->countAllResults(false);
        $data = $builder->orderBy('id', 'DESC')->limit($limit, $offset)->get()->getResultArray();
        
        $response = [
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => []
        ];
        
        foreach ($data as $i => $row) {
            $response['data'][] = [
                date('d/m/Y H:i:s', strtotime($row['created_at'])),
                $row['user_type'],
                $row['user_name'],
                $row['action'],
                $row['ip_address'],
                $row['user_agent']
            ];
        }
        return $this->response->setJSON($response);
    }
}