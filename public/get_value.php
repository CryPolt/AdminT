<?php
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$databaseId = (int)$_GET['database'] ?? 1;

$sshHost = $_ENV['SSH_HOST'];
$sshUser = $_ENV['SSH_USER'];
$sshPassword = $_ENV['SSH_PASSWORD'];
$keydbHost = $_ENV['KEYDB_HOST'];
$keydbPort = (int)$_ENV['KEYDB_PORT'];

$sshConnection = new \App\src\Model\SSHConnection($sshHost, $sshUser, $sshPassword);

$keydbService = new \App\src\Service\KeyDBService(
    $sshConnection,
    $keydbHost,
    $keydbPort,
    $databaseId // Передаем ID базы данных
);

$type = $_GET['key'] ?? '';
$searchValue = $_GET['search'] ?? '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$data = [];

if ($type) {
    try {
        $keys = $keydbService->getDatabaseData();

        if ($type === 'phone') {
            $filteredKeys = array_filter($keys, function($key) use ($searchValue) {
                return strpos($key, 'phone:'.$searchValue) !== false;
            });
        } else {
            $fullKey = $type . ':' . $searchValue;
            $filteredKeys = array_filter($keys, function($key) use ($fullKey) {
                return strpos($key, $fullKey) !== false;
            });
        }

        $keysToFetch = array_slice($filteredKeys, $offset);

        if (!empty($keysToFetch)) {
            $values = $keydbService->getValuesForKeys($keysToFetch);

            foreach ($keysToFetch as $key) {
                $data[$key] = $values[$key] ?? null;
            }
        }
    } catch (\Exception $e) {
        error_log('Error: ' . $e->getMessage());
        $data['error'] = 'Failed to retrieve data';
    }
}

header('Content-Type: application/json');
echo json_encode($data);
