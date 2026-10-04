<?php
require_once __DIR__ . '/../partials/guard.php';

$db = new DBconnection();
$user = new User($db);
$viewer = wajibLogin($user);
$csrf = csrfToken();
$e = [Helper::class, 'e'];
$flash = Helper::pullFlash();
$id = is_string($_GET['id'] ?? null) ? trim($_GET['id']) : '';
$laporanModel = new Laporan($db);
$kategori = (new Kategori($db))->all();
$lokasi = (new Lokasi($db))->all();
$postString = static fn(string $field): string => is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
$error = '';
$laporan = null;

try {
    $laporan = $laporanModel->find($id);
    if (!$laporanModel->canView($laporan, $viewer['nim'], $viewer['role'] === User::ROLE_ADMIN)) {
        http_response_code(404);
        $laporan = null;
    }
} catch (PDOException $ex) {
    http_response_code(500);
    $error = 'Gagal memuat laporan. Coba lagi nanti.';
} catch (Exception $ex) {
    http_response_code(404);
}

if ($laporan !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    wajibCsrf();
    $fotoBaru = null;
    try {
        $aksi = $postString('aksi');
        if ($aksi === 'ditemukan') {
            $laporanModel->markFound($id, $viewer['nim']);
            Helper::setFlash('success', 'Laporan ditandai sebagai ditemukan.');
            Helper::redirect(BASE_URL . '/laporan/detail.php?id=' . rawurlencode($id));
        } elseif ($aksi === 'ubah') {
            $fileFoto = is_array($_FILES['foto'] ?? null) ? $_FILES['foto'] : [];
            $fotoBaru = Upload::simpanFoto($fileFoto);
            $fotoLama = $laporanModel->update($id, $viewer['nim'], [
                'nama_kategori' => $postString('nama_kategori'),
                'id_kategori' => $postString('id_kategori'),
                'id_lokasi' => $postString('id_lokasi'),
                'tanggal_hilang' => $postString('tanggal_hilang'),
                'deskripsi' => $postString('deskripsi'),
            ], $fotoBaru);
            if ($fotoLama !== null) {
                Upload::hapusFoto($fotoLama);
            }
            Helper::setFlash('success', 'Laporan berhasil diperbarui dan dikirim ulang.');
            Helper::redirect(BASE_URL . '/laporan/detail.php?id=' . rawurlencode($id));
        } elseif ($aksi === 'hapus') {
            $fotoLama = $laporanModel->delete($id, $viewer['nim']);
            Upload::hapusFoto($fotoLama);
            Helper::setFlash('success', 'Laporan berhasil dihapus.');
            Helper::redirect(BASE_URL . '/histori.php');
        } else {
            throw new Exception('Aksi tidak valid.');
        }
    } catch (PDOException $ex) {
        if ($fotoBaru !== null) {
            Upload::hapusFoto($fotoBaru);
        }
        $error = 'Gagal memproses perubahan laporan. Coba lagi nanti.';
    } catch (Exception $ex) {
        if ($fotoBaru !== null) {
            Upload::hapusFoto($fotoBaru);
        }
        $error = $ex->getMessage();
        $laporan = $laporanModel->find($id);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Detail Laporan - LostFound Kampus</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f8fafc;color:#0f172a;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;line-height:1.5}
a{color:#2563eb;text-decoration:none}
header{background:#fff;border-bottom:1px solid #e2e8f0}
.bar,.wrap{max-width:960px;margin:auto;padding:16px 20px}
.bar{display:flex;justify-content:space-between;align-items:center;gap:16px}
nav{display:flex;gap:16px;align-items:center}
.card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px}
h1{font-size:23px;margin:8px 0 4px}
.muted{color:#64748b;font-size:14px}
.photo{width:100%;max-height:480px;object-fit:contain;background:#f1f5f9;border-radius:8px}
.alert{padding:10px 14px;border-radius:8px;margin:14px 0;background:#fef2f2;color:#b91c1c}
button{margin-top:16px;padding:10px 14px;border:0;border-radius:8px;background:#2563eb;color:#fff;font:inherit;font-weight:600;cursor:pointer}
.badge{display:inline-block;padding:3px 9px;border-radius:99px;background:#e2e8f0;font-size:13px}
label{display:block;margin:14px 0 6px;font-weight:600}
input,select,textarea{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font:inherit;background:#fff}
textarea{min-height:130px}
hr{border:0;border-top:1px solid #e2e8f0;margin:24px 0}
.danger{background:#b91c1c}
</style>
</head>
<body>
<header><div class="bar">
  <strong>LostFound Kampus</strong>
  <nav><a href="<?= $e(BASE_URL) ?>/index.php">Beranda</a><a href="<?= $e(BASE_URL) ?>/histori.php">Histori</a></nav>
</div></header>
<main class="wrap">
  <?php if ($laporan === null): ?>
    <section class="card"><h1><?= $error !== '' ? 'Terjadi kesalahan' : 'Laporan tidak ditemukan' ?></h1><p class="muted"><?= $e($error !== '' ? $error : 'Laporan tidak tersedia atau Anda tidak memiliki akses.') ?></p></section>
  <?php else: ?>
    <section class="card">
      <?php if ($flash): ?><div class="alert"><?= $e($flash['pesan']) ?></div><?php endif; ?>
      <?php if ($error !== ''): ?><div class="alert"><?= $e($error) ?></div><?php endif; ?>
      <span class="badge"><?= $e(ucfirst($laporan['status_laporan'])) ?></span>
      <h1><?= $e($laporan['nama_kategori']) ?></h1>
      <p class="muted">Dilaporkan oleh <?= $e($laporan['pelapor']) ?> · <?= $e(Helper::formatTanggal($laporan['tanggal_hilang'])) ?></p>
      <img class="photo" src="<?= $e(Upload::url($laporan['foto'])) ?>" alt="Foto laporan">
      <p><?= nl2br($e($laporan['deskripsi'])) ?></p>
      <p><strong>Kategori:</strong> <?= $e($laporan['kategori']) ?></p>
      <p><strong>Lokasi:</strong> <?= $e($laporan['lokasi']) ?></p>
      <?php if ($laporan['catatan_admin']): ?><p><strong>Catatan admin:</strong> <?= $e($laporan['catatan_admin']) ?></p><?php endif; ?>
      <?php if ($laporan['nim'] === $viewer['nim'] && in_array($laporan['status_laporan'], [Laporan::STATUS_MENUNGGU, Laporan::STATUS_DITOLAK], true)): ?>
        <hr>
        <h2>Ubah laporan</h2>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
          <input type="hidden" name="aksi" value="ubah">
          <label for="nama_kategori">Nama barang</label>
          <input id="nama_kategori" name="nama_kategori" maxlength="100" value="<?= $e($laporan['nama_kategori']) ?>" required>
          <label for="id_kategori">Kategori</label>
          <select id="id_kategori" name="id_kategori" required>
            <?php foreach ($kategori as $item): ?>
              <option value="<?= $e($item['id_kategori']) ?>" <?= $laporan['id_kategori'] === $item['id_kategori'] ? 'selected' : '' ?>><?= $e($item['nama_kategori']) ?></option>
            <?php endforeach; ?>
          </select>
          <label for="id_lokasi">Lokasi</label>
          <select id="id_lokasi" name="id_lokasi" required>
            <?php foreach ($lokasi as $item): ?>
              <option value="<?= $e($item['id_lokasi']) ?>" <?= $laporan['id_lokasi'] === $item['id_lokasi'] ? 'selected' : '' ?>><?= $e($item['nama_lokasi']) ?></option>
            <?php endforeach; ?>
          </select>
          <label for="tanggal_hilang">Tanggal hilang</label>
          <input id="tanggal_hilang" type="date" name="tanggal_hilang" max="<?= date('Y-m-d') ?>" value="<?= $e($laporan['tanggal_hilang']) ?>" required>
          <label for="deskripsi">Deskripsi</label>
          <textarea id="deskripsi" name="deskripsi" required><?= $e($laporan['deskripsi']) ?></textarea>
          <label for="foto">Ganti foto (opsional, JPG/PNG/WebP maks. 2 MB)</label>
          <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
          <button type="submit">Simpan dan kirim ulang</button>
        </form>
        <form method="post" onsubmit="return confirm('Hapus laporan ini?')">
          <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
          <input type="hidden" name="aksi" value="hapus">
          <button class="danger" type="submit">Hapus laporan</button>
        </form>
      <?php endif; ?>
      <?php if ($laporan['nim'] === $viewer['nim'] && $laporan['status_laporan'] === Laporan::STATUS_DIPUBLIKASI): ?>
        <form method="post">
          <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
          <input type="hidden" name="aksi" value="ditemukan">
          <button type="submit">Tandai sudah ditemukan</button>
        </form>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>
</body>
</html>