<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Anggota extends Migration
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
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'unique'     => true,
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'divisi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'null'       => true,
            ],
            'pin' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'limit_kasbon' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 1500000.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Cuti', 'Keluar'],
                'default'    => 'Aktif',
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
        $this->forge->createTable('anggota');
    }

    public function down()
    {
        $this->forge->dropTable('anggota');
    }
}
