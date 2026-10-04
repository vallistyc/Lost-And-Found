<?php
class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MAHASISWA = 'mahasiswa';

    private const PER_PAGE = 12;
    private const COLUMNS = 'nim, nama_user, no_hp, role, is_active, created_at';

    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    public function register(array $data): int
    {
        $nama = trim((string) ($data['nama_user'] ?? $data['nama'] ?? ''));
        $nim = trim((string) ($data['nim'] ?? ''));
        $noHp = trim((string) ($data['no_hp'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($nama === '' || $nim === '' || $noHp === '' || $password === '') {
            throw new Exception('Semua kolom wajib diisi.');
        }
        if (mb_strlen($nama) > 100) {
            throw new Exception('Nama maksimal 100 karakter.');
        }
        if (mb_strlen($nim) > 20) {
            throw new Exception('NIM maksimal 20 karakter.');
        }
        if (!preg_match('/^(08|62)[0-9]{8,12}$/', $noHp) || mb_strlen($noHp) > 20) {
            throw new Exception('No HP harus diawali 08 atau 62 dan terdiri dari 10-14 digit.');
        }
        if (strlen($password) < 8) {
            throw new Exception('Password minimal 8 karakter.');
        }
        if (isset($data['password_konfirmasi']) && $data['password_konfirmasi'] !== $password) {
            throw new Exception('Konfirmasi password tidak cocok.');
        }
        if ($this->db->fetchOne('SELECT nim FROM users WHERE nim = ? LIMIT 1', [$nim]) !== null) {
            throw new Exception('NIM sudah terdaftar.');
        }

        try {
            return $this->db->execute(
                'INSERT INTO users (nim, nama_user, no_hp, password_user, role, is_active)
                 VALUES (?, ?, ?, ?, ?, 1)',
                [$nim, $nama, $noHp, password_hash($password, PASSWORD_DEFAULT), self::ROLE_MAHASISWA]
            );
        } catch (PDOException $e) {
            $cause = $e->getPrevious() ?? $e;
            if ($cause->getCode() === '23000') {
                throw new Exception('NIM sudah terdaftar.');
            }
            throw $e;
        }
    }

    public function login(string $nim, string $password): array
    {
        $user = $this->db->fetchOne(
            'SELECT ' . self::COLUMNS . ', password_user FROM users WHERE nim = ? LIMIT 1',
            [trim($nim)]
        );

        if ($user === null || !password_verify($password, $user['password_user'])) {
            throw new Exception('NIM atau password salah.');
        }
        if ((int) $user['is_active'] === 0) {
            throw new Exception('Akun Anda dinonaktifkan. Hubungi admin.');
        }

        unset($user['password_user']);
        return $user;
    }

    public function findById(string $nim): ?array
    {
        return $this->db->fetchOne(
            'SELECT ' . self::COLUMNS . ' FROM users WHERE nim = ? LIMIT 1',
            [trim($nim)]
        );
    }

    public function profileUpdate(string $nim, string $nama, string $noHp): void
    {
        $nama = trim($nama);
        $noHp = trim($noHp);

        if ($nama === '') {
            throw new Exception('Nama wajib diisi.');
        }
        if (mb_strlen($nama) > 100) {
            throw new Exception('Nama maksimal 100 karakter.');
        }
        if (!preg_match('/^(08|62)[0-9]{8,12}$/', $noHp) || mb_strlen($noHp) > 20) {
            throw new Exception('No HP harus diawali 08 atau 62 dan terdiri dari 10-14 digit.');
        }

        $this->db->execute(
            'UPDATE users SET nama_user = ?, no_hp = ? WHERE nim = ?',
            [$nama, $noHp, trim($nim)]
        );
    }

    public function changePassword(string $nim, string $passwordLama, string $passwordBaru): void
    {
        $user = $this->db->fetchOne(
            'SELECT password_user FROM users WHERE nim = ? LIMIT 1',
            [trim($nim)]
        );

        if ($user === null || !password_verify($passwordLama, $user['password_user'])) {
            throw new Exception('Password lama salah.');
        }
        if (strlen($passwordBaru) < 8) {
            throw new Exception('Password baru minimal 8 karakter.');
        }

        $this->db->execute(
            'UPDATE users SET password_user = ? WHERE nim = ?',
            [password_hash($passwordBaru, PASSWORD_DEFAULT), trim($nim)]
        );
    }

    public function listAllUsers(string $keyword = '', int $page = 1): array
    {
        $where = '';
        $params = [];
        $keyword = trim($keyword);

        if ($keyword !== '') {
            $where = 'WHERE nama_user LIKE ? OR nim LIKE ? OR no_hp LIKE ?';
            $like = '%' . $keyword . '%';
            $params = [$like, $like, $like];
        }

        $total = (int) ($this->db->fetchOne("SELECT COUNT(*) AS n FROM users $where", $params)['n'] ?? 0);
        $page = max(1, $page);
        $offset = ($page - 1) * self::PER_PAGE;
        $items = $this->db->fetchAll(
            'SELECT ' . self::COLUMNS . " FROM users $where ORDER BY created_at DESC LIMIT ? OFFSET ?",
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

    public function setUserActive(string $nim, bool $aktif, string $nimSendiri): void
    {
        $nim = trim($nim);
        if ($nim === trim($nimSendiri)) {
            throw new Exception('Anda tidak dapat menonaktifkan akun sendiri.');
        }
        if ($this->findById($nim) === null) {
            throw new Exception('Pengguna tidak ditemukan.');
        }

        $this->db->execute('UPDATE users SET is_active = ? WHERE nim = ?', [$aktif ? 1 : 0, $nim]);
    }

    public function countAllUsers(): int
    {
        $row = $this->db->fetchOne('SELECT COUNT(*) AS n FROM users');
        return (int) ($row['n'] ?? 0);
    }
}
