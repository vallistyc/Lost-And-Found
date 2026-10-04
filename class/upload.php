<?php

/**
 * Upload: semua logika unggah foto laporan.
 * Semua method static. Halaman tidak boleh berisi logika upload.
 */
class Upload
{
    const MAX_BYTES    = 2097152; // 2 MB
    const POLA_NAMA    = '/^[a-f0-9]{32}\.(jpg|png|webp)$/';
    const MIME_DIIZINKAN = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Simpan foto dari $_FILES['foto'].
     * Mengembalikan nama file acak, atau null bila tidak ada file diunggah.
     *
     * @throws Exception
     */
    public static function simpanFoto(array $file): ?string
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new Exception('Ukuran foto maksimal 2 MB.');
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new Exception('Gagal mengunggah foto.');
        }

        $tmp = $file['tmp_name'] ?? '';
        if (!is_string($tmp) || $tmp === '' || !is_uploaded_file($tmp)) {
            throw new Exception('Gagal mengunggah foto.');
        }

        $ukuran = filesize($tmp);
        if ($ukuran === false) {
            throw new Exception('Gagal mengunggah foto.');
        }
        if ($ukuran > self::MAX_BYTES) {
            throw new Exception('Ukuran foto maksimal 2 MB.');
        }

        // MIME dicek dari isi file, bukan dari data browser
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($tmp);

        if ($mime === false || !isset(self::MIME_DIIZINKAN[$mime])) {
            throw new Exception('Format foto harus JPG, PNG, atau WebP.');
        }

        $folder = self::folderTujuan();
        if (!is_dir($folder) || !is_writable($folder)) {
            error_log('Upload: folder tujuan tidak ada atau tidak bisa ditulis: ' . $folder);
            throw new Exception('Gagal mengunggah foto.');
        }

        $nama = bin2hex(random_bytes(16)) . '.' . self::MIME_DIIZINKAN[$mime];

        if (!move_uploaded_file($tmp, $folder . $nama)) {
            error_log('Upload: move_uploaded_file gagal ke ' . $folder . $nama);
            throw new Exception('Gagal mengunggah foto.');
        }

        return $nama;
    }

    /**
     * Hapus file foto. Abaikan null/kosong, nama tidak valid, atau file tidak ada.
     * Pola nama dicek ketat untuk mencegah path traversal.
     */
    public static function hapusFoto(?string $nama): void
    {
        if ($nama === null || $nama === '') {
            return;
        }

        if (!preg_match(self::POLA_NAMA, $nama)) {
            return;
        }

        $path = self::folderTujuan() . $nama;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * URL foto untuk ditampilkan. Bila kosong, pakai gambar placeholder.
     */
    public static function url(?string $nama): string
    {
        if ($nama === null || $nama === '') {
            return BASE_URL . '/assets/img/no-image.svg';
        }

        return BASE_URL . '/uploads/laporan/' . $nama;
    }

    private static function folderTujuan(): string
    {
        return dirname(__DIR__) . '/public/uploads/laporan/';
    }
}