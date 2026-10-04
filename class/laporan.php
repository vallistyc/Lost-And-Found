<?php
/**
 * Laporan
 * Inti bisnis: data laporan, hak akses atas laporan, dan siklus statusnya.
 * Aturan transisi status HANYA ditegakkan di class ini (lihat setStatus()).
 */
class Laporan
{
    public const STATUS_MENUNGGU    = 'menunggu';
    public const STATUS_DIPUBLIKASI = 'dipublikasi';
    public const STATUS_DITOLAK     = 'ditolak';
    public const STATUS_DITEMUKAN   = 'ditemukan';

    public const PER_PAGE = 12;

    // Tabel transisi: dari status => daftar status tujuan yang sah
    private const TRANSISI = [
        self::STATUS_MENUNGGU    => [self::STATUS_DIPUBLIKASI, self::STATUS_DITOLAK],
        self::STATUS_DIPUBLIKASI => [self::STATUS_DITEMUKAN],
        self::STATUS_DITOLAK     => [],  // keluar dari ditolak hanya lewat update() (edit & kirim ulang)
        self::STATUS_DITEMUKAN   => [],  // final
    ];

    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    // ------------------------------------------------------------------
    // Pembuatan, pembacaan
    // ------------------------------------------------------------------

    /**
     * Buat laporan baru dengan status menunggu.
     * $data: nama_barang, kategori_id, lokasi_id, tanggal_hilang, deskripsi
     * $foto: nama file hasil upload (bukan path), atau null
     * @return int id laporan baru
     */
    public function create(int $userId, array $data, ?string $foto = null): int
    {
        $d = $this->validasi($data);

        $this->db->execute(
            'INSERT INTO laporan
                (user_id, kategori_id, lokasi_id, nama_barang, deskripsi, tanggal_hilang, foto, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $userId, $d['kategori_id'], $d['lokasi_id'], $d['nama_barang'],
                $d['deskripsi'], $d['tanggal_hilang'], $foto, self::STATUS_MENUNGGU,
            ]
        );
        return $this->db->lastInsertId();
    }

    /**
     * Detail laporan lengkap (JOIN kategori, lokasi, nama pelapor; tanpa NIM/no HP).
     * @throws Exception bila tidak ada
     */
    public function find(int $id): array
    {
        $row = $this->db->fetchOne(
            'SELECT l.*, k.nama AS kategori, lo.nama_lokasi AS lokasi, u.nama AS pelapor
             FROM laporan l
             JOIN kategori k  ON k.id  = l.kategori_id
             JOIN lokasi   lo ON lo.id_lokasi = l.lokasi_id
             JOIN users    u  ON u.id  = l.user_id
             WHERE l.id = ?',
            [$id]
        );
        if ($row === null) {
            throw new Exception('Laporan tidak ditemukan.');
        }
        return $row;
    }

    /**
     * Boleh dilihat bila dipublikasi, atau milik sendiri, atau oleh admin.
     * Jika false, halaman sebaiknya menampilkan "tidak ditemukan" (bukan "dilarang")
     * agar keberadaan laporan tidak bocor.
     */
    public function canView(array $laporan, int $userId, bool $isAdmin): bool
    {
        return $laporan['status'] === self::STATUS_DIPUBLIKASI
            || (int) $laporan['user_id'] === $userId
            || $isAdmin;
    }

    /**
     * Daftar beranda: laporan dipublikasi milik orang lain.
     * $filter (semua opsional): q, kategori_id, lokasi_id
     * @return array ['data', 'total', 'totalPages', 'page']
     */
    public function feed(int $exceptUserId, array $filter = [], int $page = 1): array
    {
        $where  = ['l.status = ?', 'l.user_id != ?'];
        $params = [self::STATUS_DIPUBLIKASI, $exceptUserId];

        $q = trim($filter['q'] ?? '');
        if ($q !== '') {
            $where[]  = '(l.nama_barang LIKE ? OR l.deskripsi LIKE ?)';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
        }
        if (!empty($filter['kategori_id'])) {
            $where[]  = 'l.kategori_id = ?';
            $params[] = (int) $filter['kategori_id'];
        }
        if (!empty($filter['lokasi_id'])) {
            $where[]  = 'l.lokasi_id = ?';
            $params[] = trim((string) $filter['lokasi_id']);
        }

        return $this->daftar(implode(' AND ', $where), $params, $page);
    }

    /**
     * Histori laporan milik user, lengkap dengan jumlah tanggapan.
     * $status null = semua status.
     */
    public function byUser(int $userId, ?string $status = null): array
    {
        $sql = 'SELECT l.id, l.nama_barang, l.foto, l.tanggal_hilang, l.status, l.catatan_admin,
                       l.created_at, k.nama AS kategori, lo.nama_lokasi AS lokasi,
                       (SELECT COUNT(*) FROM tanggapan t WHERE t.laporan_id = l.id) AS jumlah_tanggapan
                FROM laporan l
                JOIN kategori k  ON k.id  = l.kategori_id
                JOIN lokasi   lo ON lo.id_lokasi = l.lokasi_id
                WHERE l.user_id = ?';
        $params = [$userId];

        if ($status !== null && $status !== '') {
            if (!isset(self::TRANSISI[$status])) {
                throw new Exception('Status tidak valid.');
            }
            $sql     .= ' AND l.status = ?';
            $params[] = $status;
        }

        return $this->db->fetchAll($sql . ' ORDER BY l.created_at DESC', $params);
    }

    // ------------------------------------------------------------------
    // Ubah dan hapus (oleh pelapor)
    // ------------------------------------------------------------------

    /**
     * Edit laporan sendiri (status menunggu/ditolak). Status kembali menunggu
     * dan catatan admin dikosongkan.
     * @return string|null nama foto LAMA yang harus dihapus halaman (null bila foto tidak diganti)
     */
    public function update(int $id, int $aktorId, array $data, ?string $fotoBaru = null): ?string
    {
        $laporan = $this->find($id);
        $this->assertOwner($laporan, $aktorId);
        $this->assertBolehUbah($laporan);

        $d = $this->validasi($data);

        $sql = 'UPDATE laporan SET kategori_id = ?, lokasi_id = ?, nama_barang = ?, deskripsi = ?,
                       tanggal_hilang = ?, status = ?, catatan_admin = NULL';
        $params = [
            $d['kategori_id'], $d['lokasi_id'], $d['nama_barang'], $d['deskripsi'],
            $d['tanggal_hilang'], self::STATUS_MENUNGGU,
        ];

        if ($fotoBaru !== null) {
            $sql     .= ', foto = ?';
            $params[] = $fotoBaru;
        }

        $this->db->execute($sql . ' WHERE id = ?', array_merge($params, [$id]));

        return $fotoBaru !== null ? $laporan['foto'] : null;
    }

    /**
     * Hapus laporan sendiri (status menunggu/ditolak). Tanggapan ikut terhapus (FK CASCADE).
     * @return string|null nama foto yang harus dihapus halaman
     */
    public function delete(int $id, int $aktorId): ?string
    {
        $laporan = $this->find($id);
        $this->assertOwner($laporan, $aktorId);
        $this->assertBolehUbah($laporan);

        $this->db->execute('DELETE FROM laporan WHERE id = ?', [$id]);
        return $laporan['foto'];
    }

    // ------------------------------------------------------------------
    // Transisi status
    // ------------------------------------------------------------------

    /** Admin: publikasikan laporan menunggu. */
    public function publish(int $id): void
    {
        $this->setStatus($id, self::STATUS_DIPUBLIKASI);
    }

    /** Admin: tolak laporan menunggu, catatan wajib. */
    public function reject(int $id, string $catatan): void
    {
        $this->setStatus($id, self::STATUS_DITOLAK, $catatan);
    }

    /** Pelapor: tandai laporan dipublikasi sebagai ditemukan. */
    public function markFound(int $id, int $aktorId): void
    {
        $this->assertOwner($this->find($id), $aktorId);
        $this->setStatus($id, self::STATUS_DITEMUKAN);
    }

    /**
     * Pusat aturan transisi status (private agar tidak bisa dilewati dari luar).
     * @throws Exception bila transisi tidak sah atau catatan kosong saat menolak
     */
    private function setStatus(int $id, string $target, ?string $catatan = null): void
    {
        $laporan = $this->find($id);
        $dari    = $laporan['status'];

        if (!in_array($target, self::TRANSISI[$dari], true)) {
            throw new Exception("Status tidak dapat diubah dari \"$dari\" ke \"$target\".");
        }

        if ($target === self::STATUS_DITOLAK) {
            $catatan = trim((string) $catatan);
            if ($catatan === '') {
                throw new Exception('Catatan wajib diisi saat menolak laporan.');
            }
            if (mb_strlen($catatan) > 255) {
                throw new Exception('Catatan maksimal 255 karakter.');
            }
        } else {
            $catatan = null;
        }

        // "AND status = ?" mencegah dua aksi bersamaan saling menimpa
        $terdampak = $this->db->execute(
            'UPDATE laporan SET status = ?, catatan_admin = ? WHERE id = ? AND status = ?',
            [$target, $catatan, $id, $dari]
        );
        if ($terdampak === 0) {
            throw new Exception('Status laporan sudah berubah. Muat ulang halaman.');
        }
    }

    // ------------------------------------------------------------------
    // Admin
    // ------------------------------------------------------------------

    /**
     * Daftar semua laporan untuk admin, dengan filter status dan kata kunci.
     * @return array ['data', 'total', 'totalPages', 'page']
     */
    public function adminList(?string $status = null, string $keyword = '', int $page = 1): array
    {
        $where  = ['1 = 1'];
        $params = [];

        if ($status !== null && $status !== '') {
            if (!isset(self::TRANSISI[$status])) {
                throw new Exception('Status tidak valid.');
            }
            $where[]  = 'l.status = ?';
            $params[] = $status;
        }
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $where[]  = '(l.nama_barang LIKE ? OR l.deskripsi LIKE ? OR u.nama LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        return $this->daftar(implode(' AND ', $where), $params, $page);
    }

    /**
     * Jumlah laporan per status untuk dashboard; status tanpa data bernilai 0.
     * @return array contoh: ['menunggu' => 3, 'dipublikasi' => 10, 'ditolak' => 0, 'ditemukan' => 2]
     */
    public function countByStatus(): array
    {
        $hasil = array_fill_keys(array_keys(self::TRANSISI), 0);

        $rows = $this->db->fetchAll('SELECT status, COUNT(*) AS n FROM laporan GROUP BY status');
        foreach ($rows as $r) {
            $hasil[$r['status']] = (int) $r['n'];
        }
        return $hasil;
    }

    // ------------------------------------------------------------------
    // Pembantu (private)
    // ------------------------------------------------------------------

    /** Penjaga kepemilikan. */
    private function assertOwner(array $laporan, int $userId): void
    {
        if ((int) $laporan['user_id'] !== $userId) {
            throw new Exception('Anda tidak berhak mengubah laporan ini.');
        }
    }

    /** Edit/hapus hanya boleh saat menunggu atau ditolak. */
    private function assertBolehUbah(array $laporan): void
    {
        if (!in_array($laporan['status'], [self::STATUS_MENUNGGU, self::STATUS_DITOLAK], true)) {
            throw new Exception('Laporan dengan status "' . $laporan['status'] . '" tidak dapat diubah atau dihapus.');
        }
    }

    /**
     * Query daftar bersama untuk feed() dan adminList(): hitung total, lalu ambil satu halaman.
     * $where dan $params disusun oleh pemanggil (hanya dari kode ini, bukan input mentah).
     */
    private function daftar(string $where, array $params, int $page): array
    {
        $from = 'FROM laporan l
                JOIN kategori k  ON k.id  = l.kategori_id
                JOIN lokasi   lo ON lo.id_lokasi = l.lokasi_id
                JOIN users    u  ON u.id  = l.user_id
                WHERE ' . $where;

        $total      = (int) $this->db->fetchOne('SELECT COUNT(*) AS n ' . $from, $params)['n'];
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page       = min(max(1, $page), $totalPages);
        $limit      = self::PER_PAGE;
        $offset     = ($page - 1) * self::PER_PAGE;

        // LIMIT/OFFSET sudah integer, aman disisipkan langsung
        $data = $this->db->fetchAll(
            'SELECT l.id, l.nama_barang, l.foto, l.tanggal_hilang, l.status, l.created_at,
                    k.nama AS kategori, lo.nama_lokasi AS lokasi, u.nama AS pelapor '
            . $from . " ORDER BY l.created_at DESC LIMIT $limit OFFSET $offset",
            $params
        );

        return ['data' => $data, 'total' => $total, 'totalPages' => $totalPages, 'page' => $page];
    }

    /**
     * Validasi dan bersihkan input laporan.
     * @return array data bersih siap simpan
     */
    private function validasi(array $data): array
    {
        $nama  = trim($data['nama_barang'] ?? '');
        $desk  = trim($data['deskripsi'] ?? '');
        $tgl   = trim($data['tanggal_hilang'] ?? '');
        $katId = (int) ($data['kategori_id'] ?? 0);
        $lokId = trim((string) ($data['lokasi_id'] ?? '')); // id_lokasi berupa string, mis. LK001

        if ($nama === '') {
            throw new Exception('Nama barang wajib diisi.');
        }
        if (mb_strlen($nama) > 100) {
            throw new Exception('Nama barang maksimal 100 karakter.');
        }
        if ($this->db->fetchOne('SELECT id FROM kategori WHERE id = ?', [$katId]) === null) {
            throw new Exception('Kategori tidak valid.');
        }
        if ($this->db->fetchOne('SELECT id_lokasi FROM lokasi WHERE id_lokasi = ?', [$lokId]) === null) {
            throw new Exception('Lokasi tidak valid.');
        }

        $dt = DateTime::createFromFormat('Y-m-d', $tgl);
        if ($dt === false || $dt->format('Y-m-d') !== $tgl) {
            throw new Exception('Format tanggal hilang tidak valid.');
        }
        if ($tgl > date('Y-m-d')) {
            throw new Exception('Tanggal hilang tidak boleh di masa depan.');
        }
        if (mb_strlen($desk) < 20) {
            throw new Exception('Deskripsi minimal 20 karakter.');
        }

        return [
            'nama_barang'    => $nama,
            'kategori_id'    => $katId,
            'lokasi_id'      => $lokId,
            'tanggal_hilang' => $tgl,
            'deskripsi'      => $desk,
        ];
    }
}