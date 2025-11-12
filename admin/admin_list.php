<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

// 삭제 처리
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // 자기 자신은 삭제할 수 없음
    if ($delete_id === $_SESSION['admin_id']) {
        $error = '자기 자신은 삭제할 수 없습니다.';
    } else {
        $delete_stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        $delete_stmt->execute([$delete_id]);
        $success = '관리자가 삭제되었습니다.';
    }
}

// 관리자 목록 조회
$stmt = $pdo->query("SELECT id, username, created_at FROM admin_users ORDER BY id ASC");
$admins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 관리 - PANO 관리자</title>
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
                <h1>관리자 관리</h1>
                <a href="admin_edit.php" class="btn btn-primary">새 관리자 추가</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- 관리자 테이블 -->
            <div class="table-container">
                <table class="news-table">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>아이디</th>
                            <th width="180">생성일</th>
                            <th width="150">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($admins)): ?>
                            <tr>
                                <td colspan="4" class="text-center">등록된 관리자가 없습니다.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?php echo $admin['id']; ?></td>
                                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($admin['created_at'])); ?></td>
                                    <td class="action-cell">
                                        <a href="admin_edit.php?id=<?php echo $admin['id']; ?>" class="btn-sm btn-edit">수정</a>
                                        <?php if ($admin['id'] !== $_SESSION['admin_id']): ?>
                                            <a href="?delete=<?php echo $admin['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="info-box">
                <p>전체 <?php echo count($admins); ?>명의 관리자</p>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
