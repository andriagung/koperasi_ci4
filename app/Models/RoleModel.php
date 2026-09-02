<?php
namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = ['name', 'description'];

    public function getWithPermissions(int $roleId): array
    {
        $role = $this->find($roleId);
        if (!$role) return [];

        $perms = $this->db->table('role_permissions rp')
            ->select('p.*')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $roleId)
            ->orderBy('p.module, p.action')
            ->get()->getResultArray();

        $role['permissions'] = $perms;
        return $role;
    }
}
