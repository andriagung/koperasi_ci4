<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Phase1Production extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        $allTables = [
            'admin_users', 'akun_coa', 'anggota', 'jurnal_transaksi', 
            'penarikan_simpanan', 'pengaturan', 'pinjaman', 'pinjaman_angsuran', 
            'produk_waserda', 'purchase_order', 'riwayat_transaksi', 
            'setoran_simpanan', 'shu_tahunan', 'simpanan', 'stok_mutasi', 'supplier'
        ];

        foreach ($allTables as $table) {
            try {
                $this->forge->addColumn($table, [
                    'deleted_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                ]);
            } catch (\Exception $e) {
                // Ignore if already exists
            }
        }

        // Cek jika foreign key sudah ada
        try {
            $db->query("ALTER TABLE `purchase_order` ADD CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier`(`id`) ON DELETE RESTRICT");
        } catch (\Exception $e) {
            // Abaikan jika sudah ada
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        try {
            $db->query("ALTER TABLE `purchase_order` DROP FOREIGN KEY `fk_po_supplier`");
        } catch (\Exception $e) {
            // Abaikan jika tidak ada
        }

        $allTables = [
            'admin_users', 'akun_coa', 'anggota', 'jurnal_transaksi', 
            'penarikan_simpanan', 'pengaturan', 'pinjaman', 'pinjaman_angsuran', 
            'produk_waserda', 'purchase_order', 'riwayat_transaksi', 
            'setoran_simpanan', 'shu_tahunan', 'simpanan', 'stok_mutasi', 'supplier'
        ];

        foreach ($allTables as $table) {
            try {
                $this->forge->dropColumn($table, 'deleted_at');
            } catch (\Exception $e) {
                // Ignore if missing
            }
        }
    }
}
