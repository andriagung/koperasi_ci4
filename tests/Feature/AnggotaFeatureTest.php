<?php

namespace Tests\Feature;

use Tests\Support\FeatureTestCase;

class AnggotaFeatureTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAksesHalamanUtama()
    {
        // Beranda
        $result = $this->withSession($this->getAnggotaSession())->get('mobile/dashboard');
        $result->assertStatus(200);
        $result->assertSee('Total Aset Anda');

        // Profil
        $result = $this->withSession($this->getAnggotaSession())->get('mobile/profil');
        $result->assertStatus(200);
        $result->assertSee('Profil Saya');

        // Waserda
        $result = $this->withSession($this->getAnggotaSession())->get('mobile/waserda');
        $result->assertStatus(200);
        $result->assertSee('Kredit Waserda');
    }

    public function testAksiSetorSimpanan()
    {
        $data = [
            'nominal'  => '50000',
            'bank_pengirim' => 'BCA',
            'keterangan' => 'Test Setor Simpanan'
        ];

        // Hapus pending sebelumnya agar tidak kena error "Masih ada pengajuan"
        $this->db->table('simpanan_transaksi')->where('anggota_id', $this->getAnggotaSession()['id'])->where('jenis_transaksi', 'setoran')->delete();

        $result = $this->withSession($this->getAnggotaSession())->post('setor-simpanan', $data);
        
        // Cek JSON response
        $result->assertStatus(200);
        $result->assertJSONExact(['status' => 'success', 'message' => 'Notifikasi setoran berhasil dikirim. Menunggu verifikasi admin.']);
        
        // Verifikasi data masuk ke db
        $this->seeInDatabase('simpanan_transaksi', [
            'nominal' => 50000,
            'keterangan' => 'Setoran simpanan via Mobile',
            'jenis_transaksi' => 'Setoran'
        ]);
    }

    public function testAksiTarikSimpanan()
    {
        $data = [
            'nominal'  => '10000',
            'bank_pencairan' => 'BCA 12345678',
            'pin_konfirmasi' => '123456'
        ];

        // Hapus pending sebelumnya agar tidak kena error
        $this->db->table('simpanan_transaksi')->where('anggota_id', $this->getAnggotaSession()['id'])->where('jenis_transaksi', 'penarikan')->delete();
        
        // Beri saldo awal 100.000 agar penarikan 10k berhasil (sisa 90k, min sisa 50k)
        $this->db->table('simpanan')->where('anggota_id', $this->getAnggotaSession()['id'])->where('jenis_simpanan', 'Sukarela')->delete();
        $this->db->table('simpanan')->insert([
            'anggota_id' => $this->getAnggotaSession()['id'],
            'jenis_simpanan' => 'Sukarela',
            'saldo' => 100000,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $result = $this->withSession($this->getAnggotaSession())->post('tarik-simpanan', $data);
        
        $result->assertStatus(200);
        $result->assertJSONExact(['status' => 'success', 'message' => 'Pengajuan penarikan berhasil.']);
    }

    public function testAksiAjukanPinjaman()
    {
        $data = [
            'nominal' => '1000000',
            'tenor' => '12',
            'tujuan' => 'Renovasi Rumah Test',
            'penghasilan_bulanan' => 5000000,
            'cicilan_lainnya' => 0
        ];

        $result = $this->withSession($this->getAnggotaSession())->post('ajukan-pinjaman', $data);
        
        $result->assertStatus(200);
        $result->assertJSONExact(['status' => 'success', 'message' => 'Pengajuan pinjaman berhasil dikirim. Menunggu persetujuan admin.']);

        // Verifikasi data masuk
        $this->seeInDatabase('pinjaman', [
            'nominal_pengajuan' => 1000000,
            'tenor_bulan' => 12,
            'tujuan' => 'Renovasi Rumah Test',
            'status_pengajuan' => 'SUBMITTED'
        ]);
    }
}
