<?php
class RoleModel extends BaseModel
{
    protected string $tabel = 'role';
    protected string $primary_key = 'idrole';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO role (nama_role) VALUES ($1) RETURNING idrole',
            [$data['nama_role']]
        );
    }
}