<?php
include 'includes/header.php';
require_once 'includes/db.php';

// 활성화된 구성원 조회 (전문 자문단)
$stmt = $pdo->query("SELECT * FROM members WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
$members = $stmt->fetchAll();

// 각 구성원의 약력 조회
foreach ($members as $key => $member) {
    $stmt = $pdo->prepare("SELECT career FROM member_careers WHERE member_id = ? ORDER BY display_order ASC, id ASC");
    $stmt->execute([$member['id']]);
    $members[$key]['careers'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<main>
    <!-- Top Image Section -->
    <section class="intro-hero">
        <img src="/images/intro_main.png" alt="파노 법률사무소" style="width: 100%; display: block;">
        <div class="intro-hero-text-container">
            <div class="container">
                <div class="intro-hero-text">
                    <p class="hero-subtitle">LAW FIRM PANO</p>
                    <h1 class="hero-title">파노 소개</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Tab Buttons Section -->
    <section class="intro-tabs-section">
        <div class="container">
            <div class="intro-tab-buttons">
                <button class="intro-tab-btn active">파노 법률사무소</button>
                <button class="intro-tab-btn">구성원</button>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="intro-content-section">
        <div class="container">
            <!-- 파노 법률사무소 탭 컨텐츠 -->
            <div class="intro-tab-content active" id="tab-lawfirm">
                <div class="intro-bg-container">
                    <img src="/images/intro.png" alt="법률 서적" class="intro-bg-image">
                    <div class="intro-text-overlay">
                        <div class="intro-text">
                            <h2>세상과 당신사이,<br>파노 법률사무소가 있습니다</h2>
                            <p><strong>사람을 먼저 이해하고, 그 다음에 법을 적용합니다.</strong></p>
                            <p>법률은 사람을 위한 도구입니다.<br>
                            그런데도 세상은 종종, 법을 모르는 이들에게는 너무 차갑고 어렵습니다.<br>
                            파노는 그런 세상을 바꾸고 싶었습니다.<br>
                            누군가의 억울함이 법의 언어로 온전히 전달되길 바랐고,<br>
                            지치고 외로운 싸움에도 "내 편이 있어요"라는 말 한마디가 되기를 원했습니다.</p>
                            <p>파노 법률사무소는 형사, 금융, 회생파산, 의료, 민사 등 다양한 법률문제에서 실질적인 해결책을 제시합니다.<br>
                            검사 출신 송 동 민 대표 변호사를 중심으로, 각 분야 전문 자문단이 함께하여 의뢰인의 사건에 가장 적합한 전문성과 경험을 제공합니다.<br>
                            형사 변론에서는 검찰 실무 경험을 바탕으로 한 전략적 대응을, 금융·경제사건에서는 복잡한 거래구조에 대한 깊이 있는 분석을,<br>
                            개인파산·회생에서는 채무자의 경제적 재기를 위한 현실적 조언을 드립니다.</p>
                            <p>또한 한의사 면허를 보유한 대표 변호사의 의료 전문지식은 의료분쟁 사건에서 의학적 쟁점을 정확히 파악하고 설득력 있는 변론을 가능하게 합니다.<br>
                            단순한 법률 자문을 넘어, 의뢰인의 상황을 깊이 이해하고 가장 현실적인 대안을 함께 모색합니다.</p>
                            <p>형사 변론, 금융 사기, 개인파산·회생, 의료분쟁, 학교폭력 등 민감하고 복잡한 사안에서 의뢰인의 권익을 최우선으로 생각하며 최선을 다합니다.<br>
                            파노 법률사무소는 전문 자문단과의 협업을 통해 각 분야의 최신 판례와 실무 동향을 반영한 최적의 법률 서비스를 제공하며,<br>
                            의뢰인과의 신뢰를 바탕으로 끝까지 함께하겠습니다. 전문성과 진정성으로 의뢰인 곁에서 함께합니다.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 구성원 탭 컨텐츠 -->
            <div class="intro-tab-content" id="tab-members">
                <!-- 대표 변호사 섹션 -->
                <div class="member-main">
                    <img src="/images/person_logo.svg" alt="PANO" class="member-main-logo">
                    <div class="member-main-content">
                        <div class="member-info">
                            <h2 class="member-name">송동민 <span class="member-title">대표변호사</span></h2>
                            <h3 class="member-subtitle">검사 한의사 역임 변호사</h3>
                            <ul class="member-career">
                                <li>원광대학교 한의학과 졸업(원광대학교 전체 수석 입학)</li>
                                <li>대한 공중보건 한 의사협의회 법제 이사</li>
                                <li>전남 신안군 공중보건의/경기도 안성시 공중보건의</li>
                                <li>한방병원/요양병원 한방과장</li>
                                <li>부산대학교 법학전문대학원 졸업</li>
                                <li>변호사시험 8회</li>
                                <li>의정부지검 검사(건축/부동산, 환경 등 전담)</li>
                                <li>순천지청 검사(조세/관세, 서민 다중 범죄, 사행/퇴폐, 스토킹 등 전담)</li>
                                <li>목포지청 검사(강력, 성폭력, 가정폭력, 아동학대, 소년범죄, 조세/관세, 해양 등 전담)</li>
                                <li>법률사무소 상산 대표 변호사(전)</li>
                                <li>재외 동포 청 정보공개심의회 위원</li>
                                <li>해운사, 의약외품 제조사 등 고문 변호사</li>
                                <li>파노 법률사무소 대표 변호사(현)</li>
                                <li>&nbsp;</li>
                            </ul>
                        </div>
                        <div class="member-photo">
                            <img src="/images/person/main.png" alt="송동민 대표변호사">
                        </div>
                    </div>
                    <div class="member-expertise">
                        <span class="expertise-label">전문분야  |</span>
                        <button class="expertise-btn">성범죄</button>
                        <button class="expertise-btn">마약</button>
                        <button class="expertise-btn">학교폭력</button>
                        <button class="expertise-btn">교통사고</button>
                    </div>
                </div>

                <!-- 모바일용 대표 변호사 섹션 -->
                <div class="member-main-mobile">
                    <div class="mobile-top-section">
                        <img src="/images/person_logo.svg" alt="PANO" class="mobile-logo">
                        <div class="mobile-member-info">
                            <h2 class="mobile-member-name">송동민 <span class="mobile-member-title">대표변호사</span></h2>
                            <div class="mobile-member-badge">검사·한의사 역임 변호사</div>
                        </div>
                        <div class="mobile-member-photo">
                            <img src="/images/person/main.png" alt="송동민 대표변호사">
                        </div>
                    </div>
                    <div class="mobile-member-expertise">
                        <span class="mobile-expertise-label">전문분야  |</span>
                        <button class="mobile-expertise-btn">성범죄</button>
                        <button class="mobile-expertise-btn">마약</button>
                        <button class="mobile-expertise-btn">학교폭력</button>
                        <button class="mobile-expertise-btn">교통사고</button>
                    </div>
                    <div class="mobile-bottom-section">
                        <ul class="mobile-member-career">
                            <li>원광대학교 한의학과 졸업(원광대학교 전체 수석 입학)</li>
                            <li>대한 공중보건 한 의사협의회 법제 이사</li>
                            <li>전남 신안군 공중보건의/경기도 안성시 공중보건의</li>
                            <li>한방병원/요양병원 한방과장</li>
                            <li>부산대학교 법학전문대학원 졸업</li>
                            <li>변호사시험 8회</li>
                            <li>의정부지검 검사(건축/부동산, 환경 등 전담)</li>
                            <li>순천지청 검사(조세/관세, 서민 다중 범죄, 사행/퇴폐, 스토킹 등 전담)</li>
                            <li>목포지청 검사(강력, 성폭력, 가정폭력, 아동학대, 소년범죄, 조세/관세, 해양 등 전담)</li>
                            <li>법률사무소 상산 대표 변호사(전)</li>
                            <li>재외 동포 청 정보공개심의회 위원</li>
                            <li>해운사, 의약외품 제조사 등 고문 변호사</li>
                            <li>파노 법률사무소 대표 변호사(현)</li>
                        </ul>
                    </div>
                </div>

                <!-- 전문 자문단 섹션 -->
                <div class="advisory-team">
                    <h2 class="section-title advisory-team-title">전문 자문단</h2>
                    <!-- 첫 번째 줄: 3명 -->
                    <div class="team-grid team-grid-3">
                        <?php for ($i = 0; $i < 3 && $i < count($members); $i++):
                            $member = $members[$i];
                        ?>
                            <div class="team-card">
                                <img src="/images/person_logo.svg" alt="PANO" class="team-card-logo">
                                <div class="team-photo">
                                    <?php if (!empty($member['profile_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($member['profile_image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                    <?php else: ?>
                                        <img src="/images/person/person<?php echo $member['id']; ?>.png" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="team-info-box">
                                    <h3 class="team-name"><?php echo htmlspecialchars($member['name']); ?> <span class="team-title"><?php echo htmlspecialchars($member['position']); ?></span></h3>
                                    <ul class="team-specialty">
                                        <?php foreach ($member['careers'] as $career): ?>
                                            <li><?php echo htmlspecialchars($career); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <!-- 두 번째 줄: 4명 -->
                    <div class="team-grid team-grid-4">
                        <?php for ($i = 3; $i < count($members); $i++):
                            $member = $members[$i];
                        ?>
                            <div class="team-card">
                                <img src="/images/person_logo.svg" alt="PANO" class="team-card-logo">
                                <div class="team-photo">
                                    <?php if (!empty($member['profile_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($member['profile_image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                    <?php else: ?>
                                        <img src="/images/person/person<?php echo $member['id']; ?>.png" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="team-info-box">
                                    <h3 class="team-name"><?php echo htmlspecialchars($member['name']); ?> <span class="team-title"><?php echo htmlspecialchars($member['position']); ?></span></h3>
                                    <ul class="team-specialty">
                                        <?php foreach ($member['careers'] as $career): ?>
                                            <li><?php echo htmlspecialchars($career); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- 인재 모집 섹션 -->
                <div class="recruitment">
                    <h2 class="section-title">인재 모집</h2>
                    <p class="recruitment-text">파노 법률사무소는 고객에게 신속하고 정성 있는 최고의 법률 서비스를 제공하는 것을 목표로 합니다.<br>
                    함께 성장하며 이 목표를 실현해 갈 능력 있는 법조인재를 기다리고 있습니다.<br>
                    저희 파노 법률사무소는 열려 있는 마음으로 새로운 동료를 맞이할 준비가 되어 있으며,<br>
                    전문성과 책임감을 갖춘 분들의 적극적인 지원을 환영합니다.</p>
                    <h2 class="section-title">이메일</h2>
                    <p class="recruitment-email">intake@panolaw.com</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
