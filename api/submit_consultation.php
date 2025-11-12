<?php
header('Content-Type: application/json; charset=utf-8');

// CORS 설정 (필요시)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

require_once '../includes/db.php';

// 입력값 받기 및 검증
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$category = trim($_POST['category'] ?? '');
$content = trim($_POST['content'] ?? '');

// 필수 항목 검증
if (empty($name) || empty($phone) || empty($content)) {
    echo json_encode(['success' => false, 'message' => '필수 항목을 입력해주세요.']);
    exit;
}

// 전화번호 형식 간단 검증
if (!preg_match('/^[0-9-]+$/', $phone)) {
    echo json_encode(['success' => false, 'message' => '전화번호 형식이 올바르지 않습니다.']);
    exit;
}

// 이메일 형식 검증 (입력된 경우)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => '이메일 형식이 올바르지 않습니다.']);
    exit;
}

try {
    // 테이블이 없으면 생성
    $createTableSQL = "CREATE TABLE IF NOT EXISTS consultations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL COMMENT '이름',
        phone VARCHAR(20) NOT NULL COMMENT '전화번호',
        email VARCHAR(100) COMMENT '이메일',
        category VARCHAR(50) COMMENT '상담분야',
        content TEXT NOT NULL COMMENT '상담내용',
        status VARCHAR(20) DEFAULT 'pending' COMMENT '상태',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '신청일시',
        processed_at TIMESTAMP NULL COMMENT '처리일시',
        admin_memo TEXT COMMENT '관리자 메모',
        INDEX idx_status (status),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='상담신청'";

    $pdo->exec($createTableSQL);

    // 데이터 삽입
    $stmt = $pdo->prepare("
        INSERT INTO consultations (name, phone, email, category, content, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");

    $result = $stmt->execute([
        $name,
        $phone,
        $email ?: null,
        $category ?: null,
        $content
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => '상담신청이 완료되었습니다.']);
    } else {
        echo json_encode(['success' => false, 'message' => '상담신청 처리 중 오류가 발생했습니다.']);
    }

} catch (PDOException $e) {
    error_log('Consultation submission error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => '데이터베이스 오류가 발생했습니다.']);
}
?>
