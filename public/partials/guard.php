<?php
require_once __DIR__ . '/../../bootstrap.php';

function wajibTamu(): void {
    if (!isset($_SESSION['nim'])) {
        return;
    }

    if (($_SESSION['role'] ?? '') === User::ROLE_ADMIN) {
        Helper::redirect(BASE_URL . '/admin/index.php');
    }

    Helper::redirect(BASE_URL . '/index.php');
}

function wajibLogin(User $user): array {
    if (!isset($_SESSION['nim'])) {
        Helper::setFlash('warning', 'Silakan masuk terlebih dahulu.');
        Helper::redirect(BASE_URL . '/login.php');
    }

    $u = $user->findById((string) $_SESSION['nim']);

    if (!$u || (int) $u['is_active'] === 0) {
        unset($_SESSION['nim'], $_SESSION['role'], $_SESSION['nama']);
        Helper::setFlash('danger', 'Akun tidak ditemukan atau tidak aktif.');
        Helper::redirect(BASE_URL . '/login.php');
    }

    $_SESSION['role'] = $u['role'];
    $_SESSION['nama'] = $u['nama_user'];
    return $u;
}

function wajibRole(string $role, User $user): array {
    $u = wajibLogin($user);

    if ($u['role'] !== $role) {
        Helper::redirect(BASE_URL . '/index.php');
    }

    return $u;
}

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function wajibCsrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrfToken(), $token)) {
        http_response_code(419);
        exit('Sesi tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}
?>