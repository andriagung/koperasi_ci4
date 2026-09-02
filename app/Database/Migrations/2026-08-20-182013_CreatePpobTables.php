<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePpobTables extends Migration
{
    public function up()
    {
        // Table ppob_produk
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_produk' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'nama_produk' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kategori' => [
                'type' => 'ENUM',
                'constraint' => ['Pulsa', 'Paket Data', 'Token PLN', 'Tagihan PLN', 'BPJS', 'PDAM', 'Lainnya'],
            ],
            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'harga_beli' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'harga_jual' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->createTable('ppob_produk');

        // Table ppob_transaksi
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'invoice' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'anggota_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, // null if umum
            ],
            'ppob_produk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'no_pelanggan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'harga_beli' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'harga_jual' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'biaya_admin' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'total_bayar' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'metode_pembayaran' => [
                'type' => 'ENUM',
                'constraint' => ['Tunai', 'Kasbon'],
                'default' => 'Tunai',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Sukses', 'Gagal'],
                'default' => 'Pending',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('anggota_id', 'anggota', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('ppob_produk_id', 'ppob_produk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ppob_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('ppob_transaksi', true);
        $this->forge->dropTable('ppob_produk', true);
    }
}
