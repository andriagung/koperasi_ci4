<?php
namespace App\Services;

class InventoryService extends BaseService {
    
    /**
     * Tambah Stok ke Lokasi Tertentu
     */
    public function tambahStok(int $produkId, int $lokasiId, float $qty, string $referensiType, int $referensiId, string $keterangan, float $harga = 0): void {
        $db = \Config\Database::connect();
        
        // 1. Cek / Insert tabel stok (master stok per lokasi)
        $stokQuery = $db->table('stok')
            ->where(['produk_id' => $produkId, 'lokasi_id' => $lokasiId])
            ->get()->getRowArray();
            
        if ($stokQuery) {
            $db->table('stok')->where('id', $stokQuery['id'])->update([
                'qty' => $stokQuery['qty'] + $qty,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $saldoAkhir = $stokQuery['qty'] + $qty;
        } else {
            $db->table('stok')->insert([
                'produk_id' => $produkId,
                'lokasi_id' => $lokasiId,
                'qty' => $qty,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $saldoAkhir = $qty;
        }
        
        // 2. Update tabel produk_waserda (stok global) -> untuk backward compatibility
        $db->query("UPDATE produk_waserda SET stok = stok + ? WHERE id = ?", [$qty, $produkId]);
        
        // 3. Catat ke stok_mutasi
        $db->table('stok_mutasi')->insert([
            'produk_id' => $produkId,
            'lokasi_id' => $lokasiId,
            'jenis' => 'Masuk',
            'jumlah' => $qty,
            'saldo' => $saldoAkhir,
            'harga' => $harga,
            'keterangan' => $keterangan,
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Kurangi Stok dari Lokasi Tertentu
     */
    public function kurangiStok(int $produkId, int $lokasiId, float $qty, string $referensiType, int $referensiId, string $keterangan, float $harga = 0): void {
        $db = \Config\Database::connect();
        
        // 1. Cek tabel stok
        $stokQuery = $db->table('stok')
            ->where(['produk_id' => $produkId, 'lokasi_id' => $lokasiId])
            ->get()->getRowArray();
            
        if (!$stokQuery || $stokQuery['qty'] < $qty) {
            throw new \Exception("Stok tidak mencukupi di lokasi terpilih.");
        }
        
        // Update stok lokasi
        $db->table('stok')->where('id', $stokQuery['id'])->update([
            'qty' => $stokQuery['qty'] - $qty,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $saldoAkhir = $stokQuery['qty'] - $qty;
        
        // 2. Update tabel produk_waserda (stok global)
        $db->query("UPDATE produk_waserda SET stok = stok - ? WHERE id = ?", [$qty, $produkId]);
        
        // 3. Catat ke stok_mutasi
        $db->table('stok_mutasi')->insert([
            'produk_id' => $produkId,
            'lokasi_id' => $lokasiId,
            'jenis' => 'Keluar',
            'jumlah' => $qty, // Disimpan positif, jenisnya Keluar
            'saldo' => $saldoAkhir,
            'harga' => $harga,
            'keterangan' => $keterangan,
            'referensi_type' => $referensiType,
            'referensi_id' => $referensiId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function getSaldo(int $produkId, int $lokasiId): float {
        $db = \Config\Database::connect();
        $stokQuery = $db->table('stok')
            ->where(['produk_id' => $produkId, 'lokasi_id' => $lokasiId])
            ->get()->getRowArray();
        return $stokQuery ? (float)$stokQuery['qty'] : 0.0;
    }
    
    public function transfer(int $produkId, int $lokasiAsalId, int $lokasiTujuanId, float $qty, string $referensiType, int $referensiId): void {
        $this->kurangiStok($produkId, $lokasiAsalId, $qty, $referensiType, $referensiId, 'Transfer Keluar ke Lokasi ' . $lokasiTujuanId);
        $this->tambahStok($produkId, $lokasiTujuanId, $qty, $referensiType, $referensiId, 'Transfer Masuk dari Lokasi ' . $lokasiAsalId);
    }
    
    public function opname(array $data): array {
        // Implementasi Stock Opname akan dibuat saat Phase 8 jika diperlukan
        return ['success' => true];
    }

    public function prosesStockOpname($post, $userId) {
        if (empty($post['produk_id']) || !isset($post['stok_fisik'])) {
            throw new \Exception('Data tidak lengkap');
        }
        
        $produkModel = new \App\Models\ProdukWaserdaModel();
        $produk = $produkModel->find($post['produk_id']);
        if (!$produk) {
            throw new \Exception('Produk tidak ditemukan');
        }
        
        $stokFisik = (int)$post['stok_fisik'];
        $stokSistem = (int)$produk['stok'];
        $selisih = $stokFisik - $stokSistem;
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $opnameModel = new \App\Models\StockOpnameModel();
        $opnameModel->insert([
            'produk_id' => $produk['id'],
            'stok_sistem' => $stokSistem,
            'stok_fisik' => $stokFisik,
            'selisih' => $selisih,
            'keterangan' => $post['keterangan'] ?? '',
            'petugas' => 'Admin'
        ]);
        
        $produkModel->update($produk['id'], ['stok' => $stokFisik]);
        
        if ($selisih !== 0) {
            $mutasiModel = new \App\Models\StokMutasiModel();
            $mutasiModel->insert([
                'produk_id' => $produk['id'],
                'jenis' => $selisih > 0 ? 'Masuk' : 'Keluar',
                'jumlah' => abs($selisih),
                'keterangan' => 'Penyesuaian Stock Opname'
            ]);
            
            $coaModel = new \App\Models\AkunCoaModel();
            $akunPersediaan = $coaModel->where('kode_akun', '1130')->first(); 
            $akunBeban = $coaModel->where('kode_akun', '5100')->first(); 
            
            if ($akunPersediaan && $akunBeban) {
                $nilaiSelisih = abs($selisih) * $produk['harga_beli'];
                $noBukti = 'OPN-' . date('YmdHis');
                $accountingService = new \App\Services\AccountingService();
                if ($selisih < 0) {
                    $accountingService->catatJurnal(date('Y-m-d'), $noBukti, 'Selisih Kurang Stock Opname', [
                        ['akun_id' => $akunBeban['id'], 'debit' => $nilaiSelisih, 'kredit' => 0, 'keterangan' => 'Selisih Kurang Stock Opname: ' . $produk['nama_produk']],
                        ['akun_id' => $akunPersediaan['id'], 'debit' => 0, 'kredit' => $nilaiSelisih, 'keterangan' => 'Selisih Kurang Stock Opname: ' . $produk['nama_produk']]
                    ], 'stock_opname', null, $userId);
                } else {
                    $accountingService->catatJurnal(date('Y-m-d'), $noBukti, 'Selisih Lebih Stock Opname', [
                        ['akun_id' => $akunPersediaan['id'], 'debit' => $nilaiSelisih, 'kredit' => 0, 'keterangan' => 'Selisih Lebih Stock Opname: ' . $produk['nama_produk']],
                        ['akun_id' => $akunBeban['id'], 'debit' => 0, 'kredit' => $nilaiSelisih, 'keterangan' => 'Selisih Lebih Stock Opname: ' . $produk['nama_produk']]
                    ], 'stock_opname', null, $userId);
                }
            }
        }
        
        $auditModel = new \App\Models\AuditTrailModel();
        $auditModel->logAction('STOCK_OPNAME', 'Admin melakukan Stock Opname untuk ' . $produk['nama_produk'] . ' (Selisih: '.$selisih.')');
        
        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \Exception('Gagal memproses Stock Opname');
        }
        
        return true;
    }

    public function prosesTransfer($post) {
        $lokasiAsal = $post['lokasi_asal_id'];
        $lokasiTujuan = $post['lokasi_tujuan_id'];
        $produkId = $post['produk_id'];
        $qty = (float)$post['qty'];
        
        if ($lokasiAsal == $lokasiTujuan) {
            throw new \Exception('Lokasi asal dan tujuan tidak boleh sama.');
        }
        
        $transferModel = new \App\Models\TransferStokModel();
        
        $db = clone $transferModel->db;
        $db->transStart();
        
        // Simpan header transfer_stok
        $nomorTransfer = 'TRF-' . date('YmdHis');
        $transferModel->insert([
            'nomor_transfer' => $nomorTransfer,
            'tanggal' => date('Y-m-d'),
            'lokasi_asal_id' => $lokasiAsal,
            'lokasi_tujuan_id' => $lokasiTujuan,
            'keterangan' => $post['keterangan']
        ]);
        $transferId = $transferModel->getInsertID();
        
        $this->transfer($produkId, $lokasiAsal, $lokasiTujuan, $qty, 'transfer_stok', $transferId);
        
        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \Exception('Gagal memproses transfer stok.');
        }
        return true;
    }

    public function prosesBatchOpname($post) {
        $lokasiId = $post['lokasi_id'];
        $produkIds = $post['produk_id']; // array
        $stokSistem = $post['stok_sistem']; // array
        $stokFisik = $post['stok_fisik']; // array
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        $keteranganUmum = 'Opname ' . date('d/m/Y');
        
        for ($i = 0; $i < count($produkIds); $i++) {
            $selisih = (float)$stokFisik[$i] - (float)$stokSistem[$i];
            
            if ($selisih != 0) {
                if ($selisih > 0) {
                    $this->tambahStok($produkIds[$i], $lokasiId, abs($selisih), 'opname', 0, $keteranganUmum);
                } else {
                    $this->kurangiStok($produkIds[$i], $lokasiId, abs($selisih), 'opname', 0, $keteranganUmum);
                }
            }
        }
        
        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new \Exception('Gagal memproses stock opname batch.');
        }
        return true;
    }
}
