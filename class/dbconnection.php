<?php 
class DBconnection {
    private string $host = "localhost";
    private string $dbname = "lost_and_found";
    private string $user = "root";
    private string $pass = "";
    private ?PDO $pdo = null;

    /* PEMBUUATAN KONEKSI SEKALIGUS ERROR HANDLING
        Untuk menghubungkan ke database cukup dengan membuat objek class Dbconnection baru.
        Apabila gagal, maka akan melempar exception PDOException */
    public function __construct() {
        // Mengubah parameter prop host dan dbname ke string untuk dimasukkan sebagai paramter intanisiasi objek PDO
        $con_str = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        try {
            $this->pdo = new PDO($con_str, $this->user, $this->pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            $this->pdo->exec("SET time_zone = '+07:00'");
        } catch (PDOException $e) {
            error_log('Koneksi gagal:' . $e->getMessage());
            throw new PDOException("Databas gagal dihubungkan: ", 0, $e);
        }   
    }

    // Menutun Koneksi database secara otomatis
    public function __destruct() {
        $this->pdo = null;
    }

    // Perintah untuk mengeksekusi query SQL berdasarkan parameter yang terpisah
    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            // Mengirim struktur Query ke DBMS
            $stmt = $this->pdo->prepare($sql);
            /* Mengirim parameter ke DBMS yang nantinya akan dimasukkan ke Query 
            Metode lain untuk mencegah SQL injection
            */
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query gagal: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new PDOException('Terjadi kesalahan pada database.', 0, $e);
        }
    }

    // Perintah untuk mengambil semua baris/record (hasil kosong dikembalikan sebagai array kosong)
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    // Perintah untuk mengambil satu baris/record
    public function fetchOne(string $sql, array $params=[]):?array {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }
    
    // INSERT, UPDATE, DELETE. Mengembalikan jumlah baris yang terdampak, termasuk nol.
    public function execute(string $sql, array $params): int {
        return $this->query($sql, $params)->rowCount();
    }

    // Mendapatkan ID dari baris yang baru saja diinsert
    public function lastInsertId(): int {
        return (int) $this->pdo->lastInsertId();
    }
}