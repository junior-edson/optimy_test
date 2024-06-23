<?php

$config = require(__DIR__ . '/config/db.php');

$dsn = 'mysql:dbname=' . $config['dbname'] . ';host=' . $config['host'];
$username = $config['user'];
$password = $config['password'];

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlFile = __DIR__ . '/dbdump.sql';

    if (!file_exists($sqlFile)) {
        throw new Exception('The dbdump.sql file was not found.');
    }

    $sql = file_get_contents($sqlFile);

    $pdo->exec($sql);

    echo "Database import successful.\n";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
