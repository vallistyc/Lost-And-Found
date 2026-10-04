<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login DAN role admin, kalau bukan -> require 'error-403.php'; exit;

$id = (int) ($_GET['id'] ?? 0);

$judulHalaman = 'Detail Verifikasi';
$menuAktif    = 'laporan';

// DUMMY: ganti dengan data dari backend berdasarkan $id
$l = [
    'id' => $id, 'nama' => 'Dompet Kulit Coklat', 'status' => 'Menunggu',
    'kategori' => 'Aksesoris & Dompet', 'lokasi' => 'Kantin Pusat, Fakultas Teknik (Dekat Stand Soto)',
    'tanggal' => '24 Oktober 2023 - Pukul 12:30 WIB',
    'deskripsi' => 'Ditemukan sebuah dompet lipat kulit berwarna coklat merk "Eiger". Di dalamnya terdapat kartu identitas mahasiswa (KTM) atas nama Rian Pratama, serta beberapa struk belanja. Uang tunai masih utuh di dalam dompet. Mohon bagi pemilik untuk memverifikasi ke admin.',
    'pelapor' => 'Andi Wijaya (NIM: 21010123)', 'prodi' => 'Fakultas Teknik - Teknik Elektro',
    'foto' => 'dompet.jpg', 'nama_file' => 'dompet_coklat_FT.jpg (1.4 MB)',
];
$tanggapan = [
    ['nama' => 'Budi Santoso', 'waktu' => '2 jam yang lalu', 'isi' => 'Min, itu dompetnya teman saya kayaknya. Tadi dia lagi kelabakan nyariin dompetnya yang hilang tadi siang pas makan soto.'],
    ['nama' => 'Rian Pratama (Pemilik)', 'waktu' => '1 jam yang lalu', 'isi' => 'Ya ampun benar itu dompet saya! Boleh saya ambil ke admin besok pagi jam 9? Saya bawa identitas pendukung lain seperti KTP untuk bukti. Terima kasih banyak orang baik yang sudah menemukan!'],
    ['nama' => 'Admin Kampus (Official)', 'waktu' => '30 menit yang lalu', 'isi' => 'Halo Rian, silakan langsung datang ke Gedung Rektorat Lt. 1 Bagian Kemahasiswaan esok hari pada jam operasional kerja (08.00 - 16.00 WIB) dengan membawa tanda pengenal sah ya.', 'admin' => true],
];

$badgeStatus = [
    'Dipublikasi' => 'bg-blue-100 text-blue-700', 'Menunggu' => 'bg-amber-100 text-amber-700',
    'Ditemukan' => 'bg-emerald-100 text-emerald-700', 'Ditolak' => 'bg-red-100 text-red-700', 'Take Down' => 'bg-orange-100 text-orange-700',
];
$btnAksi = 'px-5 py-3 rounded-lg text-sm font-semibold text-white';

require_once __DIR__ . '/partials/admin-header.php';
?>

<nav class="flex items-center gap-2 text-sm text-slate-500">
    <a href="admin-kelola-laporan.php" class="hover:text-blue-600">Kelola Laporan</a><span>›</span>
    <span class="text-blue-600 font-medium">Detail Verifikasi</span>
</nav>
<h1 class="mt-3 text-3xl font-bold text-slate-900">Detail Verifikasi Laporan</h1>

<div class="mt-8 grid gap-6 xl:grid-cols-5 items-start">

    <!-- Foto -->
    <section class="xl:col-span-2 bg-white border border-slate-200 rounded-2xl p-6">
        <h2 class="font-bold text-slate-900">Foto Barang Laporan</h2>
        <div class="mt-4 aspect-[4/3] rounded-xl bg-slate-200 overflow-hidden">
            <img src="assets/img/<?= htmlspecialchars($l['foto']) ?>" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'">
        </div>
        <p class="mt-4 text-xs text-slate-500">Nama File:</p>
        <p class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($l['nama_file']) ?></p>
    </section>

    <!-- Info + aksi -->
    <section class="xl:col-span-3 bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex items-start justify-between gap-4">
            <h2 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($l['nama']) ?></h2>
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeStatus[$l['status']] ?? '' ?>"><?= htmlspecialchars($l['status']) ?></span>
        </div>

        <dl class="mt-6 grid grid-cols-[140px_1fr] gap-y-4 text-sm">
            <dt class="font-semibold text-slate-600">Kategori</dt><dd class="text-slate-800"><?= htmlspecialchars($l['kategori']) ?></dd>
            <dt class="font-semibold text-slate-600">Lokasi Temuan</dt><dd class="text-slate-800"><?= htmlspecialchars($l['lokasi']) ?></dd>
            <dt class="font-semibold text-slate-600">Tanggal Kejadian</dt><dd class="text-slate-800"><?= htmlspecialchars($l['tanggal']) ?></dd>
            <dt class="font-semibold text-slate-600">Deskripsi</dt><dd class="text-slate-700 leading-relaxed"><?= nl2br(htmlspecialchars($l['deskripsi'])) ?></dd>
        </dl>

        <div class="mt-6 pt-6 border-t border-slate-200">
            <p class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($l['pelapor']) ?></p>
            <p class="text-xs text-slate-500"><?= htmlspecialchars($l['prodi']) ?></p>
        </div>

        <!-- Tombol aksi menyesuaikan status -->
        <div class="mt-6 flex flex-wrap gap-3">
            <?php if ($l['status'] === 'Menunggu'): ?>
                <form method="POST" action="admin-setujui-laporan.php">
                    <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <button type="submit" class="<?= $btnAksi ?> bg-blue-600 hover:bg-blue-700">Setujui &amp; Publikasikan</button>
                </form>
                <button type="button" onclick="bukaModal()" class="<?= $btnAksi ?> bg-red-500 hover:bg-red-600">Tolak</button>
            <?php endif; ?>
            <?php if ($l['status'] === 'Dipublikasi'): ?>
                <form method="POST" action="admin-takedown-laporan.php" onsubmit="return confirm('Turunkan laporan ini dari publik?')">
                    <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <button type="submit" class="<?= $btnAksi ?> bg-orange-600 hover:bg-orange-700">Take Down</button>
                </form>
                <form method="POST" action="admin-tandai-ditemukan.php">
                    <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <button type="submit" class="<?= $btnAksi ?> bg-emerald-700 hover:bg-emerald-800">Tandai Ditemukan</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Tanggapan -->
<section class="mt-6 bg-white border border-slate-200 rounded-2xl p-6">
    <h2 class="font-bold text-slate-900">Tanggapan Laporan (<?= count($tanggapan) ?>)</h2>
    <div class="mt-4 divide-y divide-slate-200">
        <?php foreach ($tanggapan as $t): ?>
            <div class="flex gap-3 py-4">
                <span class="w-10 h-10 shrink-0 rounded-full bg-slate-200 flex items-center justify-center text-sm font-semibold text-slate-600"><?= htmlspecialchars(strtoupper(substr($t['nama'], 0, 1))) ?></span>
                <div class="flex-1">
                    <div class="flex items-center justify-between gap-4">
                        <p class="text-sm font-semibold <?= !empty($t['admin']) ? 'text-blue-600' : 'text-slate-900' ?>"><?= htmlspecialchars($t['nama']) ?></p>
                        <p class="text-xs text-slate-400"><?= htmlspecialchars($t['waktu']) ?></p>
                    </div>
                    <p class="mt-1 text-sm text-slate-600"><?= htmlspecialchars($t['isi']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Modal tolak laporan -->
<div id="modal-tolak" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4">
    <form method="POST" action="admin-tolak-laporan.php" class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl">
        <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Tolak Verifikasi Laporan</h3>
            <button type="button" onclick="tutupModal()" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
        </div>
        <p class="mt-4 text-sm text-slate-600">Anda akan menolak laporan barang ini. Silakan tuliskan alasan penolakan secara jelas untuk diinformasikan kepada mahasiswa pelapor.</p>
        <label for="catatan" class="mt-5 block text-sm font-semibold text-slate-900">Catatan Admin (Alasan Penolakan)</label>
        <textarea id="catatan" name="catatan" rows="4" required class="mt-2 w-full px-4 py-3 border border-red-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
        <div class="mt-5 flex justify-end gap-3">
            <button type="button" onclick="tutupModal()" class="px-5 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-sm font-semibold text-white">Tolak Laporan</button>
        </div>
    </form>
</div>

<script>
    const modal = document.getElementById('modal-tolak');
    function bukaModal()  { modal.classList.remove('hidden'); modal.classList.add('flex'); document.getElementById('catatan').focus(); }
    function tutupModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupModal(); });
</script>

<?php require_once __DIR__ . '/partials/admin-footer.php'; ?>