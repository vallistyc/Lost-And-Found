<<<<<<< HEAD
<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login, kalau belum login -> header('Location: login.php'); exit;

$judulHalaman = 'Profil Saya';
$halamanAktif = 'profil';

// DUMMY: ganti dengan data user dari backend / session
$user = ['nama' => 'Budi Utomo', 'nim' => '21010123', 'no_hp' => '081234567890'];

// Pesan dari backend setelah proses simpan (contoh: dikirim lewat flash/session)
$pesanSukses = $_GET['sukses'] ?? null; // tes: profil.php?sukses=Profil berhasil diperbarui
$pesanError  = $_GET['error'] ?? null;

$inputClass = 'w-full h-12 px-4 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
$labelClass = 'block text-sm font-semibold text-slate-900 mb-2';

require_once __DIR__ . '/partials/header.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 pt-10">

    <nav class="flex items-center gap-2 text-sm text-slate-600">
        <a href="index.php" class="hover:text-blue-600">Beranda</a>
        <span class="text-slate-400">›</span>
        <span class="text-blue-600 font-medium">Profil Saya</span>
    </nav>

    <?php if ($pesanSukses): ?>
        <div class="mt-6 flex items-center gap-3 rounded-lg bg-green-100 border border-green-200 px-5 py-4 text-sm font-medium text-green-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>
            <?= htmlspecialchars($pesanSukses) ?>
        </div>
    <?php endif; ?>
    <?php if ($pesanError): ?>
        <div class="mt-6 rounded-lg bg-red-50 border border-red-200 px-5 py-4 text-sm font-medium text-red-700">
            <?= htmlspecialchars($pesanError) ?>
        </div>
    <?php endif; ?>

    <h1 class="mt-8 text-3xl font-bold text-slate-900">Profil Saya</h1>
    <p class="mt-2 text-sm text-slate-600">Kelola data informasi akun dan kata sandi Anda.</p>

    <div class="mt-8 grid gap-8 md:grid-cols-2 items-start">

        <!-- Informasi profil -->
        <form method="POST" action="update-profil.php" class="bg-white border border-slate-200 rounded-2xl p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">Informasi Profil</h2>

            <div>
                <label for="nama" class="<?= $labelClass ?>">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($user['nama']) ?>" class="<?= $inputClass ?>">
            </div>
            <div>
                <label for="nim" class="<?= $labelClass ?>">NIM (Tidak dapat diubah)</label>
                <input type="text" id="nim" value="<?= htmlspecialchars($user['nim']) ?>" disabled
                       class="<?= $inputClass ?> bg-slate-100 text-slate-500 cursor-not-allowed">
            </div>
            <div>
                <label for="no_hp" class="<?= $labelClass ?>">No HP / WA</label>
                <input type="tel" id="no_hp" name="no_hp" required value="<?= htmlspecialchars($user['no_hp']) ?>" class="<?= $inputClass ?>">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-lg">Simpan Perubahan</button>
        </form>

        <!-- Ganti password -->
        <form method="POST" action="ganti-password.php" id="form-password" class="bg-white border border-slate-200 rounded-2xl p-8 space-y-5">
            <h2 class="text-lg font-bold text-slate-900">Ganti Password</h2>

            <div>
                <label for="password_lama" class="<?= $labelClass ?>">Password Lama</label>
                <input type="password" id="password_lama" name="password_lama" required autocomplete="current-password" class="<?= $inputClass ?>">
            </div>
            <div>
                <label for="password_baru" class="<?= $labelClass ?>">Password Baru</label>
                <input type="password" id="password_baru" name="password_baru" required minlength="8" autocomplete="new-password" class="<?= $inputClass ?>">
                <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
            </div>
            <div>
                <label for="konfirmasi" class="<?= $labelClass ?>">Konfirmasi Password Baru</label>
                <input type="password" id="konfirmasi" name="konfirmasi" required autocomplete="new-password" class="<?= $inputClass ?>">
                <p id="pesan-konfirmasi" class="mt-1 text-xs text-red-600 hidden">Konfirmasi password tidak cocok dengan password di atas.</p>
            </div>

            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold px-6 py-3 rounded-lg">Ganti Password</button>
        </form>
    </div>
</div>

<script>
    // Cek konfirmasi password di browser (validasi sebenarnya tetap di server)
    document.getElementById('form-password').addEventListener('submit', function (e) {
        const cocok = this.password_baru.value === this.konfirmasi.value;
        document.getElementById('pesan-konfirmasi').classList.toggle('hidden', cocok);
        if (!cocok) e.preventDefault();
    });
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
=======
<?php
require_once __DIR__ . '/partials/guard.php';
$db = new DBconnection();
$userObj = new User($db);
$sess = wajibLogin($userObj);
$csrf = csrfToken();

$active = 'profil';
$e = [Helper::class, 'e'];
$str = fn($k) => is_string($_POST[$k] ?? null) ? $_POST[$k] : '';
$flash = Helper::pullFlash();

$errProfil = '';
$errPass = '';
$aksi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajibCsrf();
    $aksi = $str('aksi');

    if ($aksi === 'profil') {
        try {
            $userObj->profileUpdate($sess['nim'], $str('nama'), $str('no_hp'));
            $_SESSION['nama'] = trim($str('nama'));
            Helper::setFlash('success', 'Profil berhasil diperbarui.');
            Helper::redirect(BASE_URL . '/profil.php');
        } catch (PDOException $ex) {
            $errProfil = 'Terjadi gangguan pada server. Coba lagi nanti.';
        } catch (Exception $ex) {
            $errProfil = $ex->getMessage();
        }
    } elseif ($aksi === 'password') {
        $lama = $str('password_lama');
        $baru = $str('password_baru');
        $konf = $str('password_konfirmasi');
        try {
            if ($lama === '' || $baru === '' || $konf === '') {
                $errPass = 'Semua kolom password wajib diisi.';
            } elseif ($baru !== $konf) {
                $errPass = 'Konfirmasi password tidak cocok.';
            } else {
                $userObj->changePassword($sess['nim'], $lama, $baru);
                session_regenerate_id(true);
                Helper::setFlash('success', 'Password berhasil diganti.');
                Helper::redirect(BASE_URL . '/profil.php');
            }
        } catch (Exception $ex) {
            $errPass = $ex instanceof PDOException
                ? 'Gagal mengganti password. Coba lagi nanti.'
                : $ex->getMessage();
        }
    }
}

$data = $userObj->findById($sess['nim']);
if ($data === null) {
    unset($_SESSION['nim'], $_SESSION['role'], $_SESSION['nama']);
    Helper::setFlash('danger', 'Data akun tidak ditemukan. Silakan masuk kembali.');
    Helper::redirect(BASE_URL . '/login.php');
}
$valNama = $aksi === 'profil' ? $str('nama') : $data['nama_user'];
$valHp   = $aksi === 'profil' ? $str('no_hp') : $data['no_hp'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profil - LostFound KAMPUS</title>
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
.head h1{margin:0;font-size:22px}.head p{margin:2px 0 16px}
.cols{display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start}
.card h2{margin:0 0 4px;font-size:16px}
@media(max-width:760px){.cols{grid-template-columns:1fr}}
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
  <div class="head"><h1>Profil Saya</h1><p class="muted">Kelola informasi akun dan keamanan Anda.</p></div>
  <?php if ($flash): ?><div class="alert <?= $flash['tipe'] === 'success' ? 'ok' : ($flash['tipe'] === 'danger' ? 'err' : 'info') ?>"><?= $e($flash['pesan']) ?></div><?php endif; ?>

  <div class="cols">
    <section class="card">
      <h2>Informasi Profil</h2>
      <?php if ($errProfil): ?><div class="alert err"><?= $e($errProfil) ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
        <input type="hidden" name="aksi" value="profil">
        <label for="nama">Nama Lengkap</label>
        <input class="in" id="nama" name="nama" value="<?= $e($valNama) ?>" required>
        <label>NIM (Tidak dapat diubah)</label>
        <input class="in" value="<?= $e($data['nim']) ?>" readonly>
        <label for="no_hp">No HP / WA</label>
        <input class="in" id="no_hp" name="no_hp" value="<?= $e($valHp) ?>" required>
        <button class="btn" type="submit">Simpan Perubahan</button>
      </form>
    </section>

    <section class="card">
      <h2>Ganti Password</h2>
      <?php if ($errPass): ?><div class="alert err"><?= $e($errPass) ?></div><?php endif; ?>
      <form method="post" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
        <input type="hidden" name="aksi" value="password">
        <label for="password_lama">Password Lama</label>
        <input class="in" id="password_lama" type="password" name="password_lama" required>
        <label for="password_baru">Password Baru</label>
        <input class="in" id="password_baru" type="password" name="password_baru" minlength="8" required>
        <label for="password_konfirmasi">Konfirmasi Password Baru</label>
        <input class="in" id="password_konfirmasi" type="password" name="password_konfirmasi" minlength="8" required>
        <button class="btn dark" type="submit">Ganti Password</button>
      </form>
    </section>
  </div>
</div>
<footer class="ft">&copy; <?= date('Y') ?> LostFound KAMPUS · Sistem Pelaporan Barang Hilang &amp; Temuan Mahasiswa</footer>
</body>
</html>
>>>>>>> 3cec7eba310a22a173a73022272c09a60d83023f
