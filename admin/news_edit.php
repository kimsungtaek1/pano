<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

$error = '';
$success = '';
$news = null;
$is_edit = false;

// 수정 모드인 경우 기존 데이터 로드
if (isset($_GET['id'])) {
    $is_edit = true;
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();

    if (!$news) {
        header('Location: news_list.php');
        exit;
    }
}

// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $case_type = $_POST['case_type'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $news_date = $_POST['news_date'] ?? '';
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // 이미지는 이미 즉시 업로드/삭제로 처리되므로, 기존 DB 값 유지
    // 수정 모드일 경우 현재 DB의 image_urls를 그대로 사용
    $image_urls_json = null;
    if ($is_edit) {
        // 수정 시에는 DB에서 현재 image_urls 가져오기 (이미 upload_image.php와 delete_image.php로 업데이트됨)
        $stmt = $pdo->prepare("SELECT image_urls FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch();
        $image_urls_json = $current['image_urls'] ?? '[]';
    } else {
        // 새 글 작성 시에는 빈 배열로 시작 (저장 후 이미지 추가)
        $image_urls_json = '[]';
    }

    // 유효성 검사
    if (empty($category) || empty($title) || empty($content) || empty($news_date)) {
        $error = '필수 항목을 모두 입력해주세요.';
    } else {
        try {
            if ($is_edit) {
                // 수정
                $stmt = $pdo->prepare("UPDATE news SET category = ?, title = ?, content = ?, summary = ?, case_type = ?, subtitle = ?, news_date = ?, is_published = ?, image_urls = ? WHERE id = ?");
                $stmt->execute([$category, $title, $content, $summary, $case_type, $subtitle, $news_date, $is_published, $image_urls_json, $id]);
                // 수정 성공 후 리다이렉트하여 최신 데이터 표시
                header("Location: news_edit.php?id=$id&success=2");
                exit;
            } else {
                // 새 글 작성
                $stmt = $pdo->prepare("INSERT INTO news (category, title, content, summary, case_type, subtitle, news_date, is_published, image_urls) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$category, $title, $content, $summary, $case_type, $subtitle, $news_date, $is_published, $image_urls_json]);
                $success = '뉴스가 등록되었습니다.';

                // 새로 생성된 ID로 리다이렉트
                $id = $pdo->lastInsertId();
                header("Location: news_edit.php?id=$id&success=1");
                exit;
            }
        } catch (PDOException $e) {
            $error = '저장 중 오류가 발생했습니다: ' . $e->getMessage();
        }
    }
}

// 성공 메시지 (리다이렉트 후)
if (isset($_GET['success'])) {
    if ($_GET['success'] == '2') {
        $success = '뉴스가 수정되었습니다.';
    } else {
        $success = '뉴스가 등록되었습니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? '뉴스 수정' : '뉴스 작성'; ?> - PANO 관리자</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- 사이드바 -->
        <aside class="sidebar">
            <div class="logo">
                <h2>PANO 관리자</h2>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php">대시보드</a>
                <a href="consultation_list.php">상담신청 관리</a>
                <a href="news_list.php" class="active">뉴스 관리</a>
                <a href="member_list.php">구성원 관리</a>
                <a href="admin_list.php">관리자 관리</a>
                <a href="logout.php">로그아웃</a>
            </nav>
            <div class="admin-info">
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?>님</p>
            </div>
        </aside>

        <!-- 메인 컨텐츠 -->
        <main class="main-content">
            <div class="content-header">
                <h1><?php echo $is_edit ? '뉴스 수정' : '뉴스 작성'; ?></h1>
                <a href="news_list.php" class="btn btn-secondary">목록으로</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" class="news-form" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">카테고리 <span class="required">*</span></label>
                        <select id="category" name="category" required>
                            <option value="">선택하세요</option>
                            <option value="파노 성공사례" <?php echo (isset($news) && $news['category'] === '파노 성공사례') ? 'selected' : ''; ?>>파노 성공사례</option>
                            <option value="언론보도" <?php echo (isset($news) && $news['category'] === '언론보도') ? 'selected' : ''; ?>>언론보도</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="news_date">뉴스 날짜 <span class="required">*</span></label>
                        <input type="date" id="news_date" name="news_date"
                               value="<?php echo isset($news) ? $news['news_date'] : date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_published"
                                   <?php echo (!isset($news) || $news['is_published']) ? 'checked' : ''; ?>>
                            공개
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title">제목 <span class="required">*</span></label>
                    <input type="text" id="title" name="title"
                           value="<?php echo isset($news) ? htmlspecialchars($news['title']) : ''; ?>"
                           required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="summary">요약</label>
                    <textarea id="summary" name="summary" rows="3"
                              placeholder="뉴스 목록에 표시될 요약 내용을 입력하세요"><?php echo isset($news) ? htmlspecialchars($news['summary']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="case_type">유형 (원형 외각선 버튼)</label>
                        <input type="text" id="case_type" name="case_type"
                               value="<?php echo isset($news) ? htmlspecialchars($news['case_type'] ?? '') : ''; ?>"
                               placeholder="예: 회생파산, 형사, 민사, 금융" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="subtitle">소제목 (하이라이트 박스)</label>
                        <input type="text" id="subtitle" name="subtitle"
                               value="<?php echo isset($news) ? htmlspecialchars($news['subtitle'] ?? '') : ''; ?>"
                               placeholder="예: 결과적 반감률 60%" maxlength="255">
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">내용 <span class="required">*</span></label>
                    <textarea id="content" name="content" rows="15" required><?php echo isset($news) ? htmlspecialchars($news['content']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>이미지 업로드 (최대 10개)</label>
                    <div class="image-upload-section">
                        <div class="image-list" id="image-list">
                            <?php
                            $existing_urls = [];
                            if (isset($news) && isset($news['image_urls']) && !empty($news['image_urls'])) {
                                $decoded = json_decode($news['image_urls'], true);
                                if (is_array($decoded)) {
                                    $existing_urls = $decoded;
                                }
                            }
                            foreach ($existing_urls as $index => $existing_image):
                            ?>
                            <div class="existing-image" id="existing-image-<?php echo $index; ?>">
                                <img src="<?php echo htmlspecialchars($existing_image); ?>" alt="이미지">
                                <button type="button" class="btn-delete-image" onclick="deleteImage(<?php echo $id ?? 0; ?>, '<?php echo htmlspecialchars($existing_image); ?>', <?php echo $index; ?>)">삭제</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="image-upload-input">
                            <input type="file" id="image-upload" accept="image/*" class="image-file-input">
                            <button type="button" class="btn btn-primary" onclick="uploadImage()">이미지 추가</button>
                        </div>
                    </div>
                    <small style="color: #666; display: block; margin-top: 8px;">
                        JPG, PNG, WEBP 형식의 이미지를 업로드하세요.
                    </small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $is_edit ? '수정하기' : '등록하기'; ?>
                    </button>
                    <a href="news_list.php" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </main>
    </div>

    <script src="js/admin.js"></script>
    <script>
    // 현재 뉴스 ID
    const currentNewsId = <?php echo $id ?? 0; ?>;

    // 이미지 즉시 삭제
    function deleteImage(newsId, imageUrl, index) {
        if (!newsId) {
            alert('뉴스를 먼저 저장해주세요.');
            return;
        }

        if (!confirm('이미지를 삭제하시겠습니까?')) {
            return;
        }

        fetch('delete_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'news_id=' + newsId + '&image_url=' + encodeURIComponent(imageUrl)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // UI에서 이미지 제거
                const imageDiv = document.getElementById('existing-image-' + index);
                if (imageDiv) {
                    imageDiv.remove();
                }
            } else {
                alert('삭제 실패: ' + (data.error || '알 수 없는 오류'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('삭제 중 오류가 발생했습니다.');
        });
    }

    // 이미지 즉시 업로드
    function uploadImage() {
        if (!currentNewsId) {
            alert('뉴스를 먼저 저장해주세요.');
            return;
        }

        const fileInput = document.getElementById('image-upload');
        const file = fileInput.files[0];

        if (!file) {
            alert('파일을 선택해주세요.');
            return;
        }

        const formData = new FormData();
        formData.append('news_id', currentNewsId);
        formData.append('image', file);

        fetch('upload_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // UI에 이미지 추가
                const imageList = document.getElementById('image-list');
                const newIndex = imageList.children.length;
                const newImageDiv = document.createElement('div');
                newImageDiv.className = 'existing-image';
                newImageDiv.id = 'existing-image-' + newIndex;
                newImageDiv.innerHTML = `
                    <img src="${data.image_url}" alt="이미지">
                    <button type="button" class="btn-delete-image" onclick="deleteImage(${currentNewsId}, '${data.image_url}', ${newIndex})">삭제</button>
                `;
                imageList.appendChild(newImageDiv);

                // 파일 입력 초기화
                fileInput.value = '';
            } else {
                alert('업로드 실패: ' + (data.error || '알 수 없는 오류'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('업로드 중 오류가 발생했습니다.');
        });
    }
    </script>
</body>
</html>
