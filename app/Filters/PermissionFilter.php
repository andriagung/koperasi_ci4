<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pastikan sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        // Cek permission jika ada argumen
        if (!empty($arguments)) {
            $userPermissions = session()->get('permissions') ?? [];

            foreach ($arguments as $required) {
                if (!in_array($required, $userPermissions)) {
                    if ($request->isAJAX()) {
                        return service('response')
                            ->setJSON(['success' => false, 'message' => 'Akses ditolak: ' . $required])
                            ->setStatusCode(403);
                    }
                    return redirect()->to('/admin/dashboard')
                        ->with('error', 'Anda tidak memiliki akses untuk fitur ini (' . $required . ').');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
