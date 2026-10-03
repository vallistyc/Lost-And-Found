<?php 
class Laporan {
    private DBconnection $db;
    const STATUS_MENUNGGU = 'menunggu';
    const STATUS_DIPUBLIKASI = 'dipublikasi';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_DITEMUKAN = 'ditemukan';
    // JUMLAH LAPORAN PER HALAMAN
    const PER_PAGE = 12;

    private const TRANSISI = [
        self::STATUS_MENUNGGU    => [self::STATUS_DIPUBLIKASI, self::STATUS_DITOLAK],
        self::STATUS_DIPUBLIKASI => [self::STATUS_DITEMUKAN],
        self::STATUS_DITOLAK     => [],  
        self::STATUS_DITEMUKAN   => [], 
    ];

    // MEMBUKA DAN MENYIMPAN KONEKSI DATABASE
    public function __construct(DBconnection $db) {
        $this->db = $db;
    }

    // MEMBUAT LAPORAN
    public function create(string $nim, array $data, ?string $foto = null): int {
        $judul = trim($data['judul'] ?? '');
        $deskripsi = trim($data['deskripsi'] ?? '');
        $tgl = trim($data['tanggal_hilang'] ?? '');
        $kategoriId = $data['id_kategori'] ?? '';
        $lokasiId = $data['id_lokasi'] ?? '';

        // NGECEK JUDUL
        if ($judul === '' || mb_strlen($judul) > 100) {
            throw new Exception('Ada yang salah dengan judul Laporan Anda. Silahkan cek apakah kolom judul sudah terisi atau kolom judul tak melebihi 100 karakter.');
        }

        // Cek Apakah Kategori Valid
        $kat_cek = $this->db->fetchOne('SELECT id FROM kategori WHERE id_kategori = ?', [$kategoriId]);
        if (!$kat_cek) {
            throw new Exception("Kategori anda tidak valid!"); 
        }

        // Cek Apakah Lokasi Valid
        $lok_cek = $this->db->fetchOne('SELECT id FROM lokasi WHERE id_lokasi =?', [$lokasiId]);
        if (!$lok_cek) {
            throw new Exception("Lokasi tidak valid");
        }

        // Cek Format Tanggal
        $timeConfig = DateTime::createFromFormat('Y-m-d', $tgl); 
        
        if (!$timeConfig || $timeConfig->format('Y-m-d') !== $tgl) {
            throw new Exception("Format 'tanggal kehilangan barang' tidak valid!");
        }

        // Cek Keabsurdan Tanggal. Tanggal Hilang tidak boleh melebihi dari tanggal saat ini.
        if ($tgl > date('Y-m-d')) {
            throw new Exception('Tanggal hilang tidak boleh di masa depan!');
        }

        // Cek deskripsi
        if ($deskripsi === null) {
            throw new Exception("Deskripsi tidak boleh kosong");
        }

        $this->db->execute(
            "INSERT INTO laporan (user_id, kategori_id, lokasi_id, nama_barang, deskripsi, tanggal_hilang, foto, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$nim, $kategoriId, $lokasiId, $judul, $deskripsi, $tgl, $foto, self::STATUS_MENUNGGU]
        );

        return (int) $this->db->lastInsertId();
    }

    // MENGAMBIL DATA LAPORAN
    public function find($id) {
        $sql = "SELECT  FROM"
    }
}
?>