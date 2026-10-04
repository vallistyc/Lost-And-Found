<?php
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
$token = $_POST['csrf'] ?? '';
if (!is_string($token) || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
    http_response_code(419);
    exit('Sesi tidak valid.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

session_start();
session_regenerate_id(true);
$_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Anda telah keluar.'];
header('Location: login.php');
exit;
