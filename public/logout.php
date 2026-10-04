<?php
require_once __DIR__ . '/partials/guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helper::redirect(BASE_URL . '/index.php');
}

wajibCsrf();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();
session_start();
session_regenerate_id(true);
Helper::setFlash('success', 'Anda telah keluar.');
Helper::redirect(BASE_URL . '/login.php');
