<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login, kalau belum login -> header('Location: login.php'); exit;

$judulHalaman = 'Histori Laporan';
$halamanAktif = 'histori';

// DUMMY: ganti dengan laporan milik user yang login (dari backend)
$semuaLaporan = [
    ['id' => 1, 'nama' => 'Dompet Kulit Coklat', 'kategori' => 'Dompet', 'lokasi' => 'Kantin Pusat, FT', 'tanggal' => '24 Okt 2023', 'foto' => 'dompet.jpg', 'status' => 'Menunggu'],
    ['id' => 2, 'nama' => 'Kunci Motor Honda', 'kategori' => 'Lain-lain', 'lokasi' => 'Parkiran GKB I', 'tanggal' => '23 Okt 2023', 'foto' => 'kunci.jpg', 'status' => 'Dipublikasi'],
    ['id' => 3, 'nama' => 'Tas Ransel Hitam', 'kategori' => 'Tas', 'lokasi' => 'Perpustakaan Pusat', 'tanggal' => '22 Okt 2023', 'foto' => 'tas.jpg', 'status' => 'Ditemukan'],
];

$daftarTab = ['Semua', 'Menunggu', 'Dipublikasi', 'Ditolak', 'Ditemukan'];
$tabAktif  = $_GET['status'] ?? 'Semua';
if (!in_array($tabAktif, $daftarTab, true)) {
    $tabAktif = 'Semua';
}

// Hitung jumlah per status untuk badge angka di tab
$jumlah = ['Semua' => count($semuaLaporan)];
foreach ($daftarTab as $t) {
    if ($t !== 'Semua') {
        $jumlah[$t] = count(array_filter($semuaLaporan, fn($l) => $l['status'] === $t));
    }
}

$laporanTampil = $tabAktif === 'Semua'
    ? $semuaLaporan
    : array_filter($semuaLaporan, fn($l) => $l['status'] === $tabAktif);

$badgeStatus = [
    'Menunggu'    => 'bg-amber-100 text-amber-700',
    'Dipublikasi' => 'bg-blue-100 text-blue-700',
    'Ditolak'     => 'bg-red-100 text-red-700',
    'Ditemukan'   => 'bg-emerald-100 text-emerald-700',
];

require_once __DIR__ . '/partials/header.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 pt-10">

    <nav class="flex items-center gap-2 text-sm text-slate-600">
        <a href="index.php" class="hover:text-blue-600">Beranda</a>
        <span class="text-slate-400">›</span>
        <span class="text-blue-600 font-medium">Histori Laporan</span>
    </nav>

    <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Histori Laporan Saya</h1>
            <p class="mt-2 text-sm text-slate-600">Daftar pelaporan kehilangan &amp; barang temuan yang pernah Anda ajukan.</p>
        </div>
        <a href="buat-laporan.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-lg">Buat Laporan Baru</a>
    </div>

    <!-- Tab filter status -->
    <div class="mt-8 flex overflow-x-auto bg-white border border-slate-200 rounded-lg">
        <?php foreach ($daftarTab as $t): $aktif = ($t === $tabAktif); ?>
            <a href="histori.php?status=<?= urlencode($t) ?>"
               class="shrink-0 px-5 py-3 text-sm flex items-center gap-2 <?= $aktif ? 'text-blue-600 font-semibold border border-blue-600 rounded-lg -m-px bg-white' : 'text-slate-600 hover:text-blue-600' ?>">
                <?= htmlspecialchars($t) ?>
                <span class="text-xs <?= $aktif ? 'text-blue-600' : 'text-slate-500' ?>"><?= $jumlah[$t] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Daftar laporan -->
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl divide-y divide-slate-200 overflow-hidden">
        <?php if (empty($laporanTampil)): ?>
            <p class="text-center text-slate-500 py-16">Belum ada laporan di kategori ini.</p>
        <?php endif; ?>

        <?php foreach ($laporanTampil as $l): ?>
            <div class="flex flex-wrap items-center gap-4 p-5">
                <div class="w-20 h-20 shrink-0 rounded-lg bg-slate-200 overflow-hidden">
                    <img src="assets/img/<?= htmlspecialchars($l['foto']) ?>" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-900"><?= htmlspecialchars($l['nama']) ?></h3>
                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-xs font-medium"><?= htmlspecialchars($l['kategori']) ?></span>
                    </div>
                    <p class="mt-1 flex flex-wrap items-center gap-x-4 text-sm text-slate-600">
                        <span><?= htmlspecialchars($l['lokasi']) ?></span>
                        <span>Hilang: <?= htmlspecialchars($l['tanggal']) ?></span>
                    </p>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeStatus[$l['status']] ?? 'bg-slate-100 text-slate-700' ?>">
                    <?= htmlspecialchars($l['status']) ?>
                </span>

                <div class="flex items-center gap-2 text-sm font-medium">
                    <a href="detail.php?id=<?= (int) $l['id'] ?>&pemilik=1" class="px-4 py-2 rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200">Lihat</a>
                    <?php if ($l['status'] === 'Menunggu'): ?>
                        <a href="edit-laporan.php?id=<?= (int) $l['id'] ?>" class="px-4 py-2 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100">Edit</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>