<?php
require_once __DIR__ . '/../partials/guard.php';

$db = new DBconnection();
$admin = wajibRole(User::ROLE_ADMIN, new User($db));
$model = new Lokasi($db);
$csrf = csrfToken();
$e = [Helper::class, 'e'];
$error = '';
$flash = Helper::pullFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajibCsrf();
    $aksi = is_string($_POST['aksi'] ?? null) ? $_POST['aksi'] : '';
    $nama = is_string($_POST['nama_lokasi'] ?? null) ? $_POST['nama_lokasi'] : '';
    $id = is_string($_POST['id_lokasi'] ?? null) ? trim($_POST['id_lokasi']) : '';
    try {
        if ($aksi === 'buat') {
            $model->create($nama);
            Helper::setFlash('success', 'Lokasi berhasil ditambahkan.');
        } elseif ($aksi === 'ubah') {
            $model->update($id, $nama);
            Helper::setFlash('success', 'Lokasi berhasil diperbarui.');
        } elseif ($aksi === 'hapus') {
            $model->delete($id);
            Helper::setFlash('success', 'Lokasi berhasil dihapus.');
        } else {
            throw new Exception('Aksi tidak valid.');
        }
        Helper::redirect(BASE_URL . '/admin/lokasi.php');
    } catch (Exception $ex) {
        $error = $ex instanceof PDOException ? 'Gagal menyimpan lokasi.' : $ex->getMessage();
    }
}

$rows = (new Lokasi($db))->all();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lokasi - LostFound Kampus</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f8fafc;color:#0f172a;font-family:system-ui,-apple-system,"Segoe UI",sans-serif}
a{color:#2563eb;text-decoration:none}header{background:#fff;border-bottom:1px solid #e2e8f0}
.bar,.wrap{max-width:1000px;margin:auto;padding:16px 20px}.bar{display:flex;justify-content:space-between;gap:14px;align-items:center}
nav{display:flex;gap:14px;flex-wrap:wrap}.card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:18px;margin:16px 0}
form{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin:0 0 8px}
input,button{padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;font:inherit}input{min-width:220px}
button{background:#2563eb;border:0;color:#fff;cursor:pointer}.danger{background:#b91c1c}.alert{padding:10px;margin:12px 0;background:#eff6ff;border-radius:8px}
.error{background:#fef2f2;color:#b91c1c}.muted{color:#64748b;font-size:13px}
li{padding:12px 0;border-bottom:1px solid #e2e8f0;list-style:none}ul{padding:0;margin:0}
</style>
</head>
<body>
<header><div class="bar"><strong>LostFound Kampus · Admin</strong><nav><a href="<?= $e(BASE_URL) ?>/admin/index.php">Dashboard</a><a href="<?= $e(BASE_URL) ?>/admin/laporan.php">Laporan</a><a href="<?= $e(BASE_URL) ?>/admin/kategori.php">Kategori</a><a href="<?= $e(BASE_URL) ?>/admin/user.php">Pengguna</a></nav></div></header>
<main class="wrap">
  <h1>Lokasi</h1>
  <?php if ($flash): ?><div class="alert"><?= $e($flash['pesan']) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert error"><?= $e($error) ?></div><?php endif; ?>
  <section class="card">
    <h2>Tambah lokasi</h2>
    <form method="post"><input type="hidden" name="csrf" value="<?= $e($csrf) ?>"><input type="hidden" name="aksi" value="buat"><input name="nama_lokasi" maxlength="100" required placeholder="Nama lokasi"><button type="submit">Tambah</button></form>
  </section>
  <section class="card">
    <h2>Daftar lokasi</h2>
    <?php if (!$rows): ?><p class="muted">Belum ada lokasi.</p><?php endif; ?>
    <ul><?php foreach ($rows as $row): ?>
      <li>
        <div class="muted"><?= $e($row['id_lokasi']) ?></div>
        <form method="post"><input type="hidden" name="csrf" value="<?= $e($csrf) ?>"><input type="hidden" name="id_lokasi" value="<?= $e($row['id_lokasi']) ?>"><input type="hidden" name="aksi" value="ubah"><input name="nama_lokasi" value="<?= $e($row['nama_lokasi']) ?>" maxlength="100" required><button type="submit">Simpan nama</button></form>
        <form method="post" onsubmit="return confirm('Hapus lokasi ini?')"><input type="hidden" name="csrf" value="<?= $e($csrf) ?>"><input type="hidden" name="id_lokasi" value="<?= $e($row['id_lokasi']) ?>"><input type="hidden" name="aksi" value="hapus"><button class="danger" type="submit">Hapus</button></form>
      </li>
    <?php endforeach; ?></ul>
  </section>
</main>
</body>
</html>