<?php

namespace App\Model;

class UserModel
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function loadUsers()
    {
        $json = file_get_contents($this->filename);
        return json_decode($json, true);
    }

    public function validateUser($username, $password)
    {
        $users = $this->loadUsers();
        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                return true;
            }
        }
        return false;
    }
}
