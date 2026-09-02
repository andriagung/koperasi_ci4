<?php

/**
 * Cek apakah user yang sedang login memiliki permission tertentu.
 *
 * Penggunaan di View atau Controller:
 *   if (can('pinjaman.approve')) { ... }
 */
if (!function_exists('can')) {
    function can(string $permission): bool
    {
        $permissions = session()->get('permissions') ?? [];
        return in_array($permission, $permissions);
    }
}

/**
 * Cek apakah user memiliki salah satu dari beberapa permission.
 *
 * Penggunaan:
 *   if (canAny(['pinjaman.approve', 'pinjaman.verify'])) { ... }
 */
if (!function_exists('canAny')) {
    function canAny(array $permissions): bool
    {
        $userPermissions = session()->get('permissions') ?? [];
        foreach ($permissions as $p) {
            if (in_array($p, $userPermissions)) return true;
        }
        return false;
    }
}

/**
 * Cek apakah user memiliki semua permission.
 *
 * Penggunaan:
 *   if (canAll(['pinjaman.approve', 'pinjaman.disburse'])) { ... }
 */
if (!function_exists('canAll')) {
    function canAll(array $permissions): bool
    {
        $userPermissions = session()->get('permissions') ?? [];
        foreach ($permissions as $p) {
            if (!in_array($p, $userPermissions)) return false;
        }
        return true;
    }
}

/**
 * Cek role user yang sedang login.
 */
if (!function_exists('hasRole')) {
    function hasRole(string $roleName): bool
    {
        return session()->get('role') === $roleName;
    }
}

/**
 * Mendapatkan nama user yang sedang login.
 */
if (!function_exists('authUser')) {
    function authUser(string $field = 'nama_lengkap'): ?string
    {
        return session()->get($field);
    }
}
