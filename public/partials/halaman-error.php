<?php
// Dipakai oleh error-403.php dan error-404.php
// Variabel: $kode (403|404), $judul, $pesan, $tombolKiriLabel, $tombolKiriHref

$is403 = ($kode === 403);
$warnaAngka  = $is403 ? 'text-red-500' : 'text-blue-600';
$warnaLingkar = $is403 ? 'bg-red-50 text-red-500 ring-red-100' : 'bg-blue-50 text-blue-600 ring-blue-100';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (int) $kode ?> | LostFound KAMPUS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } } };
    </script>
</head>
<body class="min-h-screen flex flex-col bg-slate-50 font-sans text-slate-800">

<header class="bg-white border-b border-slate-200">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m10 9-3 3 3 3M14 9l3 3-3 3"/></svg>
            </span>
            <span>
                <span class="block text-lg font-bold text-slate-900 leading-tight">LostFound KAMPUS</span>
                <span class="block text-xs text-slate-500">Sistem Informasi Universitas</span>
            </span>
        </a>
        <div class="flex items-center gap-3 sm:gap-6 text-sm">
            <a href="index.php" class="text-slate-600 hover:text-blue-600">Beranda</a>
            <a href="histori.php" class="text-slate-600 hover:text-blue-600">Histori Laporan</a>
            <a href="buat-laporan.php" class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium">Buat Laporan</a>
            <span class="flex items-center gap-2 font-semibold text-slate-900">
                <span class="w-9 h-9 rounded-full bg-slate-200"></span>Akun Mahasiswa
            </span>
        </div>
    </nav>
</header>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-xl bg-white border border-slate-200 rounded-3xl px-8 py-12 text-center">
        <span class="mx-auto w-20 h-20 rounded-full ring-8 flex items-center justify-center <?= $warnaLingkar ?>">
            <?php if ($is403): ?>
                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 3 4 6v6c0 4.5 3.2 8 8 9 4.8-1 8-4.5 8-9V6l-8-3Z"/><path d="M12 8v4M12 16h.01"/></svg>
            <?php else: ?>
                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3M11 8v3M11 14h.01"/></svg>
            <?php endif; ?>
        </span>

        <p class="mt-6 text-7xl font-extrabold <?= $warnaAngka ?>"><?= (int) $kode ?></p>
        <h1 class="mt-6 text-2xl font-bold text-slate-900"><?= htmlspecialchars($judul) ?></h1>
        <p class="mt-3 text-sm text-slate-600 leading-relaxed"><?= htmlspecialchars($pesan) ?></p>

        <div class="mt-8 pt-8 border-t border-slate-200 flex flex-wrap items-center justify-center gap-3">
            <a href="<?= htmlspecialchars($tombolKiriHref) ?>" class="px-5 py-3 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <?= htmlspecialchars($tombolKiriLabel) ?>
            </a>
            <a href="index.php" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-sm font-semibold text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m3 11 9-8 9 8M5 10v10h14V10"/></svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</main>

<footer class="bg-white border-t border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-slate-200">
            <span class="flex items-center gap-3 font-bold text-slate-900">
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m10 9-3 3 3 3M14 9l3 3-3 3"/></svg>
                </span>
                LostFound KAMPUS
            </span>
            <p class="text-sm text-slate-600">Bantuan • Kebijakan Privasi • Syarat &amp; Ketentuan</p>
        </div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-6 text-sm text-slate-400">
            <p>© <?= date('Y') ?> LostFound KAMPUS. Dikembangkan oleh Divisi Infrastruktur &amp; IT Mahasiswa.</p>
            <p>v1.2.0 (Stable)</p>
        </div>
    </div>
</footer>
</body>
</html>