<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="page-title">
    Kartu Anggota Digital
</div>

<div class="panel-view active" style="padding: 20px;">
    
    <div style="margin-bottom: 30px;">
        <label style="display: block; font-weight: 500; margin-bottom: 8px; color: #475569;">Pilih Anggota</label>
        <div style="display: flex; gap: 10px; max-width: 400px;">
            <select id="anggota-select" class="form-control" style="flex: 1;">
                <option value="">-- Pilih Anggota --</option>
                <?php foreach($list_anggota as $a): ?>
                    <option value="<?= idhash_encode($a['id']) ?>" <?= isset($anggota) && $anggota['id'] == $a['id'] ? 'selected' : '' ?>>
                        <?= esc($a['nama_lengkap'] ?? '') ?> (<?= esc($a['nip'] ?? '') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn-primary" onclick="tampilkanKartu()"><i class="fas fa-id-card"></i> Tampilkan</button>
        </div>
    </div>

    <?php if(isset($anggota)): ?>
        <!-- Wrapper Kartu Premium -->
        <div class="id-card-wrapper" style="perspective: 1000px; display: inline-block;">
            <div class="id-card" style="
                width: 420px;
                height: 260px;
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                border-radius: 16px;
                padding: 0;
                color: white;
                font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                box-shadow: 0 20px 40px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.1);
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(255,255,255,0.1);
                transition: transform 0.3s ease;
            ">
                <!-- Ornaments -->
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(16,185,129,0.3) 0%, rgba(0,0,0,0) 70%); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; left: -30px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(14,165,233,0.3) 0%, rgba(0,0,0,0) 70%); border-radius: 50%;"></div>
                
                <!-- Header -->
                <div style="
                    display: flex; 
                    align-items: center; 
                    padding: 20px 25px; 
                    background: rgba(255, 255, 255, 0.05); 
                    backdrop-filter: blur(10px);
                    border-bottom: 1px solid rgba(255,255,255,0.05);
                ">
                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(16,185,129,0.4);">
                        <i class="fas fa-building" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 700; letter-spacing: 0.5px; color: #f8fafc;">KOPERASI PEGAWAI RSUD</h3>
                        <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">Kartu Tanda Anggota</div>
                    </div>
                </div>
                
                <!-- Content -->
                <div style="display: flex; padding: 20px 25px; gap: 20px; align-items: flex-start; position: relative; z-index: 2;">
                    <!-- Photo -->
                    <div style="
                        width: 85px; 
                        height: 110px; 
                        background: rgba(255,255,255,0.05); 
                        border-radius: 8px; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        overflow: hidden;
                        border: 2px solid rgba(255,255,255,0.1);
                        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                        flex-shrink: 0;
                    ">
                        <?php if(!empty($anggota['foto'])): ?>
                            <img src="/uploads/foto/<?= esc($anggota['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user" style="color: #64748b; font-size: 40px;"></i>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Details -->
                    <div style="flex: 1;">
                        <h2 style="margin: 0 0 15px 0; font-size: 20px; font-weight: 700; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.5); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= esc($anggota['nama_lengkap'] ?? '') ?>
                        </h2>
                        
                        <div style="display: grid; grid-template-columns: 80px 1fr; gap: 6px; font-size: 12px;">
                            <div style="color: #94a3b8; font-weight: 500;">No. Anggota</div>
                            <div style="color: #f1f5f9; font-weight: 600; font-family: monospace; font-size: 13px;"><?= esc($anggota['nip'] ?? '') ?></div>
                            
                            <div style="color: #94a3b8; font-weight: 500;">Telepon</div>
                            <div style="color: #f1f5f9;"><?= esc($anggota['no_hp'] ?? '-') ?></div>
                            
                            <div style="color: #94a3b8; font-weight: 500;">Bergabung</div>
                            <div style="color: #f1f5f9;"><?= date('d M Y', strtotime($anggota['tanggal_masuk'] ?? $anggota['created_at'])) ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- QR Code (Decorative/Simulated) -->
                <div style="position: absolute; bottom: 20px; right: 25px; width: 45px; height: 45px; background: rgba(255,255,255,0.9); border-radius: 6px; display: flex; align-items: center; justify-content: center; padding: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= esc($anggota['nip'] ?? '') ?>" alt="QR" style="width: 100%; height: 100%; opacity: 0.9;">
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button class="btn-primary" style="background-color: #3b82f6; border: none; padding: 12px 24px; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(59,130,246,0.3);" onclick="window.print()">
                <i class="fas fa-print" style="margin-right: 8px;"></i> Cetak Kartu Fisik
            </button>
        </div>
    <?php else: ?>
        <div style="padding: 40px; text-align: center; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; color: #64748b;">
            <i class="fas fa-id-card fa-3x" style="margin-bottom: 15px; color: #94a3b8;"></i>
            <p style="margin: 0; font-size: 15px;">Pilih anggota dari *dropdown* di atas untuk menampilkan kartu digital.</p>
        </div>
    <?php endif; ?>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.id-card-wrapper:hover .id-card {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.2);
}

@media print {
    body * { visibility: hidden; }
    .id-card-wrapper, .id-card-wrapper * { visibility: visible; }
    .id-card-wrapper { 
        position: absolute; 
        left: 50%; 
        top: 50px; 
        transform: translateX(-50%) !important; 
    }
    .id-card {
        box-shadow: none !important;
        transform: none !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

<script>
function tampilkanKartu() {
    var hash = document.getElementById('anggota-select').value;
    if (hash) {
        window.location.href = "<?= base_url('admin/anggota/kartu/') ?>" + hash;
    } else {
        alert("Silakan pilih anggota terlebih dahulu!");
    }
}
</script>
<?= $this->endSection() ?>
