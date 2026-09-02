<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class KasBank extends BaseController {

    // --- MANAJEMEN KAS ---
    public function kas() {
        $db = \Config\Database::connect();
        $kas = $db->table('kas')->get()->getResultArray();
        return view('admin/keuangan/kas', ['kas' => $kas]);
    }

    public function simpanKas() {
        $db = \Config\Database::connect();
        $idRaw = $this->request->getPost('id');
        $id = $idRaw ? idhash_decode($idRaw) : null;
        $data = [
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'status' => $this->request->getPost('status') ?? 'aktif',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($id) {
            $db->table('kas')->where('id', $id)->update($data);
            $msg = 'Kas berhasil diupdate.';
        } else {
            $data['saldo'] = $this->request->getPost('saldo_awal') ?? 0;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('kas')->insert($data);
            $msg = 'Kas berhasil ditambahkan.';
        }

        return redirect()->to(previous_url(true))->with('success', $msg);
    }

    public function mutasiKas($kasId) {
        $kasId = idhash_decode($kasId);
        if (!$kasId) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $db = \Config\Database::connect();
        $kas = $db->table('kas')->where('id', $kasId)->get()->getRowArray();
        if (!$kas) return redirect()->to('/admin/keuangan/kas')->with('error', 'Kas tidak ditemukan.');

        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $mutasi = $db->table('kas_transaksi')
            ->where('kas_id', $kasId)
            ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bulan)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        return view('admin/keuangan/mutasi_kas', [
            'kas' => $kas,
            'mutasi' => $mutasi,
            'bulan' => $bulan
        ]);
    }

    // --- MANAJEMEN BANK ---
    public function bank() {
        $db = \Config\Database::connect();
        $bank = $db->table('rekening_bank')->get()->getResultArray();
        return view('admin/keuangan/bank', ['bank' => $bank]);
    }

    public function simpanBank() {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');
        $data = [
            'nama_bank' => $this->request->getPost('nama_bank'),
            'nomor_rekening' => $this->request->getPost('nomor_rekening'),
            'atas_nama' => $this->request->getPost('atas_nama'),
            'status' => $this->request->getPost('status') ?? 'aktif',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($id) {
            $db->table('rekening_bank')->where('id', $id)->update($data);
            $msg = 'Rekening Bank berhasil diupdate.';
        } else {
            $data['saldo'] = $this->request->getPost('saldo_awal') ?? 0;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('rekening_bank')->insert($data);
            $msg = 'Rekening Bank berhasil ditambahkan.';
        }

        return redirect()->to(previous_url(true))->with('success', $msg);
    }

    public function mutasiBank($bankId) {
        $bankId = idhash_decode($bankId);
        if (!$bankId) return redirect()->back()->with('error', 'ID tidak valid atau URL kadaluarsa.');

        $db = \Config\Database::connect();
        $bank = $db->table('rekening_bank')->where('id', $bankId)->get()->getRowArray();
        if (!$bank) return redirect()->to('/admin/keuangan/bank')->with('error', 'Rekening Bank tidak ditemukan.');

        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        $mutasi = $db->table('bank_transaksi')
            ->where('rekening_bank_id', $bankId)
            ->where("DATE_FORMAT(tanggal, '%Y-%m') =", $bulan)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        return view('admin/keuangan/mutasi_bank', [
            'bank' => $bank,
            'mutasi' => $mutasi,
            'bulan' => $bulan
        ]);
    }

    // --- REKONSILIASI ---
    public function rekonsiliasi() {
        $db = \Config\Database::connect();
        $bankId = $this->request->getGet('bank_id');
        $bankList = $db->table('rekening_bank')->where('status', 'aktif')->get()->getResultArray();
        
        $bank = null;
        if ($bankId) {
            $bank = $db->table('rekening_bank')->where('id', $bankId)->get()->getRowArray();
        }
        
        return view('admin/keuangan/rekonsiliasi', [
            'bankList' => $bankList,
            'bank' => $bank,
            'bank_id' => $bankId
        ]);
    }
}
