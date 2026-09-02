<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Pinjaman extends BaseController {
    
    // --- MASTER PRODUK PINJAMAN ---
    public function produk() {
        $model = new \App\Models\ProdukPinjamanModel();
        $data = ['produk' => $model->findAll()];
        return view('admin/pinjaman/produk', $data);
    }

    public function simpanProduk() {
        $model = new \App\Models\ProdukPinjamanModel();
        $data = $this->request->getPost();
        
        if (!empty($data['id'])) {
            $model->update($data['id'], $data);
            $msg = 'Produk pinjaman diperbarui.';
        } else {
            $model->insert($data);
            $msg = 'Produk pinjaman ditambahkan.';
        }
        return redirect()->to('/admin/pinjaman/produk')->with('message', $msg);
    }

    public function hapusProduk($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $model = new \App\Models\ProdukPinjamanModel();
        $model->update($id, ['status' => 'nonaktif']); // Soft delete
        return $this->response->setJSON(['status' => 'success']);
    }

    // --- PENGAJUAN PINJAMAN ---
    public function pengajuan() {
        $pinjamanModel = new \App\Models\PinjamanModel();
        $anggotaModel = new \App\Models\AnggotaModel();
        $produkModel = new \App\Models\ProdukPinjamanModel();
        
        $statusFilter = $this->request->getGet('status');
        
        $builder = $pinjamanModel->select('pinjaman.*, anggota.nama_lengkap, anggota.nip')
                                ->join('anggota', 'anggota.id = pinjaman.anggota_id');
                                
        $title = 'Daftar Pengajuan Pinjaman';
        
        if ($statusFilter === 'submitted') {
            $builder->where('status_pengajuan', 'SUBMITTED');
            $title = 'Verifikasi & Approval Pinjaman';
        } elseif ($statusFilter === 'approved') {
            $builder->where('status_pengajuan', 'APPROVED');
            $title = 'Antrean Pencairan Pinjaman';
        } elseif ($statusFilter === 'active') {
            $builder->whereIn('status_pengajuan', ['ACTIVE', 'PAID']);
            $title = 'Jadwal Angsuran & Pembayaran';
        } else {
            $builder->whereIn('status_pengajuan', ['SUBMITTED', 'APPROVED', 'REJECTED', 'ACTIVE', 'PAID']);
        }
        
        $data = [
            'title' => $title,
            'status_filter' => $statusFilter,
            'anggota' => $anggotaModel->where('status', 'Aktif')->findAll(),
            'produk' => $produkModel->where('status', 'aktif')->findAll(),
            'pengajuan' => $builder->orderBy('pinjaman.created_at', 'DESC')->findAll()
        ];
        return view('admin/pinjaman/pengajuan', $data);
    }

    public function simpanPengajuan() {
        if (!$this->validate([
            'nominal_pengajuan' => [
                'rules' => 'required|greater_than[0]',
                'errors' => ['greater_than' => 'Nominal pengajuan harus lebih dari Rp 0']
            ],
            'tenor_bulan' => [
                'rules' => 'required|greater_than[0]',
                'errors' => ['greater_than' => 'Tenor cicilan harus lebih dari 0 bulan']
            ]
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['nominal_pengajuan'] ?? $this->validator->getErrors()['tenor_bulan'] ?? 'Data tidak valid.');
        }

        $pinjamanService = new \App\Services\PinjamanService();
        $data = $this->request->getPost();
        
        // Data Tambahan untuk Analisis DSR
        $analisis = [
            'pendapatan_bulanan' => $data['pendapatan_bulanan'],
            'pengeluaran_bulanan' => $data['pengeluaran_bulanan'],
            'angsuran_lain' => $data['angsuran_lain'],
        ];
        
        // Remove from main data so model insert works cleanly
        unset($data['pendapatan_bulanan'], $data['pengeluaran_bulanan'], $data['angsuran_lain']);

        $res = $pinjamanService->ajukan($data);
        
        if ($res['success']) {
            // Save analisis
            $analisis['pinjaman_id'] = $res['id'];
            
            // Hitung DSR menggunakan Service
            $dsrData = $pinjamanService->hitungDSR([
                'penghasilan_bulanan' => $analisis['pendapatan_bulanan'],
                'nominal_pengajuan' => $data['nominal_pengajuan'],
                'tenor_bulan' => $data['tenor_bulan'],
                'cicilan_lainnya' => $analisis['angsuran_lain']
            ]);
            $dsr = $dsrData['dsr'];
            $analisis['dsr_score'] = min($dsr, 100);
            
            if ($dsr > 35) {
                $analisis['rekomendasi'] = 'Tinggi Risiko';
                $analisis['catatan_analis'] = 'DSR melebihi ambang batas aman 35%.';
            } else {
                $analisis['rekomendasi'] = 'Aman';
                $analisis['catatan_analis'] = 'Kapasitas bayar mencukupi.';
            }
            
            $analisisModel = new \App\Models\PinjamanAnalisisModel();
            $analisisModel->insert($analisis);
            
            // Log Approval
            $logModel = new \App\Models\ApprovalLogsModel();
            $logModel->insert([
                'tabel_referensi' => 'pinjaman',
                'referensi_id' => $res['id'],
                'user_id' => session()->get('user_id'),
                'action' => 'SUBMIT',
                'catatan' => 'Pengajuan baru disubmit.'
            ]);
            
            return redirect()->to('/admin/pinjaman/pengajuan')->with('message', 'Pengajuan berhasil disimpan dan masuk ke antrean verifikasi.');
        } else {
            return redirect()->to('/admin/pinjaman/pengajuan')->with('error', $res['message']);
        }
    }

    // --- APPROVAL WORKFLOW ---
    public function detail($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $pinjamanModel = new \App\Models\PinjamanModel();
        $analisisModel = new \App\Models\PinjamanAnalisisModel();
        
        $pinjaman = $pinjamanModel->select('pinjaman.*, anggota.nama_lengkap, anggota.nip, anggota.divisi, anggota.no_hp')
                    ->join('anggota', 'anggota.id = pinjaman.anggota_id')
                    ->find($id);
                    
        if (!$pinjaman) return redirect()->to('/admin/pinjaman/pengajuan')->with('error', 'Data tidak ditemukan');
        
        $data = [
            'pinjaman' => $pinjaman,
            'analisis' => $analisisModel->where('pinjaman_id', $id)->first()
        ];
        
        return view('admin/pinjaman/approval', $data);
    }
    
    public function setujui($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        // Validasi Role (Tugas: can('pinjaman.approve'))
        $pinjamanModel = new \App\Models\PinjamanModel();
        $catatan = $this->request->getPost('catatan');
        
        $pinjamanModel->update($id, ['status_pengajuan' => 'APPROVED']);
        
        $logModel = new \App\Models\ApprovalLogsModel();
        $logModel->insert([
            'tabel_referensi' => 'pinjaman',
            'referensi_id' => $id,
            'user_id' => session()->get('user_id'),
            'action' => 'APPROVE',
            'catatan' => $catatan
        ]);
        
        return redirect()->to('/admin/pinjaman/pengajuan')->with('message', 'Pengajuan telah disetujui.');
    }
    
    public function tolak($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $pinjamanModel = new \App\Models\PinjamanModel();
        $catatan = $this->request->getPost('catatan');
        
        $pinjamanModel->update($id, ['status_pengajuan' => 'REJECTED']);
        
        $logModel = new \App\Models\ApprovalLogsModel();
        $logModel->insert([
            'tabel_referensi' => 'pinjaman',
            'referensi_id' => $id,
            'user_id' => session()->get('user_id'),
            'action' => 'REJECT',
            'catatan' => $catatan
        ]);
        
        return redirect()->to('/admin/pinjaman/pengajuan')->with('message', 'Pengajuan telah ditolak.');
    }

    // --- PENCAIRAN PINJAMAN ---
    public function pencairan($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $pinjamanModel = new \App\Models\PinjamanModel();
        $pinjaman = $pinjamanModel->select('pinjaman.*, anggota.nama_lengkap, anggota.nomor_anggota')
                    ->join('anggota', 'anggota.id = pinjaman.anggota_id')
                    ->where('pinjaman.id', $id)
                    ->where('status_pengajuan', 'APPROVED')
                    ->first();
                    
        if (!$pinjaman) return redirect()->to('/admin/pinjaman/pengajuan')->with('error', 'Pinjaman tidak valid untuk pencairan.');
        
        $db = \Config\Database::connect();
        $kasModel = clone $db->table('kas');
        $bankModel = clone $db->table('rekening_bank');
        
        $data = [
            'pinjaman' => $pinjaman,
            'kas' => $kasModel->where('status', 'aktif')->get()->getResultArray(),
            'bank' => $bankModel->where('status', 'aktif')->get()->getResultArray()
        ];
        
        return view('admin/pinjaman/pencairan', $data);
    }

    public function prosesPencairan() {
        if (!$this->validate([
            'biaya_admin' => [
                'rules' => 'permit_empty|greater_than_equal_to[0]',
                'errors' => ['greater_than_equal_to' => 'Biaya admin tidak boleh minus.']
            ]
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['biaya_admin'] ?? 'Data tidak valid.');
        }

        $data = $this->request->getPost();
        $pinjamanId = $data['pinjaman_id'];
        
        // Gunakan PinjamanService untuk pencairan
        $pinjamanService = new \App\Services\PinjamanService();
        $res = $pinjamanService->cairkan($pinjamanId, $data, session()->get('user_id'));
        
        if (!$res['success']) {
            return redirect()->back()->withInput()->with('error', $res['message']);
        }
        
        return redirect()->to('/admin/pinjaman/jadwal/'.$pinjamanId)->with('message', 'Pencairan berhasil dan jadwal angsuran telah di-generate.');
    }

    // --- JADWAL & PEMBAYARAN ---
    public function jadwal($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $pinjamanModel = new \App\Models\PinjamanModel();
        $pembayaranModel = new \App\Models\PinjamanPembayaranModel();
        
        $pinjaman = $pinjamanModel->select('pinjaman.*, anggota.nama_lengkap, anggota.nomor_anggota')
                    ->join('anggota', 'anggota.id = pinjaman.anggota_id')
                    ->find($id);
                    
        if (!$pinjaman) return redirect()->to('/admin/pinjaman/pengajuan')->with('error', 'Data tidak ditemukan.');
        
        $db = \Config\Database::connect();
        $kasModel = clone $db->table('kas');
        $bankModel = clone $db->table('rekening_bank');

        // Calculate sisa_pokok dynamically
        $sisaPokok = $db->table('pinjaman_angsuran')
                         ->selectSum('pokok')
                         ->where('pinjaman_id', $id)
                         ->where('status', 'Belum Lunas')
                         ->get()->getRow()->pokok ?? 0;
        $pinjaman['sisa_pokok'] = $sisaPokok;

        // Fetch jadwal with aliases to match the view
        $jadwal = $db->table('pinjaman_angsuran')
                     ->select('id, pinjaman_id, bulan_ke as angsuran_ke, jatuh_tempo, pokok, jasa as bunga, (pokok + jasa) as total_angsuran, status, tanggal_bayar')
                     ->where('pinjaman_id', $id)
                     ->orderBy('bulan_ke', 'ASC')
                     ->get()->getResultArray();

        $data = [
            'pinjaman' => $pinjaman,
            'jadwal' => $jadwal,
            'pembayaran' => $pembayaranModel->where('pinjaman_id', $id)->orderBy('tanggal_bayar', 'DESC')->findAll(),
            'kas' => $kasModel->where('status', 'aktif')->get()->getResultArray(),
            'bank' => $bankModel->where('status', 'aktif')->get()->getResultArray()
        ];
        
        return view('admin/pinjaman/jadwal', $data);
    }
    
    public function bayarAngsuran() {
        if (!$this->validate([
            'nominal_bayar' => [
                'rules' => 'required|greater_than[0]',
                'errors' => ['greater_than' => 'Nominal pembayaran harus lebih dari Rp 0.']
            ],
            'denda_dibayar' => [
                'rules' => 'permit_empty|greater_than_equal_to[0]',
                'errors' => ['greater_than_equal_to' => 'Denda tidak boleh minus.']
            ]
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['nominal_bayar'] ?? $this->validator->getErrors()['denda_dibayar'] ?? 'Data tidak valid.');
        }

        $data = $this->request->getPost();
        $db = \Config\Database::connect();
        $pembayaranModel = new \App\Models\PinjamanPembayaranModel();
        $pinjamanModel = new \App\Models\PinjamanModel();
        
        // Fetch specific installment with aliases
        $jadwal = $db->table('pinjaman_angsuran')
                     ->select('id, pinjaman_id, bulan_ke as angsuran_ke, jatuh_tempo, pokok, jasa as bunga, (pokok + jasa) as total_angsuran, status')
                     ->where('id', $data['jadwal_id'])
                     ->get()->getRowArray();

        if (!$jadwal || $jadwal['status'] == 'Lunas') {
            return redirect()->back()->with('error', 'Jadwal tidak valid atau sudah lunas.');
        }
        
        // Simpan pembayaran
        $pembayaranModel->insert([
            'jadwal_angsuran_id' => $jadwal['id'],
            'pinjaman_id' => $jadwal['pinjaman_id'],
            'tanggal_bayar' => $data['tanggal_bayar'],
            'nominal_bayar' => $data['nominal_bayar'],
            'denda_dibayar' => $data['denda_dibayar'] ?? 0,
            'metode_pembayaran' => $data['metode_pembayaran'] ?? 'Tunai'
        ]);
        $pembayaranId = $pembayaranModel->insertID();
        
        $totalBayar = (float)$data['nominal_bayar'] + (float)($data['denda_dibayar'] ?? 0);
        
        // Integrasi Kas/Bank (Phase 9)
        $pinjaman = $pinjamanModel->find($jadwal['pinjaman_id']);
        $kodeKasBank = '1100'; // Default Kas
        if ($data['metode_pembayaran'] === 'Tunai' && !empty($data['kas_id'])) {
            $kasService = new \App\Services\KasService();
            $kasService->debit((int)$data['kas_id'], $totalBayar, 'pinjaman_pembayaran', $pembayaranId, 'Angsuran Pinjaman ID: ' . $pinjaman['id']);
            $kodeKasBank = '1100';
        } elseif ($data['metode_pembayaran'] === 'Transfer Bank' && !empty($data['bank_id'])) {
            $bankService = new \App\Services\BankService();
            $bankService->debit((int)$data['bank_id'], $totalBayar, 'pinjaman_pembayaran', $pembayaranId, 'Angsuran Pinjaman ID: ' . $pinjaman['id']);
            $kodeKasBank = '1110';
        }
        
        // JURNAL AKUNTANSI OTOMATIS (FASE 9)
        $akuntansiService = new \App\Services\AkuntansiService();
        $akunKasBank = $db->table('akun_coa')->where('kode_akun', $kodeKasBank)->get()->getRow();
        $akunPiutang = $db->table('akun_coa')->where('kode_akun', '1200')->get()->getRow(); // Piutang Anggota
        $akunPendapatanJasa = $db->table('akun_coa')->where('kode_akun', '4100')->get()->getRow(); // Pendapatan Jasa Pinjaman
        $akunPendapatanAdmin = $db->table('akun_coa')->where('kode_akun', '4300')->get()->getRow(); // Pendapatan Administrasi (Denda)
        
        if ($akunKasBank && $akunPiutang && $akunPendapatanJasa) {
            $jurnalDetail = [
                ['akun_id' => $akunKasBank->id, 'posisi' => 'debit', 'nominal' => $totalBayar],
                ['akun_id' => $akunPiutang->id, 'posisi' => 'kredit', 'nominal' => $jadwal['pokok']],
                ['akun_id' => $akunPendapatanJasa->id, 'posisi' => 'kredit', 'nominal' => $jadwal['bunga']]
            ];
            
            $denda = (float)($data['denda_dibayar'] ?? 0);
            if ($denda > 0 && $akunPendapatanAdmin) {
                $jurnalDetail[] = ['akun_id' => $akunPendapatanAdmin->id, 'posisi' => 'kredit', 'nominal' => $denda];
            }
            
            $akuntansiService->catatJurnal(
                $data['tanggal_bayar'],
                'Angsuran Ke-'.$jadwal['angsuran_ke'].' Pinjaman ID: ' . $pinjaman['id'],
                $jurnalDetail,
                'ANGS-' . $pembayaranId
            );
        }
        
        // Update status angsuran
        $db->table('pinjaman_angsuran')->where('id', $jadwal['id'])->update([
            'status' => 'Lunas',
            'tanggal_bayar' => $data['tanggal_bayar']
        ]);
        
        // Update status pinjaman jika semua sudah lunas
        $unpaidCount = $db->table('pinjaman_angsuran')
                          ->where('pinjaman_id', $jadwal['pinjaman_id'])
                          ->where('status', 'Belum Lunas')
                          ->countAllResults();
        $statusPinjaman = $unpaidCount <= 0 ? 'PAID' : 'ACTIVE';
        
        $pinjamanModel->update($pinjaman['id'], [
            'status_pengajuan' => $statusPinjaman
        ]);
        
        return redirect()->back()->with('message', 'Pembayaran angsuran berhasil disimpan.');
    }

    // --- RESTRUKTURISASI PINJAMAN (FASE 5) ---
    public function restrukturisasi()
    {
        $db = \Config\Database::connect();
        
        $pinjamanQuery = $db->table('pinjaman')
                            ->select('pinjaman.*, anggota.nama_lengkap, anggota.nip')
                            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
                            ->where('pinjaman.status_pengajuan', 'ACTIVE')
                            ->get()
                            ->getResultArray();
                            
        foreach ($pinjamanQuery as &$p) {
            $sisaPokok = $db->table('pinjaman_angsuran')
                             ->selectSum('pokok')
                             ->where('pinjaman_id', $p['id'])
                             ->where('status', 'Belum Lunas')
                             ->get()->getRow()->pokok ?? 0;
            $p['sisa_pokok'] = (float)$sisaPokok;
        }
                            
        $data = [
            'title' => 'Restrukturisasi Pinjaman',
            'list_pinjaman' => $pinjamanQuery
        ];
        
        return view('admin/pinjaman/restrukturisasi', $data);
    }

    public function prosesRestrukturisasi()
    {
        if (!$this->validate([
            'pinjaman_id' => 'required|numeric',
            'tenor_baru' => 'required|numeric|greater_than[0]',
            'bunga_baru' => 'required|numeric|greater_than_equal_to[0]',
            'alasan' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Semua data wajib diisi dengan benar.');
        }

        $data = $this->request->getPost();
        $pinjamanId = (int)$data['pinjaman_id'];
        $userId = (int)session()->get('user_id');

        $pinjamanService = new \App\Services\PinjamanService();
        $res = $pinjamanService->restrukturisasi($pinjamanId, $data, $userId);

        if (!$res['success']) {
            return redirect()->back()->withInput()->with('error', $res['message']);
        }

        return redirect()->to('/admin/pinjaman/restrukturisasi')->with('message', 'Pinjaman berhasil direstrukturisasi.');
    }
}