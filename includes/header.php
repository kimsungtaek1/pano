<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PANO - 법률사무소</title>

    <!-- Open Graph 메타 태그 (카카오톡, 페이스북 등 SNS 공유용) -->
    <meta name="description" content="의뢰인의 믿음과 신뢰를 받을 수 있도록 최선을 다하겠습니다.">
    <meta property="og:type" content="website">
    <meta property="og:title" content="PANO - 법률사무소">
    <meta property="og:description" content="의뢰인의 믿음과 신뢰를 받을 수 있도록 최선을 다하겠습니다.">
    <meta property="og:image" content="https://panolawyer.com/images/thumbnail.png">
    <meta property="og:url" content="https://panolawyer.com">
    <meta name="naver-site-verification" content="1cfc6e07e5292bb22204b23a558d2efb08ee20f9" />

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo"><a href="/index.php"><img src="/images/logo.png" alt="PANO 법률사무소"></a></div>
                <nav>
                    <ul>
                        <li><a href="/intro.php">파노소개</a></li>
                        <li><a href="/field.php">업무분야</a></li>
                        <li><a href="/news.php">소식/자료</a></li>
                        <li><a href="/info.php">오시는길</a></li>
                    </ul>
                </nav>
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="메뉴">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <!-- 전체 서브메뉴 영역 -->
        <div class="header-submenu-area">
            <div class="container">
                <div class="header-content">
                    <div class="logo submenu-logo-space"></div>
                    <nav>
                        <ul>
                            <li>
                                <a href="/intro.php">파노 법률사무소</a>
                                <a href="/intro.php?tab=members">구성원</a>
                            </li>
                            <li></li>
                            <li>
                                <a href="/news.php?tab=cases">파노 성공사례</a>
                                <a href="/news.php?tab=press">언론보도</a>
                            </li>
                            <li></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="mobile-menu-close" id="mobileMenuClose" aria-label="닫기">×</button>
        <nav>
            <ul>
                <li class="accordion-item">
                    <a href="#" class="accordion-header">파노소개</a>
                    <ul class="accordion-content">
                        <li><a href="/intro.php">파노 법률사무소</a></li>
                        <li><a href="/intro.php?tab=members">구성원</a></li>
                    </ul>
                </li>
                <li><a href="/field.php">업무분야</a></li>
                <li class="accordion-item">
                    <a href="#" class="accordion-header">소식/자료</a>
                    <ul class="accordion-content">
                        <li><a href="/news.php?tab=cases">파노 성공사례</a></li>
                        <li><a href="/news.php?tab=press">언론보도</a></li>
                    </ul>
                </li>
                <li><a href="/info.php">오시는길</a></li>
            </ul>
        </nav>
        <div class="mobile-menu-logo">
            <img src="/images/logo.png" alt="PANO 법률사무소">
        </div>
    </div>

    <script>
    // Mobile menu functionality
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuClose = document.getElementById('mobileMenuClose');

    function toggleMobileMenu() {
        mobileMenuBtn.classList.toggle('active');
        mobileMenu.classList.toggle('active');
        mobileMenuOverlay.classList.toggle('active');
        document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
    }

    function closeMobileMenu() {
        mobileMenuBtn.classList.remove('active');
        mobileMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    mobileMenuBtn?.addEventListener('click', toggleMobileMenu);
    mobileMenuOverlay?.addEventListener('click', closeMobileMenu);
    mobileMenuClose?.addEventListener('click', closeMobileMenu);

    // Accordion functionality
    const accordionHeaders = mobileMenu?.querySelectorAll('.accordion-header');
    accordionHeaders?.forEach(header => {
        header.addEventListener('click', (e) => {
            e.preventDefault();
            const accordionItem = header.parentElement;
            const isActive = accordionItem.classList.contains('active');
            
            // Close all accordion items
            document.querySelectorAll('.accordion-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                accordionItem.classList.add('active');
            }
        });
    });

    // Close mobile menu when clicking a non-accordion link
    const mobileMenuLinks = mobileMenu?.querySelectorAll('a:not(.accordion-header)');
    mobileMenuLinks?.forEach(link => {
        link.addEventListener('click', () => {
            closeMobileMenu();
        });
    });
    </script>
