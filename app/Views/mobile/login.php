<?= $this->extend('mobile/layout/main') ?>
<?= $this->section('content') ?>
<div id="screen-login" class="screen active-screen" style="background: white;">
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 30px;">
                <div style="text-align: center; margin-bottom: 40px;">
                    <div style="width: 80px; height: 80px; background: var(--bg-color); border-radius: 20px; margin: 0 auto 15px; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                        <i class="fas fa-hospital-user" style="font-size: 2.5rem; color: var(--primary);"></i>
                    </div>
                    <h2 style="color: var(--text-dark); font-size: 1.5rem;">Kopkar Assyifa</h2>
                    <p style="color: var(--text-light); font-size: 0.9rem;">RSUD 45 Kuningan</p>
                </div>
                
                
    <?= csrf_field() ?>
                    <div class="input-group">
                        <label>NIP / ID Anggota</label>
                        <input type="text" id="loginNip" placeholder="Masukkan NIP Anda" required>
                    </div>
                    <div class="input-group">
                        <label>PIN / Password</label>
                        <input type="password" id="loginPin" placeholder="Masukkan PIN" required>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="margin-top: 15px;">Masuk Aplikasi</button>
                    <p id="loginError" style="color: red; text-align: center; margin-top: 10px; display: none; font-size: 0.85rem;"></p>
                </form>
                
                <div style="text-align: center; margin-top: 25px; padding: 10px; background: #fef2f2; border-radius: 8px; border: 1px solid #fca5a5;">
                    <p style="color: #b91c1c; font-size: 0.75rem;"><i class="fas fa-info-circle"></i> Hubungi Admin Koperasi / SDM RSUD jika Anda lupa PIN atau belum terdaftar sebagai anggota.</p>
                </div>
            </div>
        </div>
<?= $this->endSection() ?>

