<?php

// 1. Konstanta dasar
define('BASE_URL', '/proyekfw/public');

// 2. Zona waktu PHP (zona waktu sesi MySQL diatur di DBconnection)
date_default_timezone_set('Asia/Jakarta');

// 3. Pengaturan error
error_reporting(E_ALL);
ini_set('display_errors', '1'); // ganti ke '0' saat produksi
ini_set('log_errors', '1');     // semua error tetap masuk error_log

// 4. Session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// 5. Autoload class: NamaClass -> class/namaclass.php
spl_autoload_register(function (string $nama): void {
    if (!preg_match('/^[A-Za-z0-9_]+$/', $nama)) {
        return;
    }

    $file = __DIR__ . '/class/' . strtolower($nama) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// 6. Handler exception yang tidak tertangkap
set_exception_handler(function (Throwable $e): void {
    error_log(sprintf(
        '[Uncaught] %s: %s in %s:%d',
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));

    if (!headers_sent()) {
        header('Location: ' . BASE_URL . '/error/500.php');
    } else {
        echo 'Terjadi kesalahan. Silakan coba lagi nanti.';
    }
    exit;
});