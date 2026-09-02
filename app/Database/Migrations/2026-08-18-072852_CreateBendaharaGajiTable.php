<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBendaharaGajiTable extends Migration
{
    public function up()
    {
        // 1. Buat tabel bendahara_gaji
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_instansi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->createTable('bendahara_gaji', true);

        // 2. Tambah bendahara_id di anggota
        $fields = [
            'bendahara_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'pekerjaan'
            ],
        ];
        $this->forge->addColumn('anggota', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'bendahara_id');
        $this->forge->dropTable('bendahara_gaji', true);
    }
}
