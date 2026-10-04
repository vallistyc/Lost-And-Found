<?php
require_once __DIR__ . '/../partials/guard.php';

$db = new DBconnection();
$user = new User($db);
$admin = wajibRole(User::ROLE_ADMIN, $user);
$csrf = csrfToken();
$status = (new Laporan($db))->countByStatus();
$totalUsers = $user->countAllUsers();
$e = [Helper::class, 'e'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin - LostFound Kampus</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f8fafc;color:#0f172a;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;line-height:1.5}
a{color:#2563eb;text-decoration:none}
header{background:#fff;border-bottom:1px solid #e2e8f0}
.bar,.wrap{max-width:1120px;margin:auto;padding:16px 20px}
.bar{display:flex;align-items:center;justify-content:space-between;gap:16px}
nav{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
button{border:0;border-radius:6px;background:#2563eb;color:#fff;padding:8px 14px;font-weight:600;cursor:pointer}
h1{font-size:24px;margin:8px 0}
.muted{color:#64748b}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin:20px 0}
.card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:18px}
.card strong{display:block;font-size:26px}
form{margin:0}
</style>
</head>
<body>
<header><div class="bar">
  <strong>LostFound Kampus · Admin</strong>
  <nav>
    <a href="<?= $e(BASE_URL) ?>/admin/laporan.php">Laporan</a>
    <a href="<?= $e(BASE_URL) ?>/admin/kategori.php">Kategori</a>
    <a href="<?= $e(BASE_URL) ?>/admin/lokasi.php">Lokasi</a>
    <a href="<?= $e(BASE_URL) ?>/admin/user.php">Pengguna</a>
    <form method="post" action="<?= $e(BASE_URL) ?>/logout.php">
      <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
      <button type="submit">Keluar</button>
    </form>
  </nav>
</div></header>
<main class="wrap">
  <p class="muted">Masuk sebagai <?= $e($admin['nama_user']) ?></p>
  <h1>Dashboard</h1>
  <div class="grid">
    <section class="card"><span>Total pengguna</span><strong><?= $totalUsers ?></strong></section>
    <?php foreach ($status as $namaStatus => $jumlah): ?>
      <section class="card"><span>Laporan <?= $e($namaStatus) ?></span><strong><?= $jumlah ?></strong></section>
    <?php endforeach; ?>
  </div>
</main>
</body>
</html>
