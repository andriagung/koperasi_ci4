<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestAutoPO extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:auto_po';
    protected $description = 'Test Auto PO creation from POS';

    public function run(array $params)
    {
        helper('idhash');
        $service = new \App\Services\WaserdaService();
        $post = [
            'metode' => 'tunai',
            'total' => 150000,
            'items' => [
                [
                    'id' => idhash_encode(1), // Paket Sembako A
                    'qty' => 1,
                    'hargabeli' => 1000
                ]
            ]
        ];
        
        try {
            $service->checkoutKasir($post, 1);
            CLI::write('Checkout berhasil.', 'green');
        } catch (\Exception $e) {
            CLI::write('Error: ' . $e->getMessage(), 'red');
        }
        
        
        // --- TEST UPDATE PO ---
        $db = \Config\Database::connect();
        $po = $db->table('purchase_order')->orderBy('id', 'DESC')->get()->getRowArray();
        
        if ($po) {
            CLI::write("PO terbaru: " . $po['nomor_po'] . " - Status: " . $po['status'] . " (Produk ID: " . $po['produk_id'] . ")", 'green');
            
            // Mock controller update
            $controller = new \App\Controllers\Admin\Waserda();
            
            $_SERVER['REQUEST_METHOD'] = 'POST';
            
            $_POST['status'] = 'Selesai';
            $_POST['id'] = idhash_encode($po['id']);
            
            $request = clone \Config\Services::request();
            $request->setMethod('post');
            
            $controller->initController($request, \Config\Services::response(), \Config\Services::logger());
            
            try {
                $controller->updateStatusPurchaseOrder(idhash_encode($po['id']));
                CLI::write("Update PO berhasil.", 'green');
            } catch (\Exception $e) {
                CLI::write("Error update PO: " . $e->getMessage(), 'red');
            }
            
            $produk = $db->table('produk_waserda')->where('id', $po['produk_id'])->get()->getRowArray();
            CLI::write("Stok produk (ID: " . $po['produk_id'] . ") sekarang: " . $produk['stok'], 'yellow');
        } else {
            CLI::write("Tidak ada PO.", 'red');
        }
    }
}
