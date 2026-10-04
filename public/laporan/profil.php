<?php
require_once __DIR__ . '/../partials/guard.php';
wajibLogin(new User(new DBconnection()));
Helper::redirect(BASE_URL . '/profil.php');