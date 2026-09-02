<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAdminUsersRoleEnum extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE admin_users MODIFY COLUMN role ENUM('Super Admin','Admin','Kasir','Gudang','Teller','Petugas Kredit','Akuntansi','Pengurus','Manajer','Bendahara') DEFAULT 'Admin'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE admin_users MODIFY COLUMN role ENUM('Super Admin','Admin','Kasir','Gudang','Teller','Petugas Kredit','Akuntansi','Pengurus','Manajer') DEFAULT 'Admin'");
    }
}
