<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../includes/db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$where_sql = "WHERE is_published = 1 AND category = '최근 업무사례'";

$count_sql = "SELECT COUNT(*) FROM news $where_sql";
$total = $pdo->query($count_sql)->fetchColumn();

$sql = "SELECT * FROM news $where_sql ORDER BY news_date DESC, created_at DESC LIMIT $per_page OFFSET $offset";
$cases_list = $pdo->query($sql)->fetchAll();

header('Content-Type: application/json');
echo json_encode([
    'cases' => $cases_list,
    'has_more' => ($offset + $per_page) < $total
]);
