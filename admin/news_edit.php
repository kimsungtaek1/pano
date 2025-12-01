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
    
    // 이미지 파일 업로드 처리 (최대 5개)
    $image_urls = [];
    $upload_dir = '../uploads/news/';

    // 업로드 디렉토리 생성 (없으면)
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // 기존 이미지 유지
    for ($i = 1; $i <= 5; $i++) {
        $existing_image = $_POST["existing_image_$i"] ?? '';
        $delete_image = isset($_POST["delete_image_$i"]);

        // URL 경로를 서버 파일 경로로 변환
        $existing_file_path = $existing_image ? '..' . $existing_image : '';

        // 삭제 체크가 되어있으면 스킵
        if ($delete_image && $existing_image) {
            // 실제 파일 삭제
            if ($existing_file_path && file_exists($existing_file_path)) {
                @unlink($existing_file_path);
            }
            continue;
        }

        // 새 파일 업로드 확인
        if (isset($_FILES["image_$i"]) && $_FILES["image_$i"]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES["image_$i"];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($extension, $allowed)) {
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $filepath = $upload_dir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $image_urls[] = '/uploads/news/' . $filename;

                    // 기존 파일이 있으면 삭제
                    if ($existing_file_path && file_exists($existing_file_path)) {
                        @unlink($existing_file_path);
                    }
                }
            }
        } elseif ($existing_image) {
            // 새 파일이 없으면 기존 이미지 유지
            $image_urls[] = $existing_image;
        }
    }

    $image_urls_json = json_encode($image_urls, JSON_UNESCAPED_UNICODE);

    // 유효성 검사
    if (empty($category) || empty($title) || empty($content) || empty($news_date)) {
        $error = '필수 항목을 모두 입력해주세요.';
    } else {
        try {
            if ($is_edit) {
                // 수정
                $stmt = $pdo->prepare("UPDATE news SET category = ?, title = ?, content = ?, summary = ?, case_type = ?, subtitle = ?, news_date = ?, is_published = ?, image_urls = ? WHERE id = ?");
                $stmt->execute([$category, $title, $content, $summary, $case_type, $subtitle, $news_date, $is_published, $image_urls_json, $id]);
                $success = '뉴스가 수정되었습니다.';
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
    $success = '뉴스가 등록되었습니다.';
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
                            <option value="최근 업무사례" <?php echo (isset($news) && $news['category'] === '최근 업무사례') ? 'selected' : ''; ?>>최근 업무사례</option>
                            <option value="언론보도" <?php echo (isset($news) && $news['category'] === '언론보도') ? 'selected' : ''; ?>>언론보도</option>
                            <option value="한경BUSINESS" <?php echo (isset($news) && $news['category'] === '한경BUSINESS') ? 'selected' : ''; ?>>한경BUSINESS</option>
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

                <div class="form-row" id="case-fields" style="display: none;">
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
                    <label>이미지 업로드 (최대 5개)</label>
                    <div class="image-upload-container">
                        <?php
                        $existing_urls = [];
                        if (isset($news) && isset($news['image_urls']) && !empty($news['image_urls'])) {
                            $decoded = json_decode($news['image_urls'], true);
                            if (is_array($decoded)) {
                                $existing_urls = $decoded;
                            }
                        }
                        for ($i = 1; $i <= 5; $i++):
                            $existing_image = $existing_urls[$i - 1] ?? '';
                        ?>
                        <div class="image-upload-row">
                            <span class="upload-number"><?php echo $i; ?>.</span>
                            <input type="file" name="image_<?php echo $i; ?>" accept="image/*" class="image-file-input">
                            <?php if ($existing_image): ?>
                                <div class="existing-image">
                                    <img src="<?php echo htmlspecialchars($existing_image); ?>" alt="이미지 <?php echo $i; ?>">
                                    <label>
                                        <input type="checkbox" name="delete_image_<?php echo $i; ?>" value="1">
                                        삭제
                                    </label>
                                    <input type="hidden" name="existing_image_<?php echo $i; ?>" value="<?php echo htmlspecialchars($existing_image); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <small style="color: #666; display: block; margin-top: 8px;">
                        JPG, PNG, WEBP 형식의 이미지를 업로드하세요. 기존 이미지를 유지하려면 새 파일을 선택하지 마세요.
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
    // 카테고리에 따라 유형/소제목 필드 표시/숨김
    function toggleCaseFields() {
        const category = document.getElementById('category').value;
        const caseFields = document.getElementById('case-fields');
        if (category === '최근 업무사례') {
            caseFields.style.display = 'flex';
        } else {
            caseFields.style.display = 'none';
        }
    }

    document.getElementById('category').addEventListener('change', toggleCaseFields);
    // 페이지 로드 시 초기 상태 설정
    document.addEventListener('DOMContentLoaded', toggleCaseFields);
    </script>
</body>
</html>
