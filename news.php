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
                <div class="detail-header-row">
                    <h2 id="detail-main-title">제목이 여기 들어갑니다</h2>
                    <button class="detail-type-btn" id="detail-type-badge">회생파산</button>
                </div>

                <div class="detail-images-row" id="detail-images-container">
                    <div class="detail-test-image"></div>
                </div>

                <div class="detail-highlight-box" id="detail-highlight">
                    결과적 반감률 60%
                </div>

                <div class="detail-body" id="detail-content">
                    <!-- 본문 내용 -->
                </div>

                <div class="detail-navigation">
                    <button class="nav-btn" id="prev-news-btn" onclick="navigateNews('prev')">
                        <img src="/images/left.svg" alt="이전">
                    </button>
                    <button class="nav-btn" id="next-news-btn" onclick="navigateNews('next')">
                        <img src="/images/right.svg" alt="다음">
                    </button>
                    <button class="nav-btn-list" onclick="hideDetail()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
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

// 현재 표시 중인 뉴스 정보
let currentNewsId = null;
let currentNewsList = [];
let currentTabType = null;

// 상세 화면 표시
function showDetail(id, tabType) {
    currentNewsId = id;
    currentTabType = tabType;

    // AJAX로 뉴스 상세 정보 가져오기
    fetch('get_news_detail.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('뉴스를 찾을 수 없습니다.');
                return;
            }

            // 메인 타이틀 (제목)
            document.getElementById('detail-main-title').textContent = data.title;

            // 종류 버튼 (일단 카테고리 표시, 추후 변경 가능)
            document.getElementById('detail-type-badge').textContent = data.category === '최근 업무사례' ? '회생파산' : '언론보도';

            // 이미지 처리 (image_urls 필드에서 가져오기)
            const imagesContainer = document.getElementById('detail-images-container');
            imagesContainer.innerHTML = '';

            if (data.image_urls && data.image_urls.length > 0) {
                // 모든 이미지 표시
                data.image_urls.forEach((url, i) => {
                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = data.title + ' 이미지 ' + (i + 1);
                    imagesContainer.appendChild(img);
                });
            } else {
                // 이미지가 없을 때 테스트 회색 이미지 1개 표시
                const testImage = document.createElement('div');
                testImage.className = 'detail-test-image';
                imagesContainer.appendChild(testImage);
            }

            // 하이라이트 박스 (본문에서 추출 또는 기본값)
            const highlightBox = document.getElementById('detail-highlight');
            // 임시로 기본값 사용, 추후 DB 필드 추가 가능
            highlightBox.style.display = data.category === '최근 업무사례' ? 'block' : 'none';

            // 본문
            document.getElementById('detail-content').innerHTML = data.content;

            // 리스트 숨기고 상세 화면 표시
            document.getElementById('tab-cases').style.display = 'none';
            document.getElementById('tab-press').style.display = 'none';
            document.getElementById('detail-view').style.display = 'block';

            // 페이지 상단으로 스크롤
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // 현재 탭의 뉴스 리스트 저장 (이전/다음 기능용)
            updateNewsList();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('오류가 발생했습니다.');
        });
}

// 현재 탭의 뉴스 리스트 업데이트
function updateNewsList() {
    const activeTab = currentTabType;
    const cards = document.querySelectorAll(activeTab === 'cases' ? '#tab-cases .case-card' : '#tab-press .case-card');
    currentNewsList = Array.from(cards).map(card => {
        const onclick = card.getAttribute('onclick');
        const match = onclick.match(/showDetail\((\d+)/);
        return match ? parseInt(match[1]) : null;
    }).filter(id => id !== null);
}

// 이전/다음 뉴스 이동
function navigateNews(direction) {
    const currentIndex = currentNewsList.indexOf(currentNewsId);
    let newIndex;

    if (direction === 'prev') {
        newIndex = currentIndex > 0 ? currentIndex - 1 : currentNewsList.length - 1;
    } else {
        newIndex = currentIndex < currentNewsList.length - 1 ? currentIndex + 1 : 0;
    }

    showDetail(currentNewsList[newIndex], currentTabType);
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
