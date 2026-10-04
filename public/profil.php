<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login, kalau belum login -> header('Location: login.php'); exit;

$judulHalaman = 'Profil Saya';
$halamanAktif = 'profil';

// DUMMY: ganti dengan data user dari backend / session
$user = ['nama' => 'Budi Utomo', 'nim' => '21010123', 'no_hp' => '081234567890'];

// Pesan dari backend setelah proses simpan (contoh: dikirim lewat flash/session)
$pesanSukses = $_GET['sukses'] ?? null; // tes: profil.php?sukses=Profil berhasil diperbarui
$pesanError  = $_GET['error'] ?? null;

$inputClass = 'w-full h-12 px-4 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
$labelClass = 'block text-sm font-semibold text-slate-900 mb-2';

require_once __DIR__ . '/partials/header.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 pt-10">

    <nav class="flex items-center gap-2 text-sm text-slate-600">
        <a href="index.php" class="hover:text-blue-600">Beranda</a>
        <span class="text-slate-400">›</span>
        <span class="text-blue-600 font-medium">Profil Saya</span>
    </nav>

    <?php if ($pesanSukses): ?>
        <div class="mt-6 flex items-center gap-3 rounded-lg bg-green-100 border border-green-200 px-5 py-4 text-sm font-medium text-green-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>
            <?= htmlspecialchars($pesanSukses) ?>
        </div>
    <?php endif; ?>
    <?php if ($pesanError): ?>
        <div class="mt-6 rounded-lg bg-red-50 border border-red-200 px-5 py-4 text-sm font-medium text-red-700">
            <?= htmlspecialchars($pesanError) ?>
        </div>
    <?php endif; ?>

    <h1 class="mt-8 text-3xl font-bold text-slate-900">Profil Saya</h1>
    <p class="mt-2 text-sm text-slate-600">Kelola data informasi akun dan kata sandi Anda.</p>

    <div class="mt-8 grid gap-8 md:grid-cols-2 items-start">

        <!-- Informasi profil -->
        <form method="POST" action="update-profil.php" class="bg-white border border-slate-200 rounded-2xl p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">Informasi Profil</h2>

            <div>
                <label for="nama" class="<?= $labelClass ?>">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($user['nama']) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label for="nim" class="<?= $labelClass ?>">NIM (Tidak dapat diubah)</label>
                <input type="text" id="nim" value="<?= htmlspecialchars($user['nim']) ?>" disabled
                       class="<?= $inputClass ?> bg-slate-100 text-slate-500 cursor-not-allowed">
            </div>
            <div>
                <label for="no_hp" class="<?= $labelClass ?>">No HP / WA</label>
                <input type="tel" id="no_hp" name="no_hp" required value="<?= htmlspecialchars($user['no_hp']) ?>" class="<?= $inputClass ?>">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-lg">Simpan Perubahan</button>
        </form>

        <!-- Ganti password -->
        <form method="POST" action="ganti-password.php" id="form-password" class="bg-white border border-slate-200 rounded-2xl p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">Ganti Password</h2>

            <div>
                <label for="password_lama" class="<?= $labelClass ?>">Password Lama</label>
                <input type="password" id="password_lama" name="password_lama" required autocomplete="current-password" class="<?= $inputClass ?>">
            </div>
            <div>
                <label for="password_baru" class="<?= $labelClass ?>">Password Baru</label>
                <input type="password" id="password_baru" name="password_baru" required minlength="8" autocomplete="new-password" class="<?= $inputClass ?>">
                <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
            </div>
            <div>
                <label for="konfirmasi" class="<?= $labelClass ?>">Konfirmasi Password Baru</label>
                <input type="password" id="konfirmasi" name="konfirmasi" required autocomplete="new-password" class="<?= $inputClass ?>">
                <p id="pesan-konfirmasi" class="mt-1 text-xs text-red-600 hidden">Konfirmasi password tidak cocok dengan password di atas.</p>
            </div>

            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold px-6 py-3 rounded-lg">Ganti Password</button>
        </form>
    </div>
</div>

<script>
    // Cek konfirmasi password di browser (validasi sebenarnya tetap di server)
    document.getElementById('form-password').addEventListener('submit', function (e) {
        const cocok = this.password_baru.value === this.konfirmasi.value;
        document.getElementById('pesan-konfirmasi').classList.toggle('hidden', cocok);
        if (!cocok) e.preventDefault();
    });
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>