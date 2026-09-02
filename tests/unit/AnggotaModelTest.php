<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\AnggotaModel;

class AnggotaModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';
    protected $namespace   = 'App';

    public function testInsertAnggotaBaru()
    {
        $model = new AnggotaModel();
        
        $data = [
            'nip'          => 'T' . time(),
            'nama_lengkap' => 'Budi Tester',
            'divisi'       => 'QA Department',
            'no_hp'        => '08123456789',
            'status'       => 'Aktif',
            'pin'          => password_hash('123456', PASSWORD_DEFAULT)
        ];

        // Memasukkan data baru
        $result = $model->insert($data);

        // Jika berhasil, model mengembalikan ID yang baru dibuat
        $this->assertIsInt($result, 'Insert gagal, ID tidak bertipe integer');
        $this->assertGreaterThan(0, $result, 'Insert gagal, ID <= 0');

        // Pastikan datanya masuk ke DB
        $anggotaDB = $model->find($result);
        $this->assertEquals('Budi Tester', $anggotaDB['nama_lengkap']);
    }

    public function testValidasiNIPUnik()
    {
        $model = new AnggotaModel();
        
        // Simpan anggota pertama
        $model->insert([
            'nip'          => '111222333',
            'nama_lengkap' => 'Anggota A',
            'divisi'       => 'IT',
            'no_hp'        => '081234',
            'status'       => 'Aktif',
            'pin'          => password_hash('123456', PASSWORD_DEFAULT)
        ]);

        // Coba simpan anggota kedua dengan NIP yang SAMA
        $result = $model->insert([
            'nip'          => '111222333',
            'nama_lengkap' => 'Anggota B',
            'divisi'       => 'HR',
            'no_hp'        => '081235',
            'status'       => 'Aktif',
            'pin_hash'     => password_hash('123456', PASSWORD_DEFAULT)
        ]);

        // Insert harus gagal (return false) karena 'nip' harus unik
        $this->assertFalse($result, 'Validasi gagal: NIP duplikat berhasil masuk ke database!');
    }
}
