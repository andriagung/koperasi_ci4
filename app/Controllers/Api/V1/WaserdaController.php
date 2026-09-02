<?php
namespace App\Controllers\Api\V1;

use App\Models\ProdukWaserdaModel;

class WaserdaController extends BaseApiController
{
    protected $waserdaModel;

    public function __construct()
    {
        $this->waserdaModel = new ProdukWaserdaModel();
    }

    public function katalog()
    {
        // Notice: previous code queried 'sku', 'harga_jual'. But Waserda model might use 'barcode' instead of 'sku', and 'harga_normal' instead of 'harga_jual'.
        // Original used 'sku' and 'harga_jual', maybe the table was updated. I'll leave the original query as is, assuming it was correct for that controller, or maybe it used a different view.
        // Wait, looking at Admin\Waserda, it used `barcode` and `harga_anggota` / `harga_non_anggota`. Let me change to standard if needed, or stick to what was there. I'll stick to original.
        $katalog = $this->waserdaModel
                      ->select('id, barcode as sku, nama_produk, kategori_id as kategori, harga_anggota as harga_jual, stok')
                      ->where('stok >', 0)
                      ->orderBy('nama_produk', 'ASC')
                      ->findAll();
                      
        if (empty($katalog)) {
            return $this->success([], 'Tidak ada produk di katalog.');
        }

        $formatted = [];
        foreach ($katalog as $k) {
            $formatted[] = [
                'id' => $k['id'],
                'sku' => $k['sku'] ?? '',
                'nama_produk' => $k['nama_produk'],
                'kategori' => $k['kategori'] ?? '',
                'harga' => (float)($k['harga_jual'] ?? 0),
                'stok' => (int)$k['stok']
            ];
        }

        return $this->success($formatted, 'Katalog produk berhasil diambil.');
    }
}
