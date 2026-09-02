<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAnggotaGaji extends Migration
{
    public function up()
    {
        $this->forge->addColumn('anggota', [
            'gaji_pokok' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 5000000,
                'after'      => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('anggota', 'gaji_pokok');
    }
}
