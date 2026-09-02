<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ShuService;

class Shu extends BaseController
{
    protected $shuService;

    public function __construct()
    {
        $this->shuService = new ShuService();
    }

    public function index()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $simulasi = $this->shuService->kalkulasiSimulasi((int)$tahun);

        $shuTahunanModel = new \App\Models\ShuTahunanModel();
        $isDitutup = $shuTahunanModel->where('tahun', $tahun)->countAllResults() > 0;

        $data = [
            'title'     => 'Kalkulasi & Pembagian SHU',
            'tahun'     => $tahun,
            'simulasi'  => $simulasi,
            'isDitutup' => $isDitutup
        ];
        return view('admin/shu/index', $data);
    }

    public function tutupBuku()
    {
        $tahun = $this->request->getPost('tahun');
        if (!$tahun) {
            return redirect()->back()->with('error', 'Tahun tidak valid.');
        }

        $result = $this->shuService->tutupBukuDanBagikan((int)$tahun);
        
        if ($result['success']) {
            return redirect()->to('admin/shu/detail?tahun='.$tahun)->with('message', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function detail()
    {
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $shuTahunanModel = new \App\Models\ShuTahunanModel();
        $header = $shuTahunanModel->where('tahun', $tahun)->first();
        
        $detail = [];
        if ($header) {
            $shuAnggotaModel = new \App\Models\ShuAnggotaModel();
            $detail = $shuAnggotaModel->select('shu_anggota.*, anggota.nama_lengkap, anggota.nip')
                         ->join('anggota', 'anggota.id = shu_anggota.anggota_id')
                         ->where('shu_periode_id', $header['id'])
                         ->findAll();
        }

        $data = [
            'title'  => 'Detail Distribusi SHU',
            'tahun'  => $tahun,
            'header' => $header,
            'detail' => $detail
        ];
        
        return view('admin/shu/detail', $data);
    }
}
