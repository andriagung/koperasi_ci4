<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pinjaman extends Migration
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
            'nominal_pengajuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'tenor_bulan' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'tujuan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_pengajuan' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Disetujui', 'Ditolak', 'Lunas'],
                'default'    => 'Pending',
            ],
            'tanggal_pengajuan' => [
                'type' => 'DATE',
                'null' => true,
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
        $this->forge->addForeignKey('anggota_id', 'anggota', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pinjaman');
    }

    public function down()
    {
        $this->forge->dropTable('pinjaman');
    }
}
