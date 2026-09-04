<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Simpanan extends BaseController {
    
    // --- MASTER JENIS SIMPANAN ---
    public function jenis() {
        $model = new \App\Models\JenisSimpananModel();
        $data = ['jenis' => $model->findAll()];
        return view('admin/simpanan/jenis', $data);
    }

    public function simpanJenis() {
        $model = new \App\Models\JenisSimpananModel();
        $data = $this->request->getPost();
        
        if (!empty($data['id'])) {
            $model->update($data['id'], $data);
            $msg = 'Jenis simpanan diperbarui.';
        } else {
            $model->insert($data);
            $msg = 'Jenis simpanan ditambahkan.';
        }
        return redirect()->to('/admin/simpanan/jenis')->with('message', $msg);
    }

    public function hapusJenis($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $model = new \App\Models\JenisSimpananModel();
        $model->update($id, ['status' => 'nonaktif']); // Soft delete by status
        return $this->response->setJSON(['status' => 'success']);
    }

    // --- TRANSAKSI SIMPANAN (SETOR / TARIK) ---
    public function transaksi()
    {
        $anggotaModel = new \App\Models\AnggotaModel();
        $transaksiModel = new \App\Models\SimpananTransaksiModel();
        $jenisModel = new \App\Models\JenisSimpananModel();
        
        $data = [
            'title' => 'Transaksi Simpanan',
            'anggota' => $anggotaModel->findAll(),
            'jenis_simpanan' => $jenisModel->findAll(),
            'kas' => [],
            'bank' => [],
            'histori' => $transaksiModel->select('simpanan_transaksi.*, anggota.nama_lengkap, anggota.nip, jenis_simpanan.nama as nama_simpanan')
                                         ->join('anggota', 'anggota.id = simpanan_transaksi.anggota_id')
                                         ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan_transaksi.jenis_simpanan_id')
                                         ->orderBy('simpanan_transaksi.tanggal', 'DESC')
                                         ->findAll(100) // limit 100
        ];
        return view('admin/simpanan/transaksi', $data);
    }
    
    public function mutasi()
    {
        $awal = $this->request->getVar('tgl_awal') ?? date('Y-m-01');
        $akhir = $this->request->getVar('tgl_akhir') ?? date('Y-m-t');
        
        $transaksiModel = new \App\Models\SimpananTransaksiModel();
        $transaksi = $transaksiModel->select('simpanan_transaksi.*, a.nama_lengkap, a.nomor_anggota, js.nama as nama_simpanan')
                      ->join('anggota a', 'a.id = simpanan_transaksi.anggota_id')
                      ->join('jenis_simpanan js', 'js.id = simpanan_transaksi.jenis_simpanan_id', 'left')
                      ->where('simpanan_transaksi.tanggal >=', $awal)
                      ->where('simpanan_transaksi.tanggal <=', $akhir)
                      ->where('simpanan_transaksi.status', 'POSTED')
                      ->orderBy('simpanan_transaksi.tanggal', 'DESC')
                      ->findAll();

        $totSetor = 0;
        $totTarik = 0;
        $db = \Config\Database::connect();
        $totals = $db->table('simpanan_transaksi')
                      ->select('jenis_transaksi, SUM(nominal) as total')
                      ->where('tanggal >=', $awal)
                      ->where('tanggal <=', $akhir)
                      ->where('status', 'POSTED')
                      ->groupBy('jenis_transaksi')
                      ->get()->getResultArray();
                      
        foreach ($totals as $t) {
            if (strtolower($t['jenis_transaksi']) == 'setoran') {
                $totSetor = $t['total'];
            } else {
                $totTarik += $t['total']; // 'tarik' or 'penarikan'
            }
        }

        $data = [
            'awal' => $awal, 
            'akhir' => $akhir,
            'title' => 'Mutasi Simpanan',
            'data' => $transaksi,
            'totSetor' => $totSetor,
            'totTarik' => $totTarik
        ];
        
        return view('admin/simpanan/mutasi', $data);
    }
    
    public function buku()
    {
        $anggotaModel = new \App\Models\AnggotaModel();
        $transaksiModel = new \App\Models\SimpananTransaksiModel();
        
        $anggota_id_raw = $this->request->getGet('anggota_id');
        $anggota_id = idhash_decode($anggota_id_raw);
        if ($anggota_id === null && is_numeric($anggota_id_raw)) {
            $anggota_id = (int)$anggota_id_raw;
        }
        
        $transaksi = [];
        $anggota = null;
        
        if ($anggota_id) {
            $anggota = $anggotaModel->find($anggota_id);
            $transaksi = $transaksiModel->where('anggota_id', $anggota_id)
                                        ->orderBy('tanggal', 'ASC')
                                        ->findAll();
        }
        
        $data = [
            'title' => 'Buku Simpanan (Passbook)',
            'list_anggota' => $anggotaModel->findAll(),
            'anggota' => $anggota,
            'transaksi' => $transaksi
        ];
        
        return view('admin/simpanan/buku', $data);
    }

    public function cetakBuku()
    {
        $anggotaModel = new \App\Models\AnggotaModel();
        $transaksiModel = new \App\Models\SimpananTransaksiModel();
        
        $anggota_id_raw = $this->request->getGet('anggota_id');
        $anggota_id = idhash_decode($anggota_id_raw);
        if ($anggota_id === null && is_numeric($anggota_id_raw)) {
            $anggota_id = (int)$anggota_id_raw;
        }
        
        if (!$anggota_id) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan.');
        }
        
        $anggota = $anggotaModel->find($anggota_id);
        if (!$anggota) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan.');
        }
        
        $transaksi = $transaksiModel->where('anggota_id', $anggota_id)
                                    ->orderBy('tanggal', 'ASC')
                                    ->findAll();
                                    
        // Create FPDF PDF in Landscape A5 size
        // Width is 210mm, Height is 148mm
        $pdf = new \FPDF('L', 'mm', 'A5');
        
        // Disable automatic page breaks so we can tightly control passbook line layout
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();
        
        // Kopkar Assyifa Logo / Header
        $pdf->SetFont('Courier', 'B', 12);
        $pdf->Cell(0, 7, 'KOPERASI KARYAWAN ASSYIFA', 0, 1, 'C');
        $pdf->SetFont('Courier', 'I', 9);
        $pdf->Cell(0, 5, 'BUKU SIMPANAN ANGGOTA (PASSBOOK)', 0, 1, 'C');
        
        // Spacer line
        $pdf->Ln(2);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(4);
        
        // Member Details
        $pdf->SetFont('Courier', '', 9);
        $pdf->Cell(30, 4, 'No. Anggota', 0, 0);
        $pdf->Cell(5, 4, ':', 0, 0);
        $pdf->Cell(65, 4, $anggota['nip'], 0, 0);
        
        $pdf->Cell(30, 4, 'Nama Anggota', 0, 0);
        $pdf->Cell(5, 4, ':', 0, 0);
        $pdf->Cell(0, 4, $anggota['nama_lengkap'], 0, 1);
        
        $pdf->Cell(30, 4, 'Status', 0, 0);
        $pdf->Cell(5, 4, ':', 0, 0);
        $pdf->Cell(65, 4, 'AKTIF', 0, 0);
        
        $pdf->Cell(30, 4, 'Tanggal Cetak', 0, 0);
        $pdf->Cell(5, 4, ':', 0, 0);
        $pdf->Cell(0, 4, date('d-m-Y H:i'), 0, 1);
        
        $pdf->Ln(4);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(3);
        
        // Table Header
        $pdf->SetFont('Courier', 'B', 8);
        $w = [22, 12, 40, 22, 22, 24, 24, 24]; // Sum is 190
        
        $pdf->Cell($w[0], 5, 'TANGGAL', 1, 0, 'C');
        $pdf->Cell($w[1], 5, 'SANDI', 1, 0, 'C');
        $pdf->Cell($w[2], 5, 'KETERANGAN', 1, 0, 'L');
        $pdf->Cell($w[3], 5, 'DEBIT', 1, 0, 'R');
        $pdf->Cell($w[4], 5, 'KREDIT', 1, 0, 'R');
        $pdf->Cell($w[5], 5, 'S. POKOK', 1, 0, 'R');
        $pdf->Cell($w[6], 5, 'S. WAJIB', 1, 0, 'R');
        $pdf->Cell($w[7], 5, 'S. SUKARELA', 1, 1, 'R');
        
        $pdf->SetFont('Courier', '', 8);
        
        $saldoPokok = 0;
        $saldoWajib = 0;
        $saldoSukarela = 0;
        
        if (empty($transaksi)) {
            $pdf->Cell(array_sum($w), 6, 'Belum ada riwayat transaksi.', 1, 1, 'C');
        } else {
            foreach ($transaksi as $t) {
                $isMasuk = ($t['jenis_transaksi'] == 'Setoran' || $t['jenis_transaksi'] == 'Masuk');
                
                if ($t['jenis_simpanan_id'] == 1) {
                    $saldoPokok += $isMasuk ? $t['nominal'] : -$t['nominal'];
                } elseif ($t['jenis_simpanan_id'] == 2) {
                    $saldoWajib += $isMasuk ? $t['nominal'] : -$t['nominal'];
                } else {
                    $saldoSukarela += $isMasuk ? $t['nominal'] : -$t['nominal'];
                }
                
                $tanggal = date('d-m-Y', strtotime($t['tanggal']));
                $sandi = ($isMasuk ? 'C' : 'D') . '-' . $t['jenis_simpanan_id'];
                $ket = substr($t['keterangan'], 0, 24);
                
                $debit = !$isMasuk ? number_format($t['nominal'], 0, ',', '.') : '-';
                $kredit = $isMasuk ? number_format($t['nominal'], 0, ',', '.') : '-';
                
                $pdf->Cell($w[0], 5, $tanggal, 1, 0, 'C');
                $pdf->Cell($w[1], 5, $sandi, 1, 0, 'C');
                $pdf->Cell($w[2], 5, $ket, 1, 0, 'L');
                $pdf->Cell($w[3], 5, $debit, 1, 0, 'R');
                $pdf->Cell($w[4], 5, $kredit, 1, 0, 'R');
                $pdf->Cell($w[5], 5, number_format($saldoPokok, 0, ',', '.'), 1, 0, 'R');
                $pdf->Cell($w[6], 5, number_format($saldoWajib, 0, ',', '.'), 1, 0, 'R');
                $pdf->Cell($w[7], 5, number_format($saldoSukarela, 0, ',', '.'), 1, 1, 'R');
            }
        }
        
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output('I', 'Buku_Simpanan_' . $anggota['nip'] . '.pdf');
        exit;
    }


    public function prosesTransaksi() {
        if (!$this->validate([
            'nominal' => [
                'rules' => 'required|greater_than[0]',
                'errors' => ['greater_than' => 'Nominal transaksi harus lebih dari Rp 0']
            ],
            'anggota_id' => 'required',
            'jenis_simpanan_id' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['nominal'] ?? 'Data tidak lengkap atau tidak valid.');
        }

        $service = new \App\Services\SimpananService();
        $data = $this->request->getPost();
        
        if ($data['jenis_transaksi'] == 'setoran') {
            $res = $service->setor($data);
        } else {
            $res = $service->tarik($data);
        }

        if ($res['success']) {
            return redirect()->to('/admin/simpanan/transaksi')->with('message', $res['message']);
        } else {
            return redirect()->to('/admin/simpanan/transaksi')->with('error', $res['message']);
        }
    }

    public function koreksiSimpanan()
    {
        $anggota_id = $this->request->getPost('anggota_id');
        $jenis = $this->request->getPost('jenis_simpanan');
        $nominal = $this->request->getPost('nominal');
        $keterangan = $this->request->getPost('keterangan');
        $tipe = $this->request->getPost('tipe'); // 'Tambah' or 'Kurang'
        
        try {
            $simpananService = new \App\Services\SimpananService();
            $simpananService->koreksiLegacy($anggota_id, $jenis, $nominal, $keterangan, $tipe);
            return redirect()->back()->with('message', 'Koreksi simpanan berhasil.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function transferSimpanan()
    {
        $anggota_id = $this->request->getPost('anggota_id');
        $dari = $this->request->getPost('dari_simpanan');
        $ke = $this->request->getPost('ke_simpanan');
        $nominal = $this->request->getPost('nominal');
        
        try {
            $simpananService = new \App\Services\SimpananService();
            $simpananService->transferLegacy($anggota_id, $dari, $ke, $nominal);
            return redirect()->back()->with('message', 'Transfer simpanan berhasil.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // --- MUTASI & CETAK BUKTI ---
    public function cetak($id) {
        $id = idhash_decode($id);
        if (!$id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $transaksiModel = new \App\Models\SimpananTransaksiModel();
        $trx = $transaksiModel->select('simpanan_transaksi.*, anggota.nama_lengkap, anggota.nomor_anggota, jenis_simpanan.nama as nama_simpanan')
                ->join('anggota', 'anggota.id = simpanan_transaksi.anggota_id')
                ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan_transaksi.jenis_simpanan_id')
                ->find($id);
                
        if (!$trx) return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');
        
        return view('admin/simpanan/cetak_struk', ['trx' => $trx]);
    }

    public function datatablesTransaksi()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('simpanan_transaksi')
            ->select('simpanan_transaksi.id, simpanan_transaksi.tanggal, simpanan_transaksi.nomor_transaksi, anggota.nama_lengkap, anggota.nip, jenis_simpanan.nama as nama_simpanan, simpanan_transaksi.jenis_transaksi, simpanan_transaksi.nominal, simpanan_transaksi.keterangan')
            ->join('anggota', 'anggota.id = simpanan_transaksi.anggota_id')
            ->join('jenis_simpanan', 'jenis_simpanan.id = simpanan_transaksi.jenis_simpanan_id');

        return \Hermawan\DataTables\DataTable::of($builder)
            ->format('tanggal', function($value){
                return date('d/m/Y', strtotime($value));
            })
            ->add('jenis', function($row){
                if ($row->jenis_transaksi == 'setoran' || $row->jenis_transaksi == 'Setoran') {
                    $html = '<span style="background:#d1fae5; color:#065f46; padding:2px 6px; border-radius:4px; font-size:0.75rem;">SETORAN</span><br>';
                } else {
                    $html = '<span style="background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px; font-size:0.75rem;">TARIK</span><br>';
                }
                return $html . '<small>' . ($row->nama_simpanan ?? '') . '</small>';
            })
            ->format('nominal', function($value){
                return 'Rp ' . number_format($value, 0, ',', '.');
            })
            ->add('aksi', function($row){
                return '<a href="'.base_url('admin/simpanan/cetak/'.idhash_encode($row->id)).'" class="btn-action" style="background:#0ea5e9; color:white; padding:4px 8px; font-size:0.8rem; text-decoration:none;" target="_blank"><i class="fas fa-print"></i> Cetak</a>';
            })
            ->toJson(true);
    }

    public function datatablesMutasi()
    {
        $awal = $this->request->getPost('tgl_awal') ?? date('Y-m-01');
        $akhir = $this->request->getPost('tgl_akhir') ?? date('Y-m-t');

        $db = \Config\Database::connect();
        $builder = $db->table('simpanan_transaksi')
            ->select('simpanan_transaksi.id, simpanan_transaksi.tanggal, a.nomor_anggota, a.nama_lengkap, js.nama as nama_simpanan, IF(simpanan_transaksi.jenis_transaksi="Setoran" OR simpanan_transaksi.jenis_transaksi="setoran", simpanan_transaksi.nominal, 0) as setor, IF(simpanan_transaksi.jenis_transaksi="Tarik" OR simpanan_transaksi.jenis_transaksi="tarik", simpanan_transaksi.nominal, 0) as tarik, simpanan_transaksi.keterangan')
            ->join('anggota a', 'a.id = simpanan_transaksi.anggota_id')
            ->join('jenis_simpanan js', 'js.id = simpanan_transaksi.jenis_simpanan_id', 'left')
            ->where('simpanan_transaksi.tanggal >=', $awal)
            ->where('simpanan_transaksi.tanggal <=', $akhir)
            ->where('simpanan_transaksi.status', 'POSTED');

        return \Hermawan\DataTables\DataTable::of($builder)
            ->format('tanggal', function($value){
                return date('d/m/Y', strtotime($value));
            })
            ->add('anggota', function($row){
                return '<span class="fw-bold">'.($row->nama_lengkap ?? '').'</span><br><small class="text-muted">'.($row->nomor_anggota ?? '').'</small>';
            })
            ->format('setor', function($value){
                return 'Rp ' . number_format($value, 0, ',', '.');
            })
            ->format('tarik', function($value){
                return 'Rp ' . number_format($value, 0, ',', '.');
            })
            ->toJson(true);
    }
}
