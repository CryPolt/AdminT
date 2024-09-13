<?php

namespace App\src\Model;

class PostgresConnection
{
    private $pdo;

    public function __construct(string $host, string $user, string $password, string $database, int $port)
    {
        $dsn = "pgsql:host=$host;port=$port;dbname=$database";
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function prepare(string $query): \PDOStatement
    {
        return $this->pdo->prepare($query);
    }

    public function query(string $query)
    {
        return $this->pdo->query($query);
    }
}
