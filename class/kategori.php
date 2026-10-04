<?php
class Kategori
{
    private const MAX_NAMA_KATEGORI = 50;
    private const PREFIX_ID = 'KT';
    private const DIGIT_ID = 3;

    private DBconnection $db;
    private string $table = 'kategori';

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    /** Semua kategori, urut nama_kategori (untuk dropdown dan tabel admin). */
    public function all(): array
    {
        return $this->db->fetchAll("SELECT id_kategori, nama_kategori FROM {$this->table} ORDER BY nama_kategori ASC");
    }

    /** Satu kategori, null bila tidak ada. */
    public function find(string $id_kategori): ?array
    {
        return $this->db->fetchOne("SELECT id_kategori, nama_kategori FROM {$this->table} WHERE id_kategori = ?", [$id_kategori]);
    }

    /**
     * Tambah kategori baru.
     * @return string id_kategori kategori baru
     * @throws Exception bila nama_kategori kosong, terlalu panjang, atau sudah ada
     */
    public function create(string $nama_kategori): string
    {
        $nama_kategori = $this->bersihkanNamaKategori($nama_kategori);
        $this->pastikanUnik($nama_kategori);

        try {
            $id = $this->buatIdBaru();
            $this->db->execute(
                "INSERT INTO {$this->table} (id_kategori, nama_kategori) VALUES (?, ?)",
                [$id, $nama_kategori]
            );
            return $id;
        } catch (PDOException $e) {
            throw $this->terjemahkanError($e);
        }
    }

    /**
     * Ubah nama_kategori kategori. nama_kategori milik sendiri tidak dianggap duplikat.
     * @throws Exception bila tidak ditemukan atau nama_kategori tidak valid
     */
    public function update(string $id_kategori, string $nama_kategori): void
    {
        if ($this->find($id_kategori) === null) {
            throw new Exception('Kategori tidak ditemukan.');
        }
        $nama_kategori = $this->bersihkanNamaKategori($nama_kategori);
        $this->pastikanUnik($nama_kategori, $id_kategori);

        try {
            $this->db->execute("UPDATE {$this->table} SET nama_kategori = ? WHERE id_kategori = ?", [$nama_kategori, $id_kategori]);
        } catch (PDOException $e) {
            throw $this->terjemahkanError($e);
        }
    }

    /** Jumlah laporan yang memakai kategori ini (0 = tidak dipakai). */
    public function isUsed(string $id_kategori): int
    {
        $row = $this->db->fetchOne('SELECT COUNT(*) AS n FROM laporan WHERE id_kategori = ?', [$id_kategori]);
        return (int) $row['n'];
    }

    /**
     * Hapus kategori yang tidak dipakai.
     * @throws Exception bila tidak ditemukan atau masih dipakai laporan
     */
    public function delete(string $id_kategori): void
    {
        if ($this->find($id_kategori) === null) {
            throw new Exception('Kategori tidak ditemukan.');
        }

        $dipakai = $this->isUsed($id_kategori);
        if ($dipakai > 0) {
            throw new Exception("Kategori tidak dapat dihapus karena masih dipakai oleh $dipakai laporan.");
        }

        $this->db->execute("DELETE FROM {$this->table} WHERE id_kategori = ?", [$id_kategori]);
    }

    // ATURAN BISNIS

    private function bersihkanNamaKategori(string $nama_kategori): string
    {
        $nama_kategori = trim($nama_kategori);
        if ($nama_kategori === '') {
            throw new Exception('Nama kategori wajib diisi.');
        }
        if (mb_strlen($nama_kategori) > self::MAX_NAMA_KATEGORI) {
            throw new Exception('Nama kategori maksimal ' . self::MAX_NAMA_KATEGORI . ' karakter.');
        }
        return $nama_kategori;
    }

    /** Cek nama_kategori belum dipakai (tidak peka huruf besar-kecil karena collation). */
    private function pastikanUnik(string $nama_kategori, ?string $kecualiIdKategori = null): void
    {
        $sql    = "SELECT id_kategori FROM {$this->table} WHERE nama_kategori = ?";
        $params = [$nama_kategori];
        if ($kecualiIdKategori !== null) {
            $sql     .= ' AND id_kategori != ?';
            $params[] = $kecualiIdKategori;
        }
        if ($this->db->fetchOne($sql . ' LIMIT 1', $params) !== null) {
            throw new Exception('Kategori dengan nama_kategori tersebut sudah ada.');
        }
    }

    private function buatIdBaru(): string
    {
        $row = $this->db->fetchOne(
            "SELECT MAX(CAST(SUBSTRING(id_kategori, 3) AS UNSIGNED)) AS maks FROM {$this->table}"
        );
        $next = (int) ($row['maks'] ?? 0) + 1;
        $id = self::PREFIX_ID . str_pad((string) $next, self::DIGIT_ID, '0', STR_PAD_LEFT);
        if (strlen($id) > 20) {
            throw new Exception('Jumlah ID kategori maksimum telah tercapai.');
        }
        return $id;
    }

    /** Pengaman bila dua admin menyimpan nama_kategori sama di saat bersamaan (UNIQUE di database). */
    private function terjemahkanError(PDOException $e): Exception
    {
        $asli = $e->getPrevious() ?? $e;
        if ($asli->getCode() === '23000') {
            return new Exception('Kategori dengan nama_kategori tersebut sudah ada.');
        }
        return $e;
    }
}