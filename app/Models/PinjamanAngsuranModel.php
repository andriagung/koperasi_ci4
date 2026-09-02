<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamanAngsuranModel extends Model
{
    protected $table            = 'pinjaman_angsuran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pinjaman_id', 'bulan_ke', 'jatuh_tempo', 'pokok', 'jasa', 'status', 'tanggal_bayar'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getTunggakanSummary()
    {
        return $this->select('
                a.id as anggota_id,
                SUM(pinjaman_angsuran.pokok + pinjaman_angsuran.jasa) as sisa_pinjaman,
                DATEDIFF(CURDATE(), MIN(pinjaman_angsuran.jatuh_tempo)) as hari_keterlambatan
            ')
            ->join('pinjaman p', 'p.id = pinjaman_angsuran.pinjaman_id')
            ->join('anggota a', 'a.id = p.anggota_id')
            ->where('pinjaman_angsuran.status', 'Belum Lunas')
            ->where('pinjaman_angsuran.jatuh_tempo <', date('Y-m-d'))
            ->where('p.status_pengajuan', 'ACTIVE')
            ->groupBy('a.id')
            ->findAll();
    }

    public function getTunggakanDatatable($search = '', $limit = 10, $offset = 0)
    {
        $limit = (int)$limit;
        $offset = (int)$offset;

        $builder = $this->db->table($this->table)
            ->select('
                a.nip, 
                a.nama_lengkap, 
                a.no_hp,
                SUM(pinjaman_angsuran.pokok + pinjaman_angsuran.jasa) as sisa_pinjaman,
                MIN(pinjaman_angsuran.jatuh_tempo) as jatuh_tempo_terlama,
                DATEDIFF(CURDATE(), MIN(pinjaman_angsuran.jatuh_tempo)) as hari_keterlambatan,
                COUNT(pinjaman_angsuran.id) as jumlah_angsuran_nunggak
            ')
            ->join('pinjaman p', 'p.id = pinjaman_angsuran.pinjaman_id')
            ->join('anggota a', 'a.id = p.anggota_id')
            ->where('pinjaman_angsuran.status', 'Belum Lunas')
            ->where('pinjaman_angsuran.jatuh_tempo <', date('Y-m-d'))
            ->where('p.status_pengajuan', 'ACTIVE');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('a.nama_lengkap', $search)
                    ->orLike('a.nip', $search)
                    ->groupEnd();
        }

        // Count queries using COUNT(DISTINCT a.id)
        $countQuery = $this->db->table($this->table)
            ->select('COUNT(DISTINCT a.id) as total')
            ->join('pinjaman p', 'p.id = pinjaman_angsuran.pinjaman_id')
            ->join('anggota a', 'a.id = p.anggota_id')
            ->where('pinjaman_angsuran.status', 'Belum Lunas')
            ->where('pinjaman_angsuran.jatuh_tempo <', date('Y-m-d'))
            ->where('p.status_pengajuan', 'ACTIVE');

        if (!empty($search)) {
            $countQuery->groupStart()
                       ->like('a.nama_lengkap', $search)
                       ->orLike('a.nip', $search)
                       ->groupEnd();
        }

        $totalFilteredRow = $countQuery->get()->getRow();
        $totalFiltered = (int)($totalFilteredRow->total ?? 0);

        $totalDataQuery = $this->db->table($this->table)
            ->select('COUNT(DISTINCT a.id) as total')
            ->join('pinjaman p', 'p.id = pinjaman_angsuran.pinjaman_id')
            ->join('anggota a', 'a.id = p.anggota_id')
            ->where('pinjaman_angsuran.status', 'Belum Lunas')
            ->where('pinjaman_angsuran.jatuh_tempo <', date('Y-m-d'))
            ->where('p.status_pengajuan', 'ACTIVE');
        $totalDataRow = $totalDataQuery->get()->getRow();
        $totalData = (int)($totalDataRow->total ?? 0);

        $builder->groupBy('a.id')
                ->orderBy('hari_keterlambatan', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return [
            'data' => $builder->get()->getResultArray(),
            'totalData' => $totalData,
            'totalFiltered' => $totalFiltered
        ];
    }
}
