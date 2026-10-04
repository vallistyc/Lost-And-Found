<?php
class Laporan
{
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DIPUBLIKASI = 'dipublikasi';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_DITEMUKAN = 'ditemukan';
    public const PER_PAGE = 12;

    private const TRANSISI = [
        self::STATUS_MENUNGGU => [self::STATUS_DIPUBLIKASI, self::STATUS_DITOLAK],
        self::STATUS_DIPUBLIKASI => [self::STATUS_DITEMUKAN],
        self::STATUS_DITOLAK => [],
        self::STATUS_DITEMUKAN => [],
    ];

    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    public function create(string $nim, array $data, ?string $foto = null): string
    {
        $d = $this->validasi($data);
        $id = $this->buatIdBaru();
        $this->db->execute(
            'INSERT INTO laporan
                (id_laporan, nim, id_kategori, id_lokasi, nama_kategori, deskripsi,
                 tanggal_hilang, foto, status_laporan)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $id, trim($nim), $d['id_kategori'], $d['id_lokasi'],
                $d['nama_kategori'], $d['deskripsi'], $d['tanggal_hilang'], $foto,
                self::STATUS_MENUNGGU,
            ]
        );
        return $id;
    }

    public function find(string $id): array
    {
        $row = $this->db->fetchOne(
            'SELECT l.*, k.nama_kategori AS kategori, lo.nama_lokasi AS lokasi,
                    u.nama_user AS pelapor
             FROM laporan l
             JOIN kategori k ON k.id_kategori = l.id_kategori
             JOIN lokasi lo ON lo.id_lokasi = l.id_lokasi
             JOIN users u ON u.nim = l.nim
             WHERE l.id_laporan = ?',
            [trim($id)]
        );
        if ($row === null) {
            throw new Exception('Laporan tidak ditemukan.');
        }
        return $row;
    }

    public function canView(array $laporan, string $nim, bool $isAdmin): bool
    {
        return $laporan['status_laporan'] === self::STATUS_DIPUBLIKASI
            || $laporan['nim'] === trim($nim)
            || $isAdmin;
    }

    public function feed(string $exceptNim = '', array $filter = [], int $page = 1): array
    {
        $where = ['l.status_laporan = ?'];
        $params = [self::STATUS_DIPUBLIKASI];

        if ($exceptNim !== '') {
            $where[] = 'l.nim != ?';
            $params[] = trim($exceptNim);
        }
        $q = trim((string) ($filter['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(l.nama_kategori LIKE ? OR l.deskripsi LIKE ? OR k.nama_kategori LIKE ?)';
            array_push($params, '%' . $q . '%', '%' . $q . '%', '%' . $q . '%');
        }
        if (!empty($filter['id_kategori'])) {
            $where[] = 'l.id_kategori = ?';
            $params[] = trim((string) $filter['id_kategori']);
        }
        if (!empty($filter['id_lokasi'])) {
            $where[] = 'l.id_lokasi = ?';
            $params[] = trim((string) $filter['id_lokasi']);
        }

        return $this->daftar(implode(' AND ', $where), $params, $page);
    }

    public function byUser(string $nim, ?string $status = null): array
    {
        $sql = 'SELECT l.id_laporan, l.nama_kategori, l.deskripsi, l.foto, l.tanggal_hilang,
                       l.status_laporan, l.catatan_admin, l.created_at,
                       k.nama_kategori AS kategori, lo.nama_lokasi AS lokasi
                FROM laporan l
                JOIN kategori k ON k.id_kategori = l.id_kategori
                JOIN lokasi lo ON lo.id_lokasi = l.id_lokasi
                WHERE l.nim = ?';
        $params = [trim($nim)];

        if ($status !== null && $status !== '') {
            if (!isset(self::TRANSISI[$status])) {
                throw new Exception('Status tidak valid.');
            }
            $sql .= ' AND l.status_laporan = ?';
            $params[] = $status;
        }

        return $this->db->fetchAll($sql . ' ORDER BY l.created_at DESC', $params);
    }

    public function update(string $id, string $nim, array $data, ?string $fotoBaru = null): ?string
    {
        $laporan = $this->find($id);
        $this->assertOwner($laporan, $nim);
        $this->assertBolehUbah($laporan);
        $d = $this->validasi($data);

        $sql = 'UPDATE laporan SET id_kategori = ?, id_lokasi = ?, nama_kategori = ?,
                       deskripsi = ?, tanggal_hilang = ?, status_laporan = ?, catatan_admin = NULL';
        $params = [
            $d['id_kategori'], $d['id_lokasi'], $d['nama_kategori'], $d['deskripsi'],
            $d['tanggal_hilang'], self::STATUS_MENUNGGU,
        ];
        if ($fotoBaru !== null) {
            $sql .= ', foto = ?';
            $params[] = $fotoBaru;
        }
        $this->db->execute($sql . ' WHERE id_laporan = ?', [...$params, trim($id)]);

        return $fotoBaru !== null ? $laporan['foto'] : null;
    }

    public function delete(string $id, string $nim): ?string
    {
        $laporan = $this->find($id);
        $this->assertOwner($laporan, $nim);
        $this->assertBolehUbah($laporan);
        $this->db->execute('DELETE FROM laporan WHERE id_laporan = ?', [trim($id)]);
        return $laporan['foto'];
    }

    public function publish(string $id): void
    {
        $this->setStatus($id, self::STATUS_DIPUBLIKASI);
    }

    public function reject(string $id, string $catatan): void
    {
        $this->setStatus($id, self::STATUS_DITOLAK, $catatan);
    }

    public function markFound(string $id, string $nim): void
    {
        $this->assertOwner($this->find($id), $nim);
        $this->setStatus($id, self::STATUS_DITEMUKAN);
    }

    public function adminList(?string $status = null, string $keyword = '', int $page = 1): array
    {
        $where = ['1 = 1'];
        $params = [];
        if ($status !== null && $status !== '') {
            if (!isset(self::TRANSISI[$status])) {
                throw new Exception('Status tidak valid.');
            }
            $where[] = 'l.status_laporan = ?';
            $params[] = $status;
        }
        $keyword = trim($keyword);
        if ($keyword !== '') {
            $where[] = '(l.nama_kategori LIKE ? OR l.deskripsi LIKE ? OR u.nama_user LIKE ?)';
            array_push($params, '%' . $keyword . '%', '%' . $keyword . '%', '%' . $keyword . '%');
        }
        return $this->daftar(implode(' AND ', $where), $params, $page);
    }

    public function countByStatus(): array
    {
        $hasil = array_fill_keys(array_keys(self::TRANSISI), 0);
        foreach ($this->db->fetchAll(
            'SELECT status_laporan, COUNT(*) AS n FROM laporan GROUP BY status_laporan'
        ) as $row) {
            $hasil[$row['status_laporan']] = (int) $row['n'];
        }
        return $hasil;
    }

    private function setStatus(string $id, string $target, ?string $catatan = null): void
    {
        $laporan = $this->find($id);
        $dari = $laporan['status_laporan'];
        if (!in_array($target, self::TRANSISI[$dari] ?? [], true)) {
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

        $affected = $this->db->execute(
            'UPDATE laporan SET status_laporan = ?, catatan_admin = ?
             WHERE id_laporan = ? AND status_laporan = ?',
            [$target, $catatan, trim($id), $dari]
        );
        if ($affected === 0) {
            throw new Exception('Status laporan sudah berubah. Muat ulang halaman.');
        }
    }

    private function assertOwner(array $laporan, string $nim): void
    {
        if ($laporan['nim'] !== trim($nim)) {
            throw new Exception('Anda tidak berhak mengubah laporan ini.');
        }
    }

    private function assertBolehUbah(array $laporan): void
    {
        if (!in_array($laporan['status_laporan'], [self::STATUS_MENUNGGU, self::STATUS_DITOLAK], true)) {
            throw new Exception('Laporan dengan status "' . $laporan['status_laporan'] . '" tidak dapat diubah atau dihapus.');
        }
    }

    private function daftar(string $where, array $params, int $page): array
    {
        $from = 'FROM laporan l
                 JOIN kategori k ON k.id_kategori = l.id_kategori
                 JOIN lokasi lo ON lo.id_lokasi = l.id_lokasi
                 JOIN users u ON u.nim = l.nim
                 WHERE ' . $where;
        $total = (int) ($this->db->fetchOne('SELECT COUNT(*) AS n ' . $from, $params)['n'] ?? 0);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, $page), $totalPages);
        $offset = ($page - 1) * self::PER_PAGE;
        $data = $this->db->fetchAll(
            'SELECT l.id_laporan, l.nama_kategori, l.deskripsi, l.foto, l.tanggal_hilang,
                    l.status_laporan, l.catatan_admin, l.created_at,
                    k.nama_kategori AS kategori, lo.nama_lokasi AS lokasi,
                    u.nama_user AS pelapor ' . $from . '
             ORDER BY l.created_at DESC LIMIT ' . self::PER_PAGE . ' OFFSET ' . $offset,
            $params
        );

        return ['data' => $data, 'total' => $total, 'totalPages' => $totalPages, 'page' => $page];
    }

    private function validasi(array $data): array
    {
        $namaKategori = trim((string) ($data['nama_kategori'] ?? ''));
        $idKategori = trim((string) ($data['id_kategori'] ?? ''));
        $idLokasi = trim((string) ($data['id_lokasi'] ?? ''));
        $deskripsi = trim((string) ($data['deskripsi'] ?? ''));
        $tanggalHilang = trim((string) ($data['tanggal_hilang'] ?? ''));

        $kategori = $this->db->fetchOne(
            'SELECT nama_kategori FROM kategori WHERE id_kategori = ?',
            [$idKategori]
        );
        if ($kategori === null) {
            throw new Exception('Kategori tidak valid.');
        }
        if ($namaKategori === '') {
            throw new Exception('Nama barang wajib diisi.');
        }
        if (mb_strlen($namaKategori) > 100) {
            throw new Exception('Nama barang maksimal 100 karakter.');
        }
        if ($this->db->fetchOne('SELECT id_lokasi FROM lokasi WHERE id_lokasi = ?', [$idLokasi]) === null) {
            throw new Exception('Lokasi tidak valid.');
        }
        $tanggal = DateTime::createFromFormat('!Y-m-d', $tanggalHilang);
        if ($tanggal === false || $tanggal->format('Y-m-d') !== $tanggalHilang) {
            throw new Exception('Format tanggal hilang tidak valid.');
        }
        if ($tanggalHilang > date('Y-m-d')) {
            throw new Exception('Tanggal hilang tidak boleh di masa depan.');
        }
        if ($deskripsi === '') {
            throw new Exception('Deskripsi wajib diisi.');
        }

        return [
            'id_kategori' => $idKategori,
            'id_lokasi' => $idLokasi,
            'nama_kategori' => $namaKategori,
            'kategori' => $kategori['nama_kategori'],
            'deskripsi' => $deskripsi,
            'tanggal_hilang' => $tanggalHilang,
        ];
    }

    private function buatIdBaru(): string
    {
        $row = $this->db->fetchOne(
            "SELECT MAX(CAST(SUBSTRING(id_laporan, 3) AS UNSIGNED)) AS maks FROM laporan"
        );
        $next = (int) ($row['maks'] ?? 0) + 1;
        $id = 'LP' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        if (strlen($id) > 20) {
            throw new Exception('Jumlah ID laporan maksimum telah tercapai.');
        }
        return $id;
    }
}
