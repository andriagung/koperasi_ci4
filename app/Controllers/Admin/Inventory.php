<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\InventoryService;
use App\Models\LokasiModel;
use App\Models\StokModel;
use App\Models\TransferStokModel;
use App\Models\ProdukWaserdaModel;

class Inventory extends BaseController {

    public function lokasi() {
        $lokasiModel = new LokasiModel();
        $lokasi = $lokasiModel->findAll();
        
        return view('admin/inventory/lokasi', [
            'lokasi' => $lokasi
        ]);
    }

    public function simpanLokasi() {
        $lokasiModel = new LokasiModel();
        $id = $this->request->getPost('id');
        $data = [
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'tipe' => $this->request->getPost('tipe'),
            'alamat' => $this->request->getPost('alamat'),
            'status' => $this->request->getPost('status') ?? 'aktif',
        ];

        if ($id) {
            $lokasiModel->update($id, $data);
            $msg = 'Lokasi berhasil diupdate.';
        } else {
            $lokasiModel->insert($data);
            $msg = 'Lokasi berhasil ditambahkan.';
        }

        return redirect()->to(previous_url(true))->with('success', $msg);
    }

    public function kartuStok() {
        $lokasiModel = new LokasiModel();
        $stokModel = new StokModel();
        $lokasiId = $this->request->getGet('lokasi_id') ?? 1;
        
        $lokasi = $lokasiModel->findAll();
        
        $stok = $stokModel
            ->select('stok.*, w.nama_produk, w.stok_minimum, w.harga_beli')
            ->join('produk_waserda w', 'w.id = stok.produk_id')
            ->where('stok.lokasi_id', $lokasiId)
            ->findAll();
            
        return view('admin/inventory/kartu_stok', [
            'stok' => $stok,
            'lokasi' => $lokasi,
            'lokasi_id' => $lokasiId
        ]);
    }

    public function transfer() {
        $lokasiModel = new LokasiModel();
        $produkModel = new ProdukWaserdaModel();
        $transferModel = new TransferStokModel();
        
        $lokasi = $lokasiModel->findAll();
        $produk = $produkModel->where('is_active', 1)->findAll();
        
        $riwayat = $transferModel
            ->select('transfer_stok.*, a.nama as asal, u.nama as tujuan')
            ->join('lokasi a', 'a.id = transfer_stok.lokasi_asal_id')
            ->join('lokasi u', 'u.id = transfer_stok.lokasi_tujuan_id')
            ->orderBy('transfer_stok.tanggal', 'DESC')
            ->limit(50)
            ->findAll();
            
        return view('admin/inventory/transfer', [
            'lokasi' => $lokasi,
            'produk' => $produk,
            'riwayat' => $riwayat
        ]);
    }

    public function simpanTransfer() {
        $post = $this->request->getPost();
        try {
            $inventoryService = new \App\Services\InventoryService();
            $inventoryService->prosesTransfer($post);
            return redirect()->to(previous_url(true))->with('success', 'Transfer stok berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->to(previous_url(true))->with('error', $e->getMessage());
        }
    }

    public function opname() {
        $lokasiModel = new LokasiModel();
        $stokModel = new StokModel();
        
        $lokasiId = $this->request->getGet('lokasi_id') ?? 1;
        $lokasi = $lokasiModel->findAll();
        
        $stok = $stokModel
            ->select('stok.*, w.nama_produk, w.harga_beli')
            ->join('produk_waserda w', 'w.id = stok.produk_id')
            ->where('stok.lokasi_id', $lokasiId)
            ->findAll();
            
        return view('admin/inventory/opname', [
            'lokasi' => $lokasi,
            'stok' => $stok,
            'lokasi_id' => $lokasiId
        ]);
    }

    public function simpanOpname() {
        $post = $this->request->getPost();
        try {
            $inventoryService = new \App\Services\InventoryService();
            $inventoryService->prosesBatchOpname($post);
            return redirect()->to(previous_url(true))->with('success', 'Stock Opname berhasil disimpan dan disesuaikan.');
        } catch (\Exception $e) {
            return redirect()->to(previous_url(true))->with('error', $e->getMessage());
        }
    }
}
