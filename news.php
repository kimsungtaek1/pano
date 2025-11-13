<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db.php';

// 탭 구분
$tab = $_GET['tab'] ?? 'press';

// 언론 탭 (페이징)
if ($tab === 'press') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;

    $search = $_GET['search'] ?? '';

    $where = ["is_published = 1", "category = '언론보도'"];
    $params = [];

    if (!empty($search)) {
        $where[] = "(title LIKE ? OR content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $where_sql = "WHERE " . implode(" AND ", $where);

    $count_sql = "SELECT COUNT(*) FROM news $where_sql";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total = $count_stmt->fetchColumn();
    $total_pages = ceil($total / $per_page);

    $sql = "SELECT * FROM news $where_sql ORDER BY news_date DESC, created_at DESC LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $news_list = $stmt->fetchAll();
}

// 성공사례 탭 (무한 스크롤)
if ($tab === 'cases') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;

    $where_sql = "WHERE is_published = 1 AND category = '최근 업무사례'";

    $count_sql = "SELECT COUNT(*) FROM news $where_sql";
    $total = $pdo->query($count_sql)->fetchColumn();

    $sql = "SELECT * FROM news $where_sql ORDER BY news_date DESC, created_at DESC LIMIT $per_page OFFSET $offset";
    $cases_list = $pdo->query($sql)->fetchAll();

    // AJAX 요청인 경우 JSON 반환
    if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
        header('Content-Type: application/json');
        echo json_encode([
            'cases' => $cases_list,
            'has_more' => ($offset + $per_page) < $total
        ]);
        exit;
    }
}

include 'includes/header.php';
?>

<main>
    <!-- 페이지 타이틀 -->
    <section class="page-title">
        <div class="container">
            <h1>소식</h1>
        </div>
    </section>

    <!-- 탭 영역 -->
    <section class="news-tabs-section">
        <div class="container">
            <div class="main-tabs">
                <a href="?tab=press" class="main-tab <?php echo $tab === 'press' ? 'active' : ''; ?>">언론</a>
                <a href="?tab=cases" class="main-tab <?php echo $tab === 'cases' ? 'active' : ''; ?>">성공사례</a>
            </div>
        </div>
    </section>

    <?php if ($tab === 'press'): ?>
        <!-- 언론 탭: 검색 + 뉴스 목록 + 페이징 -->
        <section class="news-filter">
            <div class="container">
                <form method="GET" class="search-box">
                    <input type="hidden" name="tab" value="press">
                    <input type="text" name="search" placeholder="검색" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
        </section>

        <section class="news-list">
            <div class="container">
                <div class="news-grid">
                    <?php if (empty($news_list)): ?>
                        <p style="text-align: center; padding: 60px 0; color: #999;">등록된 뉴스가 없습니다.</p>
                    <?php else: ?>
                        <?php foreach ($news_list as $news): ?>
                            <a href="news_detail.php?id=<?php echo $news['id']; ?>" class="news-card">
                                <span class="badge badge-blue">언론보도</span>
                                <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                                <p><?php echo htmlspecialchars($news['summary'] ?: mb_substr(strip_tags($news['content']), 0, 100) . '...'); ?></p>
                                <span class="date"><?php echo date('Y.m.d', strtotime($news['news_date'])); ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 페이지네이션 -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?tab=press&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>"
                               class="page <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    <?php else: ?>
        <!-- 성공사례 탭: 3열 카드 + 무한 스크롤 -->
        <section class="cases-list">
            <div class="container">
                <div class="cases-grid" id="casesGrid">
                    <?php foreach ($cases_list as $case): ?>
                        <a href="news_detail.php?id=<?php echo $case['id']; ?>" class="case-card">
                            <span class="badge badge-red">성공사례</span>
                            <h3><?php echo htmlspecialchars($case['title']); ?></h3>
                            <p><?php echo htmlspecialchars($case['summary'] ?: mb_substr(strip_tags($case['content']), 0, 100) . '...'); ?></p>
                            <span class="date"><?php echo date('Y.m.d', strtotime($case['news_date'])); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div id="casesLoader" style="text-align: center; padding: 30px; display: none;">
                    <div class="loader"></div>
                </div>
            </div>
        </section>

        <script>
        let currentPage = 1;
        let isLoading = false;
        let hasMore = <?php echo ($offset + $per_page) < $total ? 'true' : 'false'; ?>;

        window.addEventListener('scroll', function() {
            if (isLoading || !hasMore) return;

            const scrollPosition = window.innerHeight + window.scrollY;
            const pageHeight = document.documentElement.scrollHeight;

            if (scrollPosition >= pageHeight - 300) {
                loadMoreCases();
            }
        });

        function loadMoreCases() {
            isLoading = true;
            document.getElementById('casesLoader').style.display = 'block';

            currentPage++;

            fetch(`?tab=cases&page=${currentPage}&ajax=1`)
                .then(response => response.json())
                .then(data => {
                    const grid = document.getElementById('casesGrid');

                    data.cases.forEach(caseItem => {
                        const card = document.createElement('a');
                        card.href = `news_detail.php?id=${caseItem.id}`;
                        card.className = 'case-card';

                        const summary = caseItem.summary || caseItem.content.replace(/<[^>]*>/g, '').substring(0, 100) + '...';
                        const newsDate = new Date(caseItem.news_date);
                        const formattedDate = `${newsDate.getFullYear()}.${String(newsDate.getMonth() + 1).padStart(2, '0')}.${String(newsDate.getDate()).padStart(2, '0')}`;

                        card.innerHTML = `
                            <span class="badge badge-red">성공사례</span>
                            <h3>${caseItem.title}</h3>
                            <p>${summary}</p>
                            <span class="date">${formattedDate}</span>
                        `;

                        grid.appendChild(card);
                    });

                    hasMore = data.has_more;
                    isLoading = false;
                    document.getElementById('casesLoader').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    isLoading = false;
                    document.getElementById('casesLoader').style.display = 'none';
                });
        }
        </script>
    <?php endif; ?>
</main>

<style>
.news-tabs-section {
    background: #fff;
    border-bottom: 1px solid #e0e0e0;
    padding: 0;
}

.main-tabs {
    display: flex;
    gap: 0;
}

.main-tab {
    padding: 20px 40px;
    font-size: 18px;
    font-weight: 500;
    color: #666;
    text-decoration: none;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.main-tab:hover {
    color: #333;
}

.main-tab.active {
    color: #0066cc;
    border-bottom-color: #0066cc;
}

.cases-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-top: 40px;
}

.case-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 30px;
    text-decoration: none;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
}

.case-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.case-card .badge {
    align-self: flex-start;
    margin-bottom: 15px;
}

.case-card h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 12px;
    line-height: 1.4;
}

.case-card p {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
    flex: 1;
}

.case-card .date {
    font-size: 13px;
    color: #999;
}

.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #0066cc;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .cases-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .main-tab {
        padding: 15px 20px;
        font-size: 16px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
