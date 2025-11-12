<?php
include 'includes/header.php';
?>

<main>
    <!-- 상담신청 섹션 -->
    <section class="consultation">
        <div class="container">
            <div class="consultation-header">
                <p class="label">문의하기</p>
                <h2>고객님께 전담변호사를 배정하여<br>법률 상담부터 해결까지 도와드립니다.</h2>
            </div>

            <form id="consultationForm" class="consultation-form" method="POST" action="/api/submit_consultation.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">이름 <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">연락처 <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required placeholder="010-1234-5678">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="email" id="email" name="email" placeholder="example@email.com">
                    </div>
                    <div class="form-group">
                        <label for="category">상담분야</label>
                        <select id="category" name="category">
                            <option value="">선택하세요</option>
                            <option value="민사재판">민사재판</option>
                            <option value="형사재판">형사재판</option>
                            <option value="조정중재">조정중재</option>
                            <option value="기업상담">기업상담</option>
                            <option value="기타">기타</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">상담내용 <span class="required">*</span></label>
                    <textarea id="content" name="content" rows="8" required placeholder="상담하실 내용을 자세히 입력해주세요."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">무료상담 신청</button>
                </div>
            </form>
        </div>
    </section>
</main>

<script>
document.getElementById('consultationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('.btn-submit');

    // 버튼 비활성화
    submitBtn.disabled = true;
    submitBtn.textContent = '전송 중...';

    fetch('/api/submit_consultation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('상담신청이 완료되었습니다.\n빠른 시일 내에 연락드리겠습니다.');
            this.reset();
        } else {
            alert('오류가 발생했습니다: ' + (data.message || '다시 시도해주세요.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('오류가 발생했습니다. 다시 시도해주세요.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = '무료상담 신청';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
