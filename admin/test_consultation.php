<?php
// 에러 표시 활성화
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "1. 세션 시작 완료<br>";

// 로그인 체크 건너뛰기 (테스트용)
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header('Location: index.php');
//     exit;
// }

echo "2. DB 연결 시도...<br>";

try {
    require_once '../includes/db.php';
    echo "3. DB 연결 성공<br>";
} catch (Exception $e) {
    echo "DB 연결 실패: " . $e->getMessage() . "<br>";
    die();
}

echo "4. 테이블 생성 시도...<br>";

// 테이블 생성 (없는 경우)
try {
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
    echo "5. 테이블 생성/확인 완료<br>";
} catch (PDOException $e) {
    echo "테이블 생성 오류: " . $e->getMessage() . "<br>";
}

echo "6. 데이터 조회 시도...<br>";

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations");
    $count = $stmt->fetchColumn();
    echo "7. 데이터 조회 성공 - 총 {$count}건<br>";
} catch (PDOException $e) {
    echo "데이터 조회 오류: " . $e->getMessage() . "<br>";
}

echo "8. 상태별 개수 조회 시도...<br>";

try {
    $count_stmt = $pdo->query("SELECT status, COUNT(*) as cnt FROM consultations GROUP BY status");
    echo "9. 상태별 개수 조회 성공<br>";

    while ($row = $count_stmt->fetch()) {
        echo "- {$row['status']}: {$row['cnt']}건<br>";
    }
} catch (PDOException $e) {
    echo "상태별 개수 조회 오류: " . $e->getMessage() . "<br>";
}

echo "<br><strong>모든 테스트 완료!</strong>";
?>
