<?php
require_once __DIR__ . '/../partials/guard.php';
wajibRole(User::ROLE_MAHASISWA, new User(new DBconnection()));
Helper::redirect(BASE_URL . '/histori.php');