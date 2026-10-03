<?php
// Autoload: memuat file class secara otomatis ketika class pertama kali digunakan
spl_autoload_register(function (string $nama_class) {
    $file = __DIR__ . "/model/" . strtolower($nama_class) . ".php";
    if (file_exists($file)) {
        include_once($file);
    }
});