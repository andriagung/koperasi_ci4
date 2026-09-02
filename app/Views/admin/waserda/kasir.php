<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>
<style>
    .pos-container { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; height: calc(100vh - 120px); }
    .pos-left { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display:flex; flex-direction:column; }
    .pos-right { background: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; display:flex; flex-direction:column; }
    .pos-header { margin-bottom: 20px; }
    .pos-table-container { flex-grow: 1; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 6px; }
    .pos-table th { background: #f1f5f9; padding: 10px; position: sticky; top: 0; }
    .pos-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
    .btn-qty { background: #e2e8f0; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
    .summary-box { background: #0f172a; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: right; }
    .summary-total { font-size: 2rem; font-weight: bold; color: #10b981; }
</style>

<div class="page-title">Mesin Kasir (POS)</div>

<div class="pos-container">
    <!-- Kiri: Input & Keranjang -->
    <div class="pos-left">
        <div class="pos-header">
            <div style="display:flex; gap:10px;">
                <input type="text" id="input-barcode" placeholder="Scan Barcode / Ketik Kode & Enter" style="flex:1; padding:12px; font-size:1.1rem; border-radius:6px; border:2px solid #cbd5e1;" autofocus>
                <button class="btn-primary" onclick="cariProduk()">Tambah</button>
            </div>
            <small style="color:var(--text-muted);">Bisa juga mencari berdasarkan ID Produk jika barcode tidak ada.</small>
        </div>
        
        <div class="pos-table-container">
            <table class="pos-table" style="width:100%; border-collapse:collapse; text-align:left;">
                <thead>
                    <tr>
                        <th style="width:50%;">Item</th>
                        <th>Harga</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    <!-- Item masuk sini -->
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Kanan: Pembayaran -->
    <div class="pos-right">
        <div class="form-group" style="margin-bottom:15px;">
            <label>Pilih Pelanggan / Anggota (Opsional)</label>
            <select id="anggota_id" class="select2" onchange="hitungTotal()">
                <option value="">Umum (Harga Normal)</option>
                <?php foreach($anggota as $a): ?>
                    <option value="<?= $a['id'] ?? '' ?>"><?= esc($a['nama_lengkap'] ?? '') ?> (Harga Member)</option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="summary-box">
            <div style="font-size:1.1rem; color:#94a3b8;">Total Tagihan</div>
            <div class="summary-total" id="lbl-total">Rp 0</div>
        </div>
        
        <div class="form-group" style="margin-bottom:15px;">
            <label>Metode Pembayaran</label>
            <select id="metode_pembayaran">
                <option value="Tunai">Tunai</option>
                <option value="Transfer">Transfer Bank</option>
                <option value="Anggota/Simpanan">Potong Simpanan Sukarela (Hanya Anggota)</option>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom:15px;">
            <label>Uang Tunai / Dibayar (Rp)</label>
            <input type="number" id="uang_dibayar" value="0" oninput="hitungKembalian()" style="font-size:1.2rem; padding:10px;">
        </div>
        
        <div style="display:flex; justify-content:space-between; margin-bottom:20px; font-size:1.1rem;">
            <span>Kembalian:</span>
            <strong id="lbl-kembalian" style="color:#0284c7;">Rp 0</strong>
        </div>
        
        <div style="flex-grow:1;"></div>
        
        <button class="btn-primary" style="width:100%; padding:15px; font-size:1.2rem; background:#16a34a;" onclick="prosesBayar()"><i class="fas fa-check-circle"></i> Selesaikan Pembayaran</button>
    </div>
</div>

<script>
let cart = [];
let dbProduk = <?= $produk_json ?? '' ?>;

document.getElementById('input-barcode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') cariProduk();
});

function cariProduk() {
    let input = document.getElementById('input-barcode');
    let code = input.value.trim();
    if (!code) return;
    
    // Cari di lokal dulu
    let p = dbProduk.find(x => x.barcode == code || x.id == code);
    
    if (p) {
        addToCart(p);
        input.value = '';
    } else {
        // Coba cari di server
        fetch('/admin/kasir/cariBarcode/' + code)
        .then(res => res.json())
        .then(res => {
            if (res.status == 'success') {
                addToCart(res.data);
                input.value = '';
            } else {
                alert('Produk tidak ditemukan!');
            }
        });
    }
}

function addToCart(produk) {
    let existing = cart.find(x => x.id == produk.id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({
            id: produk.id,
            nama: produk.nama_produk,
            harga_normal: produk.harga_normal,
            harga_member: produk.harga_member > 0 ? produk.harga_member : produk.harga_normal,
            qty: 1
        });
    }
    renderCart();
}

function updateQty(id, delta) {
    let item = cart.find(x => x.id == id);
    if (item) {
        item.qty += delta;
        if (item.qty <= 0) cart = cart.filter(x => x.id != id);
        renderCart();
    }
}

function renderCart() {
    let tbody = document.getElementById('cart-body');
    let isMember = document.getElementById('anggota_id').value != '';
    
    tbody.innerHTML = '';
    let total = 0;
    
    cart.forEach(item => {
        let hargaAktif = isMember ? item.harga_member : item.harga_normal;
        let sub = hargaAktif * item.qty;
        total += sub;
        item.harga_aktif = hargaAktif; // simpan referensi
        
        let tr = `<tr>
            <td>${item.nama}</td>
            <td>Rp ${new Intl.NumberFormat('id-ID').format(hargaAktif)}</td>
            <td style="text-align:center;">
                <button class="btn-qty" onclick="updateQty(${item.id}, -1)">-</button>
                <span style="display:inline-block; width:30px; text-align:center;">${item.qty}</span>
                <button class="btn-qty" onclick="updateQty(${item.id}, 1)">+</button>
            </td>
            <td style="text-align:right; font-weight:bold;">Rp ${new Intl.NumberFormat('id-ID').format(sub)}</td>
            <td style="text-align:center;"><i class="fas fa-trash" style="color:#dc2626; cursor:pointer;" onclick="updateQty(${item.id}, -999)"></i></td>
        </tr>`;
        tbody.innerHTML += tr;
    });
    
    document.getElementById('lbl-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    document.getElementById('lbl-total').dataset.val = total;
    hitungKembalian();
}

function hitungTotal() {
    renderCart(); // Re-render jika member berubah (harga berubah)
}

function hitungKembalian() {
    let total = parseInt(document.getElementById('lbl-total').dataset.val) || 0;
    let bayar = parseInt(document.getElementById('uang_dibayar').value) || 0;
    let kembali = bayar - total;
    if (kembali < 0) kembali = 0;
    document.getElementById('lbl-kembalian').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(kembali);
}

function prosesBayar() {
    if (cart.length === 0) return alert('Keranjang kosong!');
    
    let total = parseInt(document.getElementById('lbl-total').dataset.val) || 0;
    let bayar = parseInt(document.getElementById('uang_dibayar').value) || 0;
    let metode = document.getElementById('metode_pembayaran').value;
    let anggota = document.getElementById('anggota_id').value;
    
    if (metode == 'Tunai' && bayar < total) {
        return alert('Uang dibayar kurang dari total!');
    }
    
    if (metode == 'Anggota/Simpanan' && !anggota) {
        return alert('Metode potong simpanan hanya untuk anggota yang terdaftar!');
    }
    
    // Format payload
    let payload = {
        anggota_id: anggota,
        metode_pembayaran: metode,
        total_bayar: total,
        diskon: 0,
        keranjang: cart.map(c => ({id: c.id, qty: c.qty, harga: c.harga_aktif}))
    };
    
    fetch('/admin/kasir/prosesBayar', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    }).then(res => res.json())
    .then(res => {
        if (res.success) {
            alert('Transaksi Berhasil! Invoice: ' + res.invoice);
            cart = [];
            document.getElementById('uang_dibayar').value = '0';
            renderCart();
            // Nanti tambahkan logic cetak struk
        } else {
            alert('Gagal: ' + res.message);
        }
    });
}
</script>
<?= $this->endSection() ?>