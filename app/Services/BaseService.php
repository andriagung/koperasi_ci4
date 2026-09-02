<?php
namespace App\Services;

abstract class BaseService {
    protected $db;
    
    public function __construct() {
        $this->db = \Config\Database::connect();
    }
    
    // Utility: generate nomor transaksi terpusat
    protected function generateNomor(string $prefix): string {
        $periode = date('Ym');
        $this->db->query("
            INSERT INTO nomor_transaksi (prefix, periode, urutan) 
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE urutan = urutan + 1
        ", [$prefix, $periode]);
        $row = $this->db->query(
            "SELECT urutan FROM nomor_transaksi WHERE prefix = ? AND periode = ?",
            [$prefix, $periode]
        )->getRow();
        return $prefix . '-' . $periode . '-' . str_pad($row->urutan, 6, '0', STR_PAD_LEFT);
    }
    
    // Utility: log audit
    protected function logAudit(string $action, string $description, $dataBefore = null, $dataAfter = null): void {
        $auditModel = new \App\Models\AuditTrailModel();
        // Cek jika session user_id null (misal dari cron atau console), gunakan 0 atau admin system
        $userId = session()->get('user_id') ?? 1; 
        $username = session()->get('username') ?? 'System';
        
        $auditModel->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'module' => 'Service Layer',
            'data_before' => $dataBefore ? json_encode($dataBefore) : null,
            'data_after' => $dataAfter ? json_encode($dataAfter) : null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
