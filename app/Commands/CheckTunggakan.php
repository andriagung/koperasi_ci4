<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckTunggakan extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Koperasi';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'koperasi:check-tunggakan';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Memeriksa angsuran jatuh tempo, menghitung denda, dan mengupdate kolektibilitas.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'koperasi:check-tunggakan';

    public function run(array $params)
    {
        CLI::write("Memulai Pengecekan Tunggakan...", "yellow");
        
        $db = \Config\Database::connect();
        
        // Aturan denda: misal 0.5% dari pokok per hari keterlambatan
        $persenDendaPerHari = 0.005; 
        
        $hariIni = date('Y-m-d');
        
        // Ambil semua angsuran yang belum lunas dan sudah lewat jatuh tempo
        $tunggakan = $db->table('pinjaman_angsuran')
            ->where('status', 'Belum Lunas')
            ->where('jatuh_tempo <', $hariIni)
            ->get()->getResult();
            
        if (empty($tunggakan)) {
            CLI::write("Tidak ada tunggakan ditemukan.", "green");
            return;
        }
        
        $countUpdated = 0;
        
        foreach ($tunggakan as $t) {
            $jatuhTempo = new \DateTime($t->jatuh_tempo);
            $sekarang = new \DateTime($hariIni);
            $selisih = $sekarang->diff($jatuhTempo)->days;
            
            // Hitung denda
            $nominalDenda = $t->pokok * $persenDendaPerHari * $selisih;
            
            // Update angsuran dengan denda dan hari terlambat
            $db->table('pinjaman_angsuran')
               ->where('id', $t->id)
               ->update([
                   'denda' => $nominalDenda,
                   'hari_terlambat' => $selisih
               ]);
               
            $countUpdated++;
            
            // Update kolektibilitas pada level pinjaman (Optional, jika ada kolom kolektibilitas di tabel pinjaman)
            // Namun, saat ini kita menghitung kolektibilitas secara dinamis di Dashboard (Lancar, DPK, Macet)
        }
        
        CLI::write("Selesai! $countUpdated angsuran diperbarui dendanya.", "green");
    }
}
