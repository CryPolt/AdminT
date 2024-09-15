<?php

namespace App\src\Service;

class KeyDBService
{
    private $keydbHost;
    private $keydbPort;
    private $databaseId;

    public function __construct(string $keydbHost, int $keydbPort, int $databaseId)
    {
        $this->keydbHost = $keydbHost;
        $this->keydbPort = $keydbPort;
        $this->databaseId = $databaseId;
    }

    public function getDatabaseData(): array
    {
        $command = "redis-cli -h {$this->keydbHost} -p {$this->keydbPort} --raw <<EOF
select {$this->databaseId}
keys *
EOF";

        $output = shell_exec($command);
        $lines = explode("\n", trim($output));

        // Логируем весь вывод для отладки
        error_log('Database Data Output: ' . print_r($lines, true));

        // Обработка вывода команды
        return $lines;
    }

    public function getValuesForKeys(array $keys): array
    {
        $results = [];
        if (!empty($keys)) {
            $keysList = implode(' ', array_map('escapeshellarg', $keys));
            $command = "redis-cli -h {$this->keydbHost} -p {$this->keydbPort} --raw <<EOF
select {$this->databaseId}
mget {$keysList}
EOF";

            $output = shell_exec($command);
            $lines = explode("\n", trim($output));

            // Логируем весь вывод для отладки
            error_log('Values for Keys Output: ' . print_r($lines, true));

            $values = array_map('trim', $lines);

            foreach ($keys as $index => $key) {
                if (isset($values[$index])) {
                    $results[$key] = $values[$index];
                }
            }
        }
        return $results;
    }
}
