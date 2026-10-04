<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login, kalau belum login -> header('Location: login.php'); exit;

$id = (int) ($_GET['id'] ?? 0);

// DUMMY DATA: ganti dengan data dari backend berdasarkan $id
$laporan = [
    'id'        => $id,
    'user_id'   => 1,
    'nama'      => 'Dompet Kulit Coklat Merk Braun Buffel',
    'kategori'  => 'Dompet',
    'status'    => 'Dipublikasi',      // Menunggu | Dipublikasi | Ditemukan
    'tipe'      => 'Mencari Pemilik',  // label kuning di samping kategori
    'lokasi'    => 'Gedung A, Lt. 2 dekat Lift',
    'tanggal'   => '15 Sep 2024',
    'pelapor'   => 'Rian Adi (Mahasiswa Teknik)',
    'deskripsi' => 'Ditemukan dompet kulit berwarna coklat gelap bertekstur jeruk di area koridor dekat lift Gedung A lantai 2. Di dalamnya terdapat beberapa kartu identitas penting dan kartu mahasiswa. Bagi yang merasa memiliki, harap kirim tanggapan verifikasi di bawah ini untuk pencocokan nama atau ciri khusus dompet.',
    'foto'      => 'dompet.jpg',
];

// DUMMY: ganti dengan id user dari session. Tes tampilan pemilik: detail.php?id=1&pemilik=1
$idUserLogin  = isset($_GET['pemilik']) ? 1 : 2;
$adalahPemilik = ($laporan['user_id'] === $idUserLogin);

$judulHalaman = $adalahPemilik ? 'Kelola Laporan' : 'Detail Laporan';
$halamanAktif = $adalahPemilik ? 'histori' : 'beranda';

$badgeStatus = [
    'Menunggu'    => 'bg-amber-100 text-amber-700',
    'Dipublikasi' => 'bg-blue-100 text-blue-700',
    'Ditemukan'   => 'bg-emerald-100 text-emerald-700',
];

require_once __DIR__ . '/partials/header.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 pt-10">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-slate-600">
        <a href="<?= $adalahPemilik ? 'histori.php' : 'index.php' ?>" class="hover:text-blue-600">Beranda</a>
        <span class="text-slate-400">›</span>
        <span class="text-blue-600 font-medium"><?= $adalahPemilik ? 'Laporan Saya' : 'Detail Laporan' ?></span>
    </nav>

    <?php if ($adalahPemilik): ?>
        <!-- Judul + aksi (pemilik) -->
        <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900">Mengelola Laporan: <?= htmlspecialchars($laporan['nama']) ?></h1>
                <span class="px-2 py-1 rounded-md text-xs font-medium <?= $badgeStatus[$laporan['status']] ?? 'bg-slate-100 text-slate-700' ?>">
                    <?= htmlspecialchars($laporan['status']) ?>
                </span>
            </div>
            <div class="flex items-center gap-5 text-sm">
                <a href="edit-laporan.php?id=<?= (int) $laporan['id'] ?>" class="text-slate-500 hover:text-blue-600">Edit Laporan</a>
                <form method="POST" action="hapus-laporan.php" onsubmit="return confirm('Hapus laporan ini? Tindakan ini tidak bisa dibatalkan.')">
                    <input type="hidden" name="id" value="<?= (int) $laporan['id'] ?>">
                    <button type="submit" class="text-red-600 font-medium hover:text-red-700">Hapus Laporan</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-8 grid gap-8 lg:grid-cols-5 items-start">

        <!-- Kolom foto + tombol -->
        <div class="lg:col-span-2">
            <div class="aspect-square rounded-2xl overflow-hidden bg-slate-200">
                <img src="assets/img/<?= htmlspecialchars($laporan['foto']) ?>" alt="<?= htmlspecialchars($laporan['nama']) ?>"
                     class="w-full h-full object-cover" onerror="this.style.display='none'">
            </div>

            <?php if ($adalahPemilik): ?>
                <form method="POST" action="tandai-ditemukan.php" class="mt-6">
                    <input type="hidden" name="id" value="<?= (int) $laporan['id'] ?>">
                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold px-6 py-3 rounded-lg">
                        Tandai Sudah Ditemukan
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Kolom info -->
        <div class="lg:col-span-3 bg-white border border-slate-200 rounded-2xl p-8">
            <?php if (!$adalahPemilik): ?>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-xs font-medium"><?= htmlspecialchars($laporan['kategori']) ?></span>
                    <span class="px-2 py-1 rounded-md bg-amber-100 text-amber-700 text-xs font-medium"><?= htmlspecialchars($laporan['tipe']) ?></span>
                </div>
                <h1 class="mt-4 text-3xl font-bold text-slate-900"><?= htmlspecialchars($laporan['nama']) ?></h1>
            <?php endif; ?>

            <ul class="<?= $adalahPemilik ? '' : 'mt-6' ?> space-y-3 text-sm text-slate-600">
                <li class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Lokasi: <strong class="text-slate-900"><?= htmlspecialchars($laporan['lokasi']) ?></strong></span>
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span><?= $adalahPemilik ? 'Tanggal Lapor' : 'Tanggal Hilang' ?>: <strong class="text-slate-900"><?= htmlspecialchars($laporan['tanggal']) ?></strong></span>
                </li>
                <?php if (!$adalahPemilik): ?>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                        <span>Dilaporkan oleh: <strong class="text-slate-900"><?= htmlspecialchars($laporan['pelapor']) ?></strong></span>
                    </li>
                <?php endif; ?>
            </ul>

            <?php if (!$adalahPemilik): ?>
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <h2 class="text-sm font-bold text-slate-900">Deskripsi Barang</h2>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($laporan['deskripsi'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$adalahPemilik): ?>
        <!-- Aksi non-pemilik -->
        <form method="POST" action="respon.php" class="mt-10 flex flex-col items-center gap-3">
            <input type="hidden" name="laporan_id" value="<?= (int) $laporan['id'] ?>">
            <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-8 py-3 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m5 12 5 5L20 7"/></svg>
                Saya Menemukan Barang Ini
            </button>
            <p class="text-xs text-slate-600">Klik jika Anda menemukan barang ini untuk menghubungi pemilik.</p>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>