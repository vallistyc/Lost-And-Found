<?php
http_response_code(403);

$kode  = 403;
$judul = 'Akses Ditolak / Dibatasi';
$pesan = 'Kamu tidak memiliki izin atau otorisasi yang cukup untuk mengakses halaman ini. Pastikan kamu masuk menggunakan NIM mahasiswa yang valid.';
$tombolKiriLabel = 'Hubungi Admin';
$tombolKiriHref  = '#'; // TODO: isi kontak/halaman bantuan admin

require_once __DIR__ . '/partials/halaman-error.php';