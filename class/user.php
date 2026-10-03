<?php 
class User {
    private DBconnection $db;
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MAHASISWA = 'mahasiswa';
    private const PER_PAGE = 12;
    private const COLUMNS = 'nim, nama, email, no_hp, role, is_active, created_at';

    // MEMBUKA DAN MENYIMPAN KONEKSI DATABASE
    public function __construct(DBconnection $db) {
        $this->db = $db;
    }

    // PENDAFTARAN PENGGUNA BARU
    public function register(array $data): int {
        $nama = trim($data['nama'] ?? '');
        $nim = trim($data['nim'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $noHp = trim($data['no_hp'] ?? '');
        $pass = trim($data['password'] ?? '');

        // Cek apakah semua kolom sudah diisi
        if (empty($nama) || empty($nim) || empty($email) || empty($noHp) || empty($pass)) {
            throw new Exception('Semua kolom wajib diisi');
        }

        // Cek apakah email ditulis dengan benar
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email tidak valid');
        }

        // Cek apakah nomor hp tertulis dengan benar (diawali 0 atau 62 dengan panjang 10-14 digit)
        if (!preg_match("/^(08|62)[0-9]{8,12}$/", $noHp)) {
            throw new Exception('No HP harus diawali dengan 08 atau 62 dan terdiri dari 10-14 digit');
        }

        if (strlen($pass) < 8) {
            throw new Exception("Password minimal 8 karakter");
        }

        // Cek apakah password yang dikonfirmasi telah sesuai
        if (isset($data['password_konfirmasi']) && $data['password_konfirmasi'] !== $pass) {
            throw new Exception('Konfirmasi password tidak cocok');
        }

        // Cek apakah nim dan email sudah didaftarkan apa belum
        $used = $this->db->fetchOne(
            'SELECT id FROM users WHERE nim = ? OR email = ? LIMIT 1',
            [$nim, $email]
        );

        if ($used !== null) {
            throw new Exception('NIM atau Email sudah terdaftar');
        }

        // MULAI BENAR-BENAR MENDAFTARKAN PENGGUNA KE DATABASE
        try {
            $statement = $this->db->query(
                'INSERT INTO users (nim, nama, email, no_hp, password, role, is_active)
                VALUES (?,?,?,?,?,?,1)',
                [$nim, $nama, $email, $noHp, password_hash($pass, PASSWORD_DEFAULT), self::ROLE_MAHASISWA]
            );
            return $statement->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new Exception('NIM atau email sudah terdaftar');
            }
            error_log('User::register failed: '.$e->getMessage());
            throw new Exception('Pendaftaran gagal, silahkan coba lagi');
        }
    }

    // LOG IN PENGGUNA
    public function login(string $nim, string $password): array {
        // Merapihkan nilain nim yang diinput oleh pengguna, agar spasinya dihilangkan
        $nim = trim($nim);

        // Mencari dan Mengambil data pengguna yang memiliki nim tersebut
        $user = $this->db->fetchOne(
            'SELECT' . self::COLUMNS . ', password FROM users
            WHERE nim = ? LIMIT 1', [$nim, strtolower($nim)]
        );

        // cek apakah akun tidak tersedia atau password salah
        if ($user === null || !password_verify($password, $user['password'])) {
            throw new Exception('NIM/Email atau password salah');
        }

        // Cek apakah user dinonaktifkan oleh Admin
        if ((int) $user['is_active'] === 0) {
            throw new Exception('Akun Anda dinonaktifkan. Hubungi Admin');
        }

        // Memastikan agar password tidak tersimpan di memory
        unset($user['password']);
        return $user;
    }

    // MENGAMBIL INFORMASI USER BERDASARKAN NIM 
    public function findById(string $nim): array {
        $user = $this->db->fetchOne(
            'SELECT ' . self::COLUMNS . ' FROM users WHERE nim = ? LIMIT 1',
            [trim($nim)]
        );

        if ($user === null) {
            throw new Exception('Pengguna tidak ditemukan');
        }

        return $user;
    }

    // MENGUBAH NAMA DAN NOMOR HP 
    public function profileUpdate(string $nim, string $nama, string $noHp): void {
        // Menghilangkan spasi di data nim, nama, dan noHp
        $nama = trim($nama);
        $noHp = trim($noHp);

        // Cek apakah data nama terisi
        if ($nama === '') {
            throw new Exception('Nama wajib diisi');
        }

        // Cek apakah nomor Hp terisi dengan benar
        if (!preg_match('/^(08|62)[0-9]{8,12}$/', $noHp)) {
            throw new Exception('No HP harus diawali 08 atau 62 dan terdiri dari 10-14 digit');
        }

        // MengUPDATE data ke database
        $this->db->execute(
            'UPDATE users SET nama = ?, no_hp = ? WHERE nim = ?',
            [$nama, $noHp, $nim]
        );
    }

    // LIST SEMUA USER
    public function listAllUsers(string $keyword = '', int $page = 1): array {
        $where = '';
        $params = [];
        $keyword = trim($keyword);

        if ($keyword !== '') {
            $where = 'WHERE nama LIKE ? OR nim LIKE ? OR email LIKE ?';
            $like = '%' . $keyword . '%';
            $params = [$like, $like, $like];
        }

        // MENDAPATKAN ANGKA TOTAL DATA USERS YANG TELAH DILIHAT
        $totalResult = $this->db->fetchOne("SELECT COUNT(*) AS n FROM users $where", $params);
        $total = (int) ($totalResult['n'] ?? 0);

        $page = max(1, $page);
        $offset = ($page - 1) * self::PER_PAGE;

        $items = $this->db->fetchAll(
            'SELECT ' . self::COLUMNS . ' FROM users ' . $where . ' ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [...$params, self::PER_PAGE, $offset]
        );

        return [
            'data' => $items,
            'page' => $page,
            'per_page' => self::PER_PAGE,
            'total' => $total,
            'total_pages' => $total > 0 ? (int) ceil($total / self::PER_PAGE) : 0,
        ];
    }

    // MENONAKTIFK DAN AKTIFKAN USERS
    public function setUserActive(string $nim, bool $aktif, string $nim_sendiri): void {
        if ($nim === $nim_sendiri) {
            throw new Exception ("Anda tidak dapat mematikan Nim akun anda sendiri");
        }

        $this->db->execute('UPDATE users SET is_active = ? WHERE nim = ?', [$aktif ? 1 : 0, $nim]);
    }

    // TOTAL SEMUA USER
    public function countAllUsers(): int {
        // MELAKUKAN FUNGSI AGREGASI UNTUK MENDAPATKAN TOTAL DATA USERS
        $row = $this->db->fetchOne('SELECT COUNT(*) AS n FROM users ');
        // MENGAMBALIKAN NILAI TOTAL DATA USERS
        return (int) $row['n'];
    }
}
?>