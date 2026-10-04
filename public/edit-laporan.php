<?php
// require_once __DIR__ . '/../bootstrap.php';
// TODO: cek session login, kalau belum login -> header('Location: login.php'); exit;

$id = (int) ($_GET['id'] ?? 0);

$judulHalaman = 'Edit Laporan';
$halamanAktif = 'histori';

// DUMMY: ganti dengan data dari backend berdasarkan $id
$daftarKategori = ['Dompet', 'Aksesoris', 'Tas', 'Elektronik', 'Dokumen', 'Lain-lain'];
$daftarLokasi   = ['Gedung A (Kantin Teknik)', 'Gedung Kuliah Bersama (GKB) I', 'Gedung Perpustakaan Pusat'];
$laporan = [
    'id'        => $id,
    'user_id'   => 1,
    'status'    => 'Menunggu',
    'nama'      => 'Dompet Kulit Coklat',
    'kategori'  => 'Dompet',
    'lokasi'    => 'Gedung A (Kantin Teknik)',
    'tanggal'   => '2024-09-15', // format Y-m-d untuk input date
    'deskripsi' => 'Dompet kulit lipat dua berwarna coklat tua merk Braun Buffel. Di bagian kanan bawah luar ada logo kerbau kecil perak. Isinya ada KTM atas nama Budi, SIM A, dan kartu parkir motor kampus.',
    'foto'      => 'dompet.jpg',
];

// Laporan hanya boleh diedit pemilik & saat status Menunggu
// TODO: bandingkan $laporan['user_id'] dengan id user dari session
if ($laporan['status'] !== 'Menunggu') {
    header('Location: detail.php?id=' . $id);
    exit;
}

$mode      = 'edit';
$actionUrl = 'update-laporan.php'; // TODO: sesuaikan dengan endpoint backend
$data      = $laporan;

require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/form-laporan.php';
require_once __DIR__ . '/partials/footer.php';