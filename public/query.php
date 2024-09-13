<?php
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$postgresHost = $_ENV['POSTGRES_HOST'];
$postgresUser = $_ENV['POSTGRES_USER'];
$postgresPassword = $_ENV['POSTGRES_PASSWORD'];
$postgresDb = $_ENV['POSTGRES_DB'];
$postgresPort = 5432;

try {
    $dsn = "pgsql:host=$postgresHost;port=$postgresPort;dbname=$postgresDb";
    $pdo = new PDO($dsn, $postgresUser, $postgresPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Получаем значение телефона из запроса
    $phone = $_GET['phone'] ?? '';

    $data = [];

    if ($phone) {
        try {
            $stmt = $pdo->prepare('SELECT * FROM external_users WHERE phone = :phone');
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $data['error'] = 'Query error: ' . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    $data['error'] = 'Database connection error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($data);
