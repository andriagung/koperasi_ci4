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
        $kategori = ['Pulsa', 'Paket Data', 'Token PLN', 'Tagihan PLN', 'PDAM', 'BPJS', 'Internet & TV'];
        return view('admin/ppob/kasir', ['kategori' => $kategori]);
    }

    public function ajaxProduk()
    {
        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw')),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
    }

    public function ajaxTransaksi()
    {
        return $this->response->setJSON([
            'draw' => intval($this->request->getPost('draw')),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
    }
}
