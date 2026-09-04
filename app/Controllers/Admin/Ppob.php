<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Ppob extends BaseController
{
    public function index()
    {
        return view('admin/ppob/index');
    }

    public function kasir()
    {
        return view('admin/ppob/kasir');
    }
}
