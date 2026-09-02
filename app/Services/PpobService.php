<?php

namespace App\Services;

use App\Models\PpobProdukModel;
use App\Models\PpobTransaksiModel;
use App\Models\RiwayatTransaksiModel;
use App\Models\AkunCoaModel;
use App\Services\AccountingService;
use App\Services\KasService;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PpobService
{
    protected $produkModel;
    protected $transaksiModel;
    protected $riwayatModel;
    protected $coaModel;
    protected $accountingService;
    protected $kasService;
    protected $db;

    public function __construct()
    {
        $this->produkModel = new PpobProdukModel();
        $this->transaksiModel = new PpobTransaksiModel();
        $this->riwayatModel = new RiwayatTransaksiModel();
        $this->coaModel = new AkunCoaModel();
        $this->accountingService = new AccountingService();
        $this->kasService = new KasService();
        $this->db = \Config\Database::connect();
    }

    public function prosesTransaksi($produkId, $noPelanggan, $metodePembayaran, $anggotaId = null)
    {
        $this->db->transStart();

        $produk = $this->produkModel->find($produkId);
        if (!$produk || !$produk['is_active']) {
            throw new \Exception('Produk tidak ditemukan atau tidak aktif');
        }

        $invoice = 'PPOB-' . date('YmdHis') . rand(100, 999);
        $totalBayar = $produk['harga_jual'];
        $hpp = $produk['harga_beli'];
        $margin = $totalBayar - $hpp; // Sebagai laba/pendapatan jasa

        $transId = $this->transaksiModel->insert([
            'invoice' => $invoice,
            'anggota_id' => $anggotaId,
            'ppob_produk_id' => $produkId,
            'no_pelanggan' => $noPelanggan,
            'harga_beli' => $hpp,
            'harga_jual' => $totalBayar,
            'biaya_admin' => 0,
            'total_bayar' => $totalBayar,
            'metode_pembayaran' => $metodePembayaran,
            'status' => 'Sukses', // Default sukses untuk mock/dummy
            'keterangan' => 'Pembelian ' . $produk['nama_produk'] . ' - ' . $noPelanggan
        ]);

        $riwayatId = null;
        if ($anggotaId && $metodePembayaran == 'Kasbon') {
            $riwayatId = $this->riwayatModel->insert([
                'anggota_id' => $anggotaId,
                'kategori' => 'Waserda', // atau PPOB
                'jenis_transaksi' => 'Keluar',
                'nominal' => $totalBayar,
                'keterangan' => 'Kasbon PPOB: ' . $produk['nama_produk']
            ]);
        }

        // Integrasi Kas jika Tunai
        if ($metodePembayaran === 'Tunai' && $totalBayar > 0) {
            $kasDefault = $this->db->table('kas')->where('status', 'aktif')->orderBy('id', 'ASC')->get()->getRowArray();
            if ($kasDefault) {
                // Masuk kas sejumlah harga jual
                $this->kasService->debit((int)$kasDefault['id'], (float)$totalBayar, 'ppob', $transId, 'Penjualan PPOB: ' . $invoice);
                // Keluar kas sejumlah HPP (karena bayar ke biller/provider langsung)
                $this->kasService->kredit((int)$kasDefault['id'], (float)$hpp, 'ppob', $transId, 'HPP PPOB (Bayar ke Provider): ' . $invoice);
            }
        }

        // Jurnal Akuntansi
        $kodeKas = $metodePembayaran === 'Kasbon' ? '1200' : '1100'; // Piutang atau Kas
        $akunDebit = $this->coaModel->where('kode_akun', $kodeKas)->first();
        $akunPendapatan = $this->coaModel->where('kode_akun', '4200')->first(); // Pendapatan PPOB/Jasa
        $akunHpp = $this->coaModel->where('kode_akun', '5100')->first(); // HPP (Atau HPP Jasa)
        
        if ($akunDebit && $akunPendapatan && $akunHpp) {
            // Debit: Kas/Piutang (Total Bayar)
            // Kredit: Kas (HPP, bayar provider) + Pendapatan (Margin)
            $jurnalDetail = [
                ['akun_id' => $akunDebit['id'], 'debit' => $totalBayar, 'kredit' => 0, 'keterangan' => 'Penjualan PPOB ' . $invoice],
                ['akun_id' => $akunPendapatan['id'], 'debit' => 0, 'kredit' => $totalBayar, 'keterangan' => 'Pendapatan PPOB ' . $invoice]
            ];
            
            // Jurnal HPP (Jika pakai metode perpetual langsung)
            // Debit: HPP (5100) sejumlah harga beli
            // Kredit: Kas/Saldo Provider (1100) sejumlah harga beli
            if ($hpp > 0) {
                $akunKasBiller = $this->coaModel->where('kode_akun', '1100')->first(); // idealnya ada akun saldo biller
                if ($akunKasBiller) {
                    $jurnalDetail[] = ['akun_id' => $akunHpp['id'], 'debit' => $hpp, 'kredit' => 0, 'keterangan' => 'HPP PPOB ' . $invoice];
                    $jurnalDetail[] = ['akun_id' => $akunKasBiller['id'], 'debit' => 0, 'kredit' => $hpp, 'keterangan' => 'Bayar Biller PPOB ' . $invoice];
                }
            }

            $this->accountingService->catatJurnal(date('Y-m-d'), $invoice, 'Transaksi PPOB ' . $invoice, $jurnalDetail, 'ppob', $transId, session()->get('user_id') ?? 0);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Gagal memproses transaksi PPOB');
        }

        return $transId;
    }
}
