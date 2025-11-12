<?php
// 데이터베이스 연결 설정
$host = 'localhost';
$dbname = 'lez0628';
$username = 'lez0628';
$password = 'vkshdb*0628';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("데이터베이스 연결 실패: " . $e->getMessage());
}
?>
