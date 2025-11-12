<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

// 페이지네이션 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// 검색 및 필터
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// WHERE 조건 구성
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $where[] = "category = ?";
    $params[] = $category;
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// 전체 개수 조회
$count_sql = "SELECT COUNT(*) FROM news $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// 뉴스 목록 조회
$sql = "SELECT * FROM news $where_sql ORDER BY news_date DESC, created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$news_list = $stmt->fetchAll();

// 삭제 처리
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $delete_stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $delete_stmt->execute([$delete_id]);
    header('Location: news_list.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>뉴스 관리 - PANO 관리자</title>
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
                <a href="news_list.php" class="active">뉴스 관리</a>
                <a href="admin_list.php">관리자 관리</a>
                <a href="logout.php">로그아웃</a>
            </nav>
            <div class="admin-info">
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?>님</p>
            </div>
        </aside>

        <!-- 메인 컨텐츠 -->
        <main class="main-content">
            <div class="content-header">
                <h1>뉴스 관리</h1>
                <a href="news_edit.php" class="btn btn-primary">새 글 작성</a>
            </div>

            <!-- 검색 및 필터 -->
            <div class="filter-section">
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="제목 또는 내용 검색" value="<?php echo htmlspecialchars($search); ?>">
                    <select name="category">
                        <option value="">전체 카테고리</option>
                        <option value="최근 업무사례" <?php echo $category === '최근 업무사례' ? 'selected' : ''; ?>>최근 업무사례</option>
                        <option value="언론보도" <?php echo $category === '언론보도' ? 'selected' : ''; ?>>언론보도</option>
                        <option value="한경BUSINESS" <?php echo $category === '한경BUSINESS' ? 'selected' : ''; ?>>한경BUSINESS</option>
                    </select>
                    <button type="submit" class="btn btn-secondary">검색</button>
                    <a href="news_list.php" class="btn btn-secondary">초기화</a>
                </form>
            </div>

            <!-- 뉴스 테이블 -->
            <div class="table-container">
                <table class="news-table">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th width="120">카테고리</th>
                            <th>제목</th>
                            <th width="100">날짜</th>
                            <th width="80">조회수</th>
                            <th width="80">공개</th>
                            <th width="150">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($news_list)): ?>
                            <tr>
                                <td colspan="7" class="text-center">등록된 뉴스가 없습니다.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($news_list as $news): ?>
                                <tr>
                                    <td><?php echo $news['id']; ?></td>
                                    <td><span class="badge"><?php echo htmlspecialchars($news['category']); ?></span></td>
                                    <td class="title-cell"><?php echo htmlspecialchars($news['title']); ?></td>
                                    <td><?php echo date('Y.m.d', strtotime($news['news_date'])); ?></td>
                                    <td><?php echo number_format($news['view_count']); ?></td>
                                    <td><?php echo $news['is_published'] ? '공개' : '비공개'; ?></td>
                                    <td class="action-cell">
                                        <a href="news_edit.php?id=<?php echo $news['id']; ?>" class="btn-sm btn-edit">수정</a>
                                        <a href="?delete=<?php echo $news['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
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
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>"
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

            <div class="info-box">
                <p>전체 <?php echo number_format($total); ?>개의 뉴스</p>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
