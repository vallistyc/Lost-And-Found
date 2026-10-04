<?php
require_once __DIR__ . '/partials/guard.php';
$db = new DBconnection();
$user = new User($db);
$u = wajibRole(User::ROLE_MAHASISWA, $user);

$active = 'histori';
$e = [Helper::class, 'e'];
$csrf = csrfToken();
$flash = Helper::pullFlash();

$tabs = ['semua' => 'Semua', 'menunggu' => 'Menunggu', 'dipublikasi' => 'Dipublikasi', 'ditolak' => 'Ditolak', 'ditemukan' => 'Ditemukan'];
$tab = (isset($_GET['status']) && is_string($_GET['status']) && isset($tabs[$_GET['status']])) ? $_GET['status'] : 'semua';

$all = [];
$error = '';
try {
    $all = (new Laporan($db))->byUser($u['nim']);
} catch (PDOException $ex) {
    $error = 'Gagal memuat histori. Coba lagi nanti.';
}

$count = array_fill_keys(array_keys($tabs), 0);
$count['semua'] = count($all);
foreach ($all as $r) { if (isset($count[$r['status_laporan']])) { $count[$r['status_laporan']]++; } }
$rows = array_values(array_filter($all, fn($r) => $tab === 'semua' || $r['status_laporan'] === $tab));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Histori Laporan - LostFound KAMPUS</title>
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
@media(max-width:600px){.authbg{background:#fff;align-items:flex-start;padding:16px}.authcard{box-shadow:none;padding:16px 4px}.links{gap:12px}}
.head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.head h1{margin:0;font-size:22px}.head p{margin:2px 0 0}
.btn-sm{display:inline-block;background:#2563eb;color:#fff;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600}
.tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
.tab{padding:6px 12px;border-radius:99px;border:1px solid #e2e8f0;background:#fff;color:#475569;font-size:13px;font-weight:500}
.tab.on{background:#2563eb;border-color:#2563eb;color:#fff}
.row{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f1f5f9;color:inherit}
.row:last-child{border-bottom:0}
.row .th{width:56px;height:56px;border-radius:8px;background:#e2e8f0;object-fit:cover;flex:none}
.row .tx{flex:1;min-width:0}.row .tx strong{display:block;font-size:14px}
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
  <div class="head">
    <div><h1>Histori Laporan Saya</h1><p class="muted">Pantau status pelaporan Anda</p></div>
    <a class="btn-sm" href="<?= $e(BASE_URL) ?>/laporan/buat.php">Buat Laporan Baru</a>
  </div>

  <div class="tabs">
    <?php foreach ($tabs as $k => $label): ?>
      <a class="tab <?= $tab === $k ? 'on' : '' ?>" href="<?= $e(BASE_URL) ?>/histori.php?status=<?= $e($k) ?>"><?= $e($label) ?> (<?= (int) $count[$k] ?>)</a>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <?php if ($error): ?><div class="alert err"><?= $e($error) ?></div>
    <?php elseif (!$rows): ?><div class="alert info" style="margin:0">Belum ada laporan<?= $tab !== 'semua' ? ' dengan status ini' : '' ?>.</div>
    <?php else: foreach ($rows as $r): ?>
      <a class="row" href="<?= $e(BASE_URL) ?>/laporan/detail.php?id=<?= rawurlencode($r['id_laporan']) ?>">
        <img class="th" src="<?= $e(Upload::url($r['foto'])) ?>" alt="">
        <div class="tx"><strong><?= $e($r['nama_kategori']) ?></strong><span class="muted">Hilang: <?= $e($r['tanggal_hilang']) ?></span></div>
        <span class="badge b-<?= $e($r['status_laporan']) ?>"><?= $e(ucfirst($r['status_laporan'])) ?></span>
      </a>
    <?php endforeach; endif; ?>
  </div>
</div>
<footer class="ft">&copy; <?= date('Y') ?> LostFound KAMPUS · Sistem Pelaporan Barang Hilang &amp; Temuan Mahasiswa</footer>
</body>
</html>
