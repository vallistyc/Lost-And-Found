<?php
require_once __DIR__ . '/../partials/guard.php';

$db = new DBconnection();
$admin = wajibRole(User::ROLE_ADMIN, new User($db));
$csrf = csrfToken();
$e = [Helper::class, 'e'];
$statuses = [
    '' => 'Semua status',
    Laporan::STATUS_MENUNGGU => 'Menunggu',
    Laporan::STATUS_DIPUBLIKASI => 'Dipublikasi',
    Laporan::STATUS_DITOLAK => 'Ditolak',
    Laporan::STATUS_DITEMUKAN => 'Ditemukan',
];
$model = new Laporan($db);
$error = '';
$flash = Helper::pullFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajibCsrf();
    $id = is_string($_POST['id_laporan'] ?? null) ? trim($_POST['id_laporan']) : '';
    $aksi = is_string($_POST['aksi'] ?? null) ? $_POST['aksi'] : '';
    try {
        if ($aksi === 'publikasi') {
            $model->publish($id);
            Helper::setFlash('success', 'Laporan dipublikasikan.');
        } elseif ($aksi === 'tolak') {
            $catatan = is_string($_POST['catatan_admin'] ?? null) ? $_POST['catatan_admin'] : '';
            $model->reject($id, $catatan);
            Helper::setFlash('success', 'Laporan ditolak.');
        } else {
            throw new Exception('Aksi tidak valid.');
        }
        Helper::redirect(BASE_URL . '/admin/laporan.php');
    } catch (Exception $ex) {
        $error = $ex instanceof PDOException ? 'Gagal memperbarui laporan.' : $ex->getMessage();
    }
}

$status = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';
if (!array_key_exists($status, $statuses)) {
    $status = '';
}
$q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = $page !== false && $page !== null ? max(1, $page) : 1;
try {
    $result = $model->adminList($status !== '' ? $status : null, $q, $page);
    $reports = $result['data'];
} catch (PDOException $ex) {
    $result = ['page' => 1, 'totalPages' => 1];
    $reports = [];
    $error = 'Gagal memuat laporan. Coba lagi nanti.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kelola Laporan - LostFound Kampus</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f8fafc;color:#0f172a;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;line-height:1.5}
a{color:#2563eb;text-decoration:none}
header{background:#fff;border-bottom:1px solid #e2e8f0}
.bar,.wrap{max-width:1200px;margin:auto;padding:16px 20px}
.bar{display:flex;justify-content:space-between;align-items:center;gap:16px}
nav{display:flex;gap:14px;align-items:center;flex-wrap:wrap}
.card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:18px}
.muted{color:#64748b;font-size:13px}
.alert{padding:10px 14px;margin:12px 0;border-radius:8px;background:#eff6ff;color:#1d4ed8}
.error{background:#fef2f2;color:#b91c1c}
form.filter{display:flex;gap:10px;margin:14px 0}
input,select,textarea,button{font:inherit;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px}
textarea{display:block;min-width:180px;min-height:50px}
button{background:#2563eb;color:white;border:0;cursor:pointer}
table{width:100%;border-collapse:collapse}
th,td{text-align:left;padding:12px 8px;border-bottom:1px solid #e2e8f0;vertical-align:top}
.actions{display:flex;gap:8px;flex-wrap:wrap}
</style>
</head>
<body>
<header><div class="bar">
  <strong>LostFound Kampus · Admin</strong>
  <nav><a href="<?= $e(BASE_URL) ?>/admin/index.php">Dashboard</a><a href="<?= $e(BASE_URL) ?>/admin/kategori.php">Kategori</a><a href="<?= $e(BASE_URL) ?>/admin/lokasi.php">Lokasi</a><a href="<?= $e(BASE_URL) ?>/admin/user.php">Pengguna</a><a href="<?= $e(BASE_URL) ?>/index.php">Beranda</a></nav>
</div></header>
<main class="wrap">
  <h1>Kelola laporan</h1>
  <?php if ($flash): ?><div class="alert"><?= $e($flash['pesan']) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert error"><?= $e($error) ?></div><?php endif; ?>
  <form class="filter" method="get">
    <select name="status">
      <?php foreach ($statuses as $value => $label): ?>
        <option value="<?= $e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= $e($label) ?></option>
      <?php endforeach; ?>
    </select>
    <input name="q" value="<?= $e($q) ?>" placeholder="Cari deskripsi, kategori, atau pelapor">
    <button type="submit">Cari</button>
  </form>
  <section class="card">
    <?php if (!$reports): ?><p class="muted">Belum ada laporan pada filter ini.</p>
    <?php else: ?>
    <table>
      <thead><tr><th>Laporan</th><th>Pelapor</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($reports as $report): ?>
        <tr>
          <td><a href="<?= $e(BASE_URL) ?>/laporan/detail.php?id=<?= rawurlencode($report['id_laporan']) ?>"><?= $e($report['nama_kategori']) ?></a><div class="muted"><?= $e(Helper::potong($report['deskripsi'], 120)) ?></div><div class="muted"><?= $e($report['lokasi']) ?></div></td>
          <td><?= $e($report['pelapor']) ?></td>
          <td><?= $e($report['status_laporan']) ?></td>
          <td><?= $e(Helper::formatTanggal($report['tanggal_hilang'])) ?></td>
          <td>
            <?php if ($report['status_laporan'] === Laporan::STATUS_MENUNGGU): ?>
              <div class="actions">
                <form method="post"><input type="hidden" name="csrf" value="<?= $e($csrf) ?>"><input type="hidden" name="id_laporan" value="<?= $e($report['id_laporan']) ?>"><input type="hidden" name="aksi" value="publikasi"><button type="submit">Publikasikan</button></form>
                <form method="post"><input type="hidden" name="csrf" value="<?= $e($csrf) ?>"><input type="hidden" name="id_laporan" value="<?= $e($report['id_laporan']) ?>"><input type="hidden" name="aksi" value="tolak"><textarea name="catatan_admin" maxlength="255" required placeholder="Catatan penolakan"></textarea><button type="submit">Tolak</button></form>
              </div>
            <?php else: ?><span class="muted">-</span><?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>
  <?php if ($result['totalPages'] > 1): ?>
    <p>Halaman <?= (int) $result['page'] ?> dari <?= (int) $result['totalPages'] ?></p>
    <?php if ($result['page'] > 1): ?><a href="?status=<?= rawurlencode($status) ?>&amp;q=<?= rawurlencode($q) ?>&amp;page=<?= (int) $result['page'] - 1 ?>">Sebelumnya</a><?php endif; ?>
    <?php if ($result['page'] < $result['totalPages']): ?><a href="?status=<?= rawurlencode($status) ?>&amp;q=<?= rawurlencode($q) ?>&amp;page=<?= (int) $result['page'] + 1 ?>">Berikutnya</a><?php endif; ?>
  <?php endif; ?>
</main>
</body>
</html>