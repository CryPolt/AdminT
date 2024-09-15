<?php
session_start();

if (!isset($_SESSION['auth'])) {
    header('Location: /index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
</head>
<body>
<h1>Welcome!</h1>
<p>You have successfully logged in.</p>
<a href="/logout.php">Logout</a>
</body>
</html>
