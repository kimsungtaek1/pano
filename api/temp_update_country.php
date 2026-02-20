<?php
header('Content-Type: application/json; charset=utf-8');
if (($_GET['token'] ?? '') !== 'pano2026update') { echo json_encode(['error' => 'unauthorized']); exit; }
require_once '../includes/db.php';
$stmt = $pdo->prepare("UPDATE consultations SET country = 'KR' WHERE country IS NULL OR country = ''");
$stmt->execute();
echo json_encode(['success' => true, 'updated_rows' => $stmt->rowCount()]);
?>
