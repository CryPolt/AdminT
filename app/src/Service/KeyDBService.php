<?php

namespace App\src\Service;

use App\src\Model\SSHConnection;

class KeyDBService
{
    private $sshConnection;
    private $keydbHost;
    private $keydbPort;
    private $databaseId;

    public function __construct(SSHConnection $sshConnection, string $keydbHost, int $keydbPort, int $databaseId)
    {
        $this->sshConnection = $sshConnection;
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
        $output = $this->sshConnection->exec($command);
        $lines = explode("\n", trim($output));

        error_log('Database Data Output: ' . print_r($lines, true));

        if (trim($lines[0]) === 'OK') {
            array_shift($lines);
            return $lines;
        } else {
            throw new \Exception("Failed to select database {$this->databaseId}");
        }
    }

    public function getValuesForKeys(array $keys): array
    {
        $results = [];
        if (!empty($keys)) {
            $keysList = implode(' ', array_map(fn($key) => escapeshellarg($key), $keys));
            $command = "redis-cli -h {$this->keydbHost} -p {$this->keydbPort} --raw <<EOF
select {$this->databaseId}
mget {$keysList}
EOF";

            $output = $this->sshConnection->exec($command);
            $lines = explode("\n", trim($output));

            error_log('Values for Keys Output: ' . print_r($lines, true));

            if (trim($lines[0]) === 'OK') {
                array_shift($lines);
                $values = array_map('trim', $lines);

                foreach ($keys as $index => $key) {
                    if (isset($values[$index])) {
                        $results[$key] = $values[$index];
                    }
                }
            } else {
                throw new \Exception("Failed to select database {$this->databaseId}");
            }
        }
        return $results;
    }
}
