<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db.php';

// 언론 탭 데이터
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

// 성공사례 탭 데이터
$cases_page = 1;
$cases_per_page = 9;
$cases_offset = 0;

$cases_where_sql = "WHERE is_published = 1 AND category = '최근 업무사례'";

$cases_count_sql = "SELECT COUNT(*) FROM news $cases_where_sql";
$cases_total = $pdo->query($cases_count_sql)->fetchColumn();

$cases_sql = "SELECT * FROM news $cases_where_sql ORDER BY news_date DESC, created_at DESC LIMIT $cases_per_page OFFSET $cases_offset";
$cases_list = $pdo->query($cases_sql)->fetchAll();

include 'includes/header.php';
?>

<main>
    <!-- Top Image Section -->
    <section class="intro-hero">
        <img src="/images/news.png" alt="소식" style="width: 100%; display: block;">
        <div class="intro-hero-text-container">
            <div class="container">
                <div class="intro-hero-text">
                    <p class="hero-subtitle">LAW FIRM PANO</p>
                    <h1 class="hero-title">소식</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Tab Buttons Section -->
    <section class="intro-tabs-section">
        <div class="container">
            <div class="intro-tab-buttons">
                <button class="intro-tab-btn active">성공사례</button>
                <button class="intro-tab-btn">언론보도</button>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="intro-content-section">
        <div class="container">
            <!-- 성공사례 탭 컨텐츠 -->
            <div class="intro-tab-content active" id="tab-cases">
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

            <!-- 언론보도 탭 컨텐츠 -->
            <div class="intro-tab-content" id="tab-press">
                <!-- 검색 영역 -->
                <section class="news-filter">
                    <form method="GET" class="search-box">
                        <input type="text" name="search" placeholder="검색" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
                </section>

                <!-- 뉴스 목록 -->
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
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>"
                               class="page <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<style>
/* 검색 영역 */
.news-filter {
    margin: 40px 0;
}

.search-box {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
}

.search-box input {
    flex: 1;
    padding: 12px 20px;
    border: 1px solid #e0e0e0;
    border-radius: 25px 0 0 25px;
    font-size: 14px;
}

.search-btn {
    padding: 12px 30px;
    background: #0066cc;
    color: white;
    border: none;
    border-radius: 0 25px 25px 0;
    cursor: pointer;
    font-size: 16px;
}

/* 뉴스 그리드 */
.news-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin: 40px 0;
}

.news-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 30px;
    text-decoration: none;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.news-card .badge {
    align-self: flex-start;
    margin-bottom: 15px;
}

.news-card h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 12px;
    line-height: 1.4;
}

.news-card p {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
    flex: 1;
}

.news-card .date {
    font-size: 13px;
    color: #999;
}

.badge-blue {
    background: #e3f2fd;
    color: #0066cc;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-red {
    background: #ffebee;
    color: #d32f2f;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

/* 페이지네이션 */
.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 40px 0;
}

.pagination .page {
    padding: 8px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s;
}

.pagination .page:hover {
    background: #f5f5f5;
}

.pagination .page.active {
    background: #0066cc;
    color: white;
    border-color: #0066cc;
}

/* 성공사례 그리드 */
.cases-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin: 40px 0;
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

/* 로더 */
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
    .news-grid,
    .cases-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}
</style>

<script>
// 탭 전환 기능
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.intro-tab-btn');
    const tabContents = document.querySelectorAll('.intro-tab-content');

    tabButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            // 모든 버튼과 컨텐츠에서 active 제거
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // 클릭한 버튼과 해당 컨텐츠에 active 추가
            button.classList.add('active');
            tabContents[index].classList.add('active');

            // 성공사례 탭으로 전환 시 무한 스크롤 초기화
            if (index === 0) {
                initInfiniteScroll();
            }
        });
    });
});

// 무한 스크롤 기능
let currentPage = 1;
let isLoading = false;
let hasMore = <?php echo ($cases_offset + $cases_per_page) < $cases_total ? 'true' : 'false'; ?>;

function initInfiniteScroll() {
    if (window.scrollInfiniteInitialized) return;
    window.scrollInfiniteInitialized = true;

    window.addEventListener('scroll', function() {
        if (isLoading || !hasMore) return;

        const casesTab = document.getElementById('tab-cases');
        if (!casesTab.classList.contains('active')) return;

        const scrollPosition = window.innerHeight + window.scrollY;
        const pageHeight = document.documentElement.scrollHeight;

        if (scrollPosition >= pageHeight - 300) {
            loadMoreCases();
        }
    });
}

function loadMoreCases() {
    isLoading = true;
    document.getElementById('casesLoader').style.display = 'block';

    currentPage++;

    fetch(`ajax/load_cases.php?page=${currentPage}`)
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

<?php include 'includes/footer.php'; ?>
