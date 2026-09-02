<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class CoaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // AKTIVA (HARTA)
            ['kode_akun' => '1100', 'nama_akun' => 'Kas Koperasi', 'tipe_akun' => 'Aktiva', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1110', 'nama_akun' => 'Kas di Bank', 'tipe_akun' => 'Aktiva', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1200', 'nama_akun' => 'Piutang Pinjaman Anggota', 'tipe_akun' => 'Aktiva', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1300', 'nama_akun' => 'Persediaan Barang Waserda', 'tipe_akun' => 'Aktiva', 'saldo_normal' => 'Debit'],
            
            // KEWAJIBAN (UTANG)
            ['kode_akun' => '2100', 'nama_akun' => 'Utang Usaha / Supplier', 'tipe_akun' => 'Kewajiban', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '2200', 'nama_akun' => 'Simpanan Sukarela Anggota', 'tipe_akun' => 'Kewajiban', 'saldo_normal' => 'Kredit'],
            
            // EKUITAS (MODAL)
            ['kode_akun' => '3100', 'nama_akun' => 'Simpanan Pokok Anggota', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '3200', 'nama_akun' => 'Simpanan Wajib Anggota', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '3300', 'nama_akun' => 'Cadangan Modal', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '3400', 'nama_akun' => 'SHU Tahun Berjalan', 'tipe_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit'],
            
            // PENDAPATAN
            ['kode_akun' => '4100', 'nama_akun' => 'Pendapatan Jasa Pinjaman', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4200', 'nama_akun' => 'Pendapatan Penjualan Waserda', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '4300', 'nama_akun' => 'Pendapatan Administrasi', 'tipe_akun' => 'Pendapatan', 'saldo_normal' => 'Kredit'],
            
            // BEBAN (PENGELUARAN)
            ['kode_akun' => '5100', 'nama_akun' => 'Harga Pokok Penjualan (HPP) Waserda', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6100', 'nama_akun' => 'Beban Gaji Pengurus', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6200', 'nama_akun' => 'Beban Listrik & Internet', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6300', 'nama_akun' => 'Beban Operasional Lainnya', 'tipe_akun' => 'Beban', 'saldo_normal' => 'Debit'],
        ];

        foreach ($data as &$row) {
            $row['created_at'] = Time::now();
            $row['updated_at'] = Time::now();
        }

        // Using Query Builder
        $this->db->table('akun_coa')->insertBatch($data);
    }
}
