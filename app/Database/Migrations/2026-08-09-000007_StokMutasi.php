<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StokMutasi extends Migration
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
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['Masuk', 'Keluar', 'Penyesuaian'],
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->addForeignKey('produk_id', 'produk_waserda', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('stok_mutasi');
    }

    public function down()
    {
        $this->forge->dropTable('stok_mutasi');
    }
}
