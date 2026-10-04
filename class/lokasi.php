<?php
/**
 * Lokasi
 * Master data lokasi hilang/terakhir terlihat, dipakai untuk dropdown form dan filter beranda.
 * Kolom: id_lokasi (string, mis. "LK001") dan nama_lokasi (string).
 * Lokasi yang masih dipakai laporan tidak boleh dihapus.
 */
class Lokasi
{
    private const MAX_NAMA  = 100;
    private const PREFIX_ID = 'LK';  // format id: LK001, LK002, ...
    private const DIGIT_ID  = 3;

    private DBconnection $db;
    private string $table = 'lokasi'; // konstan internal, bukan dari input pengguna

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    /** Semua lokasi, urut nama (untuk dropdown dan tabel admin). */
    public function all(): array
    {
        return $this->db->fetchAll(
            "SELECT id_lokasi, nama_lokasi FROM {$this->table} ORDER BY nama_lokasi ASC"
        );
    }

    /** Satu lokasi, null bila tidak ada. */
    public function find(string $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT id_lokasi, nama_lokasi FROM {$this->table} WHERE id_lokasi = ?",
            [trim($id)]
        );
    }

    /**
     * Tambah lokasi baru. id_lokasi dibuat otomatis (LK001, LK002, ...).
     * @return string id_lokasi baru
     * @throws Exception bila nama kosong, terlalu panjang, atau sudah ada
     */
    public function create(string $nama): string
    {
        $nama = $this->bersihkanNama($nama);
        $this->pastikanUnik($nama);

        // Coba beberapa kali: bila dua admin menambah bersamaan, id yang sama bisa bentrok
        for ($percobaan = 1; $percobaan <= 3; $percobaan++) {
            $id = $this->buatIdBaru();
            try {
                $this->db->execute(
                    "INSERT INTO {$this->table} (id_lokasi, nama_lokasi) VALUES (?, ?)",
                    [$id, $nama]
                );
                return $id;
            } catch (PDOException $e) {
                $asli = $e->getPrevious() ?? $e;
                if ($asli->getCode() !== '23000') {
                    throw $e;
                }
                // Duplikat: nama diambil orang lain (pastikanUnik akan melempar) atau id bentrok (ulangi)
                $this->pastikanUnik($nama);
            }
        }
        throw new Exception('Gagal membuat lokasi. Silakan coba lagi.');
    }

    /**
     * Ubah nama lokasi. Nama milik sendiri tidak dianggap duplikat.
     * @throws Exception bila tidak ditemukan atau nama tidak valid
     */
    public function update(string $id, string $nama): void
    {
        $id = trim($id);
        if ($this->find($id) === null) {
            throw new Exception('Lokasi tidak ditemukan.');
        }
        $nama = $this->bersihkanNama($nama);
        $this->pastikanUnik($nama, $id);

        try {
            $this->db->execute(
                "UPDATE {$this->table} SET nama_lokasi = ? WHERE id_lokasi = ?",
                [$nama, $id]
            );
        } catch (PDOException $e) {
            $asli = $e->getPrevious() ?? $e;
            if ($asli->getCode() === '23000') {
                throw new Exception('Lokasi dengan nama tersebut sudah ada.');
            }
            throw $e;
        }
    }

    /** Jumlah laporan yang memakai lokasi ini (0 = tidak dipakai). */
    public function isUsed(string $id): int
    {
        $row = $this->db->fetchOne(
            'SELECT COUNT(*) AS n FROM laporan WHERE id_lokasi = ?',
            [trim($id)]
        );
        return (int) $row['n'];
    }

    /**
     * Hapus lokasi yang tidak dipakai.
     * @throws Exception bila tidak ditemukan atau masih dipakai laporan
     */
    public function delete(string $id): void
    {
        $id = trim($id);
        if ($this->find($id) === null) {
            throw new Exception('Lokasi tidak ditemukan.');
        }

        $dipakai = $this->isUsed($id);
        if ($dipakai > 0) {
            throw new Exception("Lokasi tidak dapat dihapus karena masih dipakai oleh $dipakai laporan.");
        }

        $this->db->execute("DELETE FROM {$this->table} WHERE id_lokasi = ?", [$id]);
    }

    // ------------------------------------------------------------------
    // Pembantu (private)
    // ------------------------------------------------------------------

    /** Buat id berikutnya: ambil angka terbesar dari id yang ada, tambah 1. */
    private function buatIdBaru(): string
    {
        $awal = strlen(self::PREFIX_ID) + 1; // posisi mulai angka (SUBSTRING dimulai dari 1)
        $row  = $this->db->fetchOne(
            "SELECT MAX(CAST(SUBSTRING(id_lokasi, $awal) AS UNSIGNED)) AS maks FROM {$this->table}"
        );
        $berikut = ((int) ($row['maks'] ?? 0)) + 1;

        return self::PREFIX_ID . str_pad((string) $berikut, self::DIGIT_ID, '0', STR_PAD_LEFT);
    }

    private function bersihkanNama(string $nama): string
    {
        $nama = trim($nama);
        if ($nama === '') {
            throw new Exception('Nama lokasi wajib diisi.');
        }
        if (mb_strlen($nama) > self::MAX_NAMA) {
            throw new Exception('Nama lokasi maksimal ' . self::MAX_NAMA . ' karakter.');
        }
        return $nama;
    }

    /** Cek nama belum dipakai (tidak peka huruf besar-kecil karena collation). */
    private function pastikanUnik(string $nama, ?string $kecualiId = null): void
    {
        $sql    = "SELECT id_lokasi FROM {$this->table} WHERE nama_lokasi = ?";
        $params = [$nama];
        if ($kecualiId !== null) {
            $sql     .= ' AND id_lokasi != ?';
            $params[] = $kecualiId;
        }
        if ($this->db->fetchOne($sql . ' LIMIT 1', $params) !== null) {
            throw new Exception('Lokasi dengan nama tersebut sudah ada.');
        }
    }
}