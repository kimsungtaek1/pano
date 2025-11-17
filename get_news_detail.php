<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db.php';

header('Content-Type: application/json');

// 뉴스 ID 가져오기
$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['error' => '뉴스 ID가 필요합니다.']);
    exit;
}

// 뉴스 상세 조회
$sql = "SELECT * FROM news WHERE id = ? AND is_published = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    echo json_encode(['error' => '뉴스를 찾을 수 없습니다.']);
    exit;
}

// 날짜 포맷 변경
$news['news_date'] = date('Y.m.d', strtotime($news['news_date']));

echo json_encode($news);
