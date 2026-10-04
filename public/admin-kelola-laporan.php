<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login DAN role admin, kalau bukan -> require 'error-403.php'; exit;

$judulHalaman = 'Kelola Laporan';
$menuAktif    = 'laporan';

// DUMMY: ganti dengan data dari backend (idealnya filter & pagination dilakukan di query DB)
$semua = [
    ['id' => 1, 'nama' => 'Laptop ASUS Silver',   'pelapor' => 'Rahmat Hidayat', 'kategori' => 'Elektronik', 'lokasi' => 'Ruang Kelas FMIPA 302',   'status' => 'Dipublikasi', 'tanggal' => '20 Okt 2023'],
    ['id' => 2, 'nama' => 'Dompet Kulit Coklat',  'pelapor' => 'Andi Wijaya',    'kategori' => 'Aksesoris',  'lokasi' => 'Kantin Pusat, FT',       'status' => 'Menunggu',    'tanggal' => '24 Okt 2023'],
    ['id' => 3, 'nama' => 'Tas Ransel Hitam',     'pelapor' => 'Fajar Santoso',  'kategori' => 'Tas',        'lokasi' => 'Gedung Perpustakaan, Lt. 2', 'status' => 'Ditemukan', 'tanggal' => '22 Okt 2023'],
    ['id' => 4, 'nama' => 'Kunci Motor Honda',    'pelapor' => 'Siti Rahma',     'kategori' => 'Lain-lain',  'lokasi' => 'Parkiran GKB I',         'status' => 'Dipublikasi', 'tanggal' => '23 Okt 2023'],
    ['id' => 5, 'nama' => 'Kacamata Minus',       'pelapor' => 'Dewi Safitri',   'kategori' => 'Aksesoris',  'lokasi' => 'Masjid Kampus, area wudhu', 'status' => 'Ditolak',   'tanggal' => '19 Okt 2023'],
    ['id' => 6, 'nama' => 'Buku Kalkulus',        'pelapor' => 'Rian Pratama',   'kategori' => 'Buku',       'lokasi' => 'Selasar FEB',            'status' => 'Menunggu',    'tanggal' => '18 Okt 2023'],
    ['id' => 7, 'nama' => 'iPhone 13 Pro Max',    'pelapor' => 'Keisha Putri',   'kategori' => 'Elektronik', 'lokasi' => 'Gedung Auditorium',      'status' => 'Take Down',   'tanggal' => '15 Okt 2023'],
    ['id' => 8, 'nama' => 'Botol Minum Thermos',  'pelapor' => 'Dimas Saputra',  'kategori' => 'Aksesoris',  'lokasi' => 'Masjid Kampus',          'status' => 'Menunggu',    'tanggal' => '25 Okt 2023'],
];
$daftarStatus   = ['Dipublikasi', 'Menunggu', 'Ditemukan', 'Ditolak', 'Take Down'];
$daftarKategori = ['Dompet', 'Aksesoris', 'Tas', 'Elektronik', 'Buku', 'Lain-lain'];

$q        = trim($_GET['q'] ?? '');
$status   = $_GET['status'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$halaman  = max(1, (int) ($_GET['page'] ?? 1));
$totalHalaman = 3; // DUMMY: hitung dari jumlah data / per halaman

$laporan = array_filter($semua, function ($l) use ($q, $status, $kategori) {
    if ($q !== '' && stripos($l['nama'] . ' ' . $l['pelapor'], $q) === false) return false;
    if ($status !== '' && $l['status'] !== $status) return false;
    if ($kategori !== '' && $l['kategori'] !== $kategori) return false;
    return true;
});

$badgeStatus = [
    'Dipublikasi' => 'bg-blue-100 text-blue-700',
    'Menunggu'    => 'bg-amber-100 text-amber-700',
    'Ditemukan'   => 'bg-emerald-100 text-emerald-700',
    'Ditolak'     => 'bg-red-100 text-red-700',
    'Take Down'   => 'bg-orange-100 text-orange-700',
];
$field = 'h-12 px-4 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';

require_once __DIR__ . '/partials/admin-header.php';
?>

<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Kelola Laporan</h1>
        <p class="mt-2 text-sm text-slate-600">Manajemen seluruh laporan barang hilang &amp; temuan mahasiswa di lingkungan kampus.</p>
    </div>
    <a href="buat-laporan.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-3 rounded-lg">+ Buat Laporan Baru</a>
</div>

<form method="GET" class="mt-8 flex flex-col lg:flex-row gap-4">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama barang atau pelapor..." class="<?= $field ?> flex-1">
    <select name="status" onchange="this.form.submit()" class="<?= $field ?> lg:w-56">
        <option value="">Filter Status: Semua</option>
        <?php foreach ($daftarStatus as $s): ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
    </select>
    <select name="kategori" onchange="this.form.submit()" class="<?= $field ?> lg:w-56">
        <option value="">Semua Kategori</option>
        <?php foreach ($daftarKategori as $k): ?>
            <option value="<?= $k ?>" <?= $kategori === $k ? 'selected' : '' ?>><?= $k ?></option>
        <?php endforeach; ?>
    </select>
</form>

<section class="mt-6 bg-white border border-slate-200 rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase">
                <tr>
                    <th class="px-4 py-3">No</th><th class="px-4 py-3">Nama Barang</th><th class="px-4 py-3">Pelapor</th>
                    <th class="px-4 py-3">Kategori</th><th class="px-4 py-3">Lokasi</th><th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal</th><th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (empty($laporan)): ?>
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">Tidak ada laporan yang cocok.</td></tr>
                <?php endif; ?>
                <?php $no = 1; foreach ($laporan as $l): ?>
                    <tr>
                        <td class="px-4 py-4 text-slate-600"><?= $no++ ?></td>
                        <td class="px-4 py-4 font-semibold text-slate-900"><?= htmlspecialchars($l['nama']) ?></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($l['pelapor']) ?></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($l['kategori']) ?></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($l['lokasi']) ?></td>
                        <td class="px-4 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap <?= $badgeStatus[$l['status']] ?? 'bg-slate-100 text-slate-700' ?>"><?= htmlspecialchars($l['status']) ?></span></td>
                        <td class="px-4 py-4 text-slate-600 whitespace-nowrap"><?= htmlspecialchars($l['tanggal']) ?></td>
                        <td class="px-4 py-4 text-right"><a href="admin-detail-verifikasi.php?id=<?= (int) $l['id'] ?>" class="px-3 py-2 rounded-md border border-slate-200 text-xs font-semibold text-blue-600 hover:bg-blue-50">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php
    $qs = fn(int $p) => '?' . http_build_query(array_filter(['q' => $q, 'status' => $status, 'kategori' => $kategori, 'page' => $p]));
    $btn = 'w-9 h-9 flex items-center justify-center rounded-lg border text-sm';
    ?>
    <nav class="mt-6 flex items-center justify-center gap-2">
        <a href="<?= $qs(max(1, $halaman - 1)) ?>" class="<?= $btn ?> border-slate-200 text-slate-500 hover:bg-slate-50">‹</a>
        <?php for ($p = 1; $p <= $totalHalaman; $p++): ?>
            <a href="<?= $qs($p) ?>" class="<?= $btn ?> <?= $p === $halaman ? 'bg-blue-600 border-blue-600 text-white font-semibold' : 'border-slate-200 text-slate-700 hover:bg-slate-50' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <a href="<?= $qs(min($totalHalaman, $halaman + 1)) ?>" class="<?= $btn ?> border-slate-200 text-slate-500 hover:bg-slate-50">›</a>
    </nav>
</section>

<?php require_once __DIR__ . '/partials/admin-footer.php'; ?>