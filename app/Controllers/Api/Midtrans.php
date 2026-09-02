<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\SimpananTransaksiModel;

class Midtrans extends BaseController
{
    use ResponseTrait;

    public function callback()
    {
        $json = $this->request->getJSON();
        
        if (!$json) {
            return $this->fail('Invalid JSON');
        }

        $orderId = $json->order_id ?? '';
        $statusCode = $json->status_code ?? '';
        $transactionStatus = $json->transaction_status ?? '';
        $signatureKey = $json->signature_key ?? '';
        
        // Verifikasi Signature Key (opsional tapi disarankan)
        $serverKey = getenv('MIDTRANS_SERVER_KEY');
        if ($serverKey) {
            $grossAmount = $json->gross_amount ?? '';
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            if ($expectedSignature !== $signatureKey) {
                return $this->failUnauthorized('Invalid Signature');
            }
        }

        $transaksiModel = new SimpananTransaksiModel();
        
        // Contoh Order ID: SIMP-12345 (Simpanan), ANGS-12345 (Angsuran)
        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            if (strpos($orderId, 'SIM') === 0) {
                // Update Simpanan
                $transaksiModel
                   ->where('nomor_transaksi', $orderId)
                   ->set(['status' => 'POSTED'])
                   ->update();
                   
                // Catat ke saldo, dsb (Ide: panggil SimpananService)
            } elseif (strpos($orderId, 'PINJ') === 0) {
                // Update Pinjaman Angsuran
                // (Sesuaikan dengan format order ID angsuran)
            }
            return $this->respond(['status' => 'success', 'message' => 'Status updated to settlement']);
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            if (strpos($orderId, 'SIM') === 0) {
                $transaksiModel
                   ->where('nomor_transaksi', $orderId)
                   ->set(['status' => 'CANCELLED'])
                   ->update();
            }
            return $this->respond(['status' => 'success', 'message' => 'Status updated to failed']);
        }

        return $this->respond(['status' => 'ignored', 'message' => 'Transaction status not handled']);
    }
}
