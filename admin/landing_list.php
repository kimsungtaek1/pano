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

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: landing_list.php?deleted=1');
            exit;
        }
    }
}

// 랜딩페이지 도메인 목록
$landing_domains = ['victim-pano.com', 'criminallaw.kr', 'newstart-life.co.kr'];
$domain_placeholders = implode(',', array_fill(0, count($landing_domains), '?'));

// 페이지네이션 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// 검색 및 필터
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$domain_filter = $_GET['domain'] ?? '';

// WHERE 조건 구성
$where = ["domain IN ($domain_placeholders)"];
$params = $landing_domains;

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

if (!empty($domain_filter)) {
    $where[] = "domain = ?";
    $params[] = $domain_filter;
}

$where_sql = "WHERE " . implode(" AND ", $where);

// 전체 개수 조회
$count_sql = "SELECT COUNT(*) FROM consultations $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// 목록 조회
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
    $status_sql = "SELECT status, COUNT(*) as cnt FROM consultations WHERE domain IN ($domain_placeholders) GROUP BY status";
    $count_stmt = $pdo->prepare($status_sql);
    $count_stmt->execute($landing_domains);
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
    <title>랜딩페이지 - PANO</title>
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
                <a href="consultation_list.php">
                    <span class="nav-icon">💬</span> 상담신청 관리
                </a>
                <a href="landing_list.php" class="active">
                    <span class="nav-icon">🚀</span> 랜딩페이지
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
                <h1>🚀 랜딩페이지</h1>
            </div>

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
                    <select name="domain">
                        <option value="">전체 도메인</option>
                        <?php foreach ($landing_domains as $d): ?>
                            <option value="<?php echo htmlspecialchars($d); ?>" <?php echo $domain_filter === $d ? 'selected' : ''; ?>><?php echo htmlspecialchars($d); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">검색</button>
                    <?php if (!empty($search) || !empty($status_filter) || !empty($domain_filter)): ?>
                        <a href="landing_list.php" class="btn btn-secondary">초기화</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- 테이블 -->
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
                            <th width="180">홈페이지</th>
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
                    if (!empty($domain_filter)) $query_params['domain'] = $domain_filter;
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

    <style>
        .btn-sm.btn-delete { background:#e74c3c; color:#fff; border:none; padding:4px 10px; border-radius:4px; cursor:pointer; font-size:12px; }
        .btn-sm.btn-delete:hover { background:#c0392b; }
    </style>
</body>
</html>
