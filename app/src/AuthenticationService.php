<?php

namespace App;

use Laminas\Authentication\Adapter\Memory as MemoryAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Adapter\AdapterInterface;

class AuthenticationServiceFactory
{
    public static function create()
    {
        $config = include __DIR__ . '/../config/auth.php';
        $authConfig = $config['authentication'];

        $adapter = new MemoryAdapter([
            'admin' => 'password123'
        ]);

        $authService = new AuthenticationService();
        $authService->setAdapter($adapter);

        return $authService;
    }
}
