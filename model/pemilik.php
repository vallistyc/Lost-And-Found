<?php
class Pemilik extends User
{
    private string $no_wa;
    private string $alamat;

    public function __construct(
        int $iduser,
        string $nama,
        string $email,
        string $no_wa,
        string $alamat,
        string $password = ''
    ) {
        parent::__construct($iduser, $nama, $email, $password);
        $this->no_wa = $no_wa;
        $this->alamat = $alamat;
    }

    public function get_no_wa(): string
    {
        return $this->no_wa;
    }

    public function get_alamat(): string
    {
        return $this->alamat;
    }

    public function get_user(): array
    {
        return array_merge(parent::get_user(), [
            'no_wa' => $this->no_wa,
            'alamat' => $this->alamat,
        ]);
    }

    public function __toString(): string
    {
        return parent::__toString() . ' - pemilik, WA ' . $this->no_wa;
    }
}