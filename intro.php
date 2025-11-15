<?php include 'includes/header.php'; ?>

<main>
    <!-- Intro Section -->
    <section class="intro-section">
        <div class="intro-tabs">
            <button class="tab-btn active" onclick="switchTab('office')">파노 입점처</button>
            <button class="tab-btn" onclick="switchTab('members')">구성원</button>
        </div>

        <div class="intro-content">
            <div class="content-wrapper">
                <div class="text-content" id="office-content">
                    <h2>세상과 당신사이,<br>파노 법률사무소가 있습니다</h2>
                    <p><strong>사람을 먼저 이해하고, 그 다음에 법을 적용합니다.</strong></p>
                    <p>법률은 사람을 위한 도구입니다. 그런데도 세상은 종종, 법을 모르는 이들에게는 너무 차갑고 어렵습니다. 파노는 그런 세상을 바꾸고 싶었습니다. 누군가의 억울함이 법의 언어로 온전히 전달되길 바랐고, 지치고 외로운 싸움에도 "내 편이 있어요"라는 말 한마디가 되기를 원했습니다.</p>
                    <p>파노 법률사무소는 형사, 금융, 회생파산, 의료, 민사 등 다양한 법률문제에서 실질적인 해결책을 제시합니다. 검사 출신 송 등 민 대표 변호사를 중심으로, 각 분야 전문 자문단이 함께하여 의뢰인의 사건에 가장 적합한 전문성과 경험을 제공합니다. 형사 변론에서는 검찰 실무 경험을 바탕으로 한 전략적 대응을, 금융·경제사건에서는 복잡한 거래구조에 대한 깊이 있는 분석을, 개인파산·회생에서는 채무자의 경제적 재기를 위한 현실적 조언을 드립니다.</p>
                    <p>또한 한의사 면허를 보유한 대표 변호사의 의료 전문지식은 의료분쟁 사건에서 의학적 쟁점을 정확히 파악하고 설득력 있는 변론을 가능하게 합니다. 단순한 법률 자문을 넘어, 의뢰인의 상황을 깊이 이해하고 가장 현실적인 대안을 함께 모색합니다.</p>
                    <p>형사 변론, 금융 사기, 개인파산·회생, 의료분쟁, 학교폭력 등 민감하고 복잡한 사안에서 의뢰인의 권익을 최우선으로 생각하며 최선을 다합니다. 파노 법률사무소는 전문 자문단과의 협업을 통해 각 분야의 최신 판례와 실무 동향을 반영한 최적의 법률 서비스를 제공하며, 의뢰인과의 신뢰를 바탕으로 끝까지 함께하겠습니다. 전문성과 진정성으로 의뢰인 곁에서 함께합니다.</p>
                </div>

                <div class="content-nav">
                    <span class="nav-dot active"></span>
                    <span class="nav-dot"></span>
                    <span class="nav-dot"></span>
                    <span class="nav-dot"></span>
                </div>
            </div>

            <div class="intro-image">
                <img src="/images/intro.png" alt="파노 법률사무소">
            </div>
        </div>
    </section>

    <script>
    function switchTab(tab) {
        const tabs = document.querySelectorAll('.tab-btn');
        tabs.forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');

        // 탭 전환 기능은 추후 구현
        if (tab === 'members') {
            // 구성원 콘텐츠로 전환
        }
    }
    </script>
</main>

<?php include 'includes/footer.php'; ?>
