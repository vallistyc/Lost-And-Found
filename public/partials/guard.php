<?php
require __DIR__ . '/../bootstrap.php';

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
    // Cek apakah User sudah log in dg melihat data session
    if (!isset($_SESSION['nim'])) {
        Helper::setFlash('warning', 'Silahkan log in dulu');
        Helper::redirect(BASE_URL . '/login.php');
    }

    $u = $user->findbyId($_SESSION['nim']);

    if (!$u || (int) $u['is_active'] === 0) {
        unset($_SESSION['nim'], $_SESSION['role'], $_SESSION['nama']);
        Helper::setFlash('danger', 'Akun anda tidak aktif');
        Helper::redirect(BASE_URL . '/login.php');
    }

    return $u;
}

function wajibRole(string $role, User $user): array {
    // Cek apakah user sudah log in
    $u = wajibLogin($user);

    if ($u['role'] !== $role) {
        Helper::redirect(BASE_URL . '/index.php');
    }

    return $u;
}
?>