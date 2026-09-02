<?php

namespace Tests\Feature;

use Tests\Support\FeatureTestCase;

class AdminFeatureTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAksesDashboardAdmin()
    {
        $result = $this->withSession($this->getAdminSession())->get('admin/dashboard');
        $result->assertStatus(200);
        $result->assertSee('Dashboard');
    }

    public function testTambahAnggota()
    {
        $data = [
            'nip' => '999888777666',
            'nama_lengkap' => 'Anggota Baru Test',
            'divisi' => 'IT',
            'no_hp' => '08123456789',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Test No. 1',
            'departemen' => 'IT',
            'pin' => '123456'
        ];

        $result = $this->withSession($this->getAdminSession())->post('admin/tambah-anggota', $data);
        
        // Verifikasi ada di database
        $this->seeInDatabase('anggota', [
            'nip' => '999888777666',
            'nama_lengkap' => 'Anggota Baru Test'
        ]);
    }
}
