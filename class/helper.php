<?php
/**
 * Helper
 * Kumpulan fungsi bantu untuk tampilan dan alur halaman (semua static).
 * Tidak berisi aturan bisnis dan tidak mengakses database.
 * Catatan: setFlash/pullFlash/setOld/old membutuhkan session_start() sudah dipanggil.
 */
class Helper
{
    private const BULAN = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    private const FLASH_TIPE = ['success', 'danger', 'warning', 'info'];

    /** Cache old input untuk satu request (diisi dari session lalu session dikosongkan). */
    private static ?array $oldCache = null;

    // ------------------------------------------------------------------
    // Tampilan dan navigasi
    // ------------------------------------------------------------------

    /** Escape output HTML (anti XSS). Wajib untuk setiap data yang dicetak ke halaman. */
    public static function e(?string $teks): string
    {
        return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
    }

    /** Pindah halaman lalu hentikan script (dipakai setelah POST agar refresh tidak mengulang aksi). */
    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /** True bila request saat ini bermetode POST. */
    public static function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /** Ringkas teks untuk card; ditambah "..." bila terpotong. */
    public static function potong(string $teks, int $n = 100): string
    {
        return mb_strimwidth(trim($teks), 0, $n, '...', 'UTF-8');
    }

    // ------------------------------------------------------------------
    // Kontak, tanggal, status
    // ------------------------------------------------------------------

    /**
     * Buat tautan WhatsApp dari no HP (08xx atau 62xx).
     * @return string URL wa.me, atau string kosong bila nomor tidak valid
     *                (mis. kontak berisi akun Instagram, bukan nomor)
     */
    public static function waLink(string $noHp): string
    {
        $nomor = preg_replace('/[^0-9]/', '', $noHp);

        if (str_starts_with($nomor, '08')) {
            $nomor = '62' . substr($nomor, 1);
        }
        if (!preg_match('/^62[0-9]{8,12}$/', $nomor)) {
            return '';
        }
        return 'https://wa.me/' . $nomor;
    }

    /**
     * Format tanggal Indonesia: "3 Oktober 2026" (dengan jam: "3 Oktober 2026, 14:05").
     * Menerima string tanggal/datetime dari database.
     */
    public static function formatTanggal(?string $tanggal, bool $denganJam = false): string
    {
        if ($tanggal === null || $tanggal === '' || strtotime($tanggal) === false) {
            return '-';
        }
        $t     = strtotime($tanggal);
        $hasil = (int) date('j', $t) . ' ' . self::BULAN[(int) date('n', $t)] . ' ' . date('Y', $t);

        return $denganJam ? $hasil . ', ' . date('H:i', $t) : $hasil;
    }

    /** Waktu relatif: "baru saja", "5 menit lalu", "2 jam lalu", "3 hari lalu"; lebih dari 7 hari: tanggal. */
    public static function waktuRelatif(?string $datetime): string
    {
        if ($datetime === null || $datetime === '' || strtotime($datetime) === false) {
            return '-';
        }
        $selisih = time() - strtotime($datetime);

        if ($selisih < 60)       return 'baru saja';
        if ($selisih < 3600)     return floor($selisih / 60) . ' menit lalu';
        if ($selisih < 86400)    return floor($selisih / 3600) . ' jam lalu';
        if ($selisih < 7 * 86400) return floor($selisih / 86400) . ' hari lalu';

        return self::formatTanggal($datetime);
    }

    /**
     * Label dan warna badge Bootstrap untuk status laporan.
     * @return array ['label' => string, 'class' => string] contoh class: "bg-success"
     */
    public static function badgeStatus(string $status): array
    {
        return match ($status) {
            'menunggu'    => ['label' => 'Menunggu verifikasi', 'class' => 'bg-warning text-dark'],
            'dipublikasi' => ['label' => 'Dipublikasi',         'class' => 'bg-success'],
            'ditolak'     => ['label' => 'Ditolak',             'class' => 'bg-danger'],
            'ditemukan'   => ['label' => 'Ditemukan',           'class' => 'bg-primary'],
            default       => ['label' => ucfirst($status),      'class' => 'bg-secondary'],
        };
    }

    // ------------------------------------------------------------------
    // Pesan flash dan isian lama form (disimpan di session, tampil sekali)
    // ------------------------------------------------------------------

    /** Simpan pesan untuk ditampilkan sekali di halaman berikutnya. Tipe: success, danger, warning, info. */
    public static function setFlash(string $tipe, string $pesan): void
    {
        if (!in_array($tipe, self::FLASH_TIPE, true)) {
            $tipe = 'info';
        }
        $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
    }

    /**
     * Ambil pesan flash lalu hapus dari session (sekali tampil).
     * @return array|null ['tipe' => ..., 'pesan' => ...] atau null bila tidak ada
     */
    public static function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /** Simpan isian form saat validasi gagal. Field password tidak pernah disimpan. */
    public static function setOld(array $input): void
    {
        unset($input['password'], $input['password_konfirmasi'], $input['csrf_token']);
        $_SESSION['old'] = $input;
    }

    /** Ambil isian lama sebuah field untuk mengisi ulang form (kosong bila tidak ada). */
    public static function old(string $field, string $default = ''): string
    {
        if (self::$oldCache === null) {
            self::$oldCache = $_SESSION['old'] ?? [];
            unset($_SESSION['old']); // hanya berlaku untuk satu tampilan form
        }
        $nilai = self::$oldCache[$field] ?? $default;
        return is_scalar($nilai) ? (string) $nilai : $default;
    }
}