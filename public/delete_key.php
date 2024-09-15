<?php
require '../vendor/autoload.php';

use App\src\Model\SSHConnection;
use App\src\Service\KeyDBService;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$databaseId = (int)$_POST['database'];
$key = $_POST['key'];

$sshHost = $_ENV['SSH_HOST'];
$sshUser = $_ENV['SSH_USER'];
$sshPassword = $_ENV['SSH_PASSWORD'];
$keydbHost = $_ENV['KEYDB_HOST'];
$keydbPort = (int)$_ENV['KEYDB_PORT'];

$sshConnection = new SSHConnection($sshHost, $sshUser, $sshPassword);
$keydbService = new KeyDBService(
    $keydbHost,
    $keydbPort,
    $databaseId
);

try {
    $command = "redis-cli -h {$keydbHost} -p {$keydbPort} <<EOF
select {$databaseId}
del {$key}
EOF";
    $output = $sshConnection->exec($command);

    if (strpos($output, 'OK') !== false) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (\Exception $e) {
    error_log('Error: ' . $e->getMessage());
    echo json_encode(['success' => false]);
}
