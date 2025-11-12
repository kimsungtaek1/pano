<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: consultation_list.php');
    exit;
}

// 상담신청 정보 조회
$stmt = $pdo->prepare("SELECT * FROM consultations WHERE id = ?");
$stmt->execute([$id]);
$consultation = $stmt->fetch();

if (!$consultation) {
    header('Location: consultation_list.php');
    exit;
}

// POST 요청 처리 (상태 변경, 메모 저장)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $new_status = $_POST['status'] ?? '';
        $admin_memo = $_POST['admin_memo'] ?? '';

        if (in_array($new_status, ['pending', 'processed'])) {
            $processed_at = $new_status === 'processed' ? date('Y-m-d H:i:s') : null;

            $stmt = $pdo->prepare("
                UPDATE consultations
                SET status = ?, admin_memo = ?, processed_at = ?
                WHERE id = ?
            ");
            $stmt->execute([$new_status, $admin_memo, $processed_at, $id]);

            // 페이지 새로고침
            header("Location: consultation_view.php?id=$id&updated=1");
            exit;
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: consultation_list.php?deleted=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>상담신청 상세 - PANO</title>
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
                <a href="consultation_list.php" class="active">상담신청 관리</a>
                <a href="admin_list.php">관리자 관리</a>
                <a href="logout.php">로그아웃</a>
            </nav>
            <div class="admin-info">
                <p><?php echo htmlspecialchars($_SESSION['admin_username']); ?>님</p>
            </div>
        </aside>

        <!-- 메인 컨텐츠 -->
        <main class="main-content">
        <div class="admin-header">
            <h1>상담신청 상세</h1>
            <a href="consultation_list.php" class="btn-secondary">목록으로</a>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">상담신청 정보가 업데이트되었습니다.</div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="view-container">
                <div class="view-section">
                    <h2>신청자 정보</h2>
                    <table class="view-table">
                        <tr>
                            <th>이름</th>
                            <td><?php echo htmlspecialchars($consultation['name']); ?></td>
                        </tr>
                        <tr>
                            <th>연락처</th>
                            <td><?php echo htmlspecialchars($consultation['phone']); ?></td>
                        </tr>
                        <tr>
                            <th>이메일</th>
                            <td><?php echo htmlspecialchars($consultation['email'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th>상담분야</th>
                            <td><?php echo htmlspecialchars($consultation['category'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <th>신청일시</th>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($consultation['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="view-section">
                    <h2>상담내용</h2>
                    <div class="content-box">
                        <?php echo nl2br(htmlspecialchars($consultation['content'])); ?>
                    </div>
                </div>

                <div class="view-section">
                    <h2>처리 정보</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_status">

                        <div class="form-group">
                            <label for="status">상태</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" <?php echo $consultation['status'] === 'pending' ? 'selected' : ''; ?>>대기중</option>
                                <option value="processed" <?php echo $consultation['status'] === 'processed' ? 'selected' : ''; ?>>처리완료</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="admin_memo">관리자 메모</label>
                            <textarea name="admin_memo" id="admin_memo" class="form-control" rows="5"><?php echo htmlspecialchars($consultation['admin_memo'] ?? ''); ?></textarea>
                        </div>

                        <?php if ($consultation['processed_at']): ?>
                            <div class="form-group">
                                <label>처리일시</label>
                                <div class="info-text">
                                    <?php echo date('Y-m-d H:i:s', strtotime($consultation['processed_at'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">저장</button>
                            <a href="consultation_list.php" class="btn-secondary">취소</a>
                        </div>
                    </form>
                </div>

                <div class="view-section danger-zone">
                    <h2>위험 영역</h2>
                    <form method="POST" action="" onsubmit="return confirm('정말 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.');">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn-danger">이 상담신청 삭제</button>
                    </form>
                </div>
            </div>
        </div>
        </main>
    </div>
</body>
</html>
