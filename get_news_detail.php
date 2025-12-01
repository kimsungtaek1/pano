<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
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

// 조회수 카운팅 (세션 기반 중복 방지)
// 세션에 이미 본 뉴스 ID 목록 저장
if (!isset($_SESSION['viewed_news'])) {
    $_SESSION['viewed_news'] = [];
}

// 이 뉴스를 아직 보지 않았다면 조회수 증가
if (!in_array($id, $_SESSION['viewed_news'])) {
    $update_sql = "UPDATE news SET view_count = view_count + 1 WHERE id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$id]);

    // 세션에 본 뉴스 ID 추가
    $_SESSION['viewed_news'][] = $id;

    // 조회수 갱신된 값 가져오기
    $news['view_count'] = ($news['view_count'] ?? 0) + 1;
}

// 날짜 포맷 변경
$news['news_date'] = date('Y.m.d', strtotime($news['news_date']));

// 이미지 URL을 배열로 변환
if (!empty($news['image_urls'])) {
    $news['image_urls'] = json_decode($news['image_urls'], true);
} else {
    $news['image_urls'] = [];
}

// 본문 줄바꿈 처리 (textarea의 \n을 <br>로 변환)
$news['content'] = nl2br(htmlspecialchars($news['content']));

echo json_encode($news, JSON_UNESCAPED_UNICODE);
