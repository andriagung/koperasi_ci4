<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\AnalitikModel;
use App\Models\AnggotaModel;
use App\Models\SimpananModel;

class Analitik extends BaseController
{
    public function index()
    {
        $analitikModel = new AnalitikModel();
        
        // 1. Data Pertumbuhan Anggota (12 Bulan)
        $bulanIni = (int)date('m');
        $tahunIni = (int)date('Y');
        $labelsBulan = [];
        $dataAnggotaBaru = [];
        $dataTotalSimpanan = [];
        $dataTotalPinjaman = [];
        $dataPemasukanWaserda = [];

        $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        for($i = 11; $i >= 0; $i--) {
            $idxBulan = $bulanIni - $i;
            $idxTahun = $tahunIni;
            if($idxBulan <= 0) {
                $idxBulan += 12;
                $idxTahun -= 1;
            }
            $labelsBulan[] = $namaBulan[$idxBulan] . " '" . substr($idxTahun, 2);
            $monthStr = str_pad($idxBulan, 2, "0", STR_PAD_LEFT);
            $dateLike = "$idxTahun-$monthStr-%";
            
            // Tren Anggota Baru
            $dataAnggotaBaru[] = $analitikModel->getTrenAnggotaBaru($dateLike);
            
            // Tren Simpanan Sukarela Masuk (Bulan tersebut)
            $dataTotalSimpanan[] = $analitikModel->getTrenSimpananMasuk($dateLike);
            
            // Tren Pencairan Pinjaman (Bulan tersebut)
            $dataTotalPinjaman[] = $analitikModel->getTrenPinjaman($dateLike);
            
            // Tren Waserda (Penjualan)
            $dataPemasukanWaserda[] = $analitikModel->getTrenWaserda($dateLike);
        }

        // 2. Data Top 5 Anggota Penabung Terbesar
        $topPenabung = $analitikModel->getTopPenabung(5);

        // 3. Deteksi Anomali (Contoh: Penarikan simpanan > 5 juta dalam sebulan terakhir)
        $anomaliPenarikan = $analitikModel->getAnomaliPenarikan(5000000, 30);

        $data = [
            'labelsBulan' => $labelsBulan,
            'dataAnggotaBaru' => $dataAnggotaBaru,
            'dataTotalSimpanan' => $dataTotalSimpanan,
            'dataTotalPinjaman' => $dataTotalPinjaman,
            'dataPemasukanWaserda' => $dataPemasukanWaserda,
            'topPenabung' => $topPenabung,
            'anomaliPenarikan' => $anomaliPenarikan,
        ];

        return view('admin/analitik', $data);
    }

    // Algoritma Sederhana Credit Scoring
    public function creditScoring($anggota_id)
    {
        $anggota_id = idhash_decode($anggota_id);
        if (!$anggota_id) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $anggotaModel = new AnggotaModel();
        $anggota = $anggotaModel->find($anggota_id);
        
        if (!$anggota) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anggota tidak ditemukan']);
        }
        
        $score = 0;
        $details = [];
        
        // 1. Lama Keanggotaan (Max 20 poin)
        // > 3 tahun = 20, > 1 tahun = 10, < 1 tahun = 5
        $tglGabung = strtotime($anggota['created_at']);
        $umurBulan = (time() - $tglGabung) / (30 * 24 * 60 * 60);
        if ($umurBulan >= 36) { $s1 = 20; }
        elseif ($umurBulan >= 12) { $s1 = 10; }
        else { $s1 = 5; }
        $score += $s1;
        $details['Lama Keanggotaan'] = $s1 . ' / 20';
        
        // 2. Total Saldo Simpanan (Max 30 poin)
        // > 10jt = 30, > 5jt = 20, > 1jt = 10, < 1jt = 5
        $simpananModel = new SimpananModel();
        $totalSimpanan = $simpananModel->where('anggota_id', $anggota_id)->selectSum('saldo')->first()['saldo'] ?? 0;
        if ($totalSimpanan >= 10000000) { $s2 = 30; }
        elseif ($totalSimpanan >= 5000000) { $s2 = 20; }
        elseif ($totalSimpanan >= 1000000) { $s2 = 10; }
        else { $s2 = 5; }
        $score += $s2;
        $details['Saldo Simpanan'] = $s2 . ' / 30';
        
        // 3. Riwayat Pinjaman (Max 30 poin)
        // Lunas tanpa tunggakan = 30, Sedang berjalan lancar = 20, Pernah nunggak = 5, Belum pernah pinjam = 15
        $analitikModel = new AnalitikModel();
        $riwayatPinjam = $analitikModel->getRiwayatPinjaman($anggota_id);
        $s3 = 15; // default belum pinjam
        if (!empty($riwayatPinjam)) {
            $adaLunas = false;
            $adaBerjalan = false;
            foreach($riwayatPinjam as $rp) {
                if ($rp['status_pengajuan'] === 'Lunas') $adaLunas = true;
                if ($rp['status_pengajuan'] === 'Berjalan') $adaBerjalan = true;
                // Simplified: asumsi tidak ada tracker telat di database, jika ada pinjaman berjalan lancar = 20
            }
            if ($adaLunas) $s3 = 30;
            elseif ($adaBerjalan) $s3 = 20;
        }
        $score += $s3;
        $details['Riwayat Pinjaman'] = $s3 . ' / 30';
        
        // 4. Status Kepegawaian (Max 20 poin)
        // Aktif = 20
        $s4 = ($anggota['status'] === 'Aktif') ? 20 : 0;
        $score += $s4;
        $details['Status Kepegawaian'] = $s4 . ' / 20';
        
        // Kesimpulan
        $rekomendasi = '';
        if ($score >= 80) $rekomendasi = 'Sangat Layak (Low Risk)';
        elseif ($score >= 60) $rekomendasi = 'Layak (Medium Risk)';
        else $rekomendasi = 'Beresiko (High Risk)';
        
        return $this->response->setJSON([
            'status' => 'success',
            'score' => $score,
            'details' => $details,
            'rekomendasi' => $rekomendasi
        ]);
    }
}
