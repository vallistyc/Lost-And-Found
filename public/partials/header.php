<?php
// Set sebelum require header di tiap halaman:
// $judulHalaman = 'Beranda';
// $halamanAktif = 'beranda'; // beranda | histori | profil
$judulHalaman = $judulHalaman ?? 'LostFound KAMPUS';
$halamanAktif = $halamanAktif ?? '';
$menu = [
    'beranda' => ['Beranda', 'index.php'],
    'histori' => ['Histori Laporan', 'histori.php'],
    'profil'  => ['Profil', 'profil.php'],
    'logout'  => ['Logout', 'logout.php'],
];

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
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 h-16 md:h-20 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            </span>
            <span class="text-lg font-bold text-slate-900">LostFound KAMPUS</span>
        </a>

        <!-- Menu desktop -->
        <div class="hidden md:flex items-center gap-6 text-sm">
            <?php foreach ($menu as $kunci => [$label, $href]): ?>
                <a href="<?= $href ?>" class="<?= navClass($kunci, $halamanAktif) ?>"><?= $label ?></a>
            <?php endforeach; ?>
            <a href="buat-laporan.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg">Lapor</a>
        </div>

        <!-- Tombol hamburger (HP) -->
        <button id="btn-menu" type="button" aria-label="Buka menu" aria-expanded="false" class="md:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </nav>

    <!-- Menu HP -->
    <div id="menu-mobile" class="hidden md:hidden border-t border-slate-200 px-4 py-3 space-y-1 text-sm">
        <?php foreach ($menu as $kunci => [$label, $href]): ?>
            <a href="<?= $href ?>" class="block px-3 py-3 rounded-lg <?= $kunci === $halamanAktif ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-700 hover:bg-slate-50' ?>"><?= $label ?></a>
        <?php endforeach; ?>
        <a href="buat-laporan.php" class="block text-center bg-blue-600 text-white font-semibold px-4 py-3 rounded-lg">Lapor</a>
    </div>
</header>
</header>

<main class="flex-1">