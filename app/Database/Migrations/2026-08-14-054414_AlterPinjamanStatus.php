<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPinjamanStatus extends Migration
{
    public function up()
    {
        // 1. Modify column constraint to include all new statuses
        $this->forge->modifyColumn('pinjaman', [
            'status_pengajuan' => [
                'type'       => 'ENUM',
                'constraint' => ['DRAFT', 'SUBMITTED', 'VERIFICATION', 'ANALYSIS', 'APPROVED', 'REJECTED', 'CANCELLED', 'DISBURSED', 'ACTIVE', 'OVERDUE', 'DEFAULT', 'RESTRUCTURED', 'PAID', 'Pending', 'Disetujui', 'Ditolak', 'Lunas'],
                'default'    => 'DRAFT',
            ],
        ]);

        // 2. Migrate existing data
        $db = \Config\Database::connect();
        $db->query("UPDATE pinjaman SET status_pengajuan = 'SUBMITTED' WHERE status_pengajuan = 'Pending'");
        $db->query("UPDATE pinjaman SET status_pengajuan = 'APPROVED' WHERE status_pengajuan = 'Disetujui'");
        $db->query("UPDATE pinjaman SET status_pengajuan = 'REJECTED' WHERE status_pengajuan = 'Ditolak'");
        $db->query("UPDATE pinjaman SET status_pengajuan = 'PAID' WHERE status_pengajuan = 'Lunas'");

        // 3. Remove old constraints
        $this->forge->modifyColumn('pinjaman', [
            'status_pengajuan' => [
                'type'       => 'ENUM',
                'constraint' => ['DRAFT', 'SUBMITTED', 'VERIFICATION', 'ANALYSIS', 'APPROVED', 'REJECTED', 'CANCELLED', 'DISBURSED', 'ACTIVE', 'OVERDUE', 'DEFAULT', 'RESTRUCTURED', 'PAID'],
                'default'    => 'DRAFT',
            ],
        ]);
    }

    public function down()
    {
        // Add back old constraint options
        $this->forge->modifyColumn('pinjaman', [
            'status_pengajuan' => [
                'type'       => 'ENUM',
                'constraint' => ['DRAFT', 'SUBMITTED', 'VERIFICATION', 'ANALYSIS', 'APPROVED', 'REJECTED', 'CANCELLED', 'DISBURSED', 'ACTIVE', 'OVERDUE', 'DEFAULT', 'RESTRUCTURED', 'PAID', 'Pending', 'Disetujui', 'Ditolak', 'Lunas'],
                'default'    => 'Pending',
            ],
        ]);

        // Revert data
        $db = \Config\Database::connect();
        $db->query("UPDATE pinjaman SET status_pengajuan = 'Pending' WHERE status_pengajuan = 'SUBMITTED'");
        $db->query("UPDATE pinjaman SET status_pengajuan = 'Disetujui' WHERE status_pengajuan = 'APPROVED'");
        $db->query("UPDATE pinjaman SET status_pengajuan = 'Ditolak' WHERE status_pengajuan = 'REJECTED'");
        $db->query("UPDATE pinjaman SET status_pengajuan = 'Lunas' WHERE status_pengajuan = 'PAID'");
        // If there are other statuses, they will be left as is, which might fail the final constraint.
        // We'll revert to the exact original constraint.
        $this->forge->modifyColumn('pinjaman', [
            'status_pengajuan' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Disetujui', 'Ditolak', 'Lunas'],
                'default'    => 'Pending',
            ],
        ]);
    }
}
