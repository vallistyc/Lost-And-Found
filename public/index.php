<<<<<<< HEAD
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
=======
<?php
require_once __DIR__ . '/partials/guard.php';
$db = new DBconnection();
$user = new User($db);
$currentUser = wajibLogin($user);
if ($currentUser['role'] === User::ROLE_ADMIN) {
    Helper::redirect(BASE_URL . '/admin/index.php');
}
$csrf = csrfToken();

$active = 'index';
$e = [Helper::class, 'e'];
$flash = Helper::pullFlash();

$q = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';

$items = [];
$error = '';
try {
    $items = (new Laporan($db))->feed((string) $currentUser['nim'], ['q' => $q])['data'];
} catch (PDOException $ex) {
    $error = 'Gagal memuat data. Coba lagi nanti.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Beranda LostAndFound Kampus</title>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;background:#f8fafc;color:#0f172a;line-height:1.5}
a{color:#2563eb;text-decoration:none}
.topbar{background:#fff;border-bottom:1px solid #e2e8f0}
.topbar-in{max-width:1120px;margin:0 auto;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.brand{display:flex;align-items:center;gap:8px;font-weight:700;color:#0f172a;font-size:15px}
.logo{width:28px;height:28px;border-radius:8px;background:#dbeafe;color:#2563eb;display:inline-flex;align-items:center;justify-content:center}
.links{display:flex;align-items:center;gap:20px;font-size:14px;flex-wrap:wrap}
.links a{color:#475569;font-weight:500}.links a.on{color:#2563eb}
.links form{margin:0}
.btn-logout{background:#2563eb;color:#fff;border:0;border-radius:6px;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer}
.wrap{max-width:1120px;margin:0 auto;padding:24px 20px}
.card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px}
.alert{padding:10px 14px;border-radius:8px;font-size:14px;margin-bottom:14px;border:1px solid}
.alert.err{background:#fef2f2;color:#b91c1c;border-color:#fecaca}
.alert.ok{background:#f0fdf4;color:#15803d;border-color:#bbf7d0}
.alert.info{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe}
label{display:block;font-size:13px;font-weight:600;margin:14px 0 6px}
.in{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;background:#fff;font-family:inherit}
.in:focus{outline:2px solid #bfdbfe;border-color:#2563eb}
.in[readonly]{background:#f1f5f9;color:#64748b}
.btn{display:block;width:100%;margin-top:18px;padding:11px;border:0;border-radius:8px;background:#2563eb;color:#fff;font-size:14px;font-weight:600;cursor:pointer;text-align:center;font-family:inherit}
.btn.dark{background:#0f172a}
.badge{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600;background:#e2e8f0;color:#475569}
.b-menunggu{background:#fef3c7;color:#b45309}.b-dipublikasi{background:#dbeafe;color:#1d4ed8}
.b-ditolak{background:#fee2e2;color:#b91c1c}.b-ditemukan{background:#dcfce7;color:#15803d}
.muted{color:#64748b;font-size:13px}
footer.ft{border-top:1px solid #e2e8f0;background:#fff;margin-top:40px;padding:18px 20px;text-align:center;font-size:12px;color:#64748b}
.authbg{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:radial-gradient(circle at 15% 20%,#38bdf8 0,transparent 45%),radial-gradient(circle at 85% 25%,#fbbf24 0,transparent 30%),linear-gradient(135deg,#0c4a6e,#0e7490 50%,#1e3a8a)}
.authcard{width:100%;max-width:380px;background:#fff;border-radius:16px;padding:28px;box-shadow:0 20px 50px rgba(0,0,0,.25)}
.authhead{text-align:center;margin-bottom:6px}.authhead h1{font-size:18px;margin:10px 0 2px}.authhead p{font-size:12px;color:#64748b;margin:0}
.authfoot{text-align:center;margin:18px 0 0;font-size:13px;color:#64748b}
@media(max-width:600px){.authbg{background:#fff;align-items:flex-start;padding:16px}.authcard{box-shadow:none;padding:16px 4px}.links{gap:12px}}
.banner{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-radius:14px;padding:28px}
.banner h1{margin:0 0 4px;font-size:22px}.banner p{margin:0 0 16px;font-size:14px;opacity:.9}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;margin-top:20px}
.item{display:block;background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;color:inherit}
.item .ph{height:160px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:13px}
.item img{width:100%;height:160px;object-fit:cover;display:block}
.item .bd{padding:14px}.item h3{margin:6px 0 4px;font-size:15px}
.sec{margin:24px 0 0;font-size:16px}
</style>
</head>
<body>
<header class="topbar">
  <div class="topbar-in">
    <a class="brand" href="<?= $e(BASE_URL) ?>/index.php"><span class="logo"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span> LostFound KAMPUS</a>
    <nav class="links">
      <a href="<?= $e(BASE_URL) ?>/index.php" class="<?= $active === 'index' ? 'on' : '' ?>">Beranda</a>
      <a href="<?= $e(BASE_URL) ?>/histori.php" class="<?= $active === 'histori' ? 'on' : '' ?>">Histori Laporan</a>
      <a href="<?= $e(BASE_URL) ?>/profil.php" class="<?= $active === 'profil' ? 'on' : '' ?>">Profil</a>
      <form method="post" action="<?= $e(BASE_URL) ?>/logout.php">
        <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
        <button type="submit" class="btn-logout">Logout</button>
      </form>
    </nav>
  </div>
</header>
<div class="wrap">
  <?php if ($flash): ?><div class="alert <?= $flash['tipe'] === 'success' ? 'ok' : ($flash['tipe'] === 'danger' ? 'err' : 'info') ?>"><?= $e($flash['pesan']) ?></div><?php endif; ?>

  <section class="banner">
    <h1>Kehilangan Barang di Kampus?</h1>
    <p>Cari barang kamu atau bantu kembalikan ke pemiliknya.</p>
    <a class="btn" href="<?= $e(BASE_URL) ?>/laporan/buat.php">Buat Laporan</a>
    <form method="get">
      <input class="in" type="search" name="q" value="<?= $e($q) ?>" placeholder="Cari barang, kategori, atau deskripsi laporan...">
    </form>
  </section>

  <h2 class="sec">Laporan Terbaru</h2>
  <?php if ($error): ?><div class="alert err"><?= $e($error) ?></div>
  <?php elseif (!$items): ?><div class="alert info" style="margin-top:14px">Belum ada laporan yang dipublikasikan<?= $q !== '' ? ' untuk pencarian ini' : '' ?>.</div>
  <?php else: ?>
  <div class="grid">
    <?php foreach ($items as $it): ?>
    <a class="item" href="<?= $e(BASE_URL) ?>/laporan/detail.php?id=<?= rawurlencode($it['id_laporan']) ?>">
      <img src="<?= $e(Upload::url($it['foto'])) ?>" alt="">
      <div class="bd">
        <span class="badge b-dipublikasi">Dipublikasi</span>
        <h3><?= $e($it['nama_kategori']) ?></h3>
        <div class="muted">Kategori: <?= $e($it['kategori']) ?> · Lokasi: <?= $e($it['lokasi']) ?></div>
        <div class="muted">Hilang: <?= $e($it['tanggal_hilang']) ?></div>
        <div class="muted"><?= $e(mb_strimwidth((string) $it['deskripsi'], 0, 80, '…')) ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<footer class="ft">&copy; <?= date('Y') ?> LostFound KAMPUS · Sistem Pelaporan Barang Hilang &amp; Temuan Mahasiswa</footer>
</body>
</html>
>>>>>>> 3cec7eba310a22a173a73022272c09a60d83023f
