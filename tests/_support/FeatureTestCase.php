<?php

namespace Tests\Support;

use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\AnggotaModel;

class FeatureTestCase extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = false;
    protected $seed        = '';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable CSRF for testing
        $filters = config('Filters');
        if (($key = array_search('csrf', $filters->globals['before'])) !== false) {
            unset($filters->globals['before'][$key]);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getAdminSession()
    {
        return [
            'isLoggedIn' => true,
            'role'       => 'SuperAdmin',
            'username'   => 'agung',
            'nama'       => 'Super Admin'
        ];
    }

    protected function getAnggotaSession($nip = '198501012010011')
    {
        $anggotaModel = new AnggotaModel();
        $anggota = $anggotaModel->where('nip', $nip)->first();
        
        if (!$anggota) {
            $this->fail("Anggota with NIP {$nip} not found in database.");
        }

        return [
            'isLoggedIn'   => true,
            'role'         => 'Anggota',
            'anggota_id'   => $anggota['id'],
            'id'           => $anggota['id'],
            'nip'          => $anggota['nip'],
            'nama_lengkap' => $anggota['nama_lengkap']
        ];
    }

    /**
     * Helper to mock session login as Anggota
     */
    protected function loginAsAnggota($nip = '198501012010011')
    {
        $anggotaModel = new AnggotaModel();
        $anggota = $anggotaModel->where('nip', $nip)->first();
        
        if (!$anggota) {
            $this->fail("Anggota with NIP {$nip} not found in database.");
        }

        $session = session();
        $session->set([
            'isLoggedIn'   => true,
            'role'         => 'Anggota',
            'anggota_id'   => $anggota['id'],
            'nip'          => $anggota['nip'],
            'nama_lengkap' => $anggota['nama_lengkap']
        ]);
        
        return $anggota;
    }
}
