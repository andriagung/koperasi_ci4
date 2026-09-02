<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Simpanan extends Migration
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
            'anggota_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jenis_simpanan' => [
                'type'       => 'ENUM',
                'constraint' => ['Pokok', 'Wajib', 'Sukarela'],
            ],
            'saldo' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('anggota_id', 'anggota', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('simpanan');
    }

    public function down()
    {
        $this->forge->dropTable('simpanan');
    }
}
