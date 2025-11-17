<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => '권한이 없습니다.']);
    exit;
}

// 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

// 파일 업로드 체크
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => '파일 업로드에 실패했습니다.']);
    exit;
}

$file = $_FILES['image'];
$upload_dir = '../uploads/members/';

// 업로드 디렉토리 생성
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// 파일 확장자 체크
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode(['success' => false, 'error' => '허용되지 않는 파일 형식입니다. (jpg, jpeg, png, gif, webp만 가능)']);
    exit;
}

// 파일 크기 체크 (5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => '파일 크기는 5MB 이하여야 합니다.']);
    exit;
}

// 고유한 파일명 생성
$new_filename = uniqid('member_', true) . '.' . $file_extension;
$upload_path = $upload_dir . $new_filename;

// 파일 이동
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // 파일 권한 설정
    chmod($upload_path, 0644);

    // 상대 URL 반환
    $file_url = '/uploads/members/' . $new_filename;

    echo json_encode([
        'success' => true,
        'url' => $file_url,
        'filename' => $new_filename
    ]);
} else {
    echo json_encode(['success' => false, 'error' => '파일 저장에 실패했습니다.']);
}
?>
