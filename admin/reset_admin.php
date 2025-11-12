<?php
/**
 * 관리자 계정 초기화 스크립트
 * 이 파일을 서버에 업로드하고 브라우저에서 한 번 실행한 후 즉시 삭제하세요.
 */

require_once '../includes/db.php';

try {
    // admin_users 테이블이 있는지 확인
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    $tableExists = $stmt->rowCount() > 0;

    if (!$tableExists) {
        // 테이블 생성
        $pdo->exec("
            CREATE TABLE admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "✓ admin_users 테이블 생성 완료<br>";
    }

    // 기존 admin 계정 삭제
    $pdo->exec("DELETE FROM admin_users WHERE username = 'admin'");
    echo "✓ 기존 admin 계정 삭제<br>";

    // 새 admin 계정 생성 (admin/admin)
    $username = 'admin';
    $password = 'admin';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);

    echo "<h2>✓ 관리자 계정 생성 완료!</h2>";
    echo "<p>아이디: <strong>admin</strong></p>";
    echo "<p>비밀번호: <strong>admin</strong></p>";
    echo "<p style='color: red;'><strong>⚠️ 보안을 위해 이 파일(reset_admin.php)을 즉시 삭제하세요!</strong></p>";
    echo "<p><a href='index.php'>로그인 페이지로 이동</a></p>";

} catch (PDOException $e) {
    echo "오류 발생: " . $e->getMessage();
}
?>
