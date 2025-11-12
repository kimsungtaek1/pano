<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

$edit_mode = isset($_GET['id']);
$error = '';
$success = '';

// 수정 모드일 때 기존 데이터 로드
if ($edit_mode) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$id]);
    $admin = $stmt->fetch();

    if (!$admin) {
        header('Location: admin_list.php');
        exit;
    }
}

// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 유효성 검사
    if (empty($username)) {
        $error = '아이디를 입력해주세요.';
    } elseif (!$edit_mode && empty($password)) {
        $error = '비밀번호를 입력해주세요.';
    } elseif (!empty($password) && $password !== $password_confirm) {
        $error = '비밀번호가 일치하지 않습니다.';
    } elseif (!empty($password) && strlen($password) < 4) {
        $error = '비밀번호는 최소 4자 이상이어야 합니다.';
    } else {
        // 아이디 중복 체크
        if ($edit_mode) {
            $check_stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
            $check_stmt->execute([$username, $id]);
        } else {
            $check_stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
            $check_stmt->execute([$username]);
        }

        if ($check_stmt->fetch()) {
            $error = '이미 사용 중인 아이디입니다.';
        } else {
            // 저장 처리
            if ($edit_mode) {
                // 수정
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE admin_users SET username = ?, password = ? WHERE id = ?");
                    $update_stmt->execute([$username, $hashed_password, $id]);
                } else {
                    $update_stmt = $pdo->prepare("UPDATE admin_users SET username = ? WHERE id = ?");
                    $update_stmt->execute([$username, $id]);
                }

                // 자신의 정보를 수정한 경우 세션 업데이트
                if ($id === $_SESSION['admin_id']) {
                    $_SESSION['admin_username'] = $username;
                }

                $success = '관리자 정보가 수정되었습니다.';

                // 데이터 다시 로드
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
                $stmt->execute([$id]);
                $admin = $stmt->fetch();
            } else {
                // 신규 등록
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
                $insert_stmt->execute([$username, $hashed_password]);

                $success = '새 관리자가 추가되었습니다.';

                // 신규 등록 후 목록으로 이동
                header('Location: admin_list.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? '관리자 수정' : '관리자 추가'; ?> - PANO 관리자</title>
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
                <a href="news_list.php">뉴스 관리</a>
                <a href="consultation_list.php">상담신청 관리</a>
                <a href="admin_list.php" class="active">관리자 관리</a>
                <a href="logout.php">로그아웃</a>
            </nav>
            <div class="admin-info">
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?>님</p>
            </div>
        </aside>

        <!-- 메인 컨텐츠 -->
        <main class="main-content">
            <div class="content-header">
                <h1><?php echo $edit_mode ? '관리자 수정' : '새 관리자 추가'; ?></h1>
                <a href="admin_list.php" class="btn btn-secondary">목록으로</a>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="username">아이디 *</label>
                    <input type="text" id="username" name="username" required
                           value="<?php echo $edit_mode ? htmlspecialchars($admin['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">
                        <?php echo $edit_mode ? '비밀번호 (변경하지 않으려면 비워두세요)' : '비밀번호 *'; ?>
                    </label>
                    <input type="password" id="password" name="password"
                           <?php echo $edit_mode ? '' : 'required'; ?>
                           placeholder="최소 4자 이상">
                </div>

                <div class="form-group">
                    <label for="password_confirm">
                        <?php echo $edit_mode ? '비밀번호 확인' : '비밀번호 확인 *'; ?>
                    </label>
                    <input type="password" id="password_confirm" name="password_confirm"
                           <?php echo $edit_mode ? '' : 'required'; ?>>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_mode ? '수정하기' : '추가하기'; ?>
                    </button>
                    <a href="admin_list.php" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
