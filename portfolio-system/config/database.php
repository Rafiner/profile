<?php
// config/database.php
declare(strict_types=1);

function load_env(string $path): void {
    if (!is_readable($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        $k = trim($k); $v = trim($v, " \t\n\r\0\x0B\"'");
        if ($k !== '' && getenv($k) === false) putenv("$k=$v");
    }
}
load_env(dirname(__DIR__) . '/.env');

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'portfolio_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
// Add SSL requirement for cloud databases like Aiven
if ($host !== '127.0.0.1' && $host !== 'localhost') {
    $dsn .= ";ssl-mode=REQUIRED";
}
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    if ((getenv('APP_ENV') ?: 'local') === 'local') {
        die('DB error: ' . htmlspecialchars($e->getMessage()));
    }
    die('Database connection failed.');
}