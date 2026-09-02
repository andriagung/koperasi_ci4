<?php
namespace App\Models;
use CodeIgniter\Model;

class ApprovalLogsModel extends Model {
    protected $table = 'approval_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['tabel_referensi', 'referensi_id', 'user_id', 'action', 'catatan'];
    protected $useTimestamps = true;
}
