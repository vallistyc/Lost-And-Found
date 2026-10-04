<?php
require_once __DIR__ . '/../partials/guard.php';
wajibLogin(new User(new DBconnection()));

http_response_code(501);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fitur belum tersedia - LostFound Kampus</title>
</head>
<body>
<main>
  <h1>Fitur tanggapan belum tersedia</h1>
  <p>Skema database saat ini belum memiliki tabel tanggapan.</p>
  <a href="<?= htmlspecialchars(BASE_URL . '/index.php', ENT_QUOTES, 'UTF-8') ?>">Kembali ke beranda</a>
</main>
</body>
</html>