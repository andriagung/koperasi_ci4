<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPinjamanJenis extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pinjaman', [
            'jenis_pinjaman' => [
                'type'       => 'ENUM',
                'constraint' => ['Uang', 'Barang', 'Syariah'],
                'default'    => 'Uang',
                'after'      => 'anggota_id'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pinjaman', 'jenis_pinjaman');
    }
}
