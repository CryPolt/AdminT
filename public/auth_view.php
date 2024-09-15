<?php
session_start();

if (!isset($_SESSION['auth'])) {
    header('Location: /');
    exit();
}

$username = $_SESSION['auth'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KeyDB Database Viewer</title>
    <link rel="stylesheet" href="css/styles.css">
    <script defer src="js/scripts.js"></script>
    <style>
        .monitoring-button {
            background-color: #4e73df;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .monitoring-button:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>KeyDB Database Viewer</h1>

    <!-- Monitoring Button -->
    <button class="monitoring-button"><a href="monitoring.php"><b>Мониторинг</b></a></button>

    <div class="database-selection">
        <p>Пожалуйста, выберите базу данных:</p>
        <button onclick="selectDatabase(1)">База данных 1 - AUTH + SHORT_TOKEN </button>
        <button onclick="selectDatabase(5)">База данных 5 - SMS </button>
        <button onclick="selectDatabase(9)">База данных 9 - Setting </button>
        <button onclick="selectDatabase(10)">База данных 10 - SERVICES </button>
        <button onclick="selectDatabase(15)">База данных 15 - DEVICEID + SU_LOCK</button>
    </div>

    <div class="back-button-container" style="display: none;">
        <button onclick="goBack()">Назад к выбору базы данных</button>
    </div>

    <div class="tabs" style="display: none;">
        <button class="tablink" id="tab-di_counter" onclick="openTab('di_counter')">Ключи - di_counter</button>
        <button class="tablink" id="tab-qr_token" onclick="openTab('qr_token')">Ключи - qr_token</button>
        <button class="tablink" id="tab-fc_lock" onclick="openTab('fc_lock')">Ключи - fc_lock</button>
        <button class="tablink" id="tab-su_lock" onclick="openTab('su_lock')">Ключи - su_lock </button>
        <button class="tablink" id="tab-sms" onclick="openTab('sms')">Ключи - sms</button>
        <button class="tablink" id="tab-scanQr" onclick="openTab('scanQr')">Ключи - scan_qr</button>
        <button class="tablink" id="tab-pimmer_accessToken" onclick="openTab('pimmer:accessToken')">Ключи - pimmer:accessToken</button>
    </div>

    <div id="di_counter" class="tabcontent">
        <h2>Ключи - di_counter</h2>
        <input type="text" id="search-di_counter" placeholder="Поиск по ключам...">
        <button onclick="searchKeys('di_counter')">Поиск</button>
        <table id="key-values-di_counter"></table>
        <div id="pagination-di_counter"></div>
    </div>

    <div id="qr_token" class="tabcontent">
        <h2>Ключи - qr_token</h2>
        <input type="text" id="search-qr_token" placeholder="Поиск по ключам...">
        <button onclick="searchKeys('qr_token')">Поиск</button>
        <table id="key-values-qr_token"></table>
        <div id="pagination-qr_token"></div>
    </div>

    <div id="fc_lock" class="tabcontent">
        <h2>Ключи - fc_lock</h2>
        <input type="text" id="search-fc_lock" placeholder="Поиск по ключам...">
        <button onclick="searchKeys('fc_lock')">Поиск</button>
        <table id="key-values-fc_lock"></table>
    </div>

    <div id="sms" class="tabcontent">
        <h2>Ключи - SMS</h2>
        <input type="text" id="search-sms" placeholder="Поиск по ключам...">
        <button onclick="searchKeys('sms')">Поиск</button>
        <table id="key-values-sms"></table>
    </div>

    <div id="phone" class="tabcontent" style="display: none;">
        <h3>Phone Numbers</h3>
        <input type="text" id="search-phone" placeholder="Поиск по телефону">
        <button onclick="searchKeys('phone')">Поиск</button>
        <table id="key-values-phone"></table>
    </div>

    <div id="scanQr" class="tabcontent">
        <h2>Ключи - scanQr</h2>
        <input type="text" id="search-scanQr" placeholder="Поиск по ключам...">
        <button onclick="searchKeys('scanQr')">Поиск</button>
        <table id="key-values-scanQr"></table>
    </div>

    <div id="pimmer:accessToken" class="tabcontent">
        <h2>Ключи - pimmer:accessToken</h2>
        <input type="text" id="search-pimmer:accessToken" placeholder="Поиск по ключам...">
        <button onclick="searchKeys('pimmer:accessToken')">Поиск</button>
        <table id="key-values-pimmer:accessToken"></table>
    </div>

    <div id="su_lock" class="tabcontent">
        <h2>Ключи - su_lock</h2>
        <input type="text" id="search-su_lock" placeholder="Поиск по ключам...">
        <button onclick="searchKeys('su_lock')">Поиск</button>
        <table id="key-values-su_lock"></table>
    </div>
</div>

<?php
$host = 'ldb.tha.kz';
$port = '5432';
$dbname = 'dev_census';
$user = 'dev_census';
$password = 'Y0xC!A68^Iwi';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error connecting to the database: " . pg_last_error());
}

$phone = '';
$resultsHtml = '';

if (isset($_GET['search'])) {
    $phone = isset($_GET['phone']) ? pg_escape_string($_GET['phone']) : '';

    $query = "SELECT * FROM external_users WHERE phone = '$phone'";
    $result = pg_query($conn, $query);

    if (!$result) {
        $resultsHtml = "Error executing query: " . pg_last_error();
    } else {
        $resultsHtml .= "<table border='1'>";
        $resultsHtml .= "<tr><th>ID</th><th>Phone</th><th>Other Columns</th></tr>";

        while ($row = pg_fetch_assoc($result)) {
            $resultsHtml .= "<tr>";
            $resultsHtml .= "<td>" . htmlspecialchars($row['id']) . "</td>";
            $resultsHtml .= "<td>" . htmlspecialchars($row['phone']) . "</td>";
            $resultsHtml .= "</tr>";
        }

        if (pg_num_rows($result) === 0) {
            $resultsHtml .= "<tr><td colspan='3'>No data found</td></tr>";
        }

        $resultsHtml .= "</table>";
    }
}

pg_close($conn);
?>

<br>

<div class="container">
    <h2>Search PostgreSQL Database - external_users</h2>
    <form method="get" action="">
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
        <input type="submit" name="search" value="Search">
    </form>
    <div class="results">
        <?php echo $resultsHtml; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monitoringButton = document.querySelector('.monitoring-button');
        if (monitoringButton) {
            monitoringButton.addEventListener('click', function () {
                console.log('Monitoring button clicked');
                window.location.href = 'monitoring.php';
            });
        } else {
            console.error('Monitoring button not found');
        }

        // Add event listener to the button
        document.querySelector('.monitoring-button').addEventListener('click', redirectToMonitoring);

        // Additional JavaScript code if necessary
    });
</script>
</body>
