<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;
use App\Models\JadwalAngsuranModel;
use App\Models\JurnalTransaksiModel;
use App\Models\AuditTrailModel;

class MidtransController extends BaseController
{
    protected $simpananModel;
    protected $pinjamanModel;
    protected $jadwalModel;
    protected $jurnalModel;
    protected $auditModel;

    public function __construct()
    {
        $config = config('Midtrans');
        \Midtrans\Config::$serverKey = $config->serverKey;
        \Midtrans\Config::$isProduction = $config->isProduction;
        \Midtrans\Config::$isSanitized = $config->isSanitized;
        \Midtrans\Config::$is3ds = $config->is3ds;

        $this->simpananModel = new SimpananModel();
        $this->pinjamanModel = new PinjamanModel();
        $this->jadwalModel = new JadwalAngsuranModel();
        $this->jurnalModel = new JurnalTransaksiModel();
        $this->auditModel = new AuditTrailModel();
    }

    public function generateToken()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $jenis = $this->request->getPost('jenis'); // 'simpanan' atau 'angsuran'
        $nominal = $this->request->getPost('nominal');
        $idRef = $this->request->getPost('id_ref'); // ID anggota atau ID jadwal angsuran
        $keterangan = $this->request->getPost('keterangan');

        if (!$nominal || $nominal <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nominal tidak valid']);
        }

        // Generate Order ID Unik
        $orderId = 'TRX-' . strtoupper($jenis) . '-' . time() . '-' . rand(100, 999);

        // Transaction Details
        $transaction_details = [
            'order_id' => $orderId,
            'gross_amount' => $nominal,
        ];

        // Item Details
        $item_details = [
            [
                'id' => $jenis,
                'price' => $nominal,
                'quantity' => 1,
                'name' => $keterangan
            ]
        ];

        // Customer Details
        $customer_details = [
            'first_name' => session()->get('nama_lengkap') ?? 'Anggota Koperasi',
            'email' => 'member' . session()->get('id') . '@koperasi.local'
        ];

        // Build Payload
        $transaction = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            'custom_field1' => $jenis,
            'custom_field2' => $idRef // Simpan referensi ID
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($transaction);
            return $this->response->setJSON(['status' => 'success', 'token' => $snapToken]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function webhook()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $payload = $this->request->getBody();
        $notif = json_decode($payload);

        if (!$notif) {
            return $this->response->setStatusCode(400)->setBody('Invalid Payload');
        }

        $config = config('Midtrans');
        \Midtrans\Config::$serverKey = $config->serverKey;
        
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $jenis = $notif->custom_field1 ?? '';
        $idRef = $notif->custom_field2 ?? '';
        $nominal = $notif->gross_amount;

        if ($transaction == 'settlement') {
            // Pembayaran Berhasil, Proses Auto-Jurnal
            
            if ($jenis == 'angsuran') {
                // Update Jadwal Angsuran
                $this->jadwalModel->update($idRef, [
                    'status' => 'Lunas',
                    'tanggal_bayar' => date('Y-m-d H:i:s')
                ]);
                
                // Cari data angsuran untuk mengetahui pokok & jasa
                $angsuran = $this->jadwalModel->find($idRef);
                if ($angsuran) {
                    $pinjaman = $this->pinjamanModel->find($angsuran['pinjaman_id']);
                    
                    // Jurnal Angsuran
                    // Debit: Kas (1), Kredit: Piutang Pinjaman (2), Kredit: Pendapatan Bunga (8)
                    $this->jurnalModel->catatJurnal(1, 'Debit', $angsuran['pokok'] + $angsuran['jasa'], "Pembayaran Angsuran Online $order_id");
                    $this->jurnalModel->catatJurnal(2, 'Kredit', $angsuran['pokok'], "Pelunasan Pokok Pinjaman $order_id");
                    $this->jurnalModel->catatJurnal(8, 'Kredit', $angsuran['jasa'], "Pendapatan Bunga Pinjaman $order_id");
                    
                    // Update Sisa Pinjaman
                    $this->pinjamanModel->update($angsuran['pinjaman_id'], [
                        'sisa_pokok' => $pinjaman['sisa_pokok'] - $angsuran['pokok']
                    ]);
                }
            } elseif ($jenis == 'simpanan') {
                // Catat transaksi simpanan sukarela
                $this->simpananModel->insert([
                    'anggota_id' => $idRef,
                    'jenis_simpanan' => 'Sukarela',
                    'tipe_transaksi' => 'Setor',
                    'nominal' => $nominal,
                    'tanggal' => date('Y-m-d'),
                    'keterangan' => "Setoran Simpanan Online $order_id",
                    'status' => 'POSTED' // Langsung setuju karena via payment gateway
                ]);
                
                // Jurnal Setoran Simpanan
                // Debit: Kas (1), Kredit: Simpanan Anggota (4)
                $this->jurnalModel->catatJurnal(1, 'Debit', $nominal, "Setoran Simpanan Sukarela Online $order_id");
                $this->jurnalModel->catatJurnal(4, 'Kredit', $nominal, "Simpanan Sukarela Masuk $order_id");
            }
            
            // Catat ke Audit Trail (Sistem)
            $this->auditModel->logAction('PAYMENT_GATEWAY_SUCCESS', "Pembayaran $order_id ($jenis) berhasil diproses otomatis.");
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    }
}
