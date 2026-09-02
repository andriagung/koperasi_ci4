<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PurchaseOrder extends Migration
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
            'nomor_po' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'supplier_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'total_harga' => [
                'type'       => 'DOUBLE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Selesai', 'Batal'],
                'default'    => 'Selesai',
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
        $this->forge->addForeignKey('supplier_id', 'supplier', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('purchase_order');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_order');
    }
}
