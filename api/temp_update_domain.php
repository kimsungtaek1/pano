<?php
header('Content-Type: application/json; charset=utf-8');

$token = $_GET['token'] ?? '';
if ($token !== 'pano2026update') {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

require_once '../includes/db.php';

try {
    // domain 컬럼 추가 (없는 경우)
    $pdo->exec("ALTER TABLE consultations ADD COLUMN domain VARCHAR(100) COMMENT '홈페이지' AFTER status");
} catch (PDOException $e) {
    // 이미 존재하면 무시
}

$stmt = $pdo->prepare("UPDATE consultations SET domain = 'panolaw.com' WHERE domain IS NULL OR domain = ''");
$stmt->execute();
$affected = $stmt->rowCount();

echo json_encode(['success' => true, 'updated_rows' => $affected]);
?>
