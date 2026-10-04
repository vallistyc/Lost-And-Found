<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>

<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login, kalau belum login -> header('Location: login.php'); exit;

$judulHalaman = 'Beranda';
$halamanAktif = 'beranda';

// Filter dari form (GET)
$q        = trim($_GET['q'] ?? '');
$kategori = $_GET['kategori'] ?? '';
$lokasi   = $_GET['lokasi'] ?? '';

// DUMMY DATA: ganti dengan data dari backend nanti
$daftarKategori = ['Aksesoris', 'Tas', 'Elektronik', 'Dokumen', 'Lain-lain'];
$daftarLokasi   = ['Kantin Pusat', 'Gedung Perpustakaan Pusat', 'Parkiran GKB I'];
$items = [
    ['id' => 1, 'nama' => 'Dompet Kulit Coklat', 'kategori' => 'Aksesoris', 'lokasi' => 'Kantin Pusat, Fakultas Teknik', 'tanggal' => '24 Okt 2023', 'foto' => 'dompet.jpg', 'status' => 'Menunggu'],
    ['id' => 2, 'nama' => 'Kunci Motor Honda', 'kategori' => 'Lain-lain', 'lokasi' => 'Parkiran Gedung Kuliah Bersama (GKB) I', 'tanggal' => '23 Okt 2023', 'foto' => 'kunci.jpg', 'status' => 'Dipublikasi'],
    ['id' => 3, 'nama' => 'Tas Ransel Pink', 'kategori' => 'Tas', 'lokasi' => 'Gedung Perpustakaan Pusat, Lt. 2', 'tanggal' => '22 Okt 2023', 'foto' => 'tas.jpg', 'status' => 'Ditemukan'],
];

$badgeStatus = [
    'Menunggu'    => 'bg-amber-100 text-amber-700',
    'Dipublikasi' => 'bg-blue-100 text-blue-700',
    'Ditemukan'   => 'bg-emerald-100 text-emerald-700',
];

require_once __DIR__ . '/partials/header.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 pt-12">

    <!-- Hero -->
    <section class="rounded-3xl bg-blue-600 px-10 py-10 text-white">
        <h1 class="text-3xl font-bold">Kehilangan Barang di Kampus?</h1>
        <p class="mt-2 text-blue-100">Cari di database barang temuan kami atau buat laporan kehilangan baru sekarang.</p>
    </section>

    <!-- Pencarian & filter -->
    <form method="GET" action="index.php" class="mt-8 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama barang yang hilang..."
                   class="w-full h-12 pl-12 pr-4 bg-white border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <select name="kategori" onchange="this.form.submit()"
                class="h-12 px-4 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 md:w-44">
            <option value="">Semua Kategori</option>
            <?php foreach ($daftarKategori as $k): ?>
                <option value="<?= htmlspecialchars($k) ?>" <?= $kategori === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="lokasi" onchange="this.form.submit()"
                class="h-12 px-4 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 md:w-44">
            <option value="">Semua Lokasi</option>
            <?php foreach ($daftarLokasi as $l): ?>
                <option value="<?= htmlspecialchars($l) ?>" <?= $lokasi === $l ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Grid kartu -->
    <section class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php if (empty($items)): ?>
            <p class="col-span-full text-center text-slate-500 py-16">Belum ada laporan yang cocok dengan pencarian Anda.</p>
        <?php endif; ?>

        <?php foreach ($items as $item): ?>
            <a href="detail.php?id=<?= (int) $item['id'] ?>" class="block bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                <div class="relative h-48 bg-slate-200">
                    <img src="assets/img/<?= htmlspecialchars($item['foto']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>"
                         class="w-full h-full object-cover" onerror="this.style.display='none'">
                    <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold <?= $badgeStatus[$item['status']] ?? 'bg-slate-100 text-slate-700' ?>">
                        <?= htmlspecialchars($item['status']) ?>
                    </span>
                </div>
                <div class="p-5">
                    <span class="inline-block px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-xs font-medium"><?= htmlspecialchars($item['kategori']) ?></span>
                    <h3 class="mt-3 text-base font-bold text-slate-900"><?= htmlspecialchars($item['nama']) ?></h3>
                    <p class="mt-1 flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?= htmlspecialchars($item['lokasi']) ?>
                    </p>
                    <p class="mt-1 flex items-center gap-2 text-sm text-slate-600">
                        <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        Hilang: <?= htmlspecialchars($item['tanggal']) ?>
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    </section>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>