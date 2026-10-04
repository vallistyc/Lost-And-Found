<?php
require_once __DIR__ . '/partials/guard.php';
wajibTamu();

$csrf = csrfToken();
$flash = Helper::pullFlash();
$identitas = '';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajibCsrf();
    $identitas = is_string($_POST['identitas'] ?? null) ? trim($_POST['identitas']) : '';
    $password  = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if ($identitas === '' || $password === '') {
        $error = 'NIM dan password wajib diisi.';
    } else {
        try {
            $user = (new User(new DBconnection()))->login($identitas, $password);
            session_regenerate_id(true);
            $_SESSION['nim'] = $user['nim'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['nama_user'];
            Helper::redirect(BASE_URL . ($user['role'] === User::ROLE_ADMIN ? '/admin/index.php' : '/index.php'));
        } catch (PDOException $ex) {
            $error = 'Terjadi gangguan pada server. Coba lagi nanti.';
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
    }
}

$e = [Helper::class, 'e'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login LostFound KAMPUS</title>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;background:#f8fafc;color:#0f172a;line-height:1.5}
a{color:#2563eb;text-decoration:none}
.topbar-in{max-width:1120px;margin:0 auto;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.brand{display:flex;align-items:center;gap:8px;font-weight:700;color:#0f172a;font-size:15px}
.logo{width:28px;height:28px;border-radius:8px;background:#dbeafe;color:#2563eb;display:inline-flex;align-items:center;justify-content:center}
.links a{color:#475569;font-weight:500}.links a.on{color:#2563eb}
.links form{margin:0}
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
.b-ditolak{background:#fee2e2;color:#b91c1c}.b-ditemukan{background:#dcfce7;color:#15803d}
.muted{color:#64748b;font-size:13px}
footer.ft{border-top:1px solid #e2e8f0;background:#fff;margin-top:40px;padding:18px 20px;text-align:center;font-size:12px;color:#64748b}
.authbg{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:radial-gradient(circle at 15% 20%,#38bdf8 0,transparent 45%),radial-gradient(circle at 85% 25%,#fbbf24 0,transparent 30%),linear-gradient(135deg,#0c4a6e,#0e7490 50%,#1e3a8a)}
.authcard{width:100%;max-width:380px;background:#fff;border-radius:16px;padding:28px;box-shadow:0 20px 50px rgba(0,0,0,.25)}
.authhead{text-align:center;margin-bottom:6px}.authhead h1{font-size:18px;margin:10px 0 2px}.authhead p{font-size:12px;color:#64748b;margin:0}
.authfoot{text-align:center;margin:18px 0 0;font-size:13px;color:#64748b}
@media(max-width:600px){.authbg{background:#fff;align-items:flex-start;padding:16px}.authcard{box-shadow:none;padding:16px 4px}.links{gap:12px}}
</style>
</head>
<body class="authbg">
<main class="authcard">
  <div class="authhead"><span class="logo"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span><h1>LostAndFound Kampus</h1><p>Sistem Pelaporan Barang Hilang &amp; Temuan Mahasiswa</p></div>
  <?php if ($flash): ?><div class="alert <?= $flash['tipe'] === 'success' ? 'ok' : ($flash['tipe'] === 'danger' ? 'err' : 'info') ?>"><?= $e($flash['pesan']) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert err"><?= $e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
    <label for="identitas">NIM</label>
   <input class="in" id="identitas" name="identitas" value="<?= $e($identitas) ?>" required autofocus>
    <label for="password">Password</label>
    <input class="in" id="password" type="password" name="password" required>
    <button class="btn" type="submit">Masuk</button>
  </form>
  <p class="authfoot">Belum punya akun? <a href="signup.php">Daftar</a></p>
</main>
</body>
</html>
