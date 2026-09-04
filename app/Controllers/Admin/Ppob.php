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
        $anggotaModel = new \App\Models\AnggotaModel();
        
        $kategori = ['Pulsa', 'Paket Data', 'Token PLN', 'Tagihan PLN', 'PDAM', 'BPJS', 'Internet & TV'];
        $data = [
            'kategori' => $kategori,
            'anggota' => $anggotaModel->findAll()
        ];
        
        return view('admin/ppob/kasir', $data);
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
