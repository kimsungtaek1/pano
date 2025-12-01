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
                    <button class="detail-type-btn" id="detail-type-badge" style="display: none;"></button>
                </div>

                <div class="detail-slider-container" id="detail-slider-container" style="display: none;">
                    <div class="detail-slider-track" id="detail-slider-track"></div>
                </div>

                <div class="detail-highlight-box" id="detail-highlight" style="display: none;"></div>

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

            <!-- 이미지 라이트박스 -->
            <div id="image-lightbox" class="lightbox">
                <div class="lightbox-overlay" onclick="closeLightbox()"></div>
                <div class="lightbox-content">
                    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
                    <button class="lightbox-prev" onclick="slideLightbox(-1)">&#10094;</button>
                    <div class="lightbox-slider" id="lightbox-slider">
                        <div class="lightbox-track" id="lightbox-track"></div>
                    </div>
                    <button class="lightbox-next" onclick="slideLightbox(1)">&#10095;</button>
                    <div class="lightbox-counter">
                        <span id="lightbox-current">1</span> / <span id="lightbox-total">1</span>
                    </div>
                </div>
            </div>

            <!-- 성공사례 탭 컨텐츠 -->
            <div class="intro-tab-content <?php echo $tab === 'cases' ? 'active' : ''; ?>" id="tab-cases">
                <div class="cases-grid">
                    <?php if (empty($cases_list)): ?>
                        <p style="text-align: center; padding: 60px 0; color: #999;">등록된 파노 성공사례가 없습니다.</p>
                    <?php else: ?>
                        <?php foreach ($cases_list as $case):
                                $case_images = !empty($case['image_urls']) ? json_decode($case['image_urls'], true) : [];
                                $case_thumb = !empty($case_images[0]) ? $case_images[0] : '';
                            ?>
                            <a href="#" class="case-card" onclick="showDetail(<?php echo $case['id']; ?>, 'cases'); return false;">
                                <div class="thumbnail"<?php if ($case_thumb): ?> style="background-image: url('<?php echo htmlspecialchars($case_thumb); ?>');"<?php endif; ?>></div>
                                <div class="content">
                                    <?php if (!empty($case['case_type'])): ?>
                                    <span class="badge badge-red"><?php echo htmlspecialchars($case['case_type']); ?></span>
                                <?php endif; ?>
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
                        <?php foreach ($news_list as $news):
                                $news_images = !empty($news['image_urls']) ? json_decode($news['image_urls'], true) : [];
                                $news_thumb = !empty($news_images[0]) ? $news_images[0] : '';
                            ?>
                            <a href="#" class="case-card" onclick="showDetail(<?php echo $news['id']; ?>, 'press'); return false;">
                                <div class="thumbnail"<?php if ($news_thumb): ?> style="background-image: url('<?php echo htmlspecialchars($news_thumb); ?>');"<?php endif; ?>></div>
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

            // 유형 버튼 (DB의 case_type 필드 사용)
            const typeBadge = document.getElementById('detail-type-badge');
            if (data.case_type && data.case_type.trim() !== '') {
                typeBadge.textContent = data.case_type;
                typeBadge.style.display = 'inline-block';
            } else {
                typeBadge.style.display = 'none';
            }

            // 이미지 슬라이더 처리
            const sliderContainer = document.getElementById('detail-slider-container');
            const sliderTrack = document.getElementById('detail-slider-track');

            // 슬라이더용 이미지 배열 저장
            window.currentImages = data.image_urls || [];

            if (data.image_urls && data.image_urls.length > 0) {
                sliderContainer.style.display = 'block';

                // 이미지 요소 생성
                sliderTrack.innerHTML = '';
                data.image_urls.forEach((url, i) => {
                    const item = document.createElement('div');
                    item.className = 'slider-item';

                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = data.title + ' 이미지 ' + (i + 1);

                    const zoomBtn = document.createElement('button');
                    zoomBtn.className = 'slider-zoom-btn';
                    zoomBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>';
                    zoomBtn.onclick = function(e) { e.stopPropagation(); openLightbox(i); };

                    item.appendChild(img);
                    item.appendChild(zoomBtn);
                    sliderTrack.appendChild(item);
                });

                // 드래그 슬라이더 초기화
                initDragSlider();
            } else {
                sliderContainer.style.display = 'none';
            }

            // 소제목/하이라이트 박스 (DB의 subtitle 필드 사용)
            const highlightBox = document.getElementById('detail-highlight');
            if (data.subtitle && data.subtitle.trim() !== '') {
                highlightBox.textContent = data.subtitle;
                highlightBox.style.display = 'block';
            } else {
                highlightBox.style.display = 'none';
            }

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

// 드래그 슬라이더 관련 함수
function initDragSlider() {
    const track = document.getElementById('detail-slider-track');
    let isDown = false;
    let startX;
    let scrollLeft;

    // 마우스 이벤트
    track.addEventListener('mousedown', (e) => {
        isDown = true;
        track.classList.add('dragging');
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
        e.preventDefault();
    });

    track.addEventListener('mouseleave', () => {
        isDown = false;
        track.classList.remove('dragging');
    });

    track.addEventListener('mouseup', () => {
        isDown = false;
        track.classList.remove('dragging');
    });

    track.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        const walk = (x - startX) * 1.5;
        track.scrollLeft = scrollLeft - walk;
    });

    // 터치 이벤트 (모바일)
    track.addEventListener('touchstart', (e) => {
        isDown = true;
        startX = e.touches[0].pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
    }, { passive: true });

    track.addEventListener('touchend', () => {
        isDown = false;
    });

    track.addEventListener('touchmove', (e) => {
        if (!isDown) return;
        const x = e.touches[0].pageX - track.offsetLeft;
        const walk = (x - startX) * 1.5;
        track.scrollLeft = scrollLeft - walk;
    }, { passive: true });
}

// 라이트박스 관련 변수 및 함수
let currentLightboxIndex = 0;

function openLightbox(index) {
    if (!window.currentImages || window.currentImages.length === 0) return;

    currentLightboxIndex = index;
    const lightbox = document.getElementById('image-lightbox');
    const track = document.getElementById('lightbox-track');

    // 트랙에 모든 이미지 추가
    track.innerHTML = '';
    window.currentImages.forEach((url, i) => {
        const img = document.createElement('img');
        img.src = url;
        img.alt = '이미지 ' + (i + 1);
        track.appendChild(img);
    });

    document.getElementById('lightbox-current').textContent = index + 1;
    document.getElementById('lightbox-total').textContent = window.currentImages.length;

    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';

    // 해당 인덱스로 이동
    setTimeout(() => {
        updateLightboxPosition(false);
    }, 10);

    // 이미지가 1개면 좌우 버튼 숨기기
    const prevBtn = document.querySelector('.lightbox-prev');
    const nextBtn = document.querySelector('.lightbox-next');
    if (window.currentImages.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    }

    // 드래그 이벤트 초기화
    initLightboxDrag();
}

function closeLightbox() {
    const lightbox = document.getElementById('image-lightbox');
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
}

function slideLightbox(direction) {
    if (!window.currentImages || window.currentImages.length === 0) return;

    currentLightboxIndex += direction;

    // 순환
    if (currentLightboxIndex < 0) {
        currentLightboxIndex = window.currentImages.length - 1;
    } else if (currentLightboxIndex >= window.currentImages.length) {
        currentLightboxIndex = 0;
    }

    updateLightboxPosition(true);
}

function updateLightboxPosition(animate) {
    const track = document.getElementById('lightbox-track');
    const slider = document.getElementById('lightbox-slider');
    const slideWidth = slider.offsetWidth;

    track.style.transition = animate ? 'transform 0.3s ease' : 'none';
    track.style.transform = `translateX(-${currentLightboxIndex * slideWidth}px)`;

    document.getElementById('lightbox-current').textContent = currentLightboxIndex + 1;
}

function initLightboxDrag() {
    const track = document.getElementById('lightbox-track');
    const slider = document.getElementById('lightbox-slider');
    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    function handleDragStart(x) {
        isDragging = true;
        startX = x;
        track.style.transition = 'none';
    }

    function handleDragMove(x) {
        if (!isDragging) return;
        currentX = x;
        const diff = currentX - startX;
        const slideWidth = slider.offsetWidth;
        const currentOffset = -currentLightboxIndex * slideWidth;
        track.style.transform = `translateX(${currentOffset + diff}px)`;
    }

    function handleDragEnd() {
        if (!isDragging) return;
        isDragging = false;
        const diff = currentX - startX;

        if (Math.abs(diff) > 50) {
            if (diff > 0 && currentLightboxIndex > 0) {
                currentLightboxIndex--;
            } else if (diff < 0 && currentLightboxIndex < window.currentImages.length - 1) {
                currentLightboxIndex++;
            }
        }

        updateLightboxPosition(true);
    }

    // 마우스 이벤트
    track.onmousedown = (e) => {
        if (e.target.tagName === 'BUTTON') return;
        e.preventDefault();
        handleDragStart(e.clientX);
    };

    track.onmousemove = (e) => {
        handleDragMove(e.clientX);
    };

    track.onmouseup = handleDragEnd;
    track.onmouseleave = () => {
        if (isDragging) handleDragEnd();
    };

    // 터치 이벤트
    track.ontouchstart = (e) => {
        handleDragStart(e.touches[0].clientX);
    };

    track.ontouchmove = (e) => {
        handleDragMove(e.touches[0].clientX);
    };

    track.ontouchend = handleDragEnd;
}

// 키보드 이벤트 (ESC로 닫기, 좌우 화살표로 이동)
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('image-lightbox');
    if (!lightbox.classList.contains('active')) return;

    if (e.key === 'Escape') {
        closeLightbox();
    } else if (e.key === 'ArrowLeft') {
        slideLightbox(-1);
    } else if (e.key === 'ArrowRight') {
        slideLightbox(1);
    }
});

</script>

<?php include 'includes/footer.php'; ?>
