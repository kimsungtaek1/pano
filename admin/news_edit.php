<?php
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
    $news_date = $_POST['news_date'] ?? '';
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // 유효성 검사
    if (empty($category) || empty($title) || empty($content) || empty($news_date)) {
        $error = '필수 항목을 모두 입력해주세요.';
    } else {
        try {
            if ($is_edit) {
                // 수정
                $stmt = $pdo->prepare("UPDATE news SET category = ?, title = ?, content = ?, summary = ?, news_date = ?, is_published = ? WHERE id = ?");
                $stmt->execute([$category, $title, $content, $summary, $news_date, $is_published, $id]);
                $success = '뉴스가 수정되었습니다.';
            } else {
                // 새 글 작성
                $stmt = $pdo->prepare("INSERT INTO news (category, title, content, summary, news_date, is_published) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$category, $title, $content, $summary, $news_date, $is_published]);
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
                <a href="news_list.php" class="active">뉴스 관리</a>
                <a href="consultation_list.php">상담신청 관리</a>
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

            <form method="POST" class="news-form">
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

                <div class="form-group">
                    <label for="content">내용 <span class="required">*</span></label>
                    <textarea id="content" name="content" rows="15" required><?php echo isset($news) ? htmlspecialchars($news['content']) : ''; ?></textarea>
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
</body>
</html>
