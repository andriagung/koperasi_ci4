<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PinjamanAngsuran extends Migration
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
            'pinjaman_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bulan_ke' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'jatuh_tempo' => [
                'type' => 'DATE',
            ],
            'pokok' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'jasa' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Lunas', 'Belum Lunas'],
                'default'    => 'Belum Lunas',
            ],
            'tanggal_bayar' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('pinjaman_id', 'pinjaman', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pinjaman_angsuran');
    }

    public function down()
    {
        $this->forge->dropTable('pinjaman_angsuran');
    }
}
