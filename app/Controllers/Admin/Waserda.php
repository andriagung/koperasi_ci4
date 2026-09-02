        if ($id) $this->poModel->update($id, ['status' => $this->request->getPost('status')]);
        return redirect()->to('/admin/po')->with('message', 'Status PO diubah.');
    }


    public function checkoutKasir() {
        $post = $this->request->getPost();
        
        try {
            $waserdaService = new \App\Services\WaserdaService();
            $userId = session()->get('user_id') ?? 0;
            $waserdaService->checkoutKasir($post, $userId);
            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function ajaxTransaksi()
    {
        $model = new \App\Models\RiwayatTransaksiModel();
        
        $result = $this->processDataTables($model, ['riwayat_transaksi.keterangan', 'a.nama_lengkap'], function($builder) {
            $builder->select('riwayat_transaksi.*, a.nama_lengkap');
            $builder->join('anggota a', 'a.id = riwayat_transaksi.anggota_id', 'left');
            $builder->where('riwayat_transaksi.kategori', 'Waserda');
        });
        
        $data = $result['data'];
        $totalData = $result['recordsTotal'];
        $totalFiltered = $result['recordsFiltered'];
        
        $response = [
            'draw' => $result['draw'],
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => []
        ];
        
        $offset = $result['offset'];
        foreach ($data as $i => $row) {
            $statusBadge = ($row['jenis_transaksi'] == 'Keluar') 
                ? '<span class="status-badge" style="background:#e0e7ff; color:#4338ca;">Kasbon</span>'
                : '<span class="status-badge status-approved">Tunai</span>';
                
            $printUrl = "/admin/waserda/cetak-struk/" . idhash_encode($row['id']);
            $actionBtn = '<div class="action-btns"><button class="btn-action edit" onclick="window.open(\''.$printUrl.'\', \'_blank\', \'width=350,height=600\')" title="Cetak Struk"><i class="fas fa-print"></i></button></div>';
            
            $response['data'][] = [
                $offset + $i + 1,
                date('d/m/Y H:i', strtotime($row['created_at'])),
                $row['keterangan'],
                $row['nama_lengkap'] ?: 'Umum (Non-Anggota)',
                'Rp ' . number_format($row['nominal'], 0, ',', '.'),
                $statusBadge,
                $actionBtn
            ];
        }
        return $this->response->setJSON($response);
    }

    public function cetakStruk($id)
    {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        $anggotaModel = new \App\Models\AnggotaModel();
        
        $transaksi = $riwayatModel->find($id);
        if (!$transaksi || $transaksi['kategori'] !== 'Waserda') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Transaksi tidak ditemukan.');
        }

        $anggota = null;
        if ($transaksi['anggota_id']) {
            $anggota = $anggotaModel->find($transaksi['anggota_id']);
        }

        return view('admin/cetak_struk', [
            'transaksi' => $transaksi,
            'anggota' => $anggota
        ]);
    }

    public function ajaxPO()
    {
        $model = new \App\Models\PurchaseOrderModel();
        
        $result = $this->processDataTables($model, ['purchase_order.nomor_po', 's.nama_supplier', 'p.nama_produk'], function($builder) {
            $builder->select('purchase_order.*, s.nama_supplier, p.nama_produk, sm.jumlah');
            $builder->join('supplier s', 's.id = purchase_order.supplier_id', 'left');
            $builder->join('stok_mutasi sm', "sm.keterangan = CONCAT('Restock dari PO: ', purchase_order.nomor_po)", 'left');
            $builder->join('produk_waserda p', 'p.id = sm.produk_id', 'left');
        });
        
        $data = $result['data'];
        $totalData = $result['recordsTotal'];
        $totalFiltered = $result['recordsFiltered'];
        
        $response = [
            'draw' => $result['draw'],
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => []
        ];
        
        $offset = $result['offset'];
        foreach ($data as $i => $row) {
            $status = $row['status'] ?? 'Draft';
            $badgeClass = 'bg-secondary';
            if ($status == 'Selesai') $badgeClass = 'bg-success';
            elseif ($status == 'Dikirim') $badgeClass = 'bg-info';
            elseif ($status == 'Diterima Lengkap') $badgeClass = 'bg-primary';
            elseif ($status == 'Dibayar') $badgeClass = 'bg-warning';
            
            $statusBadge = '<span class="badge ' . $badgeClass . '">' . $status . '</span>';
            
            $actionBtn = '<button class="btn-sm btn-primary" onclick="updatePOModal(' . $row['id'] . ', \'' . $status . '\', \'' . $row['produk_id'] . '\', \'' . $row['jumlah'] . '\')"><i class="fas fa-edit"></i> Update Status</button>';
            
            $response['data'][] = [
                $offset + $i + 1,
                date('d/m/Y', strtotime($row['created_at'])),
                $row['nomor_po'],
                $row['nama_supplier'],
                $row['nama_produk'],
                $row['jumlah'],
                'Rp ' . number_format($row['total_harga'], 0, ',', '.'),
                $statusBadge,
                $actionBtn
            ];
        }
        return $this->response->setJSON($response);
    }

    public function simpanStockOpname()
    {
        $post = $this->request->getPost();
        
        try {
            $inventoryService = new \App\Services\InventoryService();
            $userId = session()->get('user_id') ?? 0;
            $inventoryService->prosesStockOpname($post, $userId);
            return redirect()->to('/admin/gudang#tab-opname')->with('message', 'Stock Opname berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->to('/admin/gudang#tab-opname')->with('error', $e->getMessage());
        }
    }
    
    public function ajaxStockOpname()
    {
        $model = new \App\Models\StockOpnameModel();
        
        $result = $this->processDataTables($model, ['p.nama_produk', 'stock_opname.petugas'], function($builder) {
            $builder->select('stock_opname.*, p.nama_produk');
            $builder->join('produk_waserda p', 'p.id = stock_opname.produk_id', 'left');
        });
        
        $data = $result['data'];
        $totalData = $result['recordsTotal'];
        $totalFiltered = $result['recordsFiltered'];
        
        $response = [
            'draw' => $result['draw'],
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => []
        ];
        
        $offset = $result['offset'];
        foreach ($data as $i => $row) {
            $selisihBadge = '';
            if ($row['selisih'] < 0) {
                $selisihBadge = '<span class="badge bg-danger">'.$row['selisih'].'</span>';
            } else if ($row['selisih'] > 0) {
                $selisihBadge = '<span class="badge bg-success">+'.$row['selisih'].'</span>';
            } else {
                $selisihBadge = '<span class="badge bg-secondary">0</span>';
            }
            
            $response['data'][] = [
                $offset + $i + 1,
                date('d/m/Y', strtotime($row['created_at'])),
                $row['nama_produk'],
                $row['stok_sistem'],
                $row['stok_fisik'],
                $selisihBadge,
                $row['keterangan'] ?? '-',
                $row['petugas']
            ];
        }
        return $this->response->setJSON($response);
    }

    public function prosesReturPenjualan() {
        $post = $this->request->getPost();
        $penjualanId = $post['penjualan_id'] ?? null;
        if (!$penjualanId) return redirect()->to(previous_url(true))->with('error', 'Pilih transaksi penjualan.');

        try {
            $waserdaService = new \App\Services\WaserdaService();
            $waserdaService->returPenjualan($penjualanId);
            return redirect()->to(previous_url(true))->with('success', 'Retur penjualan berhasil diproses.');
        } catch (\Exception $e) {
            return redirect()->to(previous_url(true))->with('error', $e->getMessage());
        }
    }
}
