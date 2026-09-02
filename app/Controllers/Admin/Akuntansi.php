<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AkunCoaModel;
use App\Models\JurnalTransaksiModel;
use App\Models\AkuntansiModel;

class Akuntansi extends BaseController
{
    protected $coaModel;
    protected $jurnalModel;
    protected $akuntansiModel;

    public function __construct()
    {
        $this->coaModel = new AkunCoaModel();
        $this->jurnalModel = new JurnalTransaksiModel();
        $this->akuntansiModel = new AkuntansiModel();
    }

    public function coa()
    {
        $data = [
            'title' => 'Chart of Accounts (CoA)',
            'akun'  => $this->coaModel->findAll()
        ];
        return view('admin/akuntansi/coa', $data);
    }

    public function jurnal()
    {
        $data = [
            'title'  => 'Jurnal Umum',
            'jurnal' => $this->jurnalModel->getJurnalWithAkun()
        ];
        return view('admin/akuntansi/jurnal', $data);
    }

    public function bukuBesar()
    {
        $akunId = $this->request->getGet('akun_id');
        $bukuBesar = [];
        $selectedAkun = null;

        if ($akunId) {
            $selectedAkun = $this->coaModel->find($akunId);
            $bukuBesar = $this->jurnalModel->select('jurnal_transaksi.*, akun_coa.kode_akun, akun_coa.nama_akun')
                                           ->join('akun_coa', 'akun_coa.id = jurnal_transaksi.akun_id')
                                           ->where('akun_id', $akunId)
                                           ->orderBy('tanggal', 'ASC')
                                           ->orderBy('id', 'ASC')
                                           ->findAll();
        }

        $data = [
            'title'        => 'Buku Besar',
            'list_akun'    => $this->coaModel->findAll(),
            'buku_besar'   => $bukuBesar,
            'selectedAkun' => $selectedAkun
        ];
        return view('admin/akuntansi/buku_besar', $data);
    }

    public function neracaSaldo()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $query = $this->akuntansiModel->getNeracaSaldo($bulan, $tahun);

        $totalDebit = 0;
        $totalKredit = 0;
        foreach ($query as $row) {
            $totalDebit += $row['debit'];
            $totalKredit += $row['kredit'];
        }

        $data = [
            'title' => 'Neraca Saldo',
            'neracaSaldo'  => $query,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];
        return view('admin/akuntansi/neraca_saldo', $data);
    }

    public function labaRugi()
    {
        // Pendapatan (Tipe: Pendapatan) vs Beban (Tipe: Beban)
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $queryPendapatan = $this->akuntansiModel->getPendapatan($bulan, $tahun);
        $queryBeban = $this->akuntansiModel->getBeban($bulan, $tahun);

        $data = [
            'title'      => 'Laporan Laba Rugi',
            'pendapatan' => $queryPendapatan,
            'beban'      => $queryBeban,
            'bulan'      => $bulan,
            'tahun'      => $tahun
        ];
        return view('admin/akuntansi/laba_rugi', $data);
    }

    public function neraca()
    {
        // Aktiva vs Kewajiban + Ekuitas
        $queryAktiva = $this->akuntansiModel->getAktiva();
        $queryKewajiban = $this->akuntansiModel->getKewajiban();
        $queryEkuitas = $this->akuntansiModel->getEkuitas();

        $data = [
            'title'     => 'Neraca (Balance Sheet)',
            'aktiva'    => $queryAktiva,
            'kewajiban' => $queryKewajiban,
            'ekuitas'   => $queryEkuitas
        ];
        return view('admin/akuntansi/neraca', $data);
    }
    public function ajaxDaftarCoa()
    {
        $model = new \App\Models\CoaModel();
        
        $result = $this->processDataTables($model, ['kode_akun', 'nama_akun', 'kategori_akun']);
        
        $response = [
            "draw" => $result['draw'],
            "recordsTotal" => $result['recordsTotal'],
            "recordsFiltered" => $result['recordsFiltered'],
            "data" => []
        ];
        
        foreach ($result['data'] as $row) {
            $badge = $row['saldo_normal'] == 'Debit' ? 'bg-primary' : 'bg-success';
            $response['data'][] = [
                $row['kode_akun'],
                $row['nama_akun'],
                $row['kategori_akun'],
                '<span class="badge '.$badge.'">'.$row['saldo_normal'].'</span>'
            ];
        }
        
        return $this->response->setJSON($response);
    }
    
    public function ajaxJurnalUmum()
    {
        $model = new \App\Models\JurnalTransaksiModel();
        
        $joins = [
            ['table' => 'coa', 'cond' => 'coa.kode_akun = jurnal_transaksi.kode_akun', 'type' => 'left']
        ];
        
        $result = $this->processDataTables($model, ['jurnal_transaksi.nomor_bukti', 'coa.nama_akun', 'jurnal_transaksi.keterangan'], null, $joins);
        
        $response = [
            "draw" => $result['draw'],
            "recordsTotal" => $result['recordsTotal'],
            "recordsFiltered" => $result['recordsFiltered'],
            "data" => []
        ];
        
        foreach ($result['data'] as $row) {
            $response['data'][] = [
                date('d/m/Y', strtotime($row['tanggal'])),
                $row['nomor_bukti'],
                $row['kode_akun'] . ' - ' . ($row['nama_akun'] ?? 'Unknown'),
                $row['posisi'] == 'Debit' ? '<span class="text-primary">Debit</span>' : '<span class="text-success">Kredit</span>',
                'Rp ' . number_format($row['nominal'], 0, ',', '.')
            ];
        }
        
        return $this->response->setJSON($response);
    }
    
    public function ajaxDaftarKas()
    {
        $model = new \App\Models\KasModel();
        
        $result = $this->processDataTables($model, ['kode', 'nama']);
        
        $response = [
            "draw" => $result['draw'],
            "recordsTotal" => $result['recordsTotal'],
            "recordsFiltered" => $result['recordsFiltered'],
            "data" => []
        ];
        
        foreach ($result['data'] as $row) {
            $badge = $row['status'] == 'aktif' ? 'bg-success' : 'bg-danger';
            $actionBtn = '<button class="btn btn-sm btn-primary" onclick="editKas(\''.idhash_encode($row['id']).'\', \''.$row['kode'].'\', \''.$row['nama'].'\', \''.$row['status'].'\')"><i class="fas fa-edit"></i> Edit</button>';
            $response['data'][] = [
                $row['kode'],
                $row['nama'],
                'Rp ' . number_format($row['saldo'], 0, ',', '.'),
                '<span class="badge '.$badge.'">'.strtoupper($row['status']).'</span>',
                $actionBtn
            ];
        }
        
        return $this->response->setJSON($response);
    }
}