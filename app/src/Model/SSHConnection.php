<?php

namespace App\src\Model;

class SSHConnection
{
    private $host;
    private $user;
    private $password;

    public function __construct(string $host, string $user, string $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    public function exec(string $command): string
    {
        // Используем команду ssh для выполнения команд на удаленном сервере
        // Убедитесь, что sshpass установлен, если вы используете его
        $sshCommand = "sshpass -p {$this->password} ssh -o StrictHostKeyChecking=no {$this->user}@{$this->host} '{$command}'";
        $output = shell_exec($sshCommand);

        // Логирование команды и вывода
        error_log("Executed command: $sshCommand");
        error_log("Output: $output");

        if ($output === null) {
            throw new \RuntimeException("Failed to execute command: {$sshCommand}");
        }

        return $output;
    }
}
