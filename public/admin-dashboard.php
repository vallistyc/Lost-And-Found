<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login DAN role admin.
// Kalau bukan admin: require __DIR__ . '/error-403.php'; exit;

$judulHalaman = 'Dashboard';
$menuAktif    = 'dashboard';

// DUMMY: ganti dengan data dari backend
$statistik = [
    ['label' => 'Menunggu Verifikasi', 'angka' => 12,  'warna' => 'bg-amber-50 text-amber-600',    'ikon' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
    ['label' => 'Laporan Dipublikasi', 'angka' => 45,  'warna' => 'bg-blue-50 text-blue-600',      'ikon' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>'],
    ['label' => 'Laporan Ditolak',     'angka' => 8,   'warna' => 'bg-red-50 text-red-600',        'ikon' => '<circle cx="12" cy="12" r="9"/><path d="m15 9-6 6M9 9l6 6"/>'],
    ['label' => 'Selesai Ditemukan',   'angka' => 23,  'warna' => 'bg-emerald-50 text-emerald-600', 'ikon' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>'],
    ['label' => 'Total Mahasiswa',     'angka' => 156, 'warna' => 'bg-slate-100 text-slate-600',   'ikon' => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M17 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6"/>'],
];

$laporanMenunggu = [
    ['id' => 1, 'nama' => 'Dompet Kulit Coklat',    'pelapor' => 'Andi Wijaya',   'kategori' => 'Aksesoris', 'lokasi' => 'Kantin Pusat, FT',      'tanggal' => '24 Okt 2023'],
    ['id' => 2, 'nama' => 'Kunci Motor Honda',      'pelapor' => 'Siti Rahma',    'kategori' => 'Lain-lain', 'lokasi' => 'Parkiran GKB I',        'tanggal' => '23 Okt 2023'],
    ['id' => 3, 'nama' => 'Buku Kalkulus',          'pelapor' => 'Rian Pratama',  'kategori' => 'Buku',      'lokasi' => 'Selasar FEB',           'tanggal' => '18 Okt 2023'],
    ['id' => 4, 'nama' => 'Flashdisk Kingston 64GB', 'pelapor' => 'Lisa Lestari',  'kategori' => 'Elektronik', 'lokasi' => 'Lab Komputer, Fasilkom', 'tanggal' => '24 Okt 2023'],
    ['id' => 5, 'nama' => 'Botol Minum Thermos',    'pelapor' => 'Dimas Saputra', 'kategori' => 'Aksesoris', 'lokasi' => 'Masjid Kampus',         'tanggal' => '25 Okt 2023'],
];

require_once __DIR__ . '/partials/admin-header.php';
?>

<h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
<p class="mt-2 text-sm text-slate-600">Selamat datang kembali, berikut ikhtisar sistem pelaporan barang hilang &amp; temuan hari ini.</p>

<!-- Kartu statistik -->
<section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <?php foreach ($statistik as $s): ?>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="flex items-start justify-between gap-2">
                <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($s['label']) ?></p>
                <span class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center <?= $s['warna'] ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><?= $s['ikon'] ?></svg>
                </span>
            </div>
            <p class="mt-4 text-3xl font-bold text-slate-900"><?= (int) $s['angka'] ?></p>
        </div>
    <?php endforeach; ?>
</section>

<!-- Tabel laporan menunggu -->
<section class="mt-8 bg-white border border-slate-200 rounded-2xl p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-slate-900">Laporan Menunggu Verifikasi Terbaru</h2>
        <a href="admin-kelola-laporan.php?status=Menunggu" class="px-4 py-2 rounded-lg border border-blue-600 text-sm font-semibold text-blue-600 hover:bg-blue-50">Lihat Semua</a>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase">
                <tr>
                    <th class="px-4 py-3">Nama Barang</th>
                    <th class="px-4 py-3">Pelapor</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Tanggal Hilang</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (empty($laporanMenunggu)): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Tidak ada laporan yang menunggu verifikasi.</td></tr>
                <?php endif; ?>
                <?php foreach ($laporanMenunggu as $l): ?>
                    <tr>
                        <td class="px-4 py-4 font-semibold text-slate-900"><?= htmlspecialchars($l['nama']) ?></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($l['pelapor']) ?></td>
                        <td class="px-4 py-4"><span class="px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-xs font-medium"><?= htmlspecialchars($l['kategori']) ?></span></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($l['lokasi']) ?></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($l['tanggal']) ?></td>
                        <td class="px-4 py-4 text-right">
                            <a href="admin-detail-verifikasi.php?id=<?= (int) $l['id'] ?>" class="inline-block px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-xs font-semibold text-white">Verifikasi</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/partials/admin-footer.php'; ?>