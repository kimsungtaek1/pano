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

    try {
        $delete_stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $delete_stmt->execute([$delete_id]);
        $success = '구성원이 삭제되었습니다.';
    } catch (PDOException $e) {
        $error = '구성원 삭제에 실패했습니다: ' . $e->getMessage();
    }
}

// 구성원 목록 조회
$stmt = $pdo->query("SELECT * FROM members ORDER BY display_order ASC, id DESC");
$members = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>구성원 관리 - PANO 관리자</title>
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
                <h1>구성원 관리</h1>
                <a href="member_edit.php" class="btn btn-primary">새 구성원 추가</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- 구성원 테이블 -->
            <div class="table-container">
                <table class="news-table">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th width="100">이름</th>
                            <th width="120">직책</th>
                            <th width="120">부서</th>
                            <th>이메일</th>
                            <th>전화번호</th>
                            <th width="80">순서</th>
                            <th width="80">활성</th>
                            <th width="150">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="9" class="text-center">등록된 구성원이 없습니다.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo $member['id']; ?></td>
                                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                                    <td><?php echo htmlspecialchars($member['position'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($member['department'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($member['email'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($member['phone'] ?? '-'); ?></td>
                                    <td><?php echo $member['display_order']; ?></td>
                                    <td><?php echo $member['is_active'] ? '활성' : '비활성'; ?></td>
                                    <td class="action-cell">
                                        <a href="member_edit.php?id=<?php echo $member['id']; ?>" class="btn-sm btn-edit">수정</a>
                                        <a href="?delete=<?php echo $member['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="info-box">
                <p>전체 <?php echo count($members); ?>명의 구성원</p>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
