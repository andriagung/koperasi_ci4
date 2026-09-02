<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'role_id', 'username', 'email', 'password_hash',
        'name', 'phone', 'avatar', 'status', 'last_login_at',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function getWithRole(int $id): ?array
    {
        return $this->db->table('users u')
            ->select('u.*, r.name as role_name')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.id', $id)
            ->get()->getRowArray();
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->table('users')
            ->where('username', $username)
            ->where('status', 'active')
            ->get()->getRowArray();
    }

    // ── Permission ──────────────────────────────────────────
    public function getPermissions(int $userId): array
    {
        $user = $this->find($userId);
        if (!$user) return [];

        $rows = $this->db->table('role_permissions rp')
            ->select('p.name')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $user['role_id'])
            ->get()->getResultArray();

        return array_column($rows, 'name');
    }

    public function hasPermission(int $userId, string $permission): bool
    {
        return in_array($permission, $this->getPermissions($userId));
    }

    // ── Semua user dengan role ───────────────────────────────
    public function getAllWithRole(): array
    {
        return $this->db->table('users u')
            ->select('u.id, u.name, u.username, u.email, u.status, u.last_login_at, r.name as role_name')
            ->join('roles r', 'r.id = u.role_id')
            ->orderBy('u.name')
            ->get()->getResultArray();
    }

    // ── Update last login ────────────────────────────────────
    public function updateLastLogin(int $userId): void
    {
        $this->update($userId, ['last_login_at' => date('Y-m-d H:i:s')]);
    }
}
