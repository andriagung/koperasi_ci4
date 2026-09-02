<?php
namespace App\Services;

class AngsuranService extends BaseService {
    
    public function bayar(int $pinjamanId, array $data): array {
        $this->db->transStart();
        try {
            // Implementasi pembayaran angsuran
            // update status angsuran, update outstanding pinjaman
            
            $this->db->transComplete();
            return ['success' => true];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function hitungDenda(array $angsuran): float {
        // Implementasi denda keterlambatan
        return 0.0;
    }
    
    public function hitungPinalti(array $pinjaman, float $sisaPokok): float {
        // Pinalti pelunasan dipercepat misalnya 2% dari sisa pokok
        return $sisaPokok * 0.02;
    }
    
    public function cekTunggakan(): array {
        // Implementasi
        return [];
    }
    
    public function generateJadwal(array $pinjaman, ?float $bungaPersen = null, ?string $tglMulai = null): void {
        if ($bungaPersen === null) {
            $pengaturanModel = new \App\Models\PengaturanModel();
            $bungaPersen = $pengaturanModel->where('kunci', 'jasa_bunga_pinjaman')->first()['nilai'] ?? 1.5;
        }

        $pokok = $pinjaman['nominal_pengajuan'] / $pinjaman['tenor_bulan'];
        $bunga = $pinjaman['nominal_pengajuan'] * ($bungaPersen / 100);
        
        $tgl = $tglMulai ? new \DateTime($tglMulai) : new \DateTime();
        
        $jadwal = [];
        for ($i = 1; $i <= $pinjaman['tenor_bulan']; $i++) {
            $tgl->modify('+1 month');
            $jadwal[] = [
                'pinjaman_id' => $pinjaman['id'],
                'bulan_ke' => $i,
                'jatuh_tempo' => $tgl->format('Y-m-d'),
                'pokok' => $pokok,
                'jasa' => $bunga,
                'status' => 'Belum Lunas'
            ];
        }
        
        if (!empty($jadwal)) {
            $this->db->table('pinjaman_angsuran')->insertBatch($jadwal);
        }
    }
    
    public function updateOutstanding(int $pinjamanId, float $nominalPokok): void {
        $this->db->query("UPDATE pinjaman SET sisa_pokok = sisa_pokok - ? WHERE id = ?", [$nominalPokok, $pinjamanId]);
    }
}
