<?php
namespace App\Services;

class PotonganService extends BaseService {
    
    public function prosesCsvTagihan($filepath) {
        if (($handle = fopen($filepath, "r")) === FALSE) {
            throw new \Exception("Gagal membaca file CSV.");
        }

        $tagihanModel = new \App\Models\TagihanPotonganModel();
        $simpananModel = new \App\Models\SimpananModel();
        $angsuranModel = new \App\Models\PinjamanAngsuranModel();
        $pinjamanModel = new \App\Models\PinjamanModel();
        $riwayatModel = new \App\Models\RiwayatTransaksiModel();
        
        $db = \Config\Database::connect();
        
        $row = 0;
        $successCount = 0;
        
        $db->transStart();
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;
            if ($row == 1) continue; // Skip header
            
            $idTagihan = $data[0] ?? null;
            if (!$idTagihan) continue;
            
            $tagihan = $tagihanModel->find($idTagihan);
            
            if ($tagihan && $tagihan['status'] == 'Pending') {
                // Mark as Lunas
                $tagihanModel->update($idTagihan, [
                    'status' => 'Lunas',
                    'tanggal_bayar' => date('Y-m-d H:i:s')
                ]);
                
                // Process Simpanan Wajib
                if ($tagihan['nominal_simpanan_wajib'] > 0) {
                    $simpananModel->insert([
                        'anggota_id' => $tagihan['anggota_id'],
                        'jenis_simpanan' => 'Wajib',
                        'saldo' => $tagihan['nominal_simpanan_wajib']
                    ]);
                    
                    $riwayatModel->insert([
                        'anggota_id' => $tagihan['anggota_id'],
                        'kategori' => 'Simpanan',
                        'jenis_transaksi' => 'Masuk',
                        'nominal' => $tagihan['nominal_simpanan_wajib'],
                        'keterangan' => 'Simpanan Wajib (Payroll ' . $tagihan['periode'] . ')',
                        'tanggal' => date('Y-m-d')
                    ]);
                }
                
                // Process Angsuran
                if ($tagihan['nominal_angsuran'] > 0 && !empty($tagihan['angsuran_ids'])) {
                    $angsuranIds = json_decode($tagihan['angsuran_ids'], true);
                    if (is_array($angsuranIds)) {
                        foreach ($angsuranIds as $aId) {
                            $angs = $angsuranModel->find($aId);
                            if ($angs && $angs['status'] == 'Belum Lunas') {
                                $angsuranModel->update($aId, [
                                    'status' => 'Lunas',
                                    'tanggal_bayar' => date('Y-m-d')
                                ]);
                                
                                $riwayatModel->insert([
                                    'anggota_id' => $tagihan['anggota_id'],
                                    'kategori' => 'Pinjaman',
                                    'jenis_transaksi' => 'Masuk',
                                    'nominal' => $angs['pokok'] + $angs['jasa'],
                                    'keterangan' => 'Angsuran Pinjaman (Payroll ' . $tagihan['periode'] . ')',
                                    'tanggal' => date('Y-m-d')
                                ]);
                                
                                // Update status pinjaman if all paid
                                $sisaUnpaid = $angsuranModel->where('pinjaman_id', $angs['pinjaman_id'])
                                                            ->where('status', 'Belum Lunas')
                                                            ->countAllResults();
                                if ($sisaUnpaid == 0) {
                                    $pinjamanModel->update($angs['pinjaman_id'], ['status_pengajuan' => 'Lunas']);
                                }
                            }
                        }
                    }
                }
                $successCount++;
            }
        }
        
        $db->transComplete();
        fclose($handle);
        
        if ($db->transStatus() === FALSE) {
            throw new \Exception('Gagal memproses transaksi database.');
        }
        
        return $successCount;
    }
}
