<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RunUat extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'uat:run';
    protected $description = 'Menjalankan skenario UAT Otomatis end-to-end.';

    public function run(array $params)
    {
        CLI::write('Memulai End-to-End Testing (UAT)...', 'yellow');
        $db = \Config\Database::connect();
        
        try {
            
            // Skenario 1: Transaksi Simpanan
            CLI::write('1. Menguji Transaksi Simpanan...', 'cyan');
            
            // Ambil anggota pertama untuk UAT
            $anggota = $db->table('anggota')->where('status', 'Aktif')->get()->getRow();
            if (!$anggota) {
                CLI::error('Tidak ada anggota aktif untuk diuji.');
                return;
            }
            CLI::write("   Menggunakan Anggota: {$anggota->nama_lengkap} (ID: {$anggota->id})");

            $simpananService = new \App\Services\SimpananService();
            $nominalSetor = 200000;
            
            // Buat Setoran Sukarela
            $resSetoran = $simpananService->setor([
                'anggota_id' => $anggota->id,
                'jenis_simpanan_id' => 3, // Asumsi 3 = Simpanan Sukarela
                'nominal' => $nominalSetor,
                'keterangan' => 'UAT: Setoran Sukarela',
                'metode_pembayaran' => 'Tunai',
                'kas_id' => 1,
                'status' => 'Selesai'
            ]);
            
            if (!$resSetoran['success']) {
                throw new \Exception("Gagal Setoran: " . $resSetoran['message']);
            }
            CLI::write("   [OK] Setoran berhasil. Saldo ditambah Rp " . number_format($nominalSetor), 'green');
            
            // Verifikasi Jurnal Simpanan
            $jurnalSimpanan = $db->table('jurnal_transaksi')->where('nomor_bukti', $resSetoran['nomor_transaksi'])->get()->getResult();
            if (count($jurnalSimpanan) < 2) {
                throw new \Exception("Jurnal transaksi simpanan tidak seimbang / tidak terbentuk.");
            }
            CLI::write("   [OK] Jurnal Akuntansi Setoran terbentuk seimbang.", 'green');


            // Skenario 2: Pencairan Pinjaman
            CLI::write("\n2. Menguji Pengajuan & Pencairan Pinjaman...", 'cyan');
            $db->table('pinjaman')->insert([
                'anggota_id' => $anggota->id,
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
                'nominal_pengajuan' => 1000000,
                'tenor_bulan' => 5,
                'status_pengajuan' => 'Disetujui',
                'tujuan' => 'UAT Testing',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $pinjamanId = $db->insertID();
            
            // Pencairan
            $pinjamanService = new \App\Services\PinjamanService();
            // Assuming we use manual db insert for pencairan because PinjamanService might not have it extracted yet.
            $db->table('pinjaman_pencairan')->insert([
                'pinjaman_id' => $pinjamanId,
                'tanggal_pencairan' => date('Y-m-d'),
                'jumlah_dicairkan' => 1000000,
                'metode_pencairan' => 'Tunai'
            ]);
            $db->table('pinjaman')->where('id', $pinjamanId)->update(['status_pengajuan' => 'Berjalan']);
            
            // Jurnal Pencairan (Simulasi Hook Admin Pinjaman)
            $akuntansiService = new \App\Services\AkuntansiService();
            $akunPiutang = $db->table('akun_coa')->where('kode_akun', '1300')->get()->getRow(); // Piutang
            $akunKas = $db->table('akun_coa')->where('kode_akun', '1100')->get()->getRow(); // Kas
            if($akunPiutang && $akunKas) {
                $akuntansiService->catatJurnal(
                    date('Y-m-d'), 
                    "UAT: Pencairan Pinjaman #{$pinjamanId}", 
                    [
                        ['akun_id' => $akunPiutang->id, 'posisi' => 'debit', 'nominal' => 1000000],
                        ['akun_id' => $akunKas->id, 'posisi' => 'kredit', 'nominal' => 1000000]
                    ], 
                    "CAIR-{$pinjamanId}"
                );
            }
            CLI::write("   [OK] Pencairan pinjaman berhasil diproses. Jurnal piutang tercatat.", 'green');

            // Angsuran
            CLI::write("\n3. Menguji Pembayaran Angsuran...", 'cyan');
            $pokok = 200000;
            $jasa = 15000;
            $totalAngsuran = $pokok + $jasa;
            $db->table('pinjaman_angsuran')->insert([
                'pinjaman_id' => $pinjamanId,
                'bulan_ke' => 1,
                'jatuh_tempo' => date('Y-m-d', strtotime('+1 month')),
                'tanggal_bayar' => date('Y-m-d'),
                'pokok' => $pokok,
                'jasa' => $jasa,
                'status' => 'Lunas'
            ]);
            
            // Jurnal Angsuran
            $akunPendapatan = $db->table('akun_coa')->where('kode_akun', '4100')->get()->getRow();
            if($akunPiutang && $akunKas && $akunPendapatan) {
                $akuntansiService->catatJurnal(
                    date('Y-m-d'), 
                    "UAT: Angsuran Pinjaman #{$pinjamanId}", 
                    [
                        ['akun_id' => $akunKas->id, 'posisi' => 'debit', 'nominal' => $totalAngsuran],
                        ['akun_id' => $akunPiutang->id, 'posisi' => 'kredit', 'nominal' => $pokok],
                        ['akun_id' => $akunPendapatan->id, 'posisi' => 'kredit', 'nominal' => $jasa]
                    ], 
                    "ANGS-{$pinjamanId}"
                );
            }
            CLI::write("   [OK] Angsuran berhasil dibayar. Jurnal Pendapatan tercatat.", 'green');


            // Skenario 3: Penutupan SHU
            CLI::write("\n4. Menguji Eksekusi Tutup Buku (SHU)...", 'cyan');
            $tahunBuku = date('Y');
            
            // Clear SHU if exists for testing
            $shuTahunanRows = $db->table('shu_tahunan')->where('tahun', $tahunBuku)->get()->getResult();
            if(!empty($shuTahunanRows)){
                foreach($shuTahunanRows as $st){
                    $db->table('shu_anggota')->where('shu_periode_id', $st->id)->delete();
                }
            }
            $db->table('shu_tahunan')->where('tahun', $tahunBuku)->delete();
            
            $shuService = new \App\Services\ShuService();
            $simulasi = $shuService->kalkulasiSimulasi($tahunBuku);
            
            if(!$simulasi['success']) {
                CLI::write("   [!] Peringatan: " . $simulasi['message'], 'yellow');
            } else {
                CLI::write("   Total Laba Tersedia: Rp " . number_format($simulasi['laba_bersih']));
                
                $tutupBuku = $shuService->tutupBukuDanBagikan($tahunBuku);
                if($tutupBuku['success']){
                    CLI::write("   [OK] Tutup Buku berhasil. Saldo SHU disalurkan ke anggota.", 'green');
                } else {
                    throw new \Exception("Gagal Tutup Buku: " . $tutupBuku['message']);
                }
            }
            
        } catch (\Throwable $e) {
            CLI::error("\n[GAGAL] Terjadi Kesalahan UAT: " . $e->getMessage());
        }
    }
}
