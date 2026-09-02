<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanPotonganModel extends Model
{
    // Kita tidak menggunakan property table karena query murni builder agregasi
    protected $table = 'anggota';

    public function getDaftarPotonganAnggota($unitKerja = 'all', $status = 'all', $golongan = 'all', $tglAwal = null, $tglAkhir = null)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('anggota a');
        $builder->select('
            a.id, 
            a.nik, 
            a.nama_lengkap, 
            a.golongan,
            bg.nama_instansi as unit_kerja,
            COALESCE(tp.nominal_simpanan_wajib, 0) as pot_sw,
            COALESCE(tp.pot_sp, 0) as pot_sp,
            COALESCE(tp.pot_ss, 0) as pot_ss,
            COALESCE(tp.pot_sl, 0) as pot_sl,
            COALESCE(tp.pot_ppu, 0) as pot_ppu,
            COALESCE(tp.pot_bpu, 0) as pot_bpu,
            COALESCE(tp.pot_ppb, 0) as pot_ppb,
            COALESCE(tp.pot_bpb, 0) as pot_bpb,
            COALESCE(tp.pot_pps, 0) as pot_pps,
            COALESCE(tp.pot_bps, 0) as pot_bps,
            COALESCE(tp.dansos, 0) as dansos,
            COALESCE(tp.pangan, 0) as pangan,
            COALESCE(tp.total_tagihan, 0) as jumlah
        ');
        $builder->join('bendahara_gaji bg', 'bg.id = a.bendahara_id', 'left');
        
        // Asumsi data potongan ditarik dari tagihan potongan berdasarkan periode/tanggal
        $builder->join('tagihan_potongan tp', 'tp.anggota_id = a.id', 'left');

        if ($unitKerja !== 'all' && !empty($unitKerja)) {
            $builder->where('a.bendahara_id', $unitKerja);
        }

        if ($status !== 'all' && !empty($status)) {
            $builder->where('a.status', $status);
        }

        if ($golongan !== 'all' && !empty($golongan)) {
            $builder->where('a.golongan', $golongan);
        }

        if ($tglAwal && $tglAkhir) {
            // Karena tagihan berdasarkan 'periode' (Y-m), kita konversi rentang tanggal 
            // Jika tanggal awal dan akhir diset, kita ambil bulan/tahun dari tglAwal
            $periode = date('Y-m', strtotime($tglAwal));
            $builder->where('tp.periode', $periode);
        }

        $builder->orderBy('bg.nama_instansi', 'ASC');
        $builder->orderBy('a.nama_lengkap', 'ASC');

        return $builder->get()->getResultArray();
    }
}
