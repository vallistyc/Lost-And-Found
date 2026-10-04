<?php
http_response_code(404);

$kode  = 404;
$judul = 'Halaman Tidak Ditemukan';
$pesan = 'Maaf, halaman yang kamu cari tidak dapat ditemukan, telah dihapus, berganti nama, atau sedang tidak tersedia untuk sementara waktu.';
$tombolKiriLabel = 'Laporkan Masalah';
$tombolKiriHref  = '#'; // TODO: isi halaman/kontak untuk lapor masalah

require_once __DIR__ . '/partials/halaman-error.php';