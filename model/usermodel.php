<?php
class UserModel extends BaseModel
{
    protected string $tabel = 'user';
    protected string $primary_key = 'iduser';

    public function find_all(): array
    {
        $respon = $this->db->send_query(
            'SELECT iduser, nama, email FROM "user" ORDER BY iduser'
        );
        return $respon->status ? $respon->data : [];
    }

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO "user" (nama, email, password) VALUES ($1, $2, $3) RETURNING iduser',
            [$data['nama'], strtolower(trim($data['email'])), password_hash($data['password'], PASSWORD_DEFAULT)]
        );
    }

    public function find_by_email(string $email): ?array
    {
        $respon = $this->db->send_query(
            'SELECT iduser, nama, email, password FROM "user" WHERE email = $1',
            [strtolower(trim($email))]
        );
        return $respon->status && count($respon->data) > 0 ? $respon->data[0] : null;
    }

    public function simpan_role_aktif(int $iduser, int $idrole): Respon
    {
        return $this->db->send_query(
            'INSERT INTO user_role (iduser, idrole, status) VALUES ($1, $2, TRUE)',
            [$iduser, $idrole]
        );
    }

    public function verifikasi(string $email, string $password): ?User
    {
        $data = $this->find_by_email($email);
        if ($data === null || !password_verify($password, $data['password'])) {
            return null;
        }

        $user = $this->buat_object($data);
        return $user;
    }

    public function buat_object(array $data): User
    {
        $iduser = (int) $data['iduser'];
        $pemilik = (new PemilikModel($this->db))->find_by_iduser($iduser);
        if ($pemilik !== null) {
            $user = new Pemilik(
                $iduser,
                $data['nama'],
                $data['email'],
                $pemilik['no_wa'],
                $pemilik['alamat'],
                $data['password'] ?? ''
            );
        } else {
            $dokter = (new DokterModel($this->db))->find_by_iduser($iduser);
            $user = $dokter === null
                ? new User($iduser, $data['nama'], $data['email'], $data['password'] ?? '')
                : new Dokter(
                    $iduser,
                    $data['nama'],
                    $data['email'],
                    $dokter['no_izin'],
                    $dokter['spesialisasi'],
                    $data['password'] ?? ''
                );
        }
        $this->isi_role($user);
        return $user;
    }

    private function isi_role(User $user): void
    {
        $respon = $this->db->send_query(
            'SELECT r.idrole, r.nama_role, ur.status
             FROM user_role ur JOIN role r ON r.idrole = ur.idrole
             WHERE ur.iduser = $1 ORDER BY r.idrole',
            [$user->get_iduser()]
        );
        if (!$respon->status) {
            return;
        }
        foreach ($respon->data as $row) {
            $status = in_array(strtolower((string) $row['status']), ['t', 'true', '1'], true);
            $user->set_role(new Role((int) $row['idrole'], $row['nama_role'], $status));
        }
    }
}