<?php
// Dipakai oleh buat-laporan.php dan edit-laporan.php
// Variabel yang harus di-set sebelum require:
// $mode ('buat' | 'edit'), $data (array nilai awal), $daftarKategori, $daftarLokasi, $actionUrl

if (!isset($mode)) {
    $mode = 'buat';
}
if (!isset($data) || !is_array($data)) {
    $data = [
        'id' => 0,
        'nama' => '',
        'kategori' => '',
        'lokasi' => '',
        'tanggal' => '',
        'deskripsi' => '',
        'foto' => '',
    ];
}
if (!isset($daftarKategori) || !is_array($daftarKategori)) {
    $daftarKategori = [];
}
if (!isset($daftarLokasi) || !is_array($daftarLokasi)) {
    $daftarLokasi = [];
}
if (!isset($actionUrl)) {
    $actionUrl = 'index.php';
}

$isEdit = ($mode === 'edit');
$inputClass = 'w-full h-12 px-4 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
$labelClass = 'block text-sm font-semibold text-slate-900 mb-2';
$req = '<span class="text-red-500">*</span>';
?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 pt-10">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-slate-600">
        <a href="index.php" class="hover:text-blue-600">Beranda</a>
        <span class="text-slate-400">›</span>
        <span class="text-blue-600 font-medium"><?= $isEdit ? 'Edit Laporan' : 'Buat Laporan' ?></span>
    </nav>

    <div class="mt-8 bg-white border border-slate-200 rounded-3xl p-6 sm:p-10">
        <h1 class="text-2xl font-bold text-slate-900"><?= $isEdit ? 'Edit Laporan' : 'Formulir Pelaporan Barang Hilang / Temuan' ?></h1>
        <p class="mt-1 text-sm text-slate-600">
            <?= $isEdit
                ? 'Ubah data laporan kehilangan atau temuan barang Anda yang masih dalam proses verifikasi.'
                : 'Isi data di bawah ini secara lengkap untuk memudahkan proses verifikasi barang.' ?>
        </p>

        <!-- Kotak info -->
        <div class="mt-6 flex gap-3 rounded-lg bg-blue-50 px-5 py-4 text-sm text-slate-700">
            <svg class="w-5 h-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            <p>
                <?php if ($isEdit): ?>
                    <strong class="text-blue-600">Info Penting:</strong> Laporan hanya bisa diedit saat status Menunggu verifikasi admin. Jika status telah berubah, Anda tidak dapat mengubah isi laporan ini.
                <?php else: ?>
                    <strong class="text-blue-600">Tips Pelaporan:</strong> Laporan akan diverifikasi admin sebelum tampil di publik. Sebutkan warna utama, merek, lokasi spesifik, ciri khusus, atau isi tas/dompet secara mendetail. Batas tanggal kehilangan maksimal hari ini. Unggahan foto berukuran maksimal 2 MB.
                <?php endif; ?>
            </p>
        </div>

        <form method="POST" action="<?= htmlspecialchars($actionUrl) ?>" enctype="multipart/form-data" class="mt-8 pt-8 border-t border-slate-200 space-y-6">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
            <?php endif; ?>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="nama" class="<?= $labelClass ?>">Nama Barang <?= $req ?></label>
                    <input type="text" id="nama" name="nama" required placeholder="Contoh: Kunci Motor Vario Hitam"
                           value="<?= htmlspecialchars($data['nama']) ?>" class="<?= $inputClass ?>">
                </div>
                <div>
                    <label for="kategori" class="<?= $labelClass ?>">Kategori Barang <?= $req ?></label>
                    <select id="kategori" name="kategori" required class="<?= $inputClass ?>">
                        <?php foreach ($daftarKategori as $k): ?>
                            <option value="<?= htmlspecialchars($k) ?>" <?= $data['kategori'] === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="lokasi" class="<?= $labelClass ?>">Perkiraan Lokasi Hilang / Temu <?= $req ?></label>
                    <select id="lokasi" name="lokasi" required class="<?= $inputClass ?>">
                        <?php foreach ($daftarLokasi as $l): ?>
                            <option value="<?= htmlspecialchars($l) ?>" <?= $data['lokasi'] === $l ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="tanggal" class="<?= $labelClass ?>">Tanggal Kejadian <?= $req ?></label>
                    <input type="date" id="tanggal" name="tanggal" required max="<?= date('Y-m-d') ?>"
                           value="<?= htmlspecialchars($data['tanggal']) ?>" class="<?= $inputClass ?>">
                </div>
            </div>

            <div>
                <label for="deskripsi" class="<?= $labelClass ?>">Deskripsi Lengkap Ciri-Ciri Barang <?= $req ?></label>
                <textarea id="deskripsi" name="deskripsi" rows="5" required
                          placeholder="Tulis warna, merek, ciri khusus, dan isi barang..."
                          class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>

            <div>
                <span class="<?= $labelClass ?>">Foto Barang<?= $isEdit ? '' : ' (Opsional namun sangat disarankan)' ?></span>
                <label for="foto" class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center cursor-pointer hover:border-blue-400">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3M12 4v12M7 9l5-5 5 5"/></svg>
                    <span id="foto-label" class="text-sm text-slate-600">
                        <?= $isEdit && !empty($data['foto']) ? 'Klik untuk mengganti foto (foto saat ini: ' . htmlspecialchars($data['foto']) . ')' : 'Klik untuk memilih foto' ?>
                    </span>
                    <span class="text-xs text-slate-400">JPG atau PNG, maksimal 2 MB</span>
                    <input type="file" id="foto" name="foto" accept="image/png,image/jpeg" class="sr-only">
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="<?= $isEdit ? 'detail.php?id=' . (int) $data['id'] : 'index.php' ?>"
                   class="px-6 py-3 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-sm font-semibold text-white">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Kirim Laporan' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tampilkan nama file & cek ukuran maks 2 MB
    document.getElementById('foto').addEventListener('change', function () {
        const f = this.files[0];
        if (!f) return;
        if (f.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            this.value = '';
            return;
        }
        document.getElementById('foto-label').textContent = f.name;
    });
</script>