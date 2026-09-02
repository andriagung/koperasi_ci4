<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AkunCoa extends Migration
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
            'kode_akun' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'nama_akun' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'tipe_akun' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktiva', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'],
            ],
            'saldo_normal' => [
                'type'       => 'ENUM',
                'constraint' => ['Debit', 'Kredit'],
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
        $this->forge->createTable('akun_coa');
    }

    public function down()
    {
        $this->forge->dropTable('akun_coa');
    }
}
