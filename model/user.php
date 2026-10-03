<?php
class User
{
    private int $iduser;
    private string $nama;
    private string $email;
    private string $password;
    private array $role = [];

    public function __construct(int $iduser, string $nama, string $email, string $password = '')
    {
        $this->iduser = $iduser;
        $this->nama = $nama;
        $this->email = strtolower(trim($email));
        $this->password = $password;
    }

    public function get_user(): array
    {
        return [
            'iduser' => $this->iduser,
            'nama' => $this->nama,
            'email' => $this->email,
            'role' => $this->get_role_aktif()->get_data()['nama_role'],
        ];
    }

    public function set_role(Role $role): void
    {
        if ($role->get_status() === true) {
            foreach ($this->role as $r) {
                $r->set_status(false);
            }
        }
        $this->role[] = $role;
    }

    public function get_role_aktif(): Role
    {
        $role_aktif = new Role(0, '-', false);
        foreach ($this->role as $r) {
            if ($r->get_status() === true) {
                $role_aktif = $r;
            }
        }
        return $role_aktif;
    }

    public function hapus_role(int $idrole): void
    {
        foreach ($this->role as $key => $r) {
            if ($r->get_data()['idrole'] === $idrole) {
                unset($this->role[$key]);
            }
        }
        $this->role = array_values($this->role); // rapikan ulang index array
    }

    public function set_role_aktif(int $idrole): void
    {
        foreach ($this->role as $r) {
            $data = $r->get_data();
            $r->set_status($data['idrole'] === $idrole);
        }
    }

    public function get_iduser(): int { return $this->iduser; }
    public function get_nama(): string { return $this->nama; }
    public function get_email(): string { return $this->email; }
    public function get_password(): string { return $this->password; }

    // magic method: dieksekusi saat object diperlakukan sebagai string
    public function __toString(): string
    {
        return $this->nama . " <" . $this->email . ">";
    }
}