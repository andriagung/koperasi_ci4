<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterProdukWaserda extends Migration
{
    public function up()
    {
        $fields = [
            'harga_beli' => [
                'type' => 'DOUBLE',
                'default' => 0,
                'after' => 'harga_promo',
            ],
            'stok' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'harga_beli',
            ],
            'stok_minimum' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 5,
                'after' => 'stok',
            ],
        ];
        $this->forge->addColumn('produk_waserda', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produk_waserda', 'harga_beli');
        $this->forge->dropColumn('produk_waserda', 'stok');
        $this->forge->dropColumn('produk_waserda', 'stok_minimum');
    }
}
