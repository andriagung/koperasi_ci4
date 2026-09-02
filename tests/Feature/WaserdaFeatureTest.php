<?php

namespace Tests\Feature;

use Tests\Support\FeatureTestCase;

class WaserdaFeatureTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCheckoutKasir()
    {
        $uniqueBarcode = 'TEST-' . uniqid();
        // First we need a product
        $produk = [
            'barcode' => $uniqueBarcode,
            'nama_produk' => 'Beras Test 5kg',
            'kategori_id' => 1,
            'harga_beli' => 50000,
            'harga_normal' => 55000,
            'stok' => 10,
            'stok_minimum' => 5
        ];
        
        $result = $this->withSession($this->getAdminSession())->post('admin/waserda/tambah-produk', $produk);
        
        $this->seeInDatabase('produk_waserda', [
            'barcode' => $uniqueBarcode
        ]);

        $db = \Config\Database::connect();
        $produkDb = $db->table('produk_waserda')->where('barcode', $uniqueBarcode)->get()->getRowArray();
        
        if (!$produkDb) {
            $this->fail('Produk tidak berhasil disimpan');
        }

        // Simulasikan checkout kasir
        $dataCheckout = [
            'anggota_id' => '', // Pembeli umum
            'metode' => 'Cash',
            'total' => '110000',
            'bayar' => '110000',
            'kembali' => '0',
            'items' => [
                [
                    'id' => $produkDb['id'],
                    'qty' => 2,
                    'hargabeli' => 50000,
                    'harga' => 55000,
                    'subtotal' => 110000
                ]
            ]
        ];

        $result = $this->withSession($this->getAdminSession())->post('admin/waserda/checkout', $dataCheckout);
        
        // Assert successful response (should return JSON with success/redirect)
        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'success']);
        
        // Check if stock is reduced
        $this->seeInDatabase('produk_waserda', [
            'id' => $produkDb['id'],
            'stok' => 8 // 10 - 2
        ]);
    }
}
