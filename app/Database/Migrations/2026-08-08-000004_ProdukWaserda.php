<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProdukWaserda extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'harga_normal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'harga_promo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'ikon' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'fas fa-box',
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('produk_waserda');
    }

    public function down()
    {
        $this->forge->dropTable('produk_waserda');
    }
}
