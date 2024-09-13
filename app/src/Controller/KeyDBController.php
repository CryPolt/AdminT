<?php

namespace App\src\Controller;

use App\src\Service\KeyDBService;

class KeyDBController
{
    private KeyDBService $keyDBService;

    public function __construct(KeyDBService $keyDBService)
    {
        $this->keyDBService = $keyDBService;
    }

    public function showDatabases(): void
    {
        try {
            $data = $this->keyDBService->getDatabaseData();
            header('Content-Type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Error: ' . $e->getMessage();
        }
    }
}
