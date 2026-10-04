<?php
// Set sebelum require header di tiap halaman:
// $judulHalaman = 'Beranda';
// $halamanAktif = 'beranda'; // beranda | histori | profil
$judulHalaman = $judulHalaman ?? 'LostFound KAMPUS';
$halamanAktif = $halamanAktif ?? '';

function navClass(string $nama, string $aktif): string
{
    return $nama === $aktif
        ? 'text-blue-600 font-semibold'
        : 'text-slate-600 hover:text-blue-600';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judulHalaman) ?> | LostFound KAMPUS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800">

<header class="bg-white border-b border-slate-200">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            </span>
            <span class="text-lg font-bold text-slate-900">LostFound KAMPUS</span>
        </a>

        <div class="flex items-center gap-4 sm:gap-6 text-sm">
            <a href="index.php" class="<?= navClass('beranda', $halamanAktif) ?>">Beranda</a>
            <a href="histori.php" class="<?= navClass('histori', $halamanAktif) ?>">Histori Laporan</a>
            <a href="profil.php" class="<?= navClass('profil', $halamanAktif) ?>">Profil</a>
            <a href="logout.php" class="<?= navClass('logout', $halamanAktif) ?>">Logout</a>
            <a href="buat-laporan.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg">Lapor</a>
        </div>
    </nav>
</header>

<main class="flex-1">