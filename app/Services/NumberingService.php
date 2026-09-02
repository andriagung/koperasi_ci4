<?php
namespace App\Services;

/**
 * Layanan generator nomor transaksi terpusat.
 *
 * Format: PREFIX-YYYYMM-000001
 * Contoh: SIM-202608-000001
 */
class NumberingService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Generate nomor transaksi berikutnya.
     *
     * @param string $prefix  e.g. 'SIM', 'PJM', 'BYR', 'POS', 'JRN'
     * @param string $format  'YM' untuk YYYYMM, 'YMD' untuk YYYYMMDD
     * @return string
     */
    public function generate(string $prefix, string $format = 'YM'): string
    {
        $periode = ($format === 'YMD') ? date('Ymd') : date('Ym');

        // Atomic increment: gunakan INSERT ... ON DUPLICATE KEY UPDATE
        $this->db->query(
            "INSERT INTO nomor_transaksi (prefix, periode, urutan) VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE urutan = urutan + 1",
            [$prefix, $periode]
        );

        $row = $this->db->query(
            "SELECT urutan FROM nomor_transaksi WHERE prefix = ? AND periode = ?",
            [$prefix, $periode]
        )->getRow();

        $urutan = $row ? (int) $row->urutan : 1;

        return $prefix . '-' . $periode . '-' . str_pad($urutan, 6, '0', STR_PAD_LEFT);
    }

    // ── Shortcut methods ─────────────────────────────────────
    public function simpanan(): string   { return $this->generate('SIM'); }
    public function pinjaman(): string   { return $this->generate('PJM'); }
    public function bayarAngsuran(): string { return $this->generate('BYR'); }
    public function penjualan(): string  { return $this->generate('POS', 'YMD'); }
    public function pembelian(): string  { return $this->generate('PUR'); }
    public function jurnal(): string     { return $this->generate('JRN'); }
    public function kasTransaksi(): string { return $this->generate('KAS'); }
    public function bankTransaksi(): string { return $this->generate('BNK'); }
    public function transferStok(): string { return $this->generate('TRF'); }
}
