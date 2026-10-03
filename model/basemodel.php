<?php
class BaseModel
{
    protected DBconnection $db;
    protected string $tabel;
    protected string $primary_key;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    public function find_all(): array
    {
        $respon = $this->db->send_query('SELECT * FROM "' . $this->tabel . '"');
        return $respon->status ? $respon->data : [];
    }

    public function find_by_id(int $id): ?array
    {
        $respon = $this->db->send_query(
            'SELECT * FROM "' . $this->tabel . '" WHERE ' . $this->primary_key . ' = $1',
            [$id]
        );
        return $respon->status && count($respon->data) > 0 ? $respon->data[0] : null;
    }

    public function nama_tabel(): string
    {
        return $this->tabel;
    }

    public function insert(array $data): Respon
    {
        return new Respon(false, 'Method insert() belum diimplementasikan pada ' . get_class($this));
    }
}