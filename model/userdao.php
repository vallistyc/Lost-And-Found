<?php
class UserDAO
{
    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    public function insert(User $user): ?int
    {
        $respon = $this->db->send_query(
            'INSERT INTO "user" (nama, email, password) VALUES ($1, $2, $3) RETURNING iduser',
            [$user->get_nama(), $user->get_email(), $user->get_password()]
        );
        if ($respon->status && count($respon->data) > 0) {
            return (int) $respon->data[0]['iduser'];
        }
        return null;
    }

    public function find_by_email(string $email): ?User
    {
        $respon = $this->db->send_query('SELECT * FROM "user" WHERE email = $1', [$email]);
        if ($respon->status && count($respon->data) > 0) {
            $row = $respon->data[0];
            return new User((int) $row['iduser'], $row['nama'], $row['email'], $row['password']);
        }
        return null;
    }

    public function update(User $user): bool
    {
        $respon = $this->db->send_query(
            'UPDATE "user" SET nama = $1, email = $2, password = $3 WHERE iduser = $4',
            [$user->get_nama(), $user->get_email(), $user->get_password(), $user->get_iduser()]
        );
        return $respon->status;
    }

    public function delete(int $iduser): bool
    {
        $respon = $this->db->send_query('DELETE FROM "user" WHERE iduser = $1', [$iduser]);
        return $respon->status;
    }
}