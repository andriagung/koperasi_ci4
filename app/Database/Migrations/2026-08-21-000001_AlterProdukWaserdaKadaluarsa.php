<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterProdukWaserdaKadaluarsa extends Migration
{
    public function up()
    {
        $fields = [
            'tanggal_kadaluarsa' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'is_active',
            ],
        ];
        $this->forge->addColumn('produk_waserda', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produk_waserda', 'tanggal_kadaluarsa');
    }
}
