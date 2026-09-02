<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditTrailModel extends Model
{
    protected $table            = 'audit_trail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_type', 'user_id', 'user_name', 'action', 'description', 'ip_address', 'user_agent', 'data_before', 'data_after'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function logAction($action, $description, $userType = 'Admin', $userId = null, $dataBefore = null, $dataAfter = null)
    {
        // Simple helper method to insert a log
        $request = \Config\Services::request();
        $session = session();
        $ip = $request->getIPAddress();
        
        $this->insert([
            'user_type'   => $userType,
            'user_id'     => $userId,
            'user_name'   => $session->get('nama_lengkap') ?? $session->get('username') ?? 'System',
            'action'      => $action,
            'description' => $description,
            'ip_address'  => is_cli() ? '127.0.0.1' : $ip,
            'user_agent'  => is_cli() ? 'CLI' : $request->getUserAgent()->getAgentString(),
            'data_before' => $dataBefore ? json_encode($dataBefore) : null,
            'data_after'  => $dataAfter ? json_encode($dataAfter) : null,
        ]);
    }

    public function getLogByDateRange($awal, $akhir)
    {
        return $this->where('created_at >=', $awal . ' 00:00:00')
                    ->where('created_at <=', $akhir . ' 23:59:59')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
