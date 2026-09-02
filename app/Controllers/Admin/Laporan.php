<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Laporan extends BaseController
{
    public function index()
    {
        return redirect()->to(base_url('admin/laporan/anggota'));
    }

    public function generate()
    {
        $jenis = $this->request->getPost('jenis_laporan');
        $awal = $this->request->getPost('tgl_awal');
        $akhir = $this->request->getPost('tgl_akhir');
        $action = $this->request->getPost('format') == 'csv' ? 'excel' : 'print';
        $coa_id = $this->request->getPost('coa_id');

        $params = http_build_query([
            'tgl_awal' => $awal,
            'tgl_akhir' => $akhir,
            'action' => $action,
            'coa_id' => $coa_id
        ]);

        if ($jenis == 'slow_moving') {
            return redirect()->to(base_url("admin/laporan/slow_moving?hari=60&action=$action"));
        }

        return redirect()->to(base_url("admin/laporan/$jenis?$params"));
    }

    private function exportCsv($filename, $headers, $dataArray)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($dataArray as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function anggota()
    {
        $status = $this->request->getVar('status') ?? 'Semua';
        $awal = $this->request->getVar('tgl_awal') ?? date('Y-01-01');
        $akhir = $this->request->getVar('tgl_akhir') ?? date('Y-m-t');
        
        $anggotaModel = new \App\Models\AnggotaModel();
        $builder = $anggotaModel->builder();
        $builder->where('tanggal_masuk >=', $awal);
        $builder->where('tanggal_masuk <=', $akhir);
        if ($status != 'Semua') {
            $builder->where('status', $status);
        }
        $dataAnggota = $builder->get()->getResultArray();
        
        $action = $this->request->getVar('action');
        if ($action == 'excel') {
            $csvData = [];
            foreach ($dataAnggota as $a) {
                $csvData[] = [
                    $a['nomor_anggota'], $a['nama_lengkap'], $a['nik'], 
                    $a['jenis_kelamin'], $a['no_telepon'] ?? '', $a['tanggal_masuk'], $a['status']
                ];
            }
            return $this->exportCsv("Laporan_Anggota_$awal-$akhir.csv", 
                ['No Anggota', 'Nama', 'NIK', 'Jenis Kelamin', 'Telepon', 'Tgl Gabung', 'Status'], $csvData);
        }

        $data = [
            'awal' => $awal, 'akhir' => $akhir, 'status' => $status,
            'judul' => 'Laporan Pertumbuhan Anggota',
            'data' => $dataAnggota,
            'layout' => ($action == 'print') ? 'layout/print_laporan' : 'admin/layout/main',
            'is_print' => ($action == 'print')
        ];
        
        return view('admin/laporan/anggota', $data);
    }

    public function simpanan()
    {
        $awal = $this->request->getVar('tgl_awal') ?? date('Y-m-01');
        $akhir = $this->request->getVar('tgl_akhir') ?? date('Y-m-t');
        
        $laporanModel = new \App\Models\LaporanModel();
        // Mutasi Simpanan
        $transaksi = $laporanModel->getMutasiSimpanan($awal, $akhir);

        $action = $this->request->getVar('action');
        if ($action == 'excel') {
            $csvData = [];
            foreach ($transaksi as $t) {
                $csvData[] = [
                    $t['tanggal'], $t['nomor_anggota'], $t['nama_lengkap'], 
                    $t['jenis_simpanan'], $t['kredit'], $t['debit'], $t['keterangan']
                ];
            }
            return $this->exportCsv("Laporan_Mutasi_Simpanan_$awal-$akhir.csv", 
                ['Tanggal', 'No Anggota', 'Nama', 'Jenis', 'Pemasukan (Kredit)', 'Penarikan (Debit)', 'Keterangan'], $csvData);
        }

        $data = [
            'awal' => $awal, 'akhir' => $akhir,
            'judul' => 'Laporan Mutasi Simpanan',
            'periode' => "$awal s/d $akhir",
            'data' => $transaksi,
            'layout' => ($action == 'print') ? 'layout/print_laporan' : 'admin/layout/main',
            'is_print' => ($action == 'print')
        ];
        
        return view('admin/laporan/simpanan', $data);
    }

    public function rat()
    {
        $tahun = $this->request->getVar('tahun') ?? date('Y');
        
        $action = $this->request->getVar('action');
        if ($action == 'print') {
            return view('admin/laporan/cetak_rat', ['tahun' => $tahun]);
        }

        $data = [
            'tahun' => $tahun,
            'judul' => 'Draf Laporan RAT Tahun ' . $tahun,
        ];
        
        return view('admin/laporan/rat', $data);
    }

    public function pinjaman()
    {
        $status = $this->request->getVar('status') ?? 'ACTIVE';
        $laporanModel = new \App\Models\LaporanModel();
        $pinjaman = $laporanModel->getPinjamanBeredar($status);

        $action = $this->request->getVar('action');
        if ($action == 'excel') {
            $csvData = [];
            foreach ($pinjaman as $p) {
                $csvData[] = [
                    $p['nomor_pinjaman'], $p['nomor_anggota'], $p['nama_lengkap'], 
                    $p['tanggal_pengajuan'], $p['jumlah_pinjaman'], $p['total_dibayar'], $p['sisa_pinjaman'], $p['status']
                ];
            }
            return $this->exportCsv("Laporan_Pinjaman_$status.csv", 
                ['No Pinjaman', 'No Anggota', 'Nama', 'Tgl Pengajuan', 'Plafon', 'Terbayar', 'Sisa (Outstanding)', 'Status'], $csvData);
        }

        $data = [
            'status' => $status,
            'judul' => 'Laporan Pinjaman & Kolektibilitas',
            'data' => $pinjaman,
            'layout' => ($action == 'print') ? 'layout/print_laporan' : 'admin/layout/main',
            'is_print' => ($action == 'print')
        ];
        
        return view('admin/laporan/pinjaman', $data);
    }

    public function waserda()
    {
        $awal = $this->request->getVar('tgl_awal') ?? date('Y-m-01');
        $akhir = $this->request->getVar('tgl_akhir') ?? date('Y-m-t');
        
        $laporanModel = new \App\Models\LaporanModel();
        $penjualan = $laporanModel->getPenjualanWaserda($awal, $akhir);

        $action = $this->request->getVar('action');
        if ($action == 'excel') {
            $csvData = [];
            foreach ($penjualan as $p) {
                $margin = $p['total_omset'] - $p['total_hpp'];
                $csvData[] = [
                    $p['sku'], $p['nama_produk'], $p['total_qty'], $p['total_omset'], $p['total_hpp'], $margin
                ];
            }
            return $this->exportCsv("Laporan_Waserda_$awal-$akhir.csv", 
                ['SKU', 'Nama Produk', 'Total Terjual', 'Omset', 'HPP', 'Margin (Laba Kotor)'], $csvData);
        }

        $data = [
            'awal' => $awal, 'akhir' => $akhir,
            'periode' => "$awal s/d $akhir",
            'judul' => 'Laporan Penjualan & Margin Waserda',
            'data' => $penjualan,
            'layout' => ($action == 'print') ? 'layout/print_laporan' : 'admin/layout/main',
            'is_print' => ($action == 'print')
        ];
        
        return view('admin/laporan/waserda', $data);
    }

    public function inventory()
    {
        $laporanModel = new \App\Models\LaporanModel();
        $inventory = $laporanModel->getStokWaserda();

        $action = $this->request->getVar('action');
        if ($action == 'excel') {
            $csvData = [];
            foreach ($inventory as $i) {
                $status = ($i['stok'] <= $i['stok_minimum']) ? 'KRITIS' : 'AMAN';
                $csvData[] = [
                    $i['sku'], $i['nama_produk'], $i['stok'], $i['stok_minimum'], $i['harga_beli'], $i['harga_jual'], $status
                ];
            }
            return $this->exportCsv("Laporan_Inventory_Stok.csv", 
                ['SKU', 'Produk', 'Sisa Stok', 'Stok Minimum', 'HPP', 'Harga Jual', 'Status'], $csvData);
        }

        $data = [
            'judul' => 'Laporan Stok & Inventory',
            'data' => $inventory,
            'layout' => ($action == 'print') ? 'layout/print_laporan' : 'admin/layout/main',
            'is_print' => ($action == 'print')
        ];
        
        return view('admin/laporan/inventory', $data);
    }
    public function bulanan() {
        return view('admin/laporan/bulanan', [
            'judul' => 'Laporan Bulanan', 'awal' => date('Y-m-01'), 'akhir' => date('Y-m-t'), 'layout' => 'admin/layout/main', 'is_print' => false, 'data' => [],
            'summary' => [
                'total_anggota' => 0,
                'anggota_baru' => 0,
                'saldo_kas' => 0,
                'penjualan_waserda' => 0,
                'total_simpanan' => 0,
                'piutang_pinjaman' => 0,
                'pendapatan' => 0, 
                'beban' => 0, 
                'laba' => 0
            ],
            'transaksiTerakhir' => [],
            'kolektibilitas' => ['lancar' => 0, 'macet' => 0]
        ]);
    }

    public function tunggakanPinjaman() {
        return view('admin/laporan/tunggakan', ['judul' => 'Laporan Tunggakan Pinjaman', 'akhir' => date('Y-m-t'), 'layout' => 'admin/layout/main', 'is_print' => false, 'data' => []]);
    }

    public function penjualanHarian() {
        return view('admin/laporan/penjualan_harian', ['judul' => 'Laporan Penjualan Harian', 'awal' => date('Y-m-01'), 'akhir' => date('Y-m-t'), 'layout' => 'admin/layout/main', 'is_print' => false, 'data' => [], 'bulan' => date('Y-m')]);
    }

    public function produkTerlaris() {
        return view('admin/laporan/produk_terlaris', ['judul' => 'Laporan Produk Terlaris', 'awal' => date('Y-m-01'), 'akhir' => date('Y-m-t'), 'layout' => 'admin/layout/main', 'is_print' => false, 'data' => [], 'bulan' => date('Y-m')]);
    }

    public function slowMoving() {
        $hari = $this->request->getVar('hari') ?? 60;
        $tglBatas = date('Y-m-d', strtotime("-$hari days"));
        
        $laporanModel = new \App\Models\LaporanModel();
        $dataLambat = $laporanModel->getSlowMoving($tglBatas);

        $action = $this->request->getVar('action');
        if ($action == 'excel') {
            $csvData = [];
            foreach ($dataLambat as $d) {
                $nilaiMati = $d['stok'] * $d['harga_beli'];
                $csvData[] = [
                    $d['sku'], $d['nama_produk'], $d['stok'], $d['harga_beli'], $nilaiMati, $d['tanggal_kadaluarsa']
                ];
            }
            return $this->exportCsv("Laporan_Barang_Mati_{$hari}_hari.csv", 
                ['SKU', 'Nama Produk', 'Sisa Stok', 'HPP', 'Total Nilai (Rp)', 'Kedaluwarsa'], $csvData);
        }

        $data = [
            'hari' => $hari,
            'judul' => "Laporan Barang Mati (Slow-Moving) - $hari Hari Terakhir",
            'data' => $dataLambat,
            'layout' => ($action == 'print') ? 'layout/print_laporan' : 'admin/layout/main',
            'is_print' => ($action == 'print')
        ];
        
        return view('admin/laporan/slow_moving', $data);
    }

    public function arusKas() {
        return view('admin/laporan/arus_kas', ['judul' => 'Laporan Arus Kas', 'awal' => date('Y-m-01'), 'akhir' => date('Y-m-t'), 'layout' => 'admin/layout/main', 'is_print' => false, 'data' => [], 'totalMasuk' => 0, 'totalKeluar' => 0, 'saldoAwal' => 0, 'saldoAkhir' => 0, 'netCashFlow' => 0, 'dataMasuk' => [], 'dataKeluar' => []]);
    }
}
