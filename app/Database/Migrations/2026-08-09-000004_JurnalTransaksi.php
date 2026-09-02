<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class JurnalTransaksi extends Migration
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
            'nomor_bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'akun_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'posisi' => [
                'type'       => 'ENUM',
                'constraint' => ['Debit', 'Kredit'],
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
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
        $this->forge->addForeignKey('akun_id', 'akun_coa', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('jurnal_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_transaksi');
    }
}
