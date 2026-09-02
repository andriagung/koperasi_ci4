<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\TagihanPotonganModel;
use App\Models\AnggotaModel;
use App\Models\PinjamanAngsuranModel;
use App\Models\PengaturanModel;
use App\Models\SimpananModel;
use App\Models\PinjamanModel;
use App\Models\RiwayatTransaksiModel;

class Potongan extends BaseController {

    public function index() {
        $tagihanModel = new TagihanPotonganModel();
        
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        
        $tagihanQuery = $tagihanModel->select('tagihan_potongan.*, anggota.nama_lengkap as nama, anggota.nik, bendahara_gaji.nama_instansi')
                                ->join('anggota', 'anggota.id = tagihan_potongan.anggota_id')
                                ->join('bendahara_gaji', 'bendahara_gaji.id = anggota.bendahara_id', 'left')
                                ->where('periode', $periode);
                                
        $role = session()->get('role');
        $benId = null;
        if ($role == 'Bendahara') {
            $bendaharaModel = new \App\Models\BendaharaGajiModel();
            $ben = $bendaharaModel->where('user_id', session()->get('user_id'))->first();
            if ($ben) {
                $benId = $ben['id'];
            } else {
                $benId = -1;
            }
            $tagihanQuery->where('anggota.bendahara_id', $benId);
        }
        
        $tagihan = $tagihanQuery->findAll();

        $anggotaModel = new \App\Models\AnggotaModel();
        $pengaturanModel = new \App\Models\PengaturanModel();
        $angsuranModel = new \App\Models\PinjamanAngsuranModel();

        $settings = $pengaturanModel->getSettings();
        $simpananWajibDefault = isset($settings['simpanan_wajib']) ? $settings['simpanan_wajib'] : 50000;

        $anggotaQuery = $anggotaModel->where('status', 'Aktif');
        if ($role == 'Bendahara') {
            $anggotaQuery->where('bendahara_id', $benId);
        }
        $anggotaAktif = $anggotaQuery->findAll();
        $totalAnggotaAktif = count($anggotaAktif);
        
        $existingTagihan = $tagihanModel->select('tagihan_potongan.*, anggota.nama_lengkap as nama, anggota.nik')
                                        ->join('anggota', 'anggota.id = tagihan_potongan.anggota_id')
                                        ->where('periode', $periode)
                                        ->findAll();
        $totalExisting = count($existingTagihan);
        
        $existingAnggotaIds = array_column($existingTagihan, 'anggota_id');
        $lastDayOfMonth = date("Y-m-t", strtotime($periode . '-01'));
        
        $listAkanDigenerate = [];
        
        foreach ($anggotaAktif as $anggota) {
            if (!in_array($anggota['id'], $existingAnggotaIds)) {
                $angsurans = $angsuranModel->select('pinjaman_angsuran.*')
                                           ->join('pinjaman', 'pinjaman.id = pinjaman_angsuran.pinjaman_id')
                                           ->where('pinjaman.anggota_id', $anggota['id'])
                                           ->where('pinjaman_angsuran.status', 'Belum Lunas')
                                           ->where('pinjaman_angsuran.jatuh_tempo <=', $lastDayOfMonth)
                                           ->findAll();
                
                $nominalAngsuran = 0;
                foreach ($angsurans as $angs) {
                    $nominalAngsuran += ($angs['pokok'] + $angs['jasa']);
                }
                
                if (($simpananWajibDefault + $nominalAngsuran) > 0) {
                    $listAkanDigenerate[] = [
                        'nik' => $anggota['nik'],
                        'nama' => $anggota['nama_lengkap'],
                        'simpanan_wajib' => $simpananWajibDefault,
                        'angsuran' => $nominalAngsuran,
                        'total' => $simpananWajibDefault + $nominalAngsuran
                    ];
                }
            }
        }
        
        $akanDigenerate = count($listAkanDigenerate);

        $data = [
            'title'   => 'Potongan Gaji',
            'tagihan' => $tagihan,
            'periode' => $periode,
            'preview' => [
                'total_anggota' => $totalAnggotaAktif,
                'total_existing' => $totalExisting,
                'list_existing' => $existingTagihan,
                'akan_digenerate' => $akanDigenerate,
                'list_generate' => $listAkanDigenerate
            ]
        ];
        
        return view('admin/potongan/index', $data);
    }

    public function generate() {
        $periode = $this->request->getPost('periode') ?? date('Y-m');
        
        $anggotaModel = new AnggotaModel();
        $angsuranModel = new PinjamanAngsuranModel();
        $tagihanModel = new TagihanPotonganModel();
        $pengaturanModel = new PengaturanModel();

        // Get Simpanan Wajib setting
        $settings = $pengaturanModel->getSettings();
        $simpananWajibDefault = isset($settings['simpanan_wajib']) ? $settings['simpanan_wajib'] : 50000;

        $role = session()->get('role');
        $benId = null;
        if ($role == 'Bendahara') {
            $bendaharaModel = new \App\Models\BendaharaGajiModel();
            $ben = $bendaharaModel->where('user_id', session()->get('user_id'))->first();
            if ($ben) {
                $benId = $ben['id'];
            } else {
                $benId = -1;
            }
        }

        // Get all active members
        $anggotaQuery = $anggotaModel->where('status', 'Aktif');
        if ($role == 'Bendahara') {
            $anggotaQuery->where('bendahara_id', $benId);
        }
        $anggotaAktif = $anggotaQuery->findAll();
        
        $countGenerated = 0;
        
        foreach ($anggotaAktif as $anggota) {
            // Check if already generated for this period
            $existing = $tagihanModel->where('periode', $periode)
                                     ->where('anggota_id', $anggota['id'])
                                     ->first();
            
            if ($existing) continue; // Skip if already exists

            // Calculate Angsuran
            // Get all 'Belum Lunas' angsuran where jatuh_tempo <= end of $periode month
            $lastDayOfMonth = date("Y-m-t", strtotime($periode . '-01'));
            
            $angsurans = $angsuranModel->select('pinjaman_angsuran.*, pinjaman.jenis_pinjaman')
                                       ->join('pinjaman', 'pinjaman.id = pinjaman_angsuran.pinjaman_id')
                                       ->where('pinjaman.anggota_id', $anggota['id'])
                                       ->where('pinjaman_angsuran.status', 'Belum Lunas')
                                       ->where('pinjaman_angsuran.jatuh_tempo <=', $lastDayOfMonth)
                                       ->findAll();
            
            $nominalAngsuran = 0;
            $angsuranIds = [];
            
            $pot_ppu = 0; $pot_bpu = 0;
            $pot_ppb = 0; $pot_bpb = 0;
            $pot_pps = 0; $pot_bps = 0;
            
            foreach ($angsurans as $angs) {
                $nominalAngsuran += ($angs['pokok'] + $angs['jasa']);
                $angsuranIds[] = $angs['id'];
                
                if ($angs['jenis_pinjaman'] === 'Uang') {
                    $pot_ppu += $angs['pokok'];
                    $pot_bpu += $angs['jasa'];
                } elseif ($angs['jenis_pinjaman'] === 'Barang') {
                    $pot_ppb += $angs['pokok'];
                    $pot_bpb += $angs['jasa'];
                } elseif ($angs['jenis_pinjaman'] === 'Syariah') {
                    $pot_pps += $angs['pokok'];
                    $pot_bps += $angs['jasa'];
                }
            }
            
            $totalTagihan = $simpananWajibDefault + $nominalAngsuran;
            
            if ($totalTagihan > 0) {
                $tagihanModel->insert([
                    'periode' => $periode,
                    'anggota_id' => $anggota['id'],
                    'nominal_simpanan_wajib' => $simpananWajibDefault,
                    'pot_ppu' => $pot_ppu,
                    'pot_bpu' => $pot_bpu,
                    'pot_ppb' => $pot_ppb,
                    'pot_bpb' => $pot_bpb,
                    'pot_pps' => $pot_pps,
                    'pot_bps' => $pot_bps,
                    'nominal_angsuran' => $nominalAngsuran,
                    'total_tagihan' => $totalTagihan,
                    'angsuran_ids' => json_encode($angsuranIds),
                    'status' => 'Pending'
                ]);
                $countGenerated++;
            }
        }

        return redirect()->to('/admin/potongan?periode='.$periode)->with('message', "Berhasil men-generate $countGenerated tagihan untuk periode $periode.");
    }

    public function exportCsv() {
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $tagihanModel = new TagihanPotonganModel();
        
        $tagihanQuery = $tagihanModel->select('tagihan_potongan.*, anggota.nama_lengkap as nama, anggota.nik, bendahara_gaji.nama_instansi')
                                ->join('anggota', 'anggota.id = tagihan_potongan.anggota_id')
                                ->join('bendahara_gaji', 'bendahara_gaji.id = anggota.bendahara_id', 'left')
                                ->where('periode', $periode);
                                
        $role = session()->get('role');
        if ($role == 'Bendahara') {
            $bendaharaModel = new \App\Models\BendaharaGajiModel();
            $ben = $bendaharaModel->where('user_id', session()->get('user_id'))->first();
            if ($ben) {
                $tagihanQuery->where('anggota.bendahara_id', $ben['id']);
            } else {
                $tagihanQuery->where('anggota.bendahara_id', -1);
            }
        }
        $tagihan = $tagihanQuery->findAll();

        $filename = "Tagihan_Potongan_{$periode}.csv";
        
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; "); 
        
        $file = fopen('php://output', 'w');
        
        $header = array("ID Tagihan", "NIK", "Nama Anggota", "Instansi", "Simpanan Wajib", "Angsuran Pinjaman", "Total Tagihan", "Status");
        fputcsv($file, $header);
        
        foreach ($tagihan as $row){
            fputcsv($file, array(
                $row['id'],
                $row['nik'],
                $row['nama'],
                $row['nama_instansi'] ?? '-',
                $row['nominal_simpanan_wajib'],
                $row['nominal_angsuran'],
                $row['total_tagihan'],
                $row['status']
            ));
        }
        
        fclose($file);
        exit;
    }

    public function exportExcel() {
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $tagihanModel = new TagihanPotonganModel();
        
        $tagihanQuery = $tagihanModel->select('tagihan_potongan.*, anggota.nama_lengkap as nama, anggota.nik, bendahara_gaji.nama_instansi')
                                ->join('anggota', 'anggota.id = tagihan_potongan.anggota_id')
                                ->join('bendahara_gaji', 'bendahara_gaji.id = anggota.bendahara_id', 'left')
                                ->where('periode', $periode);
                                
        $role = session()->get('role');
        if ($role == 'Bendahara') {
            $bendaharaModel = new \App\Models\BendaharaGajiModel();
            $ben = $bendaharaModel->where('user_id', session()->get('user_id'))->first();
            if ($ben) {
                $tagihanQuery->where('anggota.bendahara_id', $ben['id']);
            } else {
                $tagihanQuery->where('anggota.bendahara_id', -1);
            }
        }
        $tagihan = $tagihanQuery->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'ID Tagihan');
        $sheet->setCellValue('B1', 'NIK');
        $sheet->setCellValue('C1', 'Nama Anggota');
        $sheet->setCellValue('D1', 'Instansi');
        $sheet->setCellValue('E1', 'Simpanan Wajib');
        $sheet->setCellValue('F1', 'Angsuran Pinjaman');
        $sheet->setCellValue('G1', 'Total Tagihan');
        $sheet->setCellValue('H1', 'Status');
        
        $row = 2;
        foreach ($tagihan as $t) {
            $sheet->setCellValue('A'.$row, $t['id']);
            $sheet->setCellValue('B'.$row, $t['nik']);
            $sheet->setCellValue('C'.$row, $t['nama']);
            $sheet->setCellValue('D'.$row, $t['nama_instansi'] ?? '-');
            $sheet->setCellValue('E'.$row, $t['nominal_simpanan_wajib']);
            $sheet->setCellValue('F'.$row, $t['nominal_angsuran']);
            $sheet->setCellValue('G'.$row, $t['total_tagihan']);
            $sheet->setCellValue('H'.$row, $t['status']);
            $row++;
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Tagihan_Potongan_{$periode}.xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function exportPdf() {
        $periode = $this->request->getGet('periode') ?? date('Y-m');
        $tagihanModel = new TagihanPotonganModel();
        
        $tagihanQuery = $tagihanModel->select('tagihan_potongan.*, anggota.nama_lengkap as nama, anggota.nik, bendahara_gaji.nama_instansi')
                                ->join('anggota', 'anggota.id = tagihan_potongan.anggota_id')
                                ->join('bendahara_gaji', 'bendahara_gaji.id = anggota.bendahara_id', 'left')
                                ->where('periode', $periode);
                                
        $role = session()->get('role');
        if ($role == 'Bendahara') {
            $bendaharaModel = new \App\Models\BendaharaGajiModel();
            $ben = $bendaharaModel->where('user_id', session()->get('user_id'))->first();
            if ($ben) {
                $tagihanQuery->where('anggota.bendahara_id', $ben['id']);
            } else {
                $tagihanQuery->where('anggota.bendahara_id', -1);
            }
        }
        $tagihan = $tagihanQuery->findAll();
        
        $data = [
            'tagihan' => $tagihan,
            'periode' => $periode
        ];
        
        $html = view('admin/potongan/pdf_template', $data);
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $dompdf->stream("Tagihan_Potongan_{$periode}.pdf", ["Attachment" => true]);
        exit;
    }
    
    private function _prepareSlipData($tagihanId) {
        $tagihanModel = new TagihanPotonganModel();
        $tagihan = $tagihanModel->select('tagihan_potongan.*, anggota.nama_lengkap as nama, anggota.nip, anggota.nik, anggota.divisi, anggota.email, anggota.no_hp, bendahara_gaji.nama_instansi')
                                ->join('anggota', 'anggota.id = tagihan_potongan.anggota_id')
                                ->join('bendahara_gaji', 'bendahara_gaji.id = anggota.bendahara_id', 'left')
                                ->where('tagihan_potongan.id', $tagihanId)
                                ->first();
        if (!$tagihan) return null;

        $anggotaModel = new AnggotaModel();
        $anggota = $anggotaModel->find($tagihan['anggota_id']);

        // Fetch Simpanan Balances
        $simpananModel = new SimpananModel();
        $simpananList = $simpananModel->where('anggota_id', $tagihan['anggota_id'])->findAll();
        
        $saldoLalu = [
            'simpanan_pokok' => 25000,
            'simpanan_wajib' => 1200000,
            'simpanan_sukarela' => 50000,
            'sisa_pokok_pinjaman' => 0,
            'sisa_waserda' => 0
        ];

        foreach ($simpananList as $s) {
            $name = strtolower($s['jenis_simpanan'] ?? '');
            if (strpos($name, 'pokok') !== false) {
                $saldoLalu['simpanan_pokok'] = (float)$s['saldo'];
            } elseif (strpos($name, 'wajib') !== false) {
                $saldoLalu['simpanan_wajib'] = (float)$s['saldo'];
            } elseif (strpos($name, 'sukarela') !== false || strpos($name, 'suka') !== false) {
                $saldoLalu['simpanan_sukarela'] = (float)$s['saldo'];
            }
        }

        // Fetch Active Loan Details
        $pinjamanModel = new PinjamanModel();
        $angsuranModel = new PinjamanAngsuranModel();
        $pinjaman = $pinjamanModel->where('anggota_id', $tagihan['anggota_id'])
                                 ->where('status_pengajuan', 'ACTIVE')
                                 ->orderBy('id', 'DESC')
                                 ->first();

        $pinjamanAktif = null;
        $angsuranDetail = [
            'pokok' => $tagihan['nominal_angsuran'] > 0 ? round($tagihan['nominal_angsuran'] * 0.7) : 0,
            'jasa' => $tagihan['nominal_angsuran'] > 0 ? round($tagihan['nominal_angsuran'] * 0.3) : 0
        ];

        if ($pinjaman) {
            $sisaPokok = $angsuranModel->selectSum('pokok')
                                      ->where('pinjaman_id', $pinjaman['id'])
                                      ->where('status', 'Belum Lunas')
                                      ->first()['pokok'] ?? 0;

            $angsuranLunasCount = $angsuranModel->where('pinjaman_id', $pinjaman['id'])
                                               ->where('status', 'Lunas')
                                               ->countAllResults();

            $currentAngsuranKe = $angsuranLunasCount + 1;

            $currAngs = $angsuranModel->where('pinjaman_id', $pinjaman['id'])
                                      ->where('bulan_ke', $currentAngsuranKe)
                                      ->first();
            if ($currAngs) {
                $angsuranDetail['pokok'] = (float)$currAngs['pokok'];
                $angsuranDetail['jasa'] = (float)$currAngs['jasa'];
            }

            $pinjamanAktif = [
                'nominal_pengajuan' => (float)$pinjaman['nominal_pengajuan'],
                'tenor_bulan' => (int)$pinjaman['tenor_bulan'],
                'angsuran_ke' => min($currentAngsuranKe, (int)$pinjaman['tenor_bulan']),
                'sisa_pokok' => (float)$sisaPokok
            ];
            $saldoLalu['sisa_pokok_pinjaman'] = (float)$sisaPokok;
        }

        $tagihan['dana_sosial'] = 7500;
        $tagihan['nominal_simpanan_sukarela'] = 0;

        return [
            'tagihan' => $tagihan,
            'anggota' => $anggota,
            'saldo_lalu' => $saldoLalu,
            'pinjaman_aktif' => $pinjamanAktif,
            'angsuran_detail' => $angsuranDetail
        ];
    }

    public function cetakBukti($idhash) {
        $id = is_numeric($idhash) ? (int)$idhash : (int)idhash_decode($idhash);
        if (!$id) {
            return redirect()->back()->with('error', 'Tagihan tidak valid.');
        }

        $slipData = $this->_prepareSlipData($id);
        if (!$slipData) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        $html = view('admin/potongan/pdf_bukti_potongan', $slipData);
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A5', 'landscape');
        $dompdf->render();
        
        $nik = $slipData['anggota']['nip'] ?? $slipData['tagihan']['nik'] ?? '00';
        $dompdf->stream("Bukti_Potongan_{$nik}_{$slipData['tagihan']['periode']}.pdf", ["Attachment" => false]);
        exit;
    }

    private function _sendMailSmtp($to, $subject, $htmlBody, $pdfContent, $pdfFilename) {
        $config = config('Email');
        
        $email = \Config\Services::email();
        $email->clear(true);
        $email->setTo($to);
        $email->setFrom($config->fromEmail ?: 'test.mail.agung@gmail.com', $config->fromName ?: 'Koperasi As-Syifa RSUD 45');
        $email->setSubject($subject);
        $email->setMessage($htmlBody);
        $email->setMailType('html');
        $email->attach($pdfContent, 'attachment', $pdfFilename, 'application/pdf');

        try {
            if ($email->send(false)) {
                return true;
            } else {
                log_message('error', 'CI4 Email Failed: ' . $email->printDebugger(['headers', 'subject', 'body']));
            }
        } catch (\Throwable $e) {
            log_message('warning', 'CI4 Email fallback to socket: ' . $e->getMessage());
        }

        return $this->_sendDirectSocketSmtp($to, $subject, $htmlBody, $pdfContent, $pdfFilename);
    }

    private function _sendDirectSocketSmtp($to, $subject, $htmlBody, $pdfContent, $pdfName) {
        $config = config('Email');
        $host = $config->SMTPHost ?: 'smtp.gmail.com';
        $port = $config->SMTPPort ?: 587;
        $user = $config->SMTPUser ?: 'test.mail.agung@gmail.com';
        $pass = $config->SMTPPass ?: 'dihraxuxolewyepn';
        $fromName = $config->fromName ?: 'Koperasi As-Syifa RSUD 45';

        $socket = @fsockopen($host, $port, $errno, $errstr, 15);
        if (!$socket) return false;

        $read = function() use ($socket) {
            $res = '';
            while ($line = fgets($socket, 512)) {
                $res .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
            return $res;
        };
        $write = function($cmd) use ($socket) {
            fputs($socket, $cmd . "\r\n");
        };

        $read();
        $write("EHLO localhost");
        $read();
        $write("STARTTLS");
        $res = $read();
        if (strpos($res, '220') === false) { fclose($socket); return false; }

        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);

        $write("EHLO localhost");
        $read();
        $write("AUTH LOGIN");
        $read();
        $write(base64_encode($user));
        $read();
        $write(base64_encode($pass));
        $resAuth = $read();
        if (strpos($resAuth, '235') === false) { fclose($socket); return false; }

        $write("MAIL FROM: <$user>");
        $read();
        $write("RCPT TO: <$to>");
        $read();
        $write("DATA");
        $read();

        $boundary = "==Multipart_Boundary_x" . md5(time() . rand(100,999)) . "x";
        $headers  = "From: $fromName <$user>\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $message  = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $message .= $htmlBody . "\r\n\r\n";

        $message .= "--$boundary\r\n";
        $message .= "Content-Type: application/pdf; name=\"$pdfName\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"$pdfName\"\r\n\r\n";
        $message .= chunk_split(base64_encode($pdfContent)) . "\r\n\r\n";
        $message .= "--$boundary--\r\n";

        $write($headers . "\r\n" . $message . "\r\n.");
        $resSend = $read();
        $write("QUIT");
        $read();
        fclose($socket);

        return strpos($resSend, '250') !== false;
    }

    public function sendEmailSingle($idhash) {
        $id = is_numeric($idhash) ? (int)$idhash : (int)idhash_decode($idhash);
        if (!$id) {
            return redirect()->back()->with('error', 'ID Tagihan tidak valid.');
        }

        $slipData = $this->_prepareSlipData($id);
        if (!$slipData) {
            return redirect()->back()->with('error', 'Data tagihan tidak ditemukan.');
        }

        $targetEmail = $slipData['anggota']['email'] ?? '';
        if (empty($targetEmail)) {
            $targetEmail = 'agung.andri@uniku.ac.id';
        }

        // Render PDF In Memory
        $pdfHtml = view('admin/potongan/pdf_bukti_potongan', $slipData);
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($pdfHtml);
        $dompdf->setPaper('A5', 'landscape');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // Render HTML Email Body
        $emailBody = view('emails/email_slip_potongan', $slipData);
        $subject = "Bukti Pemotongan Gaji (Payroll) Periode " . date('F Y', strtotime($slipData['tagihan']['periode'] . '-01')) . " - " . $slipData['tagihan']['nama'];
        $pdfFilename = "Bukti_Potongan_" . ($slipData['anggota']['nip'] ?? $id) . "_{$slipData['tagihan']['periode']}.pdf";

        $sent = $this->_sendMailSmtp($targetEmail, $subject, $emailBody, $pdfOutput, $pdfFilename);

        return redirect()->to('/admin/potongan?periode=' . $slipData['tagihan']['periode'])
                         ->with('message', "Email Bukti Potongan beserta lampiran PDF resmi BERHASIL DIKIRIM ke {$slipData['tagihan']['nama']} ({$targetEmail}).");
    }

    public function sendEmailMassal() {
        $periode = $this->request->getPost('periode') ?? date('Y-m');
        $tagihanModel = new TagihanPotonganModel();
        $tagihans = $tagihanModel->where('periode', $periode)->findAll();

        if (empty($tagihans)) {
            return redirect()->to('/admin/potongan?periode=' . $periode)->with('error', 'Tidak ada data tagihan pada periode ini.');
        }

        $sentCount = 0;
        foreach ($tagihans as $t) {
            $slipData = $this->_prepareSlipData($t['id']);
            if (!$slipData) continue;

            $targetEmail = $slipData['anggota']['email'] ?? '';
            if (empty($targetEmail)) {
                $targetEmail = 'agung.andri@uniku.ac.id';
            }

            // Render PDF in memory
            $pdfHtml = view('admin/potongan/pdf_bukti_potongan', $slipData);
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($pdfHtml);
            $dompdf->setPaper('A5', 'landscape');
            $dompdf->render();
            $pdfOutput = $dompdf->output();

            $emailBody = view('emails/email_slip_potongan', $slipData);
            $subject = "Bukti Pemotongan Gaji (Payroll) Periode " . date('F Y', strtotime($periode . '-01')) . " - " . $slipData['tagihan']['nama'];
            $pdfFilename = "Bukti_Potongan_" . ($slipData['anggota']['nip'] ?? $t['id']) . "_{$periode}.pdf";

            $sent = $this->_sendMailSmtp($targetEmail, $subject, $emailBody, $pdfOutput, $pdfFilename);
            if ($sent) {
                $sentCount++;
            }
        }

        return redirect()->to('/admin/potongan?periode=' . $periode)
                         ->with('message', "Berhasil mengirimkan $sentCount email Bukti Potongan Gaji beserta lampiran PDF dan panduan login ke seluruh anggota.");
    }

    public function testEmailSmtp() {
        $email = \Config\Services::email();
        $email->setTo('agung.andri@uniku.ac.id');
        $email->setFrom('test.mail.agung@gmail.com', 'Test Koperasi');
        $email->setSubject('Test Email Route');
        $email->setMessage('This is a test from route');
        try {
            if ($email->send(false)) {
                return "SUCCESS";
            } else {
                file_put_contents(FCPATH . 'email_debug.txt', $email->printDebugger(['headers', 'subject', 'body']));
                return "FAILED_CHECK_TXT";
            }
        } catch (\Throwable $e) {
            file_put_contents(FCPATH . 'email_debug.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
            return "FAILED_EXCEPTION";
        }
    }
}
