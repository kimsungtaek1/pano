<?php
declare(strict_types=1);

$configPath = dirname(__DIR__, 2) . '/.pano-config.php';
if (!is_file($configPath)) {
    http_response_code(500);
    error_log('Pano database configuration file is missing.');
    exit('서비스 설정 오류');
}

$config = require $configPath;
$db = $config['db'] ?? null;
if (!is_array($db) || !isset($db['host'], $db['port'], $db['name'], $db['username'], $db['password'], $db['charset'])) {
    http_response_code(500);
    error_log('Pano database configuration is invalid.');
    exit('서비스 설정 오류');
}

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']),
        $db['username'],
        $db['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Pano database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('데이터베이스 연결 오류');
}
