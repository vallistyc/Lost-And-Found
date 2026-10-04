<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login DAN role admin, kalau bukan -> require 'error-403.php'; exit;

$judulHalaman = 'Kelola User Mahasiswa';
$menuAktif    = 'user';

// DUMMY: ganti dengan data dari backend
$semuaUser = [
    ['id' => 1, 'nama' => 'Andi Wijaya',   'nim' => '21010123', 'email' => 'andi.wijaya@student.ac.id',   'no_hp' => '081234567890', 'role' => 'Mahasiswa', 'aktif' => true,  'terdaftar' => '12 Sep 2021'],
    ['id' => 2, 'nama' => 'Siti Rahma',    'nim' => '21010132', 'email' => 'siti.rahma@student.ac.id',    'no_hp' => '081234567832', 'role' => 'Mahasiswa', 'aktif' => true,  'terdaftar' => '05 Okt 2021'],
    ['id' => 3, 'nama' => 'Rian Pratama',  'nim' => '20010566', 'email' => 'rian.pratama@student.ac.id',  'no_hp' => '081234567566', 'role' => 'Mahasiswa', 'aktif' => false, 'terdaftar' => '18 Agt 2020'],
    ['id' => 4, 'nama' => 'Lisa Lestari',  'nim' => '21010144', 'email' => 'lisa.lestari@student.ac.id',  'no_hp' => '081234567144', 'role' => 'Mahasiswa', 'aktif' => true,  'terdaftar' => '24 Sep 2021'],
    ['id' => 5, 'nama' => 'Dimas Saputra', 'nim' => '22010155', 'email' => 'dimas.saputra@student.ac.id', 'no_hp' => '081234567155', 'role' => 'Mahasiswa', 'aktif' => false, 'terdaftar' => '11 Jul 2022'],
    ['id' => 6, 'nama' => 'Dewi Safitri',  'nim' => '21010166', 'email' => 'dewi.safitri@student.ac.id',  'no_hp' => '081234567766', 'role' => 'Mahasiswa', 'aktif' => true,  'terdaftar' => '02 Okt 2021'],
];
$daftarRole  = ['Mahasiswa', 'Admin'];
$passwordDefault = 'mhs12345'; // hanya untuk teks di modal, reset sebenarnya dilakukan backend

$q    = trim($_GET['q'] ?? '');
$role = $_GET['role'] ?? '';

$users = array_filter($semuaUser, function ($u) use ($q, $role) {
    if ($q !== '' && stripos($u['nama'] . ' ' . $u['nim'] . ' ' . $u['email'], $q) === false) return false;
    if ($role !== '' && $u['role'] !== $role) return false;
    return true;
});

$field = 'h-12 px-4 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';

require_once __DIR__ . '/partials/admin-header.php';
?>

<h1 class="text-3xl font-bold text-slate-900">Kelola User Mahasiswa</h1>
<p class="mt-2 text-sm text-slate-600">Manajemen status akun dan otorisasi akses mahasiswa ke sistem LostFound KAMPUS.</p>

<form method="GET" class="mt-8 flex flex-col md:flex-row gap-4">
    <div class="relative flex-1">
        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari mahasiswa berdasarkan nama, NIM, atau email..." class="<?= $field ?> w-full pl-12">
    </div>
    <select name="role" onchange="this.form.submit()" class="<?= $field ?> md:w-52">
        <option value="">Role: Semua</option>
        <?php foreach ($daftarRole as $r): ?>
            <option value="<?= $r ?>" <?= $role === $r ? 'selected' : '' ?>><?= $r ?></option>
        <?php endforeach; ?>
    </select>
</form>

<section class="mt-6 bg-white border border-slate-200 rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-600 uppercase">
                <tr>
                    <th class="px-4 py-3">No</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">NIM</th>
                    <th class="px-4 py-3">Email</th><th class="px-4 py-3">No HP</th><th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th><th class="px-4 py-3">Terdaftar</th><th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (empty($users)): ?>
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-500">Tidak ada user yang cocok.</td></tr>
                <?php endif; ?>
                <?php $no = 1; foreach ($users as $u): ?>
                    <tr>
                        <td class="px-4 py-4 text-slate-600"><?= $no++ ?></td>
                        <td class="px-4 py-4 font-semibold text-slate-900 whitespace-nowrap"><?= htmlspecialchars($u['nama']) ?></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($u['nim']) ?></td>
                        <td class="px-4 py-4 text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-4 py-4 text-slate-600 whitespace-nowrap"><?= htmlspecialchars($u['no_hp']) ?></td>
                        <td class="px-4 py-4"><span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold"><?= htmlspecialchars($u['role']) ?></span></td>
                        <td class="px-4 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $u['aktif'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $u['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="px-4 py-4 text-slate-600 whitespace-nowrap"><?= htmlspecialchars($u['terdaftar']) ?></td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" onclick="bukaModal(<?= (int) $u['id'] ?>, <?= htmlspecialchars(json_encode($u['nama']), ENT_QUOTES) ?>)"
                                        class="text-xs font-semibold text-blue-600 underline hover:text-blue-800">Reset</button>

                                <!-- Toggle aktif / nonaktif -->
                                <form method="POST" action="admin-toggle-user.php">
                                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                    <input type="hidden" name="aktif" value="<?= $u['aktif'] ? 0 : 1 ?>">
                                    <button type="submit" role="switch" aria-checked="<?= $u['aktif'] ? 'true' : 'false' ?>"
                                            title="<?= $u['aktif'] ? 'Nonaktifkan akun' : 'Aktifkan akun' ?>"
                                            class="relative w-10 h-6 rounded-full transition-colors <?= $u['aktif'] ? 'bg-emerald-500' : 'bg-slate-300' ?>">
                                        <span class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-all <?= $u['aktif'] ? 'left-[18px]' : 'left-0.5' ?>"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal konfirmasi reset password -->
<div id="modal-reset" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4">
    <form method="POST" action="admin-reset-password.php" class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl text-center">
        <input type="hidden" name="id" id="reset-id" value="">
        <span class="mx-auto w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="8" cy="15" r="4"/><path d="m11 12 9-9M16 7l3 3"/></svg>
        </span>
        <h3 class="mt-4 text-lg font-bold text-slate-900">Konfirmasi Reset Password</h3>
        <p class="mt-4 text-sm text-slate-600 leading-relaxed">
            Apakah Anda yakin ingin mereset password user <strong id="reset-nama" class="text-slate-900"></strong>?
            Password akan di-reset menjadi default <strong class="text-slate-900">'<?= htmlspecialchars($passwordDefault) ?>'</strong>.
        </p>
        <div class="mt-6 flex gap-3">
            <button type="button" onclick="tutupModal()" class="flex-1 px-5 py-3 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="submit" class="flex-1 px-5 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-sm font-semibold text-white">Ya, Reset</button>
        </div>
    </form>
</div>

<script>
    const modal = document.getElementById('modal-reset');
    function bukaModal(id, nama) {
        document.getElementById('reset-id').value = id;
        document.getElementById('reset-nama').textContent = nama; // textContent: aman dari XSS
        modal.classList.remove('hidden'); modal.classList.add('flex');
    }
    function tutupModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupModal(); });
</script>

<?php require_once __DIR__ . '/partials/admin-footer.php'; ?>