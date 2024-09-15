<?php

namespace App\Controller;

use App\Model\UserModel;

class AuthController
{
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function handleLogin()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->validateUser($username, $password)) {
                // Успешная авторизация, перенаправляем
                header('Location: /success.php');
                exit();
            } else {
                $error = 'Неверное имя пользователя или пароль.';
            }
        }

        include __DIR__ . '/../public/login.php';
    }
}
