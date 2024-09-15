<?php

function getUsersFromJson() {
    $jsonFilePath = __DIR__ . '/users.json';
    if (!file_exists($jsonFilePath)) {
        throw new Exception("Users JSON file not found.");
    }

    $jsonData = file_get_contents($jsonFilePath);
    return json_decode($jsonData, true);
}

function validateUser($username, $password) {
    $users = getUsersFromJson();

    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return true;
        }
    }

    return false;
}

session_start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (validateUser($username, $password)) {
        $_SESSION['auth'] = $username;
        header('Location: /success.php');
        exit();
    } else {
        $error = 'Неверное имя пользователя или пароль.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<h1>Login</h1>
<form method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <input type="submit" value="Login">
</form>
<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
</body>
</html>
