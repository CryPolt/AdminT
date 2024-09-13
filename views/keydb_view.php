<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KeyDB Database Viewer</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { color: #333; }
        .db { margin-bottom: 20px; }
        .key { font-weight: bold; }
        .value { margin-left: 20px; color: #555; }
    </style>
</head>
<body>
<h2>Просмотр баз данных KeyDB</h2>

<?php foreach ($databases as $dbIndex => $keys): ?>
    <div class="db">
        <h3>База данных #<?= $dbIndex ?></h3>

        <?php if (!empty($keys)): ?>
            <ul>
                <?php foreach ($keys as $key => $value): ?>
                    <li>
                        <span class="key"><?= htmlspecialchars($key) ?>:</span>
                        <span class="value"><?= htmlspecialchars($value) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Ключи отсутствуют.</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</body>
</html>
