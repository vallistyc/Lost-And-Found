<?php
require_once __DIR__ . '/../partials/guard.php';

$db = new DBconnection();
$user = new User($db);
$pelapor = wajibRole(User::ROLE_MAHASISWA, $user);
$csrf = csrfToken();
$e = [Helper::class, 'e'];
$kategori = (new Kategori($db))->all();
$lokasi = (new Lokasi($db))->all();
$error = '';
$old = ['nama_kategori' => '', 'id_kategori' => '', 'id_lokasi' => '', 'tanggal_hilang' => '', 'deskripsi' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajibCsrf();
    foreach ($old as $field => $_) {
        $old[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
    }

    $foto = null;
    try {
        $foto = Upload::simpanFoto($_FILES['foto'] ?? []);
        (new Laporan($db))->create($pelapor['nim'], $old, $foto);
        Helper::setFlash('success', 'Laporan berhasil dikirim untuk ditinjau.');
        Helper::redirect(BASE_URL . '/histori.php');
    } catch (PDOException $ex) {
        if ($foto !== null) {
            Upload::hapusFoto($foto);
        }
        $error = 'Terjadi gangguan pada server. Coba lagi nanti.';
    } catch (Exception $ex) {
        if ($foto !== null) {
            Upload::hapusFoto($foto);
        }
        $error = $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Buat Laporan - LostFound Kampus</title>
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
label{display:block;margin:15px 0 6px;font-weight:600;font-size:14px}
input,select,textarea{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font:inherit;background:#fff}
textarea{min-height:150px;resize:vertical}
button{margin-top:18px;padding:11px 16px;border:0;border-radius:8px;background:#2563eb;color:#fff;font:inherit;font-weight:600;cursor:pointer}
.alert{padding:10px 14px;border-radius:8px;margin:14px 0;background:#fef2f2;color:#b91c1c}
.actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
@media(max-width:600px){.bar{align-items:flex-start;flex-direction:column}}
</style>
</head>
<body>
<header><div class="bar">
  <strong>LostFound Kampus</strong>
  <nav>
    <a href="<?= $e(BASE_URL) ?>/index.php">Beranda</a>
    <a href="<?= $e(BASE_URL) ?>/histori.php">Histori laporan</a>
  </nav>
</div></header>
<main class="wrap">
  <section class="card">
    <h1>Buat laporan kehilangan</h1>
    <p class="muted">Isi detail barang dan lokasi terakhir diketahui. Laporan akan ditinjau admin.</p>
    <?php if ($error !== ''): ?><div class="alert"><?= $e($error) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
      <label for="nama_kategori">Nama barang</label>
      <input id="nama_kategori" name="nama_kategori" maxlength="100" value="<?= $e($old['nama_kategori']) ?>" required>
      <label for="id_kategori">Kategori</label>
      <select id="id_kategori" name="id_kategori" required>
        <option value="">Pilih kategori</option>
        <?php foreach ($kategori as $item): ?>
          <option value="<?= $e($item['id_kategori']) ?>" <?= $old['id_kategori'] === $item['id_kategori'] ? 'selected' : '' ?>><?= $e($item['nama_kategori']) ?></option>
        <?php endforeach; ?>
      </select>
      <label for="id_lokasi">Lokasi</label>
      <select id="id_lokasi" name="id_lokasi" required>
        <option value="">Pilih lokasi</option>
        <?php foreach ($lokasi as $item): ?>
          <option value="<?= $e($item['id_lokasi']) ?>" <?= $old['id_lokasi'] === $item['id_lokasi'] ? 'selected' : '' ?>><?= $e($item['nama_lokasi']) ?></option>
        <?php endforeach; ?>
      </select>
      <label for="tanggal_hilang">Tanggal kehilangan</label>
      <input id="tanggal_hilang" type="date" name="tanggal_hilang" max="<?= date('Y-m-d') ?>" value="<?= $e($old['tanggal_hilang']) ?>" required>
      <label for="deskripsi">Deskripsi barang</label>
      <textarea id="deskripsi" name="deskripsi" required><?= $e($old['deskripsi']) ?></textarea>
      <label for="foto">Foto (opsional, JPG/PNG/WebP maks. 2 MB)</label>
      <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
      <div class="actions">
        <button type="submit">Kirim laporan</button>
        <a href="<?= $e(BASE_URL) ?>/histori.php">Batal</a>
      </div>
    </form>
  </section>
</main>
</body>
</html>
