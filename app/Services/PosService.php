<?php
namespace App\Services;

class PosService extends BaseService {
    
    public function jual(array $keranjang, $anggotaId, $metodeBayar, $totalBayar, $diskon = 0) {
        $db = \Config\Database::connect();
        $db->transStart();
        
        $penjualanModel = new \App\Models\PenjualanModel();
        $detailModel = new \App\Models\PenjualanDetailModel();
        $produkModel = new \App\Models\ProdukWaserdaModel();
        
        $invoice = $this->generateNomor('INV-');
        
        // 1. Simpan header penjualan
        $penjualanId = $penjualanModel->insert([
            'no_invoice' => $invoice,
            'tanggal' => date('Y-m-d H:i:s'),
            'anggota_id' => $anggotaId ?: null,
            'total_harga' => $totalBayar + $diskon,
            'total_diskon' => $diskon,
            'total_bayar' => $totalBayar,
            'metode_pembayaran' => $metodeBayar,
            'status_pembayaran' => 'Lunas',
            'kasir_id' => session()->get('user_id') ?: 1
        ]);
        
        // 2. Loop keranjang
        foreach ($keranjang as $item) {
            $produk = $produkModel->find($item['id']);
            
            // Simpan detail
            $detailModel->insert([
                'penjualan_id' => $penjualanId,
                'produk_id' => $item['id'],
                'qty' => $item['qty'],
                'harga_satuan' => $item['harga'],
                'hpp' => $produk['harga_beli'] ?? 0, // Ambil HPP dari master
                'subtotal' => $item['qty'] * $item['harga']
            ]);
            
            // Kurangi stok (bisa dipindah ke InventoryService nanti)
            if (isset($produk['stok'])) {
                $produkModel->update($produk['id'], [
                    'stok' => $produk['stok'] - $item['qty']
                ]);
            }
        }
        
        // 3. Jurnal & Kas (Jika Tunai/Transfer masuk ke Kas, jika Anggota potong simpanan)
        if ($metodeBayar == 'Anggota/Simpanan' && $anggotaId) {
            // Potong saldo sukarela
            $simpananSaldoModel = new \App\Models\SimpananSaldoModel();
            $saldoSukarela = $simpananSaldoModel->where('anggota_id', $anggotaId)->where('jenis_simpanan_id', 3)->first();
            if ($saldoSukarela) {
                $simpananSaldoModel->update($saldoSukarela['id'], [
                    'saldo' => $saldoSukarela['saldo'] - $totalBayar
                ]);
                
                // Catat transaksi simpanan
                $simpananTransaksiModel = new \App\Models\SimpananTransaksiModel();
                $simpananTransaksiModel->insert([
                    'anggota_id' => $anggotaId,
                    'jenis_simpanan_id' => 3,
                    'jenis_transaksi' => 'Tarik',
                    'nominal' => $totalBayar,
                    'keterangan' => 'Pembayaran Belanja WARSerDA (INV: '.$invoice.')',
                    'status' => 'Berhasil'
                ]);
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Transaksi gagal diproses.'];
        }
        
        $this->logAudit('PENJUALAN', 'Penjualan baru diselesaikan: ' . $invoice, 'penjualan', $penjualanId);
        return ['success' => true, 'invoice' => $invoice, 'id' => $penjualanId];
    }
}
