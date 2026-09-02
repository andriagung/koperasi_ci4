<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        
        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return \Config\Services::response()->setJSON([
                'success' => false,
                'message' => 'Token otentikasi tidak ditemukan.',
                'data' => null
            ])->setStatusCode(401);
        }
        
        $token = $matches[1];
        
        // Simple token validation (For production, use Firebase JWT)
        // Token format expected: "koperasi-token-{anggota_id}"
        if (strpos($token, 'koperasi-token-') === 0) {
            $anggota_id = str_replace('koperasi-token-', '', $token);
            // Simpan ID anggota di global header / service (atau pass via request)
            $request->setHeader('X-Anggota-Id', $anggota_id);
            return;
        }

        return \Config\Services::response()->setJSON([
            'success' => false,
            'message' => 'Token tidak valid atau sudah kadaluarsa.',
            'data' => null
        ])->setStatusCode(401);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
