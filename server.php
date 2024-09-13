<?php

function isPortAvailable($port)
{
    $connection = @fsockopen('127.0.0.1', $port);
    if ($connection) {
        fclose($connection);
        return false;
    }
    return true;
}

$port = 8000;
while (!isPortAvailable($port)) {
    $port++;
}

echo "Запуск сервера на порту: $port\n";
$documentRoot = __DIR__ . '/public';
$command = "php -S 127.0.0.1:$port -t $documentRoot";
echo "Команда: $command\n";

passthru($command);
