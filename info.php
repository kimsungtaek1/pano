<?php
// 네이버 지도 API 키 (환경변수에서 로드)
$naver_map_client_id = 'xchb6be6bp';

include 'includes/header.php';
?>

<main>
    <!-- Top Image Section -->
    <section class="intro-hero">
        <img src="/images/info.png" alt="오시는길" style="width: 100%; display: block;">
        <div class="intro-hero-text-container">
            <div class="container">
                <div class="intro-hero-text">
                    <p class="hero-subtitle">LAW FIRM PANO</p>
                    <h1 class="hero-title">오시는길</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- 지도 섹션 -->
    <section class="map-section">
        <div class="container">
            <div id="map" style="width:100%;aspect-ratio:2/1;"></div>
        </div>
    </section>


    <!-- 연락처 정보 -->
    <section class="contact-info">
        <div class="container">
            <div class="info-row">
                <div class="info-label">오시는길</div>
                <div class="info-value">서울특별시 서초구 반포대로28길 63, 3층(남양빌딩) <br class="mobile-br">(교대역 14번 출구에서 215m)</div>
            </div>

            <div class="info-row">
                <div class="info-label">업무시간</div>
                <div class="info-value">평일 09:00~18:00</div>
            </div>

            <div class="info-row">
                <div class="info-label">대표전화</div>
                <div class="info-value">1551-8385</div>
            </div>

            <div class="info-row">
                <div class="info-label">대표팩스</div>
                <div class="info-value">02-6008-2884</div>
            </div>
        </div>
    </section>

    <!-- 건물 사진 섹션 -->
    <section class="building-photos">
        <div class="container">
            <div class="photo-grid">
                <div class="photo-card" data-index="0">
                    <img src="/images/comming1.png" alt="사무실 사진 1" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; cursor: pointer;">
                </div>
                <div class="photo-card" data-index="1">
                    <img src="/images/comming2.png" alt="사무실 사진 2" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; cursor: pointer;">
                </div>
                <div class="photo-card" data-index="2">
                    <img src="/images/comming3.png" alt="사무실 사진 3" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; cursor: pointer;">
                </div>
            </div>
        </div>
    </section>

    <!-- 이미지 모달 -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <span class="modal-close">&times;</span>
        <button class="modal-prev" onclick="event.stopPropagation(); changeSlide(-1)">&#10094;</button>
        <img class="modal-content" id="modalImage" onclick="event.stopPropagation()">
        <button class="modal-next" onclick="event.stopPropagation(); changeSlide(1)">&#10095;</button>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<!-- 네이버 지도 API -->
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpKeyId=xchb6be6bp"></script>

<script>
    // DOM과 네이버 지도 스크립트가 모두 로드된 후 실행
    function initMap() {
        if (typeof naver === 'undefined' || !naver.maps) {
            setTimeout(initMap, 100);
            return;
        }

        // 네이버 지도 초기화 (서울 서초구 반포대로28길 63, 3층)
        var mapOptions = {
            center: new naver.maps.LatLng(37.491680, 127.011780),
            zoom: 17
        };

        var map = new naver.maps.Map('map', mapOptions);

        // 마커 추가
        var marker = new naver.maps.Marker({
            position: new naver.maps.LatLng(37.491680, 127.011780),
            map: map
        });

        // 정보창 추가
        var infowindow = new naver.maps.InfoWindow({
            content: '<div style="padding:10px;"><b>파노 법률사무소</b><br>서울 서초구 반포대로28길 63, 3층</div>'
        });

        // 마커 클릭 시 정보창 표시
        naver.maps.Event.addListener(marker, 'click', function() {
            if (infowindow.getMap()) {
                infowindow.close();
            } else {
                infowindow.open(map, marker);
            }
        });

        // 처음에 정보창 열기
        infowindow.open(map, marker);
    }

    // 페이지 로드 후 지도 초기화
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }

    // 이미지 모달 관련 코드
    const images = [
        '/images/comming1.png',
        '/images/comming2.png',
        '/images/comming3.png'
    ];
    let currentImageIndex = 0;

    function openModal(index) {
        currentImageIndex = index;
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modal.style.display = 'flex';
        modalImg.src = images[currentImageIndex];
        document.body.style.overflow = 'hidden'; // 배경 스크롤 방지
    }

    // photo-card 클릭 이벤트 (모바일 터치 지원)
    document.querySelectorAll('.photo-card').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const index = parseInt(this.getAttribute('data-index'));
            openModal(index);
        });
    });

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // 배경 스크롤 복원
    }

    function changeSlide(direction) {
        currentImageIndex += direction;
        if (currentImageIndex < 0) {
            currentImageIndex = images.length - 1;
        } else if (currentImageIndex >= images.length) {
            currentImageIndex = 0;
        }
        const modalImg = document.getElementById('modalImage');
        modalImg.src = images[currentImageIndex];
    }

    // ESC 키로 모달 닫기
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // 좌우 화살표 키로 슬라이드
    document.addEventListener('keydown', function(event) {
        const modal = document.getElementById('imageModal');
        if (modal.style.display === 'flex') {
            if (event.key === 'ArrowLeft') {
                changeSlide(-1);
            } else if (event.key === 'ArrowRight') {
                changeSlide(1);
            }
        }
    });
</script>
