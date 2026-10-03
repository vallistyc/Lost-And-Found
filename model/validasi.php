<?php
class Validasi
{
    public static function email_valid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function password_kuat(string $password): bool
    {
        return strlen($password) >= 8;
    }

    // $data = array asosiatif yang diinput user, $wajib = daftar nama field yang wajib diisi
    public static function tidak_kosong(array $data, array $wajib): bool
    {
        foreach ($wajib as $field) {
            if (!isset($data[$field]) || empty(trim((string) $data[$field]))) {
                return false;
            }
        }
        return true;
    }
}