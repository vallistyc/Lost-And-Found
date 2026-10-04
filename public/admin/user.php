<?php
require_once __DIR__ . '/../partials/guard.php';

$db = new DBconnection();
$model = new User($db);
$admin = wajibRole(User::ROLE_ADMIN, $model);
$csrf = csrfToken();
$e = [Helper::class, 'e'];
$error = '';
$flash = Helper::pullFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajibCsrf();
    $nim = is_string($_POST['nim'] ?? null) ? trim($_POST['nim']) : '';
    try {
        $model->setUserActive($nim, ($_POST['is_active'] ?? '') === '1', $admin['nim']);
        Helper::setFlash('success', 'Status akun diperbarui.');
        Helper::redirect(BASE_URL . '/admin/user.php');
    } catch (Exception $ex) {
        $error = $ex instanceof PDOException ? 'Gagal memperbarui status akun.' : $ex->getMessage();
    }
}

$keyword = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = $page !== false && $page !== null ? max(1, $page) : 1;
$result = $model->listAllUsers($keyword, $page);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengguna - LostFound Kampus</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f8fafc;color:#0f172a;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;line-height:1.5}
a{color:#2563eb;text-decoration:none}header{background:#fff;border-bottom:1px solid #e2e8f0}
.bar,.wrap{max-width:1120px;margin:auto;padding:16px 20px}.bar{display:flex;justify-content:space-between;gap:14px;align-items:center}
nav{display:flex;gap:14px;flex-wrap:wrap}.card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:18px}
.alert{padding:10px;margin:12px 0;background:#eff6ff;border-radius:8px}.error{background:#fef2f2;color:#b91c1c}
form{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin:0}
input,button{padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;font:inherit}button{background:#2563eb;border:0;color:#fff;cursor:pointer}
.muted{color:#64748b;font-size:13px}table{width:100%;border-collapse:collapse;margin-top:14px}
th,td{text-align:left;padding:12px 8px;border-bottom:1px solid #e2e8f0;vertical-align:top}
</style>
</head>
<body>
<header><div class="bar"><strong>LostFound Kampus · Admin</strong><nav><a href="<?= $e(BASE_URL) ?>/admin/index.php">Dashboard</a><a href="<?= $e(BASE_URL) ?>/admin/laporan.php">Laporan</a><a href="<?= $e(BASE_URL) ?>/admin/kategori.php">Kategori</a><a href="<?= $e(BASE_URL) ?>/admin/lokasi.php">Lokasi</a></nav></div></header>
<main class="wrap">
  <h1>Pengguna</h1>
  <?php if ($flash): ?><div class="alert"><?= $e($flash['pesan']) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert error"><?= $e($error) ?></div><?php endif; ?>
  <section class="card">
    <form method="get"><input name="q" value="<?= $e($keyword) ?>" placeholder="Cari nama, NIM, atau nomor HP"><button type="submit">Cari</button></form>
    <?php if (!$result['data']): ?><p class="muted">Pengguna tidak ditemukan.</p>
    <?php else: ?>
    <table>
      <thead><tr><th>Nama</th><th>NIM</th><th>No. HP</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody><?php foreach ($result['data'] as $row): ?>
        <tr>
          <td><?= $e($row['nama_user']) ?></td><td><?= $e($row['nim']) ?></td><td><?= $e($row['no_hp']) ?></td>
          <td><?= $e($row['role']) ?></td><td><?= (int) $row['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></td>
          <td><?php if ($row['nim'] !== $admin['nim']): ?>
            <form method="post"><input type="hidden" name="csrf" value="<?= $e($csrf) ?>"><input type="hidden" name="nim" value="<?= $e($row['nim']) ?>"><input type="hidden" name="is_active" value="<?= (int) $row['is_active'] === 1 ? '0' : '1' ?>"><button type="submit"><?= (int) $row['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
          <?php else: ?><span class="muted">Akun Anda</span><?php endif; ?></td>
        </tr>
      <?php endforeach; ?></tbody>
    </table>
    <?php endif; ?>
    <?php if ($result['total_pages'] > 1): ?>
      <p class="muted">Halaman <?= (int) $result['page'] ?> dari <?= (int) $result['total_pages'] ?></p>
      <?php if ($result['page'] > 1): ?><a href="?q=<?= rawurlencode($keyword) ?>&amp;page=<?= (int) $result['page'] - 1 ?>">Sebelumnya</a><?php endif; ?>
      <?php if ($result['page'] < $result['total_pages']): ?><a href="?q=<?= rawurlencode($keyword) ?>&amp;page=<?= (int) $result['page'] + 1 ?>">Berikutnya</a><?php endif; ?>
    <?php endif; ?>
  </section>
</main>
</body>
</html>