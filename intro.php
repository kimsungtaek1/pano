<?php include 'includes/header.php'; ?>

<main>
    <!-- Intro Image -->
    <section class="intro-full-image">
        <img src="/images/intro.png" alt="Pano Law Office Introduction" style="width: 100%; display: block;">
    </section>

    <!-- Success Stories Slider -->
    <section class="success-stories">
        <div class="container">
            <h2>Success Stories</h2>
            <div class="slider-container">
                <div class="slider success-slider">
                    <div class="slide">
                        <h3>저금 성장 법률사무소</h3>
                        <p>성장지 성장 응력</p>
                        <span class="year">권장되다</span>
                    </div>
                    <div class="slide">
                        <h3>블레이터 내송,<br>큰 결과의 자여 하버시의 회복</h3>
                        <span class="year">법무제인스타터</span>
                    </div>
                    <div class="slide">
                        <h3>힘내라 머들의 가죽 고개병원<br>반으시 약 양항라스,<br>가금 병원년 박수스</h3>
                        <span class="year">형형 박상라부터</span>
                    </div>
                </div>
                <button class="slider-btn prev" onclick="moveSlide('success', -1)">‹</button>
                <button class="slider-btn next" onclick="moveSlide('success', 1)">›</button>
            </div>
        </div>
    </section>

    <!-- Press Coverage Slider -->
    <section class="press-coverage">
        <div class="container">
            <h2>Press Coverage</h2>
            <div class="slider-container">
                <div class="slider press-slider">
                    <div class="slide">
                        <h3>가장 큰검즌 되장<br>선재화나주의 명주화니의 썽시</h3>
                        <span class="year">Legal Times</span>
                    </div>
                    <div class="slide">
                        <h3>아역에 담당- 기곃 무거담</h3>
                        <span class="year">서울규현지법원</span>
                    </div>
                    <div class="slide">
                        <h3>손해액 담당하시는<br>혼돈 생명미하나 거주 축함니</h3>
                        <span class="year">책형 담하니</span>
                    </div>
                </div>
                <button class="slider-btn prev" onclick="moveSlide('press', -1)">‹</button>
                <button class="slider-btn next" onclick="moveSlide('press', 1)">›</button>
            </div>
        </div>
    </section>

    <script>
    let successIndex = 0;
    let pressIndex = 0;

    function moveSlide(type, direction) {
        const slider = document.querySelector(`.${type}-slider`);
        const slides = slider.querySelectorAll('.slide');
        
        if (type === 'success') {
            successIndex += direction;
            if (successIndex < 0) successIndex = slides.length - 1;
            if (successIndex >= slides.length) successIndex = 0;
            slider.style.transform = `translateX(-${successIndex * 100}%)`;
        } else {
            pressIndex += direction;
            if (pressIndex < 0) pressIndex = slides.length - 1;
            if (pressIndex >= slides.length) pressIndex = 0;
            slider.style.transform = `translateX(-${pressIndex * 100}%)`;
        }
    }
    </script>
</main>

<?php include 'includes/footer.php'; ?>
