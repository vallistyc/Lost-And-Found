<?php 
class Notification2 {
    // PROPERTY
    private int $id;
    private int $userId;
    private string $title;
    private string $body;
    private string $createdAt;
    private bool $isRead;

    // METHODE 
    // METHODE SETTER GUNANYA UNTUK MENGISI PROPERTY
    public function __construct(int $id, int $userId, string $title, string $body, string $createdAt, bool $isRead = false) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->isRead = $isRead;
    }

    // METHODE GETTER
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

    public static function create(int $userId, string $title, string $body) : self {

    }


}