<?php
namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    public function getPendapatanHariIni($tanggal)
    {
        return $this->db->table('penjualan')
            ->selectSum('total_bayar')
            ->where('tanggal', $tanggal)
            ->get()->getRow()->total_bayar ?? 0;
    }

    public function getJumlahTransaksiHariIni($tanggal)
    {
        return $this->db->table('penjualan')
            ->where('tanggal', $tanggal)
            ->countAllResults();
    }

    public function getItemTerjualHariIni($tanggal)
    {
        return $this->db->table('penjualan_detail pd')
            ->selectSum('pd.jumlah')
            ->join('penjualan p', 'p.id = pd.penjualan_id')
            ->where('p.tanggal', $tanggal)
            ->get()->getRow()->jumlah ?? 0;
    }

    public function getTotalItemGudang()
    {
        return $this->db->table('produk_waserda')->selectSum('stok')->get()->getRow()->stok ?? 0;
    }

    public function getStokKritisCount()
    {
        return $this->db->table('produk_waserda')->where('stok <= stok_minimum')->countAllResults();
    }

    public function getTotalKategori()
    {
        return $this->db->table('kategori_produk')->countAllResults();
    }

    public function getTopStokKritis($limit = 10)
    {
        return $this->db->table('produk_waserda pw')
            ->select('pw.nama_produk, kp.nama_kategori, pw.stok, pw.stok_minimum')
            ->join('kategori_produk kp', 'kp.id = pw.kategori_id', 'left')
            ->where('pw.stok <= pw.stok_minimum')
            ->orderBy('pw.stok', 'ASC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function getPiutangBerjalan()
    {
        return $this->db->table('pinjaman_angsuran')
            ->selectSum('pokok')
            ->where('status', 'Belum Lunas')
            ->get()->getRow()->pokok ?? 0;
    }

    public function getKomposisiSimpanan()
    {
        return $this->db->table('simpanan_saldo ss')
            ->select('js.nama, SUM(ss.saldo) as total')
            ->join('jenis_simpanan js', 'js.id = ss.jenis_simpanan_id')
            ->groupBy('ss.jenis_simpanan_id')
            ->get()->getResultArray();
    }
    
    public function getOmzetBulanan()
    {
        $labels = [];
        $dataPenjualan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('m', strtotime("-$i month"));
            $tahun = date('Y', strtotime("-$i month"));
            $labels[] = date('M Y', strtotime("-$i month"));
            
            $omzet = $this->db->table('penjualan')
                ->selectSum('total_bayar')
                ->where('MONTH(tanggal)', $bulan)
                ->where('YEAR(tanggal)', $tahun)
                ->get()->getRow()->total_bayar ?? 0;
                
            $dataPenjualan[] = $omzet;
        }
        return ['labels' => $labels, 'data' => $dataPenjualan];
    }

    public function getPenjualanHarian($days = 30)
    {
        $labels = [];
        $dataPenjualan = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $tgl = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d M', strtotime($tgl));
            
            $omzet = $this->db->table('penjualan')
                ->selectSum('total_bayar')
                ->where('tanggal', $tgl)
                ->get()->getRow()->total_bayar ?? 0;
                
            $dataPenjualan[] = $omzet;
        }
        return ['labels' => $labels, 'data' => $dataPenjualan];
    }
    
    public function getPinjamanPending()
    {
        return $this->db->table('pinjaman')
            ->select('pinjaman.*, anggota.nama_lengkap, anggota.nip')
            ->join('anggota', 'anggota.id = pinjaman.anggota_id')
            ->where('pinjaman.status_pengajuan', 'SUBMITTED')
            ->orderBy('pinjaman.created_at', 'ASC')
            ->limit(5)
            ->get()->getResultArray();
    }
    
    public function getStokMenipis()
    {
        return $this->db->table('produk_waserda')
            ->select('produk_waserda.*, kategori_produk.nama_kategori')
            ->join('kategori_produk', 'kategori_produk.id = produk_waserda.kategori_id', 'left')
            ->where('produk_waserda.stok <= produk_waserda.stok_minimum')
            ->orderBy('produk_waserda.stok', 'ASC')
            ->limit(5)
            ->get()->getResultArray();
    }
    
    public function getProdukKadaluarsa()
    {
        return $this->db->table('produk_waserda')
            ->select('produk_waserda.*, kategori_produk.nama_kategori')
            ->join('kategori_produk', 'kategori_produk.id = produk_waserda.kategori_id', 'left')
            ->where('tanggal_kadaluarsa IS NOT NULL')
            ->where('tanggal_kadaluarsa <=', date('Y-m-d', strtotime('+30 days')))
            ->orderBy('tanggal_kadaluarsa', 'ASC')
            ->limit(5)
            ->get()->getResultArray();
    }
    
    public function getTransaksiLive()
    {
        return $this->db->table('penjualan p')
            ->select('p.no_invoice as nomor_struk, p.total_bayar, p.metode_pembayaran, p.tanggal as created_at, u.nama_lengkap as kasir')
            ->join('users u', 'u.id = p.kasir_id')
            ->orderBy('p.tanggal', 'DESC')
            ->limit(5)
            ->get()->getResultArray();
    }
}
