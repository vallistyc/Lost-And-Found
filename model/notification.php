<?php 
class Notification {
    private int $id;
    private int $userId;
    private string $title;
    private string $body;
    private string $createdAt;
    private bool $isRead;

    // METHOD CONSTRUCTOR
    public function __construct(int $id, int $userId, string $title, string $body, string $createdAt, bool $isRead = false) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->isRead = $isRead;
    }

    public function getId() : int {
        return $this->id;
    }

    public function getUserId() : int {
        return $this->userId;
    }

    public function getTitle() : string {
        return $this->title;
    }
    
    public function getBody() : string {
        return $this->body;
    }

    public function getCreatedAt() : string {
        return $this->createdAt;
    }

    public function isRead() : bool {
        return $this->isRead;
    }

    // CREATE METHODE
    public static function create(int $userId, string $title, string $body): self {
        // Bikin koneksi ke database
        $db = new DBconnection();
        // Menyimpan codingan SQL kita
        $query = 'INSERT INTO notifications (user_id, title, body) VALUES ($1, $2, $3) RETURNING id, created_at';
        // Mengirim QUERY ke Database menggunakan METHOD SEND_QUERY
        $respon = $db->send_query($query, [$userId, $title, $body]);
        // Menutup Koneksi ke Database
        $db->close_connection();

        // Pengecekan Keberhasilan Query
        // DatabaseException gunanya untuk menampilkan pesan error yang lebih spesifik alasan errornya
        if ($respon->status === false) {
            throw new DatabaseException($respon->message);
        }
        
        // Mengembalikan nilai berupa objek baru dengan property yang telah ditentukan (self)
        return new self((int)$respon->data[0]['id'], $userId, $title, $body, $respon->data[0]['created_at']);
    }

    // READ METHOD 
    // by id
    public static function find(int $id): ?self {
        // Membuka koneksi ke database dengan cara membuat objek class DBconnection. Begitu objek terbentuk, maka langsung terhubung ke database karena method init_connection() sudah tertera di dalamnya
        $db = new DBconnection();
        // Menuliskan kode query dengan template $1 $2 $3 untuk menghindari SQL injection
        $query = 'SELECT * FROM notifications WHERE id = $1';
        // Mengirim query ke database
        $respon = $db->send_query($query, [$id]);
        
        // Cek, apakah penulisan query kita udah bener
        if ($respon->status === false) {
            throw new DatabaseException($respon->message);
        }

        // Cek, apakah ada data di tabelnya. Bisa pake fungsi bawaan empty() atau count($respon->data) === 0 yang menghitung ada berapa banyak data pada tabel tersebut
        if (empty($respon->data)) {
            return null;
        }

        return new self(
            // Karena array Data ter return sebagai string, sehingga harus diubah ke int
            (int) $respon->data[0]['id'],
            (int) $respon->data[0]['user_id'],
            $respon->data[0]['title'],
            $respon->data[0]['body'],
            $respon->data[0]['created_at'],
            $respon->data[0]['is_read'] === 't'
        );
    }

    // by user
    public static function findByUser(int $userId): array {
        $db = new DBconnection();
        $query = 'SELECT * FROM notifications WHERE user_id = $1';
        $respon = $db->send_query($query, [$userId]);

        if ($respon->status === false) {
            throw new DatabaseException($respon->message);
        }

        $result = [];

        // cek apakah ada record data. Tpi karena tipe data return nya tidak ada ? (nullable), maka kita mesti return dalam bentuk array kosongan
        if (empty($respon->data)) {
            return $result;
        }

        foreach ($respon->data as $row) {
            $result[] = new self(
                (int) $row['id'],
                $row['title'],
                $row['body'],
                $row['created_at'],
                $row['is_read'] === 't'
            );
        }

        return $result;
    }

    // UPDATE METHOD
    public function update(string $title, string $body) : void {
        $db = new DBconnection();
        $query = 'UPDATE notifications SET title=$1, body=$2 WHERE id=$3';
        $respon = $db->send_query($query, [$title, $body, $this->id]);

        if ($respon->status === false) {
            throw new DatabaseException($respon->message);
        }
        
        else {
            $this->title = $title;
            $this->body = $body;
        }
    }

    // Method Mark As Read
    public function markAsRead() :void {
        $db = new DBconnection();
        $query = 'UPDATE notifications SET is_read=true WHERE id = $1';
        $respon = $db->send_query($query, [$this->id]);

        if ($respon->status === false) {
            throw new DatabaseException($respon->message);
        }

        else {
            $this->isRead = true;
        }
    }

    public function delete() :void {
        $db = new DBconnection();
        $query = 'DELETE FROM notifications WHERE id=$1';
        $respon = $db->send_query($query, [$this->id]);

        if ($respon->status === false) {
            throw new DatabaseException($respon->message);
        }
    }

    public static function unread (array $list) :array {
        $belumdibaca=[];
        foreach ($list as $notif) {
            if ($notif->isRead === false) {
                $belumdibaca[]= $notif;
            }
        }
        return $belumdibaca;
    }

    public static function countUnread(array $list) : int {
        $arraybelumdibaca=array_filter($list, fn($notif)=>$notif->isRead === false);
        return count($arraybelumdibaca);
    }

    public function __toString()
    {
        if ($this->isRead === false) {
            return $this->title . ' - UNREAD';
        }

        else {
            return $this->title . ' - READ';
        }
    }
}

