    <!-- Fixed Bottom Consultation Bar -->
    <div class="fixed-consultation-bar" id="fixedConsultationBar">
        <form id="fixedConsultationForm" class="fixed-consultation-form" method="POST" action="/api/submit_consultation.php">
            <input type="text" name="name" placeholder="이름" required>
            <input type="tel" name="phone" placeholder="연락처 (010-1234-5678)" required>
            <textarea name="content" placeholder="상담내용을 간단히 입력해주세요" required></textarea>
            <button type="submit" class="btn-submit-bar">상담신청</button>
        </form>
    </div>

    <script>
    // 모바일 고정 상담신청 폼 토글 기능
    (function() {
        const fixedBar = document.getElementById('fixedConsultationBar');
        const fixedForm = document.getElementById('fixedConsultationForm');
        
        if (!fixedBar || !fixedForm) return;
        
        let isFormVisible = false;
        
        // 모바일인지 체크
        function isMobile() {
            return window.innerWidth <= 768;
        }
        
        // 폼 표시/숨김 토글
        function toggleForm() {
            if (!isMobile()) return;
            
            isFormVisible = !isFormVisible;
            if (isFormVisible) {
                fixedBar.classList.add('expanded');
            } else {
                fixedBar.classList.remove('expanded');
            }
        }
        
        // 폼 숨기기
        function hideForm() {
            if (!isMobile() || !isFormVisible) return;
            
            isFormVisible = false;
            fixedBar.classList.remove('expanded');
        }
        
        // 버튼 클릭 이벤트
        const submitBtn = fixedForm.querySelector('.btn-submit-bar');
        submitBtn.addEventListener('click', function(e) {
            if (!isMobile()) return;
            
            // 폼이 닫혀있으면 열기만 하고 제출 방지
            if (!isFormVisible) {
                e.preventDefault();
                toggleForm();
                return;
            }
            
            // 폼이 열려있고 유효성 검사 통과하면 제출 진행
            if (!fixedForm.checkValidity()) {
                return; // 브라우저 기본 유효성 검사 메시지 표시
            }
        });
        
        // 스크롤 시 폼 숨기기
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(hideForm, 100);
        }, { passive: true });
        
        // 외부 터치 시 폼 숨기기
        document.addEventListener('touchstart', function(e) {
            if (!isMobile() || !isFormVisible) return;
            
            // 고정 바 외부를 터치한 경우에만 숨김
            if (!fixedBar.contains(e.target)) {
                hideForm();
            }
        }, { passive: true });
        
        // 화면 크기 변경 시 모바일이 아니면 폼 숨김
        window.addEventListener('resize', function() {
            if (!isMobile() && isFormVisible) {
                hideForm();
            }
        });
    })();
    </script>

    <!-- Floating Action Buttons -->
    <div class="floating-buttons">
        <a href="tel:1551-8385" class="floating-btn phone" title="전화 상담">
            <img src="/images/phone.png" alt="전화">
        </a>
        <a href="http://pf.kakao.com/_GGltn/chat" target="_blank" class="floating-btn kakao" title="카카오톡 상담">
            <img src="/images/kakao.png" alt="카카오톡">
        </a>
        <a href="https://blog.naver.com/cthrtic64924" target="_blank" class="floating-btn blog" title="블로그">
            <img src="/images/blog.png" alt="블로그">
        </a>

    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-logo">
                        <img src="/images/logo_w.png" alt="PANO">
                    </div>
                </div>
                <div class="footer-right">
                    <div class="footer-info">
                        <p>파노 법률사무소 | 광고책임변호사 송동민</p>
                        <p>주소: 서울 서초구 반포대로28길 63, 3층 | 대표번호: 02-1551-8385 | 팩스번호: 02-6008-2884 | 대표이메일: intake@panolaw.com</p>
                        <p>© Copyright 2025 Law Firm PANO. All Rights Reserved.</p>
                    </div>
                    <div class="footer-links">
                        <a href="#">이용약관</a>
                        <a href="#">개인정보처리방침</a>
                        <a href="#">이메일무단수집거부</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="/js/script.js"></script>
</body>
</html>
