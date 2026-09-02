<?php
namespace App\Controllers\Mobile;
use App\Controllers\BaseController;
use App\Models\AnggotaModel;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;
use App\Models\PinjamanAngsuranModel;

class Profil extends BaseController
{
    protected $anggotaModel;
    protected $simpananModel;
    protected $pinjamanModel;
    protected $angsuranModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
        $this->simpananModel = new SimpananModel();
        $this->pinjamanModel = new PinjamanModel();
        $this->angsuranModel = new PinjamanAngsuranModel();
    }

    public function index()
    {
        $session = session();
        $userId = $session->get('anggota_id');
        $anggota = $this->anggotaModel->find($userId);
        
        // Data global yang dibutuhkan layout/main.php
        $totalSimpanan = $this->simpananModel->where('anggota_id', $userId)->selectSum('saldo')->first()['saldo'] ?? 0;

        $pinjamanAktif = $this->pinjamanModel->where('anggota_id', $userId)->where('status_pengajuan', 'ACTIVE')->selectSum('nominal_pengajuan')->first()['nominal_pengajuan'] ?? 0;
        
        $data = [
            'isLoggedIn' => true,
            'totalSimpanan' => $totalSimpanan,
            'anggota' => $anggota,
            'pinjamanAktif' => $pinjamanAktif
        ];
        return view('mobile/profil', $data);
    }

    public function qrCode()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Anggota') {
            return $this->response->setStatusCode(401);
        }

        $userId = $session->get('anggota_id');
        $anggota = $this->anggotaModel->find($userId);

        if (!$anggota) {
            return $this->response->setStatusCode(404);
        }

        // Setup endroid qr-code
        $result = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->writerOptions([])
            ->data(json_encode([
                'id' => $anggota['id'],
                'nip' => $anggota['nip'],
                'nama' => $anggota['nama_lengkap']
            ]))
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->errorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(\Endroid\QrCode\RoundBlockSizeMode::Margin)
            ->build();

        $response = $this->response
            ->setContentType($result->getMimeType())
            ->setBody($result->getString());

        return $response;
    }

    public function verifyPin()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $pinInput = $this->request->getPost('pin');
        
        $anggota = $this->anggotaModel->find($session->get('id'));

        if (password_verify($pinInput, $anggota['pin'])) {
            // Beri token izin download yang berlaku sementara
            $session->set('pdf_download_token', true);
            return $this->response->setJSON(['status' => 'success', 'message' => 'PIN Valid. Dokumen siap diunduh.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'PIN yang Anda masukkan salah.']);
        }
    }

    public function downloadPdf()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !$session->get('pdf_download_token')) {
            return redirect()->to('/');
        }

        // Hapus token setelah digunakan agar aman (sekali unduh)
        $session->remove('pdf_download_token');

        $file = $this->request->getGet('file');
        $anggotaId = $session->get('id');

        if ($file === 'Mutasi_Buku_Tabungan.pdf') {
            // Gunakan DomPDF untuk Laporan Mutasi (berbasis HTML)
            $html = '
            <html>
            <head>
                <style>
                    body { font-family: sans-serif; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .header { text-align: center; margin-bottom: 30px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>KOPERASI KARYAWAN ASSYIFA RSUD 45</h2>
                    <p>Laporan Mutasi Buku Tabungan Anggota</p>
                </div>
                <p><strong>Nama Anggota:</strong> ' . esc($session->get('nama_lengkap')) . '</p>
                <p><strong>Tanggal Cetak:</strong> ' . date('d F Y') . '</p>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>01 Ags 2026</td>
                            <td>Setor Simpanan Wajib</td>
                            <td>+ Rp 50.000</td>
                            <td>Sukses</td>
                        </tr>
                        <tr>
                            <td>05 Ags 2026</td>
                            <td>Belanja Waserda (Kasbon)</td>
                            <td>- Rp 85.000</td>
                            <td>Sukses</td>
                        </tr>
                    </tbody>
                </table>
            </body>
            </html>';

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($file, ['Attachment' => 1]);
            exit;

        } else if ($file === 'E-Contract Pinjaman.pdf') {
            // Gunakan FPDF untuk Sertifikat Kontrak (berbasis koordinat X,Y)
            $pdf = new \FPDF();
            $pdf->AddPage();
            
            // Judul
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'E-CONTRACT PERJANJIAN PINJAMAN', 0, 1, 'C');
            $pdf->Ln(10);
            
            // Isi
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 10, "Pada hari ini, tanggal " . date('d F Y') . ", telah disepakati perjanjian pinjaman antara Koperasi Assyifa RSUD 45 dan anggota atas nama " . strtoupper($session->get('nama_lengkap')) . ".\n\nDemikian e-contract ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah.");
            
            $pdf->Ln(20);
            $pdf->Cell(0, 10, 'Disetujui secara digital oleh sistem.', 0, 1, 'R');

            $pdf->Output('D', $file);
            exit;

        } else if (strpos($file, 'Kuitansi_Angsuran_Bulan_') === 0) {
            // FPDF untuk Kuitansi Angsuran Lunas
            $pdf = new \FPDF();
            $pdf->AddPage('P', 'A5');
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'KUITANSI ANGSURAN PINJAMAN', 0, 1, 'C');
            $pdf->Ln(5);

            $pinjamanAktif = $this->pinjamanModel->where('anggota_id', $session->get('id'))->where('status_pengajuan', 'ACTIVE')->first();

            if ($pinjamanAktif) {
                preg_match('/Bulan_(\d+)/', $file, $matches);
                $bulanKe = $matches[1] ?? 0;
                $angsuran = $this->angsuranModel->where('pinjaman_id', $pinjamanAktif['id'])->where('bulan_ke', $bulanKe)->first();
                $firstAngsuran = $this->angsuranModel->where('pinjaman_id', $pinjamanAktif['id'])->orderBy('bulan_ke', 'ASC')->first();
                $lastAngsuran = $this->angsuranModel->where('pinjaman_id', $pinjamanAktif['id'])->orderBy('bulan_ke', 'DESC')->first();

                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 7, 'Telah diterima dari: ' . strtoupper($session->get('nama_lengkap')), 0, 1);
                $pdf->Cell(0, 7, 'Total Pinjaman: Rp ' . number_format($pinjamanAktif['nominal_pengajuan'], 0, ',', '.'), 0, 1);
                $pdf->Cell(0, 7, 'Periode: ' . date('M Y', strtotime($firstAngsuran['jatuh_tempo'])) . ' s/d ' . date('M Y', strtotime($lastAngsuran['jatuh_tempo'])), 0, 1);
                
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(0, 7, 'Detail Pembayaran (Angsuran Ke-' . $bulanKe . ')', 0, 1);
                $pdf->SetFont('Arial', '', 10);
                
                if ($angsuran) {
                    $pdf->Cell(40, 7, 'Pokok Angsuran', 0, 0); $pdf->Cell(0, 7, ': Rp ' . number_format($angsuran['pokok'], 0, ',', '.'), 0, 1);
                    $pdf->Cell(40, 7, 'Jasa / Bunga', 0, 0); $pdf->Cell(0, 7, ': Rp ' . number_format($angsuran['jasa'], 0, ',', '.'), 0, 1);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(40, 7, 'Total Dibayar', 0, 0); $pdf->Cell(0, 7, ': Rp ' . number_format($angsuran['pokok'] + $angsuran['jasa'], 0, ',', '.'), 0, 1);
                    
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetTextColor(22, 163, 74); // Hijau
                    $pdf->Cell(0, 7, 'Tanggal Bayar: ' . date('d F Y H:i', strtotime($angsuran['tanggal_bayar'])), 0, 1);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(0, 7, 'Status: LUNAS', 0, 1);
                }
            }
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(0, 10, 'Dicetak otomatis oleh Sistem Koperasi pada ' . date('d F Y'), 0, 1, 'C');
            
            $pdf->Output('D', $file);
            exit;
        } else if (strpos($file, 'Tagihan_Angsuran_Bulan_') === 0) {
            // FPDF untuk Tagihan Angsuran Belum Lunas
            $pdf = new \FPDF();
            $pdf->AddPage('P', 'A5');
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'TAGIHAN ANGSURAN PINJAMAN', 0, 1, 'C');
            $pdf->Ln(5);

            $pinjamanAktif = $this->pinjamanModel->where('anggota_id', $session->get('id'))->where('status_pengajuan', 'ACTIVE')->first();

            if ($pinjamanAktif) {
                preg_match('/Bulan_(\d+)/', $file, $matches);
                $bulanKe = $matches[1] ?? 0;
                $angsuran = $this->angsuranModel->where('pinjaman_id', $pinjamanAktif['id'])->where('bulan_ke', $bulanKe)->first();
                $firstAngsuran = $this->angsuranModel->where('pinjaman_id', $pinjamanAktif['id'])->orderBy('bulan_ke', 'ASC')->first();
                $lastAngsuran = $this->angsuranModel->where('pinjaman_id', $pinjamanAktif['id'])->orderBy('bulan_ke', 'DESC')->first();

                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 7, 'Nama Anggota: ' . strtoupper($session->get('nama_lengkap')), 0, 1);
                $pdf->Cell(0, 7, 'Total Pinjaman: Rp ' . number_format($pinjamanAktif['nominal_pengajuan'], 0, ',', '.'), 0, 1);
                $pdf->Cell(0, 7, 'Periode: ' . date('M Y', strtotime($firstAngsuran['jatuh_tempo'])) . ' s/d ' . date('M Y', strtotime($lastAngsuran['jatuh_tempo'])), 0, 1);
                
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(0, 7, 'Detail Tagihan (Angsuran Ke-' . $bulanKe . ')', 0, 1);
                $pdf->SetFont('Arial', '', 10);
                
                if ($angsuran) {
                    $pdf->Cell(40, 7, 'Pokok Angsuran', 0, 0); $pdf->Cell(0, 7, ': Rp ' . number_format($angsuran['pokok'], 0, ',', '.'), 0, 1);
                    $pdf->Cell(40, 7, 'Jasa / Bunga', 0, 0); $pdf->Cell(0, 7, ': Rp ' . number_format($angsuran['jasa'], 0, ',', '.'), 0, 1);
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Cell(40, 7, 'Total Tagihan', 0, 0); $pdf->Cell(0, 7, ': Rp ' . number_format($angsuran['pokok'] + $angsuran['jasa'], 0, ',', '.'), 0, 1);
                    
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetTextColor(220, 38, 38); // Merah
                    $pdf->Cell(0, 7, 'Jatuh Tempo: ' . date('d F Y', strtotime($angsuran['jatuh_tempo'])), 0, 1);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(0, 7, 'Status: BELUM LUNAS', 0, 1);
                }
            }
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(0, 10, 'Harap lakukan pembayaran sebelum tanggal jatuh tempo.', 0, 1, 'C');
            
            $pdf->Output('D', $file);
            exit;
        } else {
            // Fallback (LPJ dll)
            $pdf = new \FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'DOKUMEN RAHASIA: ' . $file, 0, 1, 'C');
            $pdf->Output('D', $file);
            exit;
        }
    }
}
