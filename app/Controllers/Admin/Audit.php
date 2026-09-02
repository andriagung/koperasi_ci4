<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Audit extends BaseController
{
    public function index()
    {
        $awal = $this->request->getVar('tgl_awal') ?? date('Y-m-d', strtotime('-7 days'));
        $akhir = $this->request->getVar('tgl_akhir') ?? date('Y-m-d');
        
        $auditModel = new \App\Models\AuditTrailModel();
        $auditLog = $auditModel->getLogByDateRange($awal, $akhir);
        
        $data = [
            'awal' => $awal,
            'akhir' => $akhir,
            'judul' => 'Laporan Audit Trail & Aktivitas Sistem',
            'data' => $auditLog
        ];
        
        return view('admin/laporan/audit_trail', $data);
    }
}
