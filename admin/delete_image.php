<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();

header('Content-Type: application/json');

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => '로그인이 필요합니다.']);
    exit;
}

require_once '../includes/db.php';

// POST 데이터 확인
$news_id = $_POST['news_id'] ?? 0;
$image_url = $_POST['image_url'] ?? '';

if (!$news_id || !$image_url) {
    echo json_encode(['success' => false, 'error' => '필수 파라미터가 없습니다.']);
    exit;
}

try {
    // 현재 뉴스의 이미지 목록 가져오기
    $stmt = $pdo->prepare("SELECT image_urls FROM news WHERE id = ?");
    $stmt->execute([$news_id]);
    $news = $stmt->fetch();

    if (!$news) {
        echo json_encode(['success' => false, 'error' => '뉴스를 찾을 수 없습니다.']);
        exit;
    }

    $image_urls = !empty($news['image_urls']) ? json_decode($news['image_urls'], true) : [];

    // 해당 이미지 URL 찾아서 제거
    $key = array_search($image_url, $image_urls);
    if ($key !== false) {
        // 배열에서 제거
        unset($image_urls[$key]);
        $image_urls = array_values($image_urls); // 인덱스 재정렬

        // 실제 파일 삭제
        $file_path = '..' . $image_url;
        if (file_exists($file_path)) {
            @unlink($file_path);
        }

        // DB 업데이트
        $image_urls_json = json_encode($image_urls, JSON_UNESCAPED_UNICODE);
        $stmt = $pdo->prepare("UPDATE news SET image_urls = ? WHERE id = ?");
        $stmt->execute([$image_urls_json, $news_id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => '이미지를 찾을 수 없습니다.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => '데이터베이스 오류: ' . $e->getMessage()]);
}
