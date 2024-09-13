<?php

namespace App\src\Service;

use App\src\Model\PostgresConnection;

class PostgresService
{
    private $postgresConnection;

    public function __construct(PostgresConnection $postgresConnection)
    {
        $this->postgresConnection = $postgresConnection;
    }

    public function getExternalUsersByPhone(array $phones): array
    {
        $placeholders = implode(',', array_fill(0, count($phones), '?'));
        $query = "SELECT * FROM external_users WHERE phone IN ($placeholders)";

        $stmt = $this->postgresConnection->prepare($query);
        $stmt->execute($phones);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
