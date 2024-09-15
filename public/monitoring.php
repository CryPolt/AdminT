<?php

require '../vendor/autoload.php';

use phpseclib3\Net\SSH2;

// Конфигурация
$sshHost = '10.23.247.5';
$sshUser = 'vpoltavskiy';
$sshPassword = '9nsoyAinO8GI';

$keydbHost = 'keydb';
$keydbPort = 6380;
$keydbDbIndex = 15;

// Подключение к SSH
$ssh = new SSH2($sshHost);
if (!$ssh->login($sshUser, $sshPassword)) {
    die('Login Failed');
}

// Выполнение команды для получения информации от KeyDB
$redisInfoCommand = "redis-cli -h $keydbHost -p $keydbPort -n $keydbDbIndex INFO";
$redisInfo = $ssh->exec($redisInfoCommand);

// Разделение информации на строки
$lines = explode("\n", $redisInfo);
$infoData = [];

// Пример обработки данных для сохранения
foreach ($lines as $line) {
    $parts = explode(":", $line, 2);
    if (count($parts) == 2) {
        $infoData[trim($parts[0])] = trim($parts[1]);
    }
}

// Функция для получения информации по ключу
function getInfoValue($key, $infoData) {
    return isset($infoData[$key]) ? $infoData[$key] : 'N/A';
}

// Сохранение данных для графиков (удалено)
$dataFile = 'metrics.json';
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);

    $data24h = array_filter($data, function ($entry) {
        return strtotime($entry['timestamp']) >= strtotime('-24 hours');
    });

    $data15m = array_filter($data, function ($entry) {
        return strtotime($entry['timestamp']) >= strtotime('-15 minutes');
    });

} else {
    $data24h = [];
    $data15m = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KeyDB Monitoring</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e2a3a;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #2d3a50;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        h1 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 20px;
        }
        #infoContainer {
            margin-top: 20px;
        }
        .info-section {
            display: none;
        }
        .info-section.active {
            display: block;
        }
        .info-buttons button {
            background-color: #4e73df;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
        }
        .info-buttons button.active {
            background-color: #2e59d9;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #4e73df;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
<button class="back-button" onclick="window.location.href='/auth_view.php'">Вернуться на главную</button>
<div class="container">
    <h1>KeyDB Monitoring</h1>

    <div id="infoContainer">
        <div class="info-buttons">
            <button data-target="info">ALL INFO</button>
            <button data-target="general">General</button>
            <button data-target="memory">Memory</button>
            <button data-target="cpu">CPU</button>
            <button data-target="connections">Connections</button>
            <button data-target="replication">Replication</button>
            <button data-target="database">Database Keys</button>
        </div>

        <div id="info" class="info-section">
            <h2>All Information</h2>
            <p><strong><?php echo nl2br(htmlspecialchars(print_r($infoData, true))); ?></strong></p>
        </div>

        <div id="general" class="info-section">
            <h2>General Information</h2>
            <p><strong>Redis Version:</strong> <?php echo getInfoValue('redis_version', $infoData); ?></p>
            <p><strong>OS:</strong> <?php echo getInfoValue('os', $infoData); ?></p>
            <p><strong>Uptime:</strong> <?php echo getInfoValue('uptime_in_days', $infoData); ?> days</p>
            <p><strong>Role:</strong> <?php echo getInfoValue('role', $infoData); ?></p>
        </div>

        <div id="memory" class="info-section">
            <h2>Memory Usage</h2>
            <p><strong>Used Memory:</strong> <?php echo getInfoValue('used_memory_human', $infoData); ?></p>
            <p><strong>Memory Fragmentation Ratio:</strong> <?php echo getInfoValue('mem_fragmentation_ratio', $infoData); ?></p>
            <p><strong>Total System Memory:</strong> <?php echo getInfoValue('total_system_memory_human', $infoData); ?></p>
        </div>

        <div id="cpu" class="info-section">
            <h2>CPU Usage</h2>
            <p><strong>CPU System:</strong> <?php echo getInfoValue('used_cpu_sys', $infoData); ?> seconds</p>
            <p><strong>CPU User:</strong> <?php echo getInfoValue('used_cpu_user', $infoData); ?> seconds</p>
        </div>

        <div id="connections" class="info-section">
            <h2>Connections</h2>
            <p><strong>Total Connections Received:</strong> <?php echo getInfoValue('total_connections_received', $infoData); ?></p>
            <p><strong>Connected Clients:</strong> <?php echo getInfoValue('connected_clients', $infoData); ?></p>
        </div>

        <div id="replication" class="info-section">
            <h2>Replication</h2>
            <p><strong>Connected Slaves:</strong> <?php echo getInfoValue('connected_slaves', $infoData); ?></p>
            <p><strong>Master Host:</strong> <?php echo getInfoValue('master_host', $infoData); ?></p>
            <p><strong>Master Port:</strong> <?php echo getInfoValue('master_port', $infoData); ?></p>
        </div>
        <div id="database" class="info-section">
            <h2>Database keys</h2>
            <p><strong>db1:</strong> <?php echo getInfoValue('db1', $infoData); ?></p>
            <p><strong>db5:</strong> <?php echo getInfoValue('db5', $infoData); ?></p>
            <p><strong>db9:</strong> <?php echo getInfoValue('db9', $infoData); ?></p>
            <p><strong>db10:</strong> <?php echo getInfoValue('db10', $infoData); ?></p>
            <p><strong>db15:</strong> <?php echo getInfoValue('db15', $infoData); ?></p>
        </div>
    </div>
</div>

<script>
    // Toggle info sections
    document.querySelectorAll('.info-buttons button').forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            document.querySelectorAll('.info-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(targetId).classList.add('active');

            document.querySelectorAll('.info-buttons button').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
        });
    });

    // Show the general section by default
    document.querySelector('.info-buttons button').click();
</script>
</body>
</html>
