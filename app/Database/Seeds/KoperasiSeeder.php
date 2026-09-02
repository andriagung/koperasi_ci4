<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KoperasiSeeder extends Seeder
{
    public function run()
    {
        $dataAnggota = [
            [
                'nip'          => '198501012010011',
                'nama_lengkap' => 'Agung Andri',
                'divisi'       => 'Manajemen',
                'no_hp'        => '085711223344',
                'pin'          => password_hash('123456', PASSWORD_DEFAULT),
                'limit_kasbon' => 1500000,
                'status'       => 'Aktif',
            ],
            [
                'nip'          => '198005122010011',
                'nama_lengkap' => 'Dr. Budi Santoso',
                'divisi'       => 'Poli Dalam',
                'no_hp'        => '08123456789',
                'pin'          => password_hash('123456', PASSWORD_DEFAULT),
                'limit_kasbon' => 1500000,
                'status'       => 'Aktif',
            ],
        ];

        // Insert into anggota
        $this->db->table('anggota')->insertBatch($dataAnggota);
    }
}
