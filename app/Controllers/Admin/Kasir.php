<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnggotaModel;
use App\Models\ProdukWaserdaModel;
use App\Services\PosService;

class Kasir extends BaseController {
    
    protected $anggotaModel;
    protected $produkModel;
    protected $posService;

    public function __construct() {
        $this->anggotaModel = new AnggotaModel();
        $this->produkModel = new ProdukWaserdaModel();
        $this->posService = new PosService();
    }

    public function index() {
        $data = [
            'anggota' => $this->anggotaModel->where('status', 'Aktif')->findAll(),
            'produk_json' => json_encode($this->produkModel->where('is_active', 1)->findAll())
        ];
        
        return view('admin/waserda/kasir', $data);
    }
    
    public function cariBarcode($barcode) {
        if (!$this->request->is('post') && !$this->request->is('ajax')) {
            // It could be a GET request depending on how the frontend is implemented, but if we need to restrict to AJAX
            // we can leave it flexible or enforce ajax
        }

        $produk = $this->produkModel->where('barcode', $barcode)
                              ->orWhere('id', $barcode)
                              ->first();
                              
        if ($produk) {
            return $this->response->setJSON(['status' => 'success', 'data' => $produk]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Produk tidak ditemukan']);
    }

    public function prosesBayar() {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $data = $this->request->getJSON(true);
        
        if (empty($data)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak valid']);
        }

        $res = $this->posService->jual(
            $data['keranjang'] ?? [],
            $data['anggota_id'] ?? null,
            $data['metode_pembayaran'] ?? 'Cash',
            $data['total_bayar'] ?? 0,
            $data['diskon'] ?? 0
        );
        
        return $this->response->setJSON($res);
    }
}
