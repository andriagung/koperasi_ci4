<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // 1. Cek apakah user sudah login
        if (!$session->get('isLoggedIn')) {
            $uri = current_url(true);
            if (strpos($uri->getPath(), 'admin') !== false) {
                return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
            }
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Jika tidak ada argument (roles) yang disyaratkan di route, izinkan lewat
        if (empty($arguments)) {
            return;
        }

        // 3. Cek apakah role dari user ada di dalam daftar roles yang diizinkan (arguments)
        $userRole = $session->get('role');
        
        // Handle penamaan 'Super Admin' di session vs 'SuperAdmin' di filter
        if ($userRole === 'Super Admin') {
            $userRole = 'SuperAdmin';
        }

        if (!in_array($userRole, $arguments)) {
            // Jika request via AJAX, return JSON error
            if ($request->isAJAX()) {
                $response = service('response');
                $response->setJSON([
                    'status' => 'error',
                    'message' => 'Access Denied: Anda tidak memiliki akses ke fitur ini.'
                ]);
                $response->setStatusCode(403);
                return $response;
            }

            // Jika request normal, arahkan ke halaman dashboard dengan pesan error
            $uri = current_url(true);
            $path = $uri->getPath();
            
            // Mencegah infinite redirect loop jika halaman saat ini sudah admin/dashboard
            if (strpos($path, 'admin/dashboard') !== false) {
                return redirect()->to('/admin/logout')->with('error', 'Akses Ditolak. Akun Anda tidak diizinkan masuk ke Dashboard.');
            }

            return redirect()->to('/admin/dashboard')->with('error', 'Akses Ditolak. Role Anda (' . session()->get('role') . ') tidak memiliki wewenang untuk membuka halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}