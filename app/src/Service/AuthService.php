<?php

namespace App\src\Service;

class AuthService
{
    private $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function authenticate($username, $password)
    {
        if (isset($this->users[$username]) && password_verify($password, $this->users[$username])) {
            return true;
        }
        return false;
    }
}
