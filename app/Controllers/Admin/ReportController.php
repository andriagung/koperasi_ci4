<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

/**
 * ReportController: Dashboard analitik, panel indikator capaian, & ekspor data.
 * Modul 7 — SIREMAJA.
 *
 * Endpoint:
 * - GET  admin/reports         → halaman laporan (Chart.js + indikator)
 * - GET  admin/reports/export  → ekspor responses ke XLSX
 * - GET  admin/reports/codebook → ekspor codebook kolom
 */
class ReportController extends BaseController
{
    // =========================================================
    // DASHBOARD LAPORAN + INDIKATOR CAPAIAN
    // =========================================================

    public function index(): string
    {
        $reportModel = new \App\Models\ReportModel();
        $data = $reportModel->getDashboardStats();
        $data['title'] = 'Laporan & Ekspor — SIREMAJA';

        return $this->renderView('admin/reports/index', $data);
    }

    // =========================================================
    // EKSPOR RESPONSES → XLSX
    // =========================================================

    /**
     * Ekspor data mentah `responses` ke file XLSX.
     * Hanya kolom anonim: unique_code, group_type, school_name, questionnaire type, question, answer, score.
     * Hanya bisa diakses role admin/peneliti (diverifikasi di Routes via filter).
     */
    public function export()
    {
        $reportModel = new \App\Models\ReportModel();
        $rows = $reportModel->getExportData();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Responses');

        // === HEADER ===
        $headers = [
            'A' => 'unique_code',
            'B' => 'group_type',
            'C' => 'age',
            'D' => 'gender',
            'E' => 'school_name',
            'F' => 'area_type',
            'G' => 'questionnaire_type',
            'H' => 'questionnaire_title',
            'I' => 'question_text',
            'J' => 'question_type',
            'K' => 'answer',
            'L' => 'score',
            'M' => 'submitted_at',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}1", $label);
        }

        // Style header
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // === DATA ===
        $rowNum = 2;
        foreach ($rows as $r) {
            $sheet->fromArray([
                $r['unique_code'],
                $r['group_type'],
                $r['age'],
                $r['gender'],
                $r['school_name'],
                $r['area_type'],
                $r['questionnaire_type'],
                $r['questionnaire_title'],
                $r['question_text'],
                $r['question_type'],
                $r['answer'],
                $r['score'],
                $r['submitted_at'],
            ], null, "A{$rowNum}");
            $rowNum++;
        }

        // Auto-width kolom
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Log ekspor
        $auditModel = new \App\Models\AuditLogModel();
        $auditModel->insert([
            'user_id'        => session()->get('user_id'),
            'action'         => 'export_responses:rows=' . count($rows),
            'table_affected' => 'responses',
            'record_id'      => 0,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $filename = 'SIREMAJA_responses_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // =========================================================
    // EKSPOR CODEBOOK → XLSX
    // =========================================================

    /**
     * Ekspor codebook — keterangan tiap kolom hasil ekspor agar mudah diolah di SPSS/R.
     */
    public function codebook()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Codebook');

        $headers = ['Nama Kolom', 'Deskripsi', 'Tipe Data', 'Nilai Valid', 'Catatan'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
        }
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
        ]);

        $codebook = [
            ['unique_code',          'Kode unik anonim siswa',                 'String',   'Alfanumerik unik per siswa',                      'Tidak mengandung nama asli atau identitas lain'],
            ['group_type',           'Kelompok penelitian siswa',               'Kategori', 'intervention | control',                          'Menentukan perlakuan yang diterima siswa'],
            ['age',                  'Usia siswa saat pendaftaran',             'Integer',  '11–24',                                           ''],
            ['gender',               'Jenis kelamin siswa',                     'Kategori', 'male | female',                                   ''],
            ['school_name',          'Nama sekolah mitra',                      'String',   'Nama sekolah',                                    'Dapat digunakan untuk analisis per sekolah'],
            ['area_type',            'Jenis wilayah sekolah',                   'Kategori', 'semi_urban | urban',                              ''],
            ['questionnaire_type',   'Tipe kuesioner',                          'Kategori', 'pretest | posttest | usability | qualitative',    'pretest/posttest = soal literasi; usability = kepuasan'],
            ['questionnaire_title',  'Judul kuesioner',                         'String',   'Teks',                                            ''],
            ['question_text',        'Teks butir soal',                         'String',   'Teks pertanyaan',                                 ''],
            ['question_type',        'Tipe butir soal',                         'Kategori', 'multiple_choice | likert | text',                 ''],
            ['answer',               'Jawaban siswa',                           'String',   'Kode opsi (A/B/C/D) atau angka Likert atau teks', 'Untuk pilihan ganda: bandingkan dengan kolom correct_answer'],
            ['score',                'Skor per butir soal',                     'Decimal',  '0 atau nilai weight',                             'Likert & teks: skor=0 (dihitung manual di SPSS/R)'],
            ['submitted_at',         'Waktu pengumpulan jawaban',               'Datetime', 'YYYY-MM-DD HH:MM:SS',                             ''],
        ];

        $row = 2;
        foreach ($codebook as $entry) {
            $sheet->fromArray($entry, null, "A{$row}");
            $row++;
        }

        foreach (range('A','E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Lembar ke-2: catatan metodologis
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Catatan Analisis');
        $notes = [
            ['Indikator',                          'Cara Hitung di SPSS/R'],
            ['Peningkatan skor literasi (≥20%)',    'Paired t-test: bandingkan total score pretest vs posttest per siswa, hanya kelompok intervention'],
            ['Tingkat partisipasi (≥85%)',           'Hitung % siswa yang punya baris pretest DAN posttest dari total terdaftar'],
            ['Kelengkapan data (≥95%)',              'Hitung % siswa dengan data tidak kosong di semua kolom kuesioner'],
            ['Kelayakan media (≥80%)',               'Gunakan tabel validations: AVG(score) per content_id'],
            ['Keterterimaan/Usability (≥80%)',       'Rata-rata skor Likert kuesioner usability, konversi ke persentase'],
            ['Reliabilitas (Cronbach α)',             'Hitung di SPSS: Analyze > Scale > Reliability Analysis pada kolom score per kuesioner'],
            ['Uji signifikansi (t-test/Mann-Whitney)','Hitung di SPSS/R menggunakan data ekspor ini'],
        ];
        $r = 1;
        foreach ($notes as $n) {
            $sheet2->fromArray($n, null, "A{$r}");
            $r++;
        }
        $sheet2->getStyle('A1:B1')->getFont()->setBold(true);
        foreach (['A','B'] as $c) $sheet2->getColumnDimension($c)->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);

        // Log
        $auditModel = new \App\Models\AuditLogModel();
        $auditModel->insert([
            'user_id'        => session()->get('user_id'),
            'action'         => 'export_codebook',
            'table_affected' => 'codebook',
            'record_id'      => 0,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $filename = 'SIREMAJA_codebook_' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
