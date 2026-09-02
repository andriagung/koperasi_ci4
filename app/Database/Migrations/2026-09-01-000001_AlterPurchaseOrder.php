<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPurchaseOrder extends Migration
{
    public function up()
    {
        $fields = [
            'produk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, // Make it nullable in case existing data doesn't have it
                'after' => 'supplier_id',
            ],
            'jumlah' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'produk_id',
            ],
        ];
        $this->forge->addColumn('purchase_order', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('purchase_order', 'produk_id');
        $this->forge->dropColumn('purchase_order', 'jumlah');
    }
}
