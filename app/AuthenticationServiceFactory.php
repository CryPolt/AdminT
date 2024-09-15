<?php

namespace App;

use Laminas\Authentication\AuthenticationService;


class AuthenticationServiceFactory
{
    public static function create()
    {
        return new AuthenticationService();
    }
}
