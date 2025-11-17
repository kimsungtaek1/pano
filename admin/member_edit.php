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
$member = null;
$is_edit = false;

// 수정 모드인 경우 기존 데이터 로드
if (isset($_GET['id'])) {
    $is_edit = true;
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch();

    if (!$member) {
        header('Location: member_list.php');
        exit;
    }
}

// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $profile_image = trim($_POST['profile_image'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // 유효성 검사
    if (empty($name)) {
        $error = '이름은 필수 항목입니다.';
    } else {
        try {
            if ($is_edit) {
                // 수정
                $stmt = $pdo->prepare("UPDATE members SET name = ?, position = ?, department = ?, email = ?, phone = ?, profile_image = ?, description = ?, display_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $position, $department, $email, $phone, $profile_image, $description, $display_order, $is_active, $id]);
                $success = '구성원 정보가 수정되었습니다.';

                // 수정된 데이터 다시 로드
                $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
                $stmt->execute([$id]);
                $member = $stmt->fetch();
            } else {
                // 새 구성원 추가
                $stmt = $pdo->prepare("INSERT INTO members (name, position, department, email, phone, profile_image, description, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $position, $department, $email, $phone, $profile_image, $description, $display_order, $is_active]);
                $success = '구성원이 등록되었습니다.';

                // 새로 생성된 ID로 리다이렉트
                $id = $pdo->lastInsertId();
                header("Location: member_edit.php?id=$id&success=1");
                exit;
            }
        } catch (PDOException $e) {
            $error = '저장 중 오류가 발생했습니다: ' . $e->getMessage();
        }
    }
}

// 성공 메시지 (리다이렉트 후)
if (isset($_GET['success'])) {
    $success = '구성원이 등록되었습니다.';
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? '구성원 수정' : '구성원 추가'; ?> - PANO 관리자</title>
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
                <a href="news_list.php">뉴스 관리</a>
                <a href="member_list.php" class="active">구성원 관리</a>
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
                <h1><?php echo $is_edit ? '구성원 수정' : '구성원 추가'; ?></h1>
                <a href="member_list.php" class="btn btn-secondary">목록으로</a>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" class="edit-form">
                <div class="form-section">
                    <h2>기본 정보</h2>

                    <div class="form-group">
                        <label for="name">이름 <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($member['name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="position">직책</label>
                        <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($member['position'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="department">부서</label>
                        <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($member['department'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">전화번호</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>" placeholder="예: 02-1234-5678">
                    </div>

                    <div class="form-group">
                        <label for="profile_image">프로필 이미지 URL</label>
                        <input type="url" id="profile_image" name="profile_image" value="<?php echo htmlspecialchars($member['profile_image'] ?? ''); ?>" placeholder="https://example.com/image.jpg">
                        <?php if (!empty($member['profile_image'])): ?>
                            <div class="image-preview" style="margin-top: 10px;">
                                <img src="<?php echo htmlspecialchars($member['profile_image']); ?>" alt="프로필 미리보기" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">소개</label>
                        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($member['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="display_order">표시 순서</label>
                        <input type="number" id="display_order" name="display_order" value="<?php echo $member['display_order'] ?? 0; ?>" min="0">
                        <small>작은 숫자일수록 먼저 표시됩니다.</small>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" <?php echo (!isset($member) || $member['is_active']) ? 'checked' : ''; ?>>
                            활성화 (체크 해제 시 목록에서 숨김)
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">저장</button>
                    <a href="member_list.php" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
