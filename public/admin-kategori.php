<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login DAN role admin, kalau bukan -> require 'error-403.php'; exit;

$judulHalaman = 'Kelola Kategori';
$menuAktif    = 'kategori';

// DUMMY: ganti dengan data dari backend (jumlah = banyak laporan yang memakai kategori)
$daftarKategori = [
    ['id' => 1, 'nama' => 'Dompet',        'jumlah' => 15],
    ['id' => 2, 'nama' => 'HP/Smartphone', 'jumlah' => 12],
    ['id' => 3, 'nama' => 'Kunci',         'jumlah' => 0],
    ['id' => 4, 'nama' => 'Tas',           'jumlah' => 0],
    ['id' => 5, 'nama' => 'KTM/Dokumen',   'jumlah' => 4],
    ['id' => 6, 'nama' => 'Elektronik',    'jumlah' => 9],
    ['id' => 7, 'nama' => 'Lainnya',       'jumlah' => 7],
];

require_once __DIR__ . '/partials/admin-header.php';
?>

<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Kelola Kategori</h1>
        <p class="mt-2 text-sm text-slate-600">Manajemen kategori barang temuan dan hilang untuk mempermudah pencarian mahasiswa.</p>
    </div>
    <button type="button" onclick="bukaModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-3 rounded-lg">+ Tambah Kategori</button>
</div>

<section class="mt-8 bg-white border border-slate-200 rounded-2xl p-6">
    <table class="w-full text-sm text-left">
        <thead class="text-xs font-semibold text-slate-600 uppercase">
            <tr><th class="px-4 py-3 w-16">No</th><th class="px-4 py-3">Nama Kategori</th><th class="px-4 py-3">Jumlah Laporan</th><th class="px-4 py-3 text-right">Aksi</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            <?php $no = 1; foreach ($daftarKategori as $k): $dipakai = $k['jumlah'] > 0; ?>
                <tr>
                    <td class="px-4 py-4 text-slate-600"><?= $no++ ?></td>
                    <td class="px-4 py-4 font-semibold text-slate-900"><?= htmlspecialchars($k['nama']) ?></td>
                    <td class="px-4 py-4"><span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold"><?= (int) $k['jumlah'] ?> Laporan</span></td>
                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="bukaModal(<?= (int) $k['id'] ?>, <?= htmlspecialchars(json_encode($k['nama']), ENT_QUOTES) ?>)"
                                    class="px-3 py-1.5 rounded-md border border-slate-200 text-xs font-semibold text-blue-600 hover:bg-blue-50">Ubah</button>

                            <?php if ($dipakai): ?>
                                <span title="Tidak bisa dihapus, masih digunakan laporan"
                                      class="px-3 py-1.5 rounded-md border border-slate-200 text-xs font-semibold text-slate-300 cursor-not-allowed">Hapus</span>
                            <?php else: ?>
                                <form method="POST" action="admin-hapus-kategori.php" onsubmit="return confirm('Hapus kategori ini?')">
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

<!-- Modal tambah / ubah kategori -->
<div id="modal-kategori" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4">
    <form method="POST" action="admin-simpan-kategori.php" class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl">
        <input type="hidden" name="id" id="kategori-id" value="">
        <div class="flex items-center justify-between">
            <h3 id="modal-judul" class="text-lg font-bold text-slate-900">Tambah Kategori Baru</h3>
            <button type="button" onclick="tutupModal()" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
        </div>
        <label for="kategori-nama" class="mt-5 block text-sm font-semibold text-slate-900">Nama Kategori</label>
        <input type="text" id="kategori-nama" name="nama" required maxlength="50" placeholder="Masukkan nama kategori baru..."
               class="mt-2 w-full h-12 px-4 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="mt-5 flex justify-end gap-3">
            <button type="button" onclick="tutupModal()" class="px-5 py-2.5 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
</div>

<script>
    const modal = document.getElementById('modal-kategori');
    // bukaModal()          -> mode tambah
    // bukaModal(id, nama)  -> mode ubah
    function bukaModal(id = '', nama = '') {
        document.getElementById('kategori-id').value = id;
        document.getElementById('kategori-nama').value = nama;
        document.getElementById('modal-judul').textContent = id ? 'Ubah Kategori' : 'Tambah Kategori Baru';
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.getElementById('kategori-nama').focus();
    }
    function tutupModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupModal(); });
</script>

<?php require_once __DIR__ . '/partials/admin-footer.php'; ?>