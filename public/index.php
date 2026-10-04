<?php
require_once __DIR__ . '/../class/dbconnection.php';
require_once __DIR__ . '/../class/user.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }

$active = 'index';
$e = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$q = (isset($_GET['q']) && is_string($_GET['q'])) ? trim($_GET['q']) : '';

$sql = 'SELECT nama_barang, deskripsi, tanggal_hilang, foto FROM laporan WHERE status = ?';
$params = ['dipublikasi'];
if ($q !== '') {
    $sql .= ' AND nama_barang LIKE ?';
    $params[] = '%' . $q . '%';
}
$sql .= ' ORDER BY tanggal_hilang DESC LIMIT 12';

$items = [];
$error = '';
try {
    $items = (new DBconnection())->fetchAll($sql, $params);
} catch (PDOException $ex) {
    $error = 'Gagal memuat data. Coba lagi nanti.';
} catch (Exception $ex) {
    $items = [];
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
.item{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden}
.item .ph{height:160px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:13px}
.item img{width:100%;height:160px;object-fit:cover;display:block}
.item .bd{padding:14px}.item h3{margin:6px 0 4px;font-size:15px}
.sec{margin:24px 0 0;font-size:16px}
</style>
</head>
<body>
<header class="topbar">
  <div class="topbar-in">
    <a class="brand" href="index.php"><span class="logo"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span> LostFound KAMPUS</a>
    <nav class="links">
      <a href="index.php" class="<?= $active === 'index' ? 'on' : '' ?>">Beranda</a>
      <a href="histori.php" class="<?= $active === 'histori' ? 'on' : '' ?>">Histori Laporan</a>
      <a href="profil.php" class="<?= $active === 'profil' ? 'on' : '' ?>">Profil</a>
      <form method="post" action="logout.php">
        <input type="hidden" name="csrf" value="<?= $e($_SESSION['csrf']) ?>">
        <button type="submit" class="btn-logout">Logout</button>
      </form>
    </nav>
  </div>
</header>
<div class="wrap">
  <?php if ($flash): ?><div class="alert <?= $e($flash['type']) ?>"><?= $e($flash['msg']) ?></div><?php endif; ?>

  <section class="banner">
    <h1>Kehilangan Barang di Kampus?</h1>
    <p>Cari barang kamu atau bantu kembalikan ke pemiliknya.</p>
    <form method="get">
      <input class="in" type="search" name="q" value="<?= $e($q) ?>" placeholder="Cari nama barang yang hilang...">
    </form>
  </section>

  <h2 class="sec">Laporan Terbaru</h2>
  <?php if ($error): ?><div class="alert err"><?= $e($error) ?></div>
  <?php elseif (!$items): ?><div class="alert info" style="margin-top:14px">Belum ada laporan yang dipublikasikan<?= $q !== '' ? ' untuk pencarian ini' : '' ?>.</div>
  <?php else: ?>
  <div class="grid">
    <?php foreach ($items as $it): ?>
    <article class="item">
      <?php if ($it['foto']): ?>
        <img src="uploads/<?= $e(rawurlencode($it['foto'])) ?>" alt="<?= $e($it['nama_barang']) ?>">
      <?php else: ?><div class="ph">Tanpa foto</div><?php endif; ?>
      <div class="bd">
        <span class="badge b-dipublikasi">Dipublikasi</span>
        <h3><?= $e($it['nama_barang']) ?></h3>
        <div class="muted">Hilang: <?= $e($it['tanggal_hilang']) ?></div>
        <div class="muted"><?= $e(mb_strimwidth((string) $it['deskripsi'], 0, 80, '…')) ?></div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<footer class="ft">&copy; <?= date('Y') ?> LostFound KAMPUS · Sistem Pelaporan Barang Hilang &amp; Temuan Mahasiswa</footer>
</body>
</html>
