<?php
/**
 * Tanggapan
 * Tanggapan mahasiswa lain terhadap laporan yang sudah dipublikasi.
 * Kontak penanggap hanya boleh dilihat oleh pelapor dan admin.
 */
class Tanggapan
{
    private DBconnection $db;
    private Laporan $laporan;

    public function __construct(DBconnection $db, Laporan $laporan)
    {
        $this->db      = $db;
        $this->laporan = $laporan;
    }

    /**
     * Simpan tanggapan baru.
     * Syarat: laporan berstatus dipublikasi, penanggap bukan pemilik,
     * dan penanggap belum pernah menanggapi laporan yang sama.
     * @return int id tanggapan baru
     * @throws Exception bila ada syarat yang tidak terpenuhi
     */
    public function create(int $laporanId, int $userId, string $pesan, string $kontak): int
    {
        $pesan  = trim($pesan);
        $kontak = trim($kontak);

        if (mb_strlen($pesan) < 10) {
            throw new Exception('Pesan minimal 10 karakter.');
        }
        if ($kontak === '') {
            throw new Exception('Kontak wajib diisi.');
        }
        if (mb_strlen($kontak) > 50) {
            throw new Exception('Kontak maksimal 50 karakter.');
        }

        // find() melempar Exception bila laporan tidak ada
        $laporan = $this->laporan->find($laporanId);

        if ($laporan['status'] !== Laporan::STATUS_DIPUBLIKASI) {
            throw new Exception('Laporan ini tidak menerima tanggapan.');
        }
        if ((int) $laporan['user_id'] === $userId) {
            throw new Exception('Anda tidak dapat menanggapi laporan sendiri.');
        }
        if ($this->hasResponded($laporanId, $userId)) {
            throw new Exception('Anda sudah pernah menanggapi laporan ini.');
        }

        try {
            $this->db->execute(
                'INSERT INTO tanggapan (laporan_id, user_id, pesan, kontak) VALUES (?, ?, ?, ?)',
                [$laporanId, $userId, $pesan, $kontak]
            );
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Pengaman terakhir: index UNIQUE (laporan_id, user_id) bila ada kiriman ganda bersamaan
            $asli = $e->getPrevious() ?? $e;
            if ($asli->getCode() === '23000') {
                throw new Exception('Anda sudah pernah menanggapi laporan ini.');
            }
            throw $e;
        }
    }

    /**
     * Daftar tanggapan sebuah laporan, terbaru dulu (nama penanggap tanpa NIM).
     * Hanya pemilik laporan atau admin yang boleh melihat, karena memuat kontak.
     * @throws Exception bila bukan pemilik/admin
     */
    public function byLaporan(int $laporanId, int $aktorId, bool $isAdmin): array
    {
        $laporan = $this->laporan->find($laporanId);

        if (!$isAdmin && (int) $laporan['user_id'] !== $aktorId) {
            throw new Exception('Anda tidak berhak melihat tanggapan ini.');
        }

        return $this->db->fetchAll(
            'SELECT t.id, t.pesan, t.kontak, t.created_at, u.nama AS penanggap
             FROM tanggapan t
             JOIN users u ON u.id = t.user_id
             WHERE t.laporan_id = ?
             ORDER BY t.created_at DESC',
            [$laporanId]
        );
    }

    /** True bila user sudah pernah menanggapi laporan ini. */
    public function hasResponded(int $laporanId, int $userId): bool
    {
        $row = $this->db->fetchOne(
            'SELECT COUNT(*) AS n FROM tanggapan WHERE laporan_id = ? AND user_id = ?',
            [$laporanId, $userId]
        );
        return (int) $row['n'] > 0;
    }

    /** Jumlah tanggapan pada sebuah laporan. */
    public function countByLaporan(int $laporanId): int
    {
        $row = $this->db->fetchOne(
            'SELECT COUNT(*) AS n FROM tanggapan WHERE laporan_id = ?',
            [$laporanId]
        );
        return (int) $row['n'];
    }
}