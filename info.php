<?php include 'includes/header.php'; ?>

<main>
    <!-- 페이지 타이틀 -->
    <section class="page-title">
        <div class="container">
            <h1>법인정보</h1>
        </div>
    </section>

    <!-- 지도 섹션 -->
    <section class="map-section">
        <div class="container">
            <div id="map" style="width:100%;height:400px;"></div>
        </div>
    </section>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // 지도 초기화 (서울 서초구 반포대로28길 63, 3층)
        var map = L.map('map').setView([37.4982, 127.0067], 17);

        // OpenStreetMap 타일 레이어 추가
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // 마커 추가
        var marker = L.marker([37.4982, 127.0067]).addTo(map);
        
        // 팝업 추가
        marker.bindPopup('<b>법무법인 파노</b><br>서울 서초구 반포대로28길 63, 3층').openPopup();
    </script>

    <!-- 연락처 정보 -->
    <section class="contact-info">
        <div class="container">
            <div class="info-grid">
                <!-- 주소 -->
                <div class="info-item">
                    <h3>주소</h3>
                    <p class="info-large">서울 서초구 반포대로28길 63, 3층</p>
                </div>

                <!-- 이메일 -->
                <div class="info-item">
                    <h3>이메일</h3>
                    <p class="info-large">intake@panolaw.com</p>
                </div>
            </div>

            <div class="info-grid">
                <!-- 전화번호 -->
                <div class="info-item">
                    <h3>전화번호</h3>
                    <p class="info-large">02-1551-8385</p>
                </div>

                <!-- 팩스번호 -->
                <div class="info-item">
                    <h3>팩스번호</h3>
                    <p class="info-large">02-6008-2884</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 인재모집 -->
    <section class="recruit-section">
        <div class="container">
            <h2>인재모집</h2>
            <p class="recruit-desc">
                고객에게 모태나 신속하고 정성있으르는 최고의 서비스를 제공하드는 목표를 가진 법무법인 파노과 함께 걸어갈 능력있는 인재에게 남성의 믿우 문을 채니 열어 놓고 있습니다. 법무법인 파노과 함께 하고자 하는 법조사의 적극적인 지원을 바랍니다.
            </p>
            <div class="recruit-contact">
                <h3>이메일</h3>
                <p class="info-large">recruit@panolaw.com</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
