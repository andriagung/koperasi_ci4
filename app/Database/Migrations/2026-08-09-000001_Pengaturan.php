<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pengaturan extends Migration
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
            'kunci' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nilai' => [
                'type' => 'TEXT',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengaturan');
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan');
    }
}
