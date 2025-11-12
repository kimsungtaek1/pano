// 관리자 페이지 JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // 삭제 확인
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('정말 삭제하시겠습니까?')) {
                e.preventDefault();
            }
        });
    });

    // 폼 유효성 검사
    const newsForm = document.querySelector('.news-form');
    if (newsForm) {
        newsForm.addEventListener('submit', function(e) {
            const category = document.getElementById('category').value;
            const title = document.getElementById('title').value;
            const content = document.getElementById('content').value;
            const newsDate = document.getElementById('news_date').value;

            if (!category || !title || !content || !newsDate) {
                e.preventDefault();
                alert('필수 항목을 모두 입력해주세요.');
                return false;
            }
        });
    }

    // 자동 저장 기능 (선택사항)
    let autoSaveTimer;
    const contentTextarea = document.getElementById('content');
    if (contentTextarea) {
        contentTextarea.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // 여기에 자동 저장 로직 추가 가능
                console.log('Auto-save triggered');
            }, 5000); // 5초 후 자동 저장
        });
    }
});
