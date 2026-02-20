<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $domain = trim($_POST['domain'] ?? '');
        $utm_source = trim($_POST['utm_source'] ?? '');

        if ($name && $phone && $content) {
            $stmt = $pdo->prepare("INSERT INTO consultations (name, phone, content, status, domain, utm_source) VALUES (?, ?, ?, 'pending', ?, ?)");
            $stmt->execute([$name, $phone, $content, $domain ?: null, $utm_source ?: null]);
            header('Location: consultation_list.php?added=1');
            exit;
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: consultation_list.php?deleted=1');
            exit;
        }
    }
}

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
$sql = "SELECT * FROM consultations $where_sql ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
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

try {
    $count_stmt = $pdo->query("SELECT status, COUNT(*) as cnt FROM consultations GROUP BY status");
    while ($row = $count_stmt->fetch()) {
        if (isset($status_counts[$row['status']])) {
            $status_counts[$row['status']] = $row['cnt'];
        }
        $status_counts['all'] += $row['cnt'];
    }
} catch (PDOException $e) {
    // 에러 무시
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
                <h2>⚖️ PANO</h2>
                <p style="font-size: 12px; color: #95a5a6; margin-top: 5px;">법률사무소 관리</p>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php">
                    <span class="nav-icon">📊</span> 대시보드
                </a>
                <a href="consultation_list.php" class="active">
                    <span class="nav-icon">💬</span> 상담신청 관리
                </a>
                <a href="news_list.php">
                    <span class="nav-icon">📰</span> 뉴스 관리
                </a>
                <a href="member_list.php">
                    <span class="nav-icon">👤</span> 구성원 관리
                </a>
                <a href="admin_list.php">
                    <span class="nav-icon">👥</span> 관리자 관리
                </a>
                <a href="logout.php">
                    <span class="nav-icon">🚪</span> 로그아웃
                </a>
            </nav>
            <div class="admin-info">
                <div style="padding: 15px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                    <p style="font-size: 13px; color: #ecf0f1; margin-bottom: 3px;">로그인:</p>
                    <p style="font-size: 14px; font-weight: 600; color: #fff;"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                </div>
            </div>
        </aside>

        <!-- 메인 컨텐츠 -->
        <main class="main-content">
            <div class="content-header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <h1>💬 상담신청 관리</h1>
                <button onclick="document.getElementById('addModal').style.display='flex'" class="btn btn-primary">+ 상담 추가</button>
            </div>

            <?php if (isset($_GET['added'])): ?>
                <div class="alert alert-success" style="margin-bottom: 20px; padding: 12px 16px; background: #d4edda; color: #155724; border-radius: 6px;">상담신청이 추가되었습니다.</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success" style="margin-bottom: 20px; padding: 12px 16px; background: #d4edda; color: #155724; border-radius: 6px;">상담신청이 삭제되었습니다.</div>
            <?php endif; ?>

            <!-- 통계 카드 -->
            <div class="stats-grid-main" style="margin-bottom: 30px;">
                <div class="stat-card-modern">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">📊</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">전체</p>
                        <p class="stat-number-large"><?php echo number_format($status_counts['all']); ?></p>
                    </div>
                </div>

                <div class="stat-card-modern stat-card-urgent">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">⚠️</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">미처리</p>
                        <p class="stat-number-large"><?php echo number_format($status_counts['pending']); ?></p>
                    </div>
                </div>

                <div class="stat-card-modern stat-card-processing">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">⏳</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">처리중</p>
                        <p class="stat-number-large"><?php echo number_format($status_counts['processing']); ?></p>
                    </div>
                </div>

                <div class="stat-card-modern stat-card-completed">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">✅</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">완료</p>
                        <p class="stat-number-large"><?php echo number_format($status_counts['completed']); ?></p>
                    </div>
                </div>
            </div>

            <!-- 검색 및 필터 -->
            <div class="filter-section">
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="이름, 전화번호, 내용 검색" value="<?php echo htmlspecialchars($search); ?>">
                    <select name="status">
                        <option value="">전체 상태</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>미처리</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>처리중</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>완료</option>
                    </select>
                    <button type="submit" class="btn btn-primary">검색</button>
                    <?php if (!empty($search) || !empty($status_filter)): ?>
                        <a href="consultation_list.php" class="btn btn-secondary">초기화</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- 상담신청 테이블 -->
            <div class="table-container">
                <table class="news-table">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th width="100">이름</th>
                            <th width="130">연락처</th>
                            <th>내용</th>
                            <th width="60">국가</th>
                            <th width="80">광고</th>
                            <th width="100">홈페이지</th>
                            <th width="160">신청일시</th>
                            <th width="80">상태</th>
                            <th width="100">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($consultations)): ?>
                            <tr>
                                <td colspan="10" class="text-center">등록된 상담신청이 없습니다.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($consultations as $idx => $consultation): ?>
                                <tr>
                                    <td><?php echo $total - ($offset + $idx); ?></td>
                                    <td><?php echo htmlspecialchars($consultation['name']); ?></td>
                                    <td><?php echo htmlspecialchars($consultation['phone']); ?></td>
                                    <td class="title-cell">
                                        <?php echo htmlspecialchars(mb_substr($consultation['content'], 0, 50)); ?>
                                        <?php if (mb_strlen($consultation['content']) > 50): ?>...<?php endif; ?>
                                    </td>
                                    <td<?php echo (!empty($consultation['country']) && $consultation['country'] !== 'KR') ? ' style="color:red;font-weight:bold;"' : ''; ?>><?php echo htmlspecialchars($consultation['country'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($consultation['utm_source'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($consultation['domain'] ?? '-'); ?></td>
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
                                    <td class="action-cell">
                                        <a href="consultation_view.php?id=<?php echo $consultation['id']; ?>" class="btn-sm btn-edit">수정</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $consultation['id']; ?>">
                                            <button type="submit" class="btn-sm btn-delete">삭제</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    $query_params = [];
                    if (!empty($search)) $query_params['search'] = $search;
                    if (!empty($status_filter)) $query_params['status'] = $status_filter;
                    $query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="?page=1<?php echo $query_string; ?>" class="page-link">처음</a>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $query_string; ?>" class="page-link">이전</a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?><?php echo $query_string; ?>"
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $query_string; ?>" class="page-link">다음</a>
                        <a href="?page=<?php echo $total_pages; ?><?php echo $query_string; ?>" class="page-link">마지막</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="info-box">
                <p>전체 <?php echo number_format($total); ?>개의 상담신청</p>
            </div>
        </main>
    </div>
    <!-- 상담 추가 모달 -->
    <div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:#fff; border-radius:12px; padding:30px; width:500px; max-width:90%;">
            <h2 style="margin:0 0 20px 0; font-size:20px;">상담신청 추가</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:14px;">이름 *</label>
                    <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:14px;">연락처 *</label>
                    <input type="text" name="phone" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:14px;">내용 *</label>
                    <textarea name="content" required rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box; resize:vertical;"></textarea>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:14px;">광고</label>
                    <input type="text" name="utm_source" placeholder="예: meta, google" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;">
                </div>
                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600; font-size:14px;">홈페이지</label>
                    <input type="text" name="domain" value="panolaw.com" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;">
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('addModal').style.display='none'" style="padding:10px 20px; border:1px solid #ddd; background:#fff; border-radius:6px; cursor:pointer;">취소</button>
                    <button type="submit" class="btn btn-primary" style="padding:10px 20px;">추가</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .btn-sm.btn-delete { background:#e74c3c; color:#fff; border:none; padding:4px 10px; border-radius:4px; cursor:pointer; font-size:12px; }
        .btn-sm.btn-delete:hover { background:#c0392b; }
    </style>
</body>
</html>
