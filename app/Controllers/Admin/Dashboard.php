<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    use \App\Traits\DataTablesTrait;

    public function index()
    {
        $db = \Config\Database::connect();
        
        $anggotaModel = new \App\Models\AnggotaModel();
        
        // --- 1. Total Anggota Aktif ---
        $totalAnggotaAktif = $anggotaModel->where('status', 'Aktif')->countAllResults();
        
        // --- 2. Pendapatan Hari Ini ---
        $hariIni = date('Y-m-d');
        $pendapatanHariIni = $db->table('penjualan')
            ->selectSum('total_bayar')
            ->where('tanggal', $hariIni)
            ->get()->getRow()->total_bayar ?? 0;
            
        // --- 3. Piutang Anggota ---
        $piutangBerjalan = $db->table('pinjaman_angsuran')
            ->selectSum('pokok')
            ->where('status', 'Belum Lunas')
            ->get()->getRow()->pokok ?? 0;
            
        // --- 4. Stok Menipis (Cards) ---
        $stokKritis = $db->table('produk_waserda')
            ->where('stok <= stok_minimum')
            ->countAllResults();
            
        // --- 5. Chart Penjualan Bulanan (30 Hari Terakhir) ---
        $labelsPenjualan = [];
        $dataPenjualan = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $tgl = date('Y-m-d', strtotime("-$i days"));
            $labelsPenjualan[] = date('d M', strtotime($tgl));
            $omzet = $db->table('penjualan')
                ->selectSum('total_bayar')
                ->where('tanggal', $tgl)
                ->get()->getRow()->total_bayar ?? 0;
            $dataPenjualan[] = $omzet;
        }
        
        // --- 6. Chart Komposisi Simpanan ---
        $komposisiSimpanan = $db->table('simpanan_saldo ss')
            ->select('js.nama, SUM(ss.saldo) as total')
            ->join('jenis_simpanan js', 'js.id = ss.jenis_simpanan_id')
            ->groupBy('ss.jenis_simpanan_id')
            ->get()->getResultArray();
            
        $labelsSimpanan = [];
        $dataSimpanan = [];
        foreach ($komposisiSimpanan as $ks) {
            $labelsSimpanan[] = $ks['nama'];
            $dataSimpanan[] = $ks['total'];
        }

        // --- 7. Top 5 Peringatan Stok Kritis ---
        $topStokKritis = $db->table('produk_waserda pw')
            ->select('pw.nama_produk, kp.nama_kategori, pw.stok, pw.stok_minimum')
            ->join('kategori_produk kp', 'kp.id = pw.kategori_id', 'left')
            ->where('pw.stok <= pw.stok_minimum')
            ->orderBy('pw.stok', 'ASC')
            ->limit(5)
            ->get()->getResultArray();

        $data = [
            'totalAnggotaAktif' => $totalAnggotaAktif,
            'pendapatanHariIni' => $pendapatanHariIni,
            'piutangBerjalan' => $piutangBerjalan,
            'stokKritis' => $stokKritis,
            'topStokKritis' => $topStokKritis,
            'chartPenjualan' => [
                'labels' => $labelsPenjualan,
                'data' => $dataPenjualan
            ],
            'chartSimpanan' => [
                'labels' => $labelsSimpanan,
                'data' => $dataSimpanan
            ]
        ];
        
        return view('admin/dashboard', $data);
    }

    public function get_notif()
    {
        $db = \Config\Database::connect();
        $notifData = [];

        // Cek pinjaman pending
        $pinjamanPending = $db->table('pinjaman')
            ->select('pinjaman.id, anggota.nama_lengkap')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->where('status_pengajuan', 'Pending')
            ->get()->getResultArray();
            
        foreach ($pinjamanPending as $p) {
            $notifData[] = [
                'type' => 'pinjaman',
                'title' => 'Pengajuan Pinjaman Baru',
                'msg' => 'Anggota ' . esc($p['nama_lengkap']) . ' mengajukan pinjaman dan menunggu persetujuan.'
            ];
        }

        // Cek stok waserda menipis
        $stokMenipis = $db->table('produk_waserda')
            ->where('stok <= stok_minimum')
            ->get()->getResultArray();

        foreach ($stokMenipis as $s) {
            $notifData[] = [
                'type' => 'stok',
                'title' => 'Stok Menipis',
                'msg' => 'Stok produk ' . esc($s['nama_produk']) . ' sisa ' . $s['stok'] . '. Segera lakukan Restock.'
            ];
        }

        // Cek produk mendekati kadaluarsa (30 hari)
        $tglBatas = date('Y-m-d', strtotime('+30 days'));
        $produkKadaluarsa = $db->table('produk_waserda')
            ->where('tanggal_kadaluarsa IS NOT NULL')
            ->where('tanggal_kadaluarsa <=', $tglBatas)
            ->get()->getResultArray();
            
        foreach ($produkKadaluarsa as $pk) {
            $isExpired = $pk['tanggal_kadaluarsa'] < date('Y-m-d');
            $title = $isExpired ? 'Produk KADALUARSA!' : 'Produk Mendekati Kadaluarsa';
            $notifData[] = [
                'type' => 'stok',
                'title' => $title,
                'msg' => 'Produk ' . esc($pk['nama_produk']) . ' ' . ($isExpired ? 'telah kadaluarsa pada ' : 'akan kadaluarsa pada ') . date('d M Y', strtotime($pk['tanggal_kadaluarsa'])) . '.'
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $notifData]);
    }

    public function get_transaksi_live()
    {
        // Mengambil transaksi penjualan Waserda (Kasir) terakhir
        $db = \Config\Database::connect();
        $transaksi = $db->table('penjualan p')
            ->select('p.no_invoice, a.nama_lengkap as nama_pembeli, p.total_bayar, p.status_pembayaran, p.created_at')
            ->join('anggota a', 'a.id = p.anggota_id', 'left')
            ->orderBy('p.created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $html = "";
        foreach ($transaksi as $t) {
            $namaPembeli = $t["nama_pembeli"] ? esc($t["nama_pembeli"]) : "Umum (Non-Anggota)";
            $badgeColor = $t["status_pembayaran"] === "Lunas" ? "success" : "warning";
            
            $html .= "<div class=\"d-flex align-items-center mb-3 pb-2 border-bottom\">";
            $html .= "<div class=\"bg-primary bg-opacity-10 p-2 rounded me-3 text-primary\"><i class=\"fas fa-shopping-cart\"></i></div>";
            $html .= "<div class=\"flex-grow-1\">";
            $html .= "<h6 class=\"mb-0 text-sm\">".esc($t["no_invoice"])." - ".$namaPembeli."</h6>";
            $html .= "<small class=\"text-muted\">".date("d M Y H:i", strtotime($t["created_at"]))."</small>";
            $html .= "</div>";
            $html .= "<div class=\"text-end\">";
            $html .= "<div class=\"fw-bold text-dark mb-1\">Rp ".number_format($t["total_bayar"],0,",",".")."</div>";
            $html .= "<span class=\"badge bg-".$badgeColor."\">".esc($t["status_pembayaran"])."</span>";
            $html .= "</div>";
            $html .= "</div>";
        }
        
        if (empty($transaksi)) {
            $html = "<div class=\"text-center text-muted py-3\">Belum ada transaksi Waserda</div>";
        }

        return $this->response->setBody($html);
    }
}
