<?php
class Dokter extends User
{
    private string $no_izin;
    private string $spesialisasi;

    public function __construct(
        int $iduser,
        string $nama,
        string $email,
        string $no_izin,
        string $spesialisasi,
        string $password = ''
    ) {
        parent::__construct($iduser, $nama, $email, $password);
        $this->no_izin = $no_izin;
        $this->spesialisasi = $spesialisasi;
    }

    public function get_no_izin(): string
    {
        return $this->no_izin;
    }

    public function get_spesialisasi(): string
    {
        return $this->spesialisasi;
    }

    public function get_user(): array
    {
        return array_merge(parent::get_user(), [
            'no_izin' => $this->no_izin,
            'spesialisasi' => $this->spesialisasi,
        ]);
    }

    public function __toString(): string
    {
        return parent::__toString() . ' - dokter, izin ' . $this->no_izin;
    }
}