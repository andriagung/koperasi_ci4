<?php
namespace App\Controllers\Mobile;
use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        $session = session();
        if ($session->get('isLoggedIn') && $session->get('role') === 'Anggota') {
            return redirect()->to('/mobile/dashboard');
        }
        return view('mobile/login', ['isLoggedIn' => false]);
    }
}
