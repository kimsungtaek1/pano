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
$content = trim($_POST['content'] ?? '');
$utm_source = trim($_POST['utm_source'] ?? '');
$utm_medium = trim($_POST['utm_medium'] ?? '');
$utm_campaign = trim($_POST['utm_campaign'] ?? '');
$utm_content = trim($_POST['utm_content'] ?? '');
$utm_term = trim($_POST['utm_term'] ?? '');
$fbclid = trim($_POST['fbclid'] ?? '');
$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// IP 기반 국가코드 조회
$country = null;
if ($ip_address) {
    $ch = curl_init("http://ip-api.com/json/{$ip_address}?fields=countryCode");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
    ]);
    $geoData = curl_exec($ch);
    curl_close($ch);
    if ($geoData) {
        $country = json_decode($geoData, true)['countryCode'] ?? null;
    }
}

// 이름: 2자 이상
if (mb_strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => '이름을 2자 이상 입력해주세요.']);
    exit;
}

// 전화번호: 숫자만 추출 후 01x로 시작하는 10~11자리
$phone = preg_replace('/[^0-9]/', '', $phone);
if (!preg_match('/^01[0-9][0-9]{7,8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => '유효한 휴대폰 번호를 입력해주세요.']);
    exit;
}

// 문의내용: 5자 이상
if (mb_strlen($content) < 5) {
    echo json_encode(['success' => false, 'message' => '문의내용을 5자 이상 입력해주세요.']);
    exit;
}

try {
    // 테이블이 없으면 생성
    $createTableSQL = "CREATE TABLE IF NOT EXISTS consultations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL COMMENT '이름',
        phone VARCHAR(20) NOT NULL COMMENT '전화번호',
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
        INSERT INTO consultations (name, phone, content, status, utm_source, utm_medium, utm_campaign, utm_content, utm_term, fbclid, ip_address, user_agent, country)
        VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $result = $stmt->execute([
        $name,
        $phone,
        $content,
        $utm_source ?: null,
        $utm_medium ?: null,
        $utm_campaign ?: null,
        $utm_content ?: null,
        $utm_term ?: null,
        $fbclid ?: null,
        $ip_address ?: null,
        $user_agent ?: null,
        $country
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
