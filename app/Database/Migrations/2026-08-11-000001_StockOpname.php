<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StockOpname extends Migration
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
            'produk_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'stok_sistem' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'stok_fisik' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'selisih' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'petugas' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addForeignKey('produk_id', 'produk_waserda', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_opname');
    }

    public function down()
    {
        $this->forge->dropTable('stock_opname');
    }
}
