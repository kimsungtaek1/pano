<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

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
} catch (PDOException $e) {
    // 테이블 생성 오류 무시 (이미 있는 경우)
}

// 페이지네이션 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// 검색 및 필터
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// WHERE 조건 구성
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(name LIKE ? OR phone LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $where[] = "status = ?";
    $params[] = $status_filter;
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// 전체 개수 조회
$count_sql = "SELECT COUNT(*) FROM consultations $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// 상담신청 목록 조회
$sql = "SELECT * FROM consultations $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$consultations = $stmt->fetchAll();

// 상태별 개수
$status_counts = [
    'all' => 0,
    'pending' => 0,
    'processing' => 0,
    'completed' => 0
];

$count_stmt = $pdo->query("SELECT status, COUNT(*) as cnt FROM consultations GROUP BY status");
while ($row = $count_stmt->fetch()) {
    if (isset($status_counts[$row['status']])) {
        $status_counts[$row['status']] = $row['cnt'];
    }
    $status_counts['all'] += $row['cnt'];
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상담신청 관리 - PANO</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- 사이드바 -->
        <aside class="sidebar">
            <div class="logo">
                <h2>PANO 관리자</h2>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php">대시보드</a>
                <a href="consultation_list.php" class="active">상담신청 관리</a>
                <a href="admin_list.php">관리자 관리</a>
                <a href="logout.php">로그아웃</a>
            </nav>
            <div class="admin-info">
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?>님</p>
            </div>
        </aside>

        <!-- 메인 컨텐츠 -->
        <main class="main-content">
        <div class="admin-header">
            <h1>상담신청 관리</h1>
        </div>

        <div class="admin-stats">
            <div class="stat-item">
                <span class="stat-label">전체</span>
                <span class="stat-value"><?php echo number_format($status_counts['all']); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">미처리</span>
                <span class="stat-value pending"><?php echo number_format($status_counts['pending']); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">처리중</span>
                <span class="stat-value processing"><?php echo number_format($status_counts['processing']); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">완료</span>
                <span class="stat-value processed"><?php echo number_format($status_counts['completed']); ?></span>
            </div>
        </div>

        <div class="admin-filter">
            <form method="GET" action="">
                <div class="filter-group">
                    <select name="status">
                        <option value="">전체 상태</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>미처리</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>처리중</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>완료</option>
                    </select>
                    <input type="text" name="search" placeholder="이름, 전화번호, 내용 검색" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-primary">검색</button>
                    <?php if (!empty($search) || !empty($status_filter)): ?>
                        <a href="consultation_list.php" class="btn-secondary">초기화</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="admin-content">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="8%">이름</th>
                        <th width="12%">연락처</th>
                        <th width="10%">분야</th>
                        <th width="30%">내용</th>
                        <th width="10%">신청일시</th>
                        <th width="8%">상태</th>
                        <th width="12%">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($consultations)): ?>
                        <tr>
                            <td colspan="8" class="no-data">등록된 상담신청이 없습니다.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($consultations as $idx => $consultation): ?>
                            <tr>
                                <td><?php echo $total - ($offset + $idx); ?></td>
                                <td><?php echo htmlspecialchars($consultation['name']); ?></td>
                                <td><?php echo htmlspecialchars($consultation['phone']); ?></td>
                                <td><?php echo htmlspecialchars($consultation['category'] ?: '-'); ?></td>
                                <td class="text-left">
                                    <div class="content-preview">
                                        <?php echo htmlspecialchars(mb_substr($consultation['content'], 0, 50)); ?>
                                        <?php if (mb_strlen($consultation['content']) > 50): ?>...<?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($consultation['created_at'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $consultation['status']; ?>">
                                        <?php
                                        $status_labels = [
                                            'pending' => '미처리',
                                            'processing' => '처리중',
                                            'completed' => '완료'
                                        ];
                                        echo $status_labels[$consultation['status']] ?? $consultation['status'];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="consultation_view.php?id=<?php echo $consultation['id']; ?>" class="btn-small">상세보기</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    $query_string = http_build_query(array_filter([
                        'search' => $search,
                        'status' => $status_filter
                    ]));
                    $query_prefix = $query_string ? '&' : '';
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php echo $query_prefix . $query_string; ?>" class="page-link">처음</a>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $query_prefix . $query_string; ?>" class="page-link">이전</a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?><?php echo $query_prefix . $query_string; ?>"
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $query_prefix . $query_string; ?>" class="page-link">다음</a>
                        <a href="?page=<?php echo $total_pages; ?><?php echo $query_prefix . $query_string; ?>" class="page-link">마지막</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        </main>
    </div>
</body>
</html>
