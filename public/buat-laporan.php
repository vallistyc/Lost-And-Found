<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login, kalau belum login -> header('Location: login.php'); exit;

$judulHalaman = 'Buat Laporan';
$halamanAktif = 'beranda';

// DUMMY: ganti dengan data dari backend
$daftarKategori = ['Dompet', 'Aksesoris', 'Tas', 'Elektronik', 'Dokumen', 'Lain-lain'];
$daftarLokasi   = ['Gedung Kuliah Bersama (GKB) I', 'Gedung A (Kantin Teknik)', 'Gedung Perpustakaan Pusat'];

$mode      = 'buat';
$actionUrl = 'proses-laporan.php'; // TODO: sesuaikan dengan endpoint backend
$data = ['id' => 0, 'nama' => '', 'kategori' => '', 'lokasi' => '', 'tanggal' => '', 'deskripsi' => '', 'foto' => ''];

require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/form-laporan.php';
require_once __DIR__ . '/partials/footer.php';