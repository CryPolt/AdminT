<?php

namespace App\src\Controller;

use App\src\Service\PostgresService;

class PostgresController
{
    private $postgresService;

    public function __construct(PostgresService $postgresService)
    {
        $this->postgresService = $postgresService;
    }

    public function showExternalUsers(): void
    {
        try {
            $phones = $_GET['phones'] ?? [];
            if (!is_array($phones)) {
                $phones = [$phones];
            }
            $data = $this->postgresService->getExternalUsersByPhone($phones);
            header('Content-Type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve data', 'message' => $e->getMessage()]);
        }
    }
}
