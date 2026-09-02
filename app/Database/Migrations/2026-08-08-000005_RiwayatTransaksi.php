<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RiwayatTransaksi extends Migration
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
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['Simpanan', 'Pinjaman', 'Waserda'],
            ],
            'jenis_transaksi' => [
                'type'       => 'ENUM',
                'constraint' => ['Masuk', 'Keluar'],
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('anggota_id', 'anggota', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('riwayat_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('riwayat_transaksi');
    }
}
