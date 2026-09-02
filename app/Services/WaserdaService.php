<?php
namespace App\Services;

class WaserdaService extends BaseService {
    
    public function checkoutKasir($post, $userId) {
        if (!isset($post['total']) || $post['total'] <= 0) {
            throw new \Exception('Total belanja tidak valid (harus lebih dari 0).');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        $penjualanModel = new \App\Models\PenjualanModel();
        $penjualanDetailModel = new \App\Models\PenjualanDetailModel();
        $noBukti = 'WSR-' . date('YmdHis');

        $penjualanId = $penjualanModel->insert([
            'no_invoice' => $noBukti,
            'tanggal' => date('Y-m-d H:i:s'),
            'anggota_id' => !empty($post['anggota_id']) ? idhash_decode($post['anggota_id']) : null,
            'total_harga' => $post['total'],
            'total_diskon' => 0,
            'total_bayar' => $post['total'],
            'metode_pembayaran' => ucfirst($post['metode']),
            'status_pembayaran' => 'Lunas',
            'kasir_id' => $userId
        ]);

        if (!empty($post['anggota_id'])) {
            $anggota_id = idhash_decode($post['anggota_id']);
            if ($anggota_id) {
                $riwayatId = $riwayatModel->insert([
                    'anggota_id' => $anggota_id,
                    'kategori' => 'Waserda',
                    'jenis_transaksi' => $post['metode'] === 'kasbon' ? 'Keluar' : 'Masuk',
                    'nominal' => $post['total'],
                    'keterangan' => 'Pembelian Kasir Waserda (' . ucfirst($post['metode']) . ') Inv: ' . $noBukti,
                    'referensi_id' => $penjualanId
                ]);
            }
        }
        
        if ($post['metode'] === 'tunai' && $post['total'] > 0) {
            $kasService = new \App\Services\KasService();
            $kasModel = new \App\Models\KasModel();
            $kasDefault = $kasModel->where('status', 'aktif')->orderBy('id', 'ASC')->first();
            if ($kasDefault) {
                $kasService->debit((int)$kasDefault['id'], (float)$post['total'], isset($riwayatId) ? 'riwayat_transaksi' : '', $riwayatId ?? 0, 'Penjualan POS Waserda');
            }
        }

        $coaModel = new \App\Models\AkunCoaModel();
        $produkModel = new \App\Models\ProdukWaserdaModel();
        $mutasiModel = new \App\Models\StokMutasiModel();
        
        $kodeDebit = $post['metode'] === 'kasbon' ? '1200' : '1100';
        $akunDebit = cache()->remember('coa_' . $kodeDebit, 3600, function() use ($coaModel, $kodeDebit) { return $coaModel->where('kode_akun', $kodeDebit)->first(); });
        $akunPendapatan = cache()->remember('coa_4200', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '4200')->first(); });

        if ($akunDebit && $akunPendapatan) {
            $accountingService = new \App\Services\AccountingService();
            $accountingService->catatJurnal(date('Y-m-d'), $noBukti, 'Penjualan Waserda (' . ucfirst($post['metode']) . ')', [
                ['akun_id' => $akunDebit['id'], 'debit' => $post['total'], 'kredit' => 0, 'keterangan' => 'Penjualan Waserda (' . ucfirst($post['metode']) . ')'],
                ['akun_id' => $akunPendapatan['id'], 'debit' => 0, 'kredit' => $post['total'], 'keterangan' => 'Pendapatan Penjualan Waserda']
            ], isset($riwayatId) ? 'riwayat_transaksi' : null, $riwayatId ?? null, $userId);
        }

        $totalHpp = 0;
        if (!empty($post['items']) && is_array($post['items'])) {
            foreach ($post['items'] as $item) {
                $qty = (int)$item['qty'];
                if ($qty <= 0) {
                    $db->transRollback();
                    throw new \Exception('Kuantitas barang harus lebih dari 0.');
                }
                $hpp = (float)$item['hargabeli'];
                $totalHpp += ($qty * $hpp);

                $itemId = idhash_decode($item['id']);
                if (!$itemId) continue;
                $produk = $produkModel->find($itemId);
                if ($produk) {
                    if ($produk['stok'] < $qty) {
                        $db->transRollback();
                        throw new \Exception('Stok ' . $produk['nama_produk'] . ' tidak mencukupi (Sisa: ' . $produk['stok'] . ').');
                    }

                    $sisaStok = $produk['stok'] - $qty;
                    $produkModel->update($itemId, ['stok' => $sisaStok]);
                    
                    if ($sisaStok <= ($produk['stok_minimum'] ?? 0)) {
                        $notifModel = new \App\Models\NotificationModel();
                        
                        $poModel = new \App\Models\PurchaseOrderModel();
                        $existingPO = $poModel->where('produk_id', $itemId)
                                              ->whereIn('status', ['Draft', 'Dikirim', 'Diterima Sebagian'])
                                              ->first();
                        
                        $pesan = 'Sisa stok ' . $produk['nama_produk'] . ' adalah ' . $sisaStok . ' (Minimum: ' . ($produk['stok_minimum'] ?? 0) . ').';
                        if (!$existingPO) {
                            $jumlahRestock = 20; // Default restock quantity
                            $poModel->insert([
                                'nomor_po' => 'PO-' . time() . '-' . rand(100, 999),
                                'supplier_id' => !empty($produk['supplier_id']) ? $produk['supplier_id'] : 1,
                                'produk_id' => $itemId,
                                'jumlah' => $jumlahRestock,
                                'tanggal' => date('Y-m-d'),
                                'total_harga' => $jumlahRestock * ($produk['harga_beli'] ?? 0),
                                'status' => 'Draft'
                            ]);
                            $pesan .= ' Draft PO otomatis telah dibuat.';
                        } else {
                            $pesan .= ' Draft PO sudah ada dan sedang diproses.';
                        }

                        $notifModel->insert([
                            'user_type' => 'Gudang',
                            'user_id' => 0, // Global Gudang
                            'judul' => 'Stok Kritis: ' . $produk['nama_produk'],
                            'pesan' => $pesan,
                            'jenis' => 'warning',
                            'is_read' => 0
                        ]);
                    }

                    $mutasiModel->insert([
                        'produk_id' => $itemId,
                        'jenis' => 'Keluar',
                        'jumlah' => $qty,
                        'keterangan' => 'Penjualan POS: ' . $noBukti,
                        'referensi_id' => $penjualanId
                    ]);

                    $penjualanDetailModel->insert([
                        'penjualan_id' => $penjualanId,
                        'produk_id' => $itemId,
                        'qty' => $qty,
                        'harga_satuan' => $hpp, // Note: hargabeli in post is actually selling price in POS form.
                        'hpp' => $produk['harga_beli'] ?? 0,
                        'subtotal' => $qty * $hpp
                    ]);
                }
            }
            cache()->delete('waserda_list');
        }

        if ($totalHpp > 0) {
            $akunHpp = cache()->remember('coa_5100', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '5100')->first(); });
            $akunPersediaan = cache()->remember('coa_1300', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '1300')->first(); });
            if ($akunHpp && $akunPersediaan) {
                $accountingService = new \App\Services\AccountingService();
                $accountingService->catatJurnal(date('Y-m-d'), $noBukti . '-HPP', 'HPP Penjualan POS: ' . $noBukti, [
                    ['akun_id' => $akunHpp['id'], 'debit' => $totalHpp, 'kredit' => 0, 'keterangan' => 'HPP Penjualan POS'],
                    ['akun_id' => $akunPersediaan['id'], 'debit' => 0, 'kredit' => $totalHpp, 'keterangan' => 'Pengurangan Persediaan POS']
                ], isset($riwayatId) ? 'riwayat_transaksi' : null, $riwayatId ?? null, $userId);
            }
        }

        $auditModel = new \App\Models\AuditTrailModel();
        $auditModel->logAction('CHECKOUT_WASERDA', 'Admin memproses transaksi POS Waserda sejumlah Rp ' . number_format($post['total'],0,',','.') . ' (Metode: ' . $post['metode'] . ')');

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \Exception('Transaksi database gagal.');
        }

        return true;
    }

    public function returPenjualan($penjualanId) {
        $penjualanModel = new \App\Models\PenjualanModel();
        $penjualanDetailModel = new \App\Models\PenjualanDetailModel();
        $penjualan = $penjualanModel->find($penjualanId);
        
        if (!$penjualan) throw new \Exception('Transaksi tidak ditemukan.');
        if ($penjualan['status_pembayaran'] === 'Retur') throw new \Exception('Transaksi sudah diretur sebelumnya.');

        $db = \Config\Database::connect();
        $db->transStart();
        
        $penjualanModel->update($penjualanId, ['status_pembayaran' => 'Retur']);

        $details = $penjualanDetailModel->where('penjualan_id', $penjualanId)->findAll();
        $inventoryService = new \App\Services\InventoryService();
        foreach ($details as $d) {
            $inventoryService->tambahStok($d['produk_id'], 1, (float)$d['qty'], 'retur_penjualan', $penjualanId, 'Retur Penjualan Invoice: ' . $penjualan['no_invoice'], (float)$d['hpp']);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \Exception('Gagal memproses retur penjualan.');
        }
    }

    public function checkoutKasbonMobile($anggotaId, $produkId, $harga, $namaProduk) {
        if ($harga <= 0) {
            throw new \Exception('Harga produk tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $produkModel = new \App\Models\ProdukWaserdaModel();
        $produk = $produkModel->find($produkId);
        if (!$produk) throw new \Exception('Produk tidak ditemukan.');
        if ($produk['stok'] < 1) throw new \Exception('Stok produk habis.');

        $penjualanModel = new \App\Models\PenjualanModel();
        $penjualanDetailModel = new \App\Models\PenjualanDetailModel();
        $noBukti = 'WSR-M-' . date('YmdHis');

        $penjualanId = $penjualanModel->insert([
            'no_invoice' => $noBukti,
            'tanggal' => date('Y-m-d H:i:s'),
            'anggota_id' => $anggotaId,
            'total_harga' => $harga,
            'total_diskon' => 0,
            'total_bayar' => $harga,
            'metode_pembayaran' => 'Kasbon',
            'status_pembayaran' => 'Lunas',
            'kasir_id' => 0 // 0 means Mobile self-checkout
        ]);

        $penjualanDetailModel->insert([
            'penjualan_id' => $penjualanId,
            'produk_id' => $produkId,
            'qty' => 1,
            'harga_satuan' => $harga,
            'hpp' => $produk['harga_beli'] ?? 0,
            'subtotal' => $harga
        ]);

        $sisaStok = $produk['stok'] - 1;
        $produkModel->update($produkId, ['stok' => $sisaStok]);
        
        if ($sisaStok <= ($produk['stok_minimum'] ?? 0)) {
            $notifModel = new \App\Models\NotificationModel();
            $notifModel->insert([
                'user_type' => 'Gudang',
                'user_id' => 0,
                'judul' => 'Stok Kritis: ' . $produk['nama_produk'],
                'pesan' => 'Sisa stok ' . $produk['nama_produk'] . ' adalah ' . $sisaStok . ' (Minimum: ' . ($produk['stok_minimum'] ?? 0) . '). Segera lakukan reorder (PO)!',
                'jenis' => 'warning',
                'is_read' => 0
            ]);
        }

        $mutasiModel = new \App\Models\StokMutasiModel();
        $mutasiModel->insert([
            'produk_id' => $produkId,
            'jenis' => 'Keluar',
            'jumlah' => 1,
            'keterangan' => 'Penjualan Mobile: ' . $noBukti,
            'referensi_id' => $penjualanId
        ]);

        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        $riwayatId = $riwayatModel->insert([
            'anggota_id' => $anggotaId,
            'kategori' => 'Waserda',
            'jenis_transaksi' => 'Keluar',
            'nominal' => $harga,
            'keterangan' => 'Pembelian Kasbon Mobile: ' . $namaProduk,
            'tanggal' => date('Y-m-d'),
            'referensi_id' => $penjualanId
        ]);

        // Jurnal Akuntansi Kasbon Mobile
        $coaModel = new \App\Models\AkunCoaModel();
        $akunDebit = cache()->remember('coa_1200', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '1200')->first(); }); // Piutang Anggota
        $akunPendapatan = cache()->remember('coa_4200', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '4200')->first(); });
        $akunHpp = cache()->remember('coa_5100', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '5100')->first(); });
        $akunPersediaan = cache()->remember('coa_1300', 3600, function() use ($coaModel) { return $coaModel->where('kode_akun', '1300')->first(); });

        $accountingService = new \App\Services\AccountingService();
        if ($akunDebit && $akunPendapatan) {
            $accountingService->catatJurnal(date('Y-m-d'), $noBukti, 'Penjualan Mobile Kasbon', [
                ['akun_id' => $akunDebit['id'], 'debit' => $harga, 'kredit' => 0, 'keterangan' => 'Penjualan Mobile Kasbon'],
                ['akun_id' => $akunPendapatan['id'], 'debit' => 0, 'kredit' => $harga, 'keterangan' => 'Pendapatan Penjualan Waserda']
            ], 'riwayat_transaksi', $riwayatId, 0);
        }

        $hpp = $produk['harga_beli'] ?? 0;
        if ($hpp > 0 && $akunHpp && $akunPersediaan) {
            $accountingService->catatJurnal(date('Y-m-d'), $noBukti . '-HPP', 'HPP Penjualan Mobile', [
                ['akun_id' => $akunHpp['id'], 'debit' => $hpp, 'kredit' => 0, 'keterangan' => 'HPP Penjualan Mobile'],
                ['akun_id' => $akunPersediaan['id'], 'debit' => 0, 'kredit' => $hpp, 'keterangan' => 'Pengurangan Persediaan Mobile']
            ], 'riwayat_transaksi', $riwayatId, 0);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \Exception('Transaksi database gagal.');
        }
        return true;
    }
}
