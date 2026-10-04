<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login DAN role admin, kalau bukan -> require 'error-403.php'; exit;

$judulHalaman = 'Kelola Lokasi';
$menuAktif    = 'lokasi';

// DUMMY: ganti dengan data dari backend (jumlah = banyak laporan yang memakai lokasi)
$daftarLokasi = [
    ['id' => 1, 'nama' => 'Gedung A (Rektorat)',  'jumlah' => 20],
    ['id' => 2, 'nama' => 'Perpustakaan Pusat',   'jumlah' => 15],
    ['id' => 3, 'nama' => 'Kantin Utama',         'jumlah' => 0],
    ['id' => 4, 'nama' => 'Parkiran GKB I',       'jumlah' => 9],
    ['id' => 5, 'nama' => 'Masjid Kampus',        'jumlah' => 6],
];

require_once __DIR__ . '/partials/admin-header.php';
?>

<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Kelola Lokasi</h1>
        <p class="mt-2 text-sm text-slate-600">Manajemen titik penemuan dan kehilangan barang di seluruh area universitas.</p>
    </div>
    <button type="button" onclick="bukaModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-3 rounded-lg">+ Tambah Lokasi</button>
</div>

<section class="mt-8 bg-white border border-slate-200 rounded-2xl p-6">
    <table class="w-full text-sm text-left">
        <thead class="text-xs font-semibold text-slate-600 uppercase">
            <tr><th class="px-4 py-3 w-16">No</th><th class="px-4 py-3">Nama Lokasi</th><th class="px-4 py-3">Jumlah Laporan</th><th class="px-4 py-3 text-right">Aksi</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            <?php $no = 1; foreach ($daftarLokasi as $k): $dipakai = $k['jumlah'] > 0; ?>
                <tr>
                    <td class="px-4 py-4 text-slate-600"><?= $no++ ?></td>
                    <td class="px-4 py-4 font-semibold text-slate-900"><?= htmlspecialchars($k['nama']) ?></td>
                    <td class="px-4 py-4"><span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold"><?= (int) $k['jumlah'] ?> Laporan</span></td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="bukaModal(<?= (int) $k['id'] ?>, <?= htmlspecialchars(json_encode($k['nama']), ENT_QUOTES) ?>)"
                                    class="px-3 py-1.5 rounded-md border border-slate-200 text-xs font-semibold text-blue-600 hover:bg-blue-50">Ubah</button>

                            <?php if ($dipakai): ?>
                                <span title="Tidak bisa dihapus, masih digunakan laporan"
                                      class="px-3 py-1.5 rounded-md border border-slate-200 text-xs font-semibold text-slate-300 cursor-not-allowed">Hapus</span>
                            <?php else: ?>
                                <form method="POST" action="admin-hapus-lokasi.php" onsubmit="return confirm('Hapus lokasi ini?')">
                                    <input type="hidden" name="id" value="<?= (int) $k['id'] ?>">
                                    <button type="submit" class="px-3 py-1.5 rounded-md border border-red-300 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!-- Modal tambah / ubah lokasi -->
<div id="modal-lokasi" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4">
    <form method="POST" action="admin-simpan-lokasi.php" class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl">
        <input type="hidden" name="id" id="lokasi-id" value="">
        <div class="flex items-center justify-between">
            <h3 id="modal-judul" class="text-lg font-bold text-slate-900">Tambah Lokasi Baru</h3>
            <button type="button" onclick="tutupModal()" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
        </div>
        <label for="lokasi-nama" class="mt-5 block text-sm font-semibold text-slate-900">Nama Lokasi / Ruangan</label>
        <input type="text" id="lokasi-nama" name="nama" required maxlength="100" placeholder="Contoh: Lab Kimia GKB III, Lt. 2"
               class="mt-2 w-full h-12 px-4 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="mt-5 flex justify-end gap-3">
            <button type="button" onclick="tutupModal()" class="px-5 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
</div>

<script>
    const modal = document.getElementById('modal-lokasi');
    // bukaModal()          -> mode tambah
    // bukaModal(id, nama)  -> mode ubah
    function bukaModal(id = '', nama = '') {
        document.getElementById('lokasi-id').value = id;
        document.getElementById('lokasi-nama').value = nama;
        document.getElementById('modal-judul').textContent = id ? 'Ubah Lokasi' : 'Tambah Lokasi Baru';
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.getElementById('lokasi-nama').focus();
    }
    function tutupModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupModal(); });
</script>

<?php require_once __DIR__ . '/partials/admin-footer.php'; ?>