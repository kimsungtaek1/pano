<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db.php';

// 현재 탭 확인
$tab = $_GET['tab'] ?? 'cases';

// 성공사례 탭 데이터
$cases_page = isset($_GET['cases_page']) ? (int)$_GET['cases_page'] : 1;
$cases_per_page = 9;
$cases_offset = ($cases_page - 1) * $cases_per_page;

$cases_where_sql = "WHERE is_published = 1 AND category = '최근 업무사례'";

$cases_count_sql = "SELECT COUNT(*) FROM news $cases_where_sql";
$cases_total = $pdo->query($cases_count_sql)->fetchColumn();
$cases_total_pages = ceil($cases_total / $cases_per_page);

$cases_sql = "SELECT * FROM news $cases_where_sql ORDER BY news_date DESC, created_at DESC LIMIT $cases_per_page OFFSET $cases_offset";
$cases_list = $pdo->query($cases_sql)->fetchAll();

// 언론보도 탭 데이터
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$where_sql = "WHERE is_published = 1 AND category = '언론보도'";

$count_sql = "SELECT COUNT(*) FROM news $where_sql";
$total = $pdo->query($count_sql)->fetchColumn();
$total_pages = ceil($total / $per_page);

$sql = "SELECT * FROM news $where_sql ORDER BY news_date DESC, created_at DESC LIMIT $per_page OFFSET $offset";
$news_list = $pdo->query($sql)->fetchAll();

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
                <button class="intro-tab-btn <?php echo $tab === 'cases' ? 'active' : ''; ?>" data-tab="cases">파노 성공사례</button>
                <button class="intro-tab-btn <?php echo $tab === 'press' ? 'active' : ''; ?>" data-tab="press">언론보도</button>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="intro-content-section">
        <div class="container">
            <!-- 뉴스 상세 화면 -->
            <div id="detail-view" style="display: none;">
                <div class="news-detail-content">
                    <div class="news-detail-header">
                        <span class="badge" id="detail-badge"></span>
                        <h2 id="detail-title"></h2>
                        <span class="date" id="detail-date"></span>
                    </div>

                    <div class="news-detail-image" id="detail-image-container" style="display: none;">
                        <img id="detail-image" src="" alt="">
                    </div>

                    <div class="news-detail-body" id="detail-content">
                    </div>

                    <div class="news-detail-actions">
                        <a href="#" class="btn-back" onclick="hideDetail(); return false;">목록으로</a>
                    </div>
                </div>
            </div>

            <!-- 성공사례 탭 컨텐츠 -->
            <div class="intro-tab-content <?php echo $tab === 'cases' ? 'active' : ''; ?>" id="tab-cases">
                <div class="cases-grid">
                    <?php if (empty($cases_list)): ?>
                        <p style="text-align: center; padding: 60px 0; color: #999;">등록된 파노 성공사례가 없습니다.</p>
                    <?php else: ?>
                        <?php foreach ($cases_list as $case): ?>
                            <a href="#" class="case-card" onclick="showDetail(<?php echo $case['id']; ?>, 'cases'); return false;">
                                <div class="thumbnail"></div>
                                <div class="content">
                                    <span class="badge badge-red">구속영장 기각</span>
                                    <h3><?php echo htmlspecialchars($case['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($case['summary'] ?: mb_substr(strip_tags($case['content']), 0, 100) . '...'); ?></p>
                                    <span class="date"><?php echo date('Y.m.d', strtotime($case['news_date'])); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 페이지네이션 -->
                <?php if ($cases_total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $cases_total_pages; $i++): ?>
                            <a href="?tab=cases&cases_page=<?php echo $i; ?>"
                               class="page <?php echo $i === $cases_page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 언론보도 탭 컨텐츠 -->
            <div class="intro-tab-content <?php echo $tab === 'press' ? 'active' : ''; ?>" id="tab-press">
                <div class="news-grid">
                    <?php if (empty($news_list)): ?>
                        <p style="text-align: center; padding: 60px 0; color: #999;">등록된 뉴스가 없습니다.</p>
                    <?php else: ?>
                        <?php foreach ($news_list as $news): ?>
                            <a href="#" class="case-card" onclick="showDetail(<?php echo $news['id']; ?>, 'press'); return false;">
                                <div class="thumbnail"></div>
                                <div class="content">
                                    <span class="badge badge-blue">언론보도</span>
                                    <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($news['summary'] ?: mb_substr(strip_tags($news['content']), 0, 100) . '...'); ?></p>
                                    <span class="date"><?php echo date('Y.m.d', strtotime($news['news_date'])); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 페이지네이션 -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?tab=press&page=<?php echo $i; ?>"
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
/* 뉴스 및 성공사례 리스트 */
.news-grid,
.cases-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin: 40px 0;
}

.case-card {
    background: #fff;
    border-top: 1px solid #ddd;
    border-bottom: none;
    border-left: none;
    border-right: none;
    padding: 15px 0;
    text-decoration: none;
    display: flex;
    flex-direction: row;
    gap: 15px;
}

.case-card:hover {
    background: #fafafa;
}

.cases-grid .case-card .thumbnail {
    width: 120px;
    height: 180px;
    background: #e8e8e8;
    flex-shrink: 0;
}

.news-grid .case-card .thumbnail {
    width: 240px;
    height: 180px;
    background: #e8e8e8;
    flex-shrink: 0;
}

.case-card .content {
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 8px;
    justify-content: center;
}

.case-card .badge {
    align-self: flex-start;
}

.case-card h3 {
    font-size: 18px;
    color: #000;
    line-height: 1.5;
    font-weight: 500;
}

.case-card p {
    font-size: 15px;
    color: #666;
    line-height: 1.5;
}

.case-card .date {
    font-size: 14px;
    color: #999;
}

.badge-red {
    background: #182650;
    color: #fff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 400;
}

.badge-blue {
    background: transparent;
    color: #000;
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid #000;
    font-size: 13px;
    font-weight: 400;
}

/* 페이지네이션 */
.pagination {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin: 40px 0;
}

.pagination .page {
    padding: 8px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 0;
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


/* 뉴스 상세 화면 스타일 */
.news-detail-content {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.news-detail-header {
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.news-detail-header .badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 15px;
}

.news-detail-header h2 {
    font-size: 28px;
    color: #333;
    margin: 0 0 15px 0;
    line-height: 1.4;
}

.news-detail-header .date {
    color: #999;
    font-size: 14px;
}

.news-detail-image {
    margin: 30px 0;
    text-align: center;
}

.news-detail-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

.news-detail-body {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
    margin-bottom: 40px;
}

.news-detail-body p {
    margin-bottom: 20px;
}

.news-detail-body img {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
}

.news-detail-actions {
    text-align: center;
    padding-top: 30px;
    border-top: 1px solid #f0f0f0;
}

.btn-back {
    display: inline-block;
    padding: 12px 30px;
    background: #333;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 15px;
    transition: background 0.3s;
}

.btn-back:hover {
    background: #555;
}

@media (max-width: 768px) {
    .case-card {
        flex-direction: column;
        gap: 10px;
        padding: 15px;
    }

    .case-card .thumbnail {
        width: 100%;
        height: 200px;
    }

    .news-detail-content {
        padding: 30px 20px;
    }

    .news-detail-header h2 {
        font-size: 22px;
    }

    .news-detail-body {
        font-size: 15px;
    }
}
</style>

<script>
// 탭 전환 기능
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.intro-tab-btn');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tab = button.getAttribute('data-tab');
            window.location.href = '?tab=' + tab;
        });
    });
});

// 상세 화면 표시
function showDetail(id, tabType) {
    // AJAX로 뉴스 상세 정보 가져오기
    fetch('get_news_detail.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('뉴스를 찾을 수 없습니다.');
                return;
            }

            // 상세 정보 채우기
            const badge = document.getElementById('detail-badge');
            badge.textContent = data.category;
            badge.className = 'badge ' + (data.category === '최근 업무사례' ? 'badge-red' : 'badge-blue');

            document.getElementById('detail-title').textContent = data.title;
            document.getElementById('detail-date').textContent = data.news_date;
            document.getElementById('detail-content').innerHTML = data.content;

            // 이미지 처리
            const imageContainer = document.getElementById('detail-image-container');
            const image = document.getElementById('detail-image');
            if (data.image) {
                image.src = data.image;
                image.alt = data.title;
                imageContainer.style.display = 'block';
            } else {
                imageContainer.style.display = 'none';
            }

            // 리스트 숨기고 상세 화면 표시
            document.getElementById('tab-cases').style.display = 'none';
            document.getElementById('tab-press').style.display = 'none';
            document.getElementById('detail-view').style.display = 'block';

            // 페이지 상단으로 스크롤
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('오류가 발생했습니다.');
        });
}

// 상세 화면 숨기기
function hideDetail() {
    document.getElementById('detail-view').style.display = 'none';

    // 현재 활성 탭 다시 표시
    const activeTab = document.querySelector('.intro-tab-btn.active').getAttribute('data-tab');
    if (activeTab === 'cases') {
        document.getElementById('tab-cases').style.display = 'block';
    } else {
        document.getElementById('tab-press').style.display = 'block';
    }

    // 페이지 상단으로 스크롤
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include 'includes/footer.php'; ?>
