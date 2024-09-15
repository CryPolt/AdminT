<?php

function isPortAvailable($port)
{
    $connection = @fsockopen('10.23.247.5', $port);
    if ($connection) {
        fclose($connection);
        return false;
    }
    return true;
}

$port = 9605;
while (!isPortAvailable($port)) {
    $port++;
}

echo "Запуск сервера на порту: $port\n";
$documentRoot = __DIR__ . '/public';
$command = "php -S 10.23.247.5:$port -t $documentRoot";

// Запуск команды в фоновом режиме с использованием nohup
$command = "nohup $command > /dev/null 2>&1 &";

echo "Команда: $command\n";

shell_exec($command);
echo "Сервер запущен на порту $port\n";
