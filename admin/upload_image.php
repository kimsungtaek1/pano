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

if (!$news_id) {
    echo json_encode(['success' => false, 'error' => '뉴스 ID가 필요합니다.']);
    exit;
}

// 파일 업로드 확인
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => '파일 업로드에 실패했습니다.']);
    exit;
}

$file = $_FILES['image'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extension, $allowed)) {
    echo json_encode(['success' => false, 'error' => '허용되지 않는 파일 형식입니다. (jpg, jpeg, png, webp만 가능)']);
    exit;
}

$upload_dir = '../uploads/news/';

// 업로드 디렉토리 생성 (없으면)
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$filename = uniqid() . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;
$image_url = '/uploads/news/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['success' => false, 'error' => '파일 저장에 실패했습니다.']);
    exit;
}

try {
    // 현재 뉴스의 이미지 목록 가져오기
    $stmt = $pdo->prepare("SELECT image_urls FROM news WHERE id = ?");
    $stmt->execute([$news_id]);
    $news = $stmt->fetch();

    if (!$news) {
        // 업로드된 파일 삭제
        @unlink($filepath);
        echo json_encode(['success' => false, 'error' => '뉴스를 찾을 수 없습니다.']);
        exit;
    }

    $image_urls = !empty($news['image_urls']) ? json_decode($news['image_urls'], true) : [];

    // 최대 10개 제한
    if (count($image_urls) >= 10) {
        @unlink($filepath);
        echo json_encode(['success' => false, 'error' => '이미지는 최대 10개까지 업로드할 수 있습니다.']);
        exit;
    }

    // 새 이미지 추가
    $image_urls[] = $image_url;

    // DB 업데이트
    $image_urls_json = json_encode($image_urls, JSON_UNESCAPED_UNICODE);
    $stmt = $pdo->prepare("UPDATE news SET image_urls = ? WHERE id = ?");
    $stmt->execute([$image_urls_json, $news_id]);

    echo json_encode([
        'success' => true,
        'image_url' => $image_url,
        'index' => count($image_urls)
    ]);
} catch (PDOException $e) {
    @unlink($filepath);
    echo json_encode(['success' => false, 'error' => '데이터베이스 오류: ' . $e->getMessage()]);
}
