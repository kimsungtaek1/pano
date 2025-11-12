<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once '../includes/db.php';

// 통계 데이터 조회
try {
    // 총 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations");
    $total_consultations = $stmt->fetchColumn();

    // 미처리 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE status = 'pending'");
    $pending_consultations = $stmt->fetchColumn();

    // 오늘 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE DATE(created_at) = CURDATE()");
    $today_consultations = $stmt->fetchColumn();

    // 최근 상담신청 5건
    $stmt = $pdo->query("SELECT * FROM consultations ORDER BY created_at DESC LIMIT 5");
    $recent_consultations = $stmt->fetchAll();

} catch (PDOException $e) {
    $total_consultations = 0;
    $pending_consultations = 0;
    $today_consultations = 0;
    $recent_consultations = [];
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드 - PANO 관리자</title>
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
                <a href="dashboard.php" class="active">대시보드</a>
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
                <h1>대시보드</h1>
                <p>법률사무소 PANO 관리 시스템</p>
            </div>

            <!-- 통계 카드 -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3>총 상담신청</h3>
                        <p class="stat-number"><?php echo number_format($total_consultations); ?></p>
                    </div>
                </div>

                <div class="stat-card pending">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-info">
                        <h3>미처리</h3>
                        <p class="stat-number"><?php echo number_format($pending_consultations); ?></p>
                    </div>
                </div>

                <div class="stat-card today">
                    <div class="stat-icon">📅</div>
                    <div class="stat-info">
                        <h3>오늘 접수</h3>
                        <p class="stat-number"><?php echo number_format($today_consultations); ?></p>
                    </div>
                </div>
            </div>

            <!-- 최근 상담신청 -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2>최근 상담신청</h2>
                    <a href="consultation_list.php" class="btn btn-secondary">전체보기</a>
                </div>

                <div class="table-container">
                    <table class="news-table">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th width="100">이름</th>
                                <th width="150">연락처</th>
                                <th>상담내용</th>
                                <th width="100">상태</th>
                                <th width="150">접수일시</th>
                                <th width="80">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_consultations)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">상담신청이 없습니다.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_consultations as $consultation): ?>
                                    <tr>
                                        <td><?php echo $consultation['id']; ?></td>
                                        <td><?php echo htmlspecialchars($consultation['name']); ?></td>
                                        <td><?php echo htmlspecialchars($consultation['phone']); ?></td>
                                        <td class="title-cell"><?php echo htmlspecialchars(mb_substr($consultation['message'], 0, 50)) . (mb_strlen($consultation['message']) > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <?php
                                            $status_text = [
                                                'pending' => '미처리',
                                                'processing' => '처리중',
                                                'completed' => '완료'
                                            ];
                                            echo '<span class="badge badge-' . $consultation['status'] . '">' . $status_text[$consultation['status']] . '</span>';
                                            ?>
                                        </td>
                                        <td><?php echo date('Y.m.d H:i', strtotime($consultation['created_at'])); ?></td>
                                        <td class="action-cell">
                                            <a href="consultation_view.php?id=<?php echo $consultation['id']; ?>" class="btn-sm btn-edit">보기</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 빠른 작업 -->
            <div class="dashboard-section">
                <h2>빠른 작업</h2>
                <div class="quick-actions">
                    <a href="consultation_list.php?status=pending" class="quick-action-card">
                        <div class="action-icon">📋</div>
                        <h3>미처리 상담 확인</h3>
                        <p>답변이 필요한 상담신청을 확인하세요</p>
                    </a>

                    <a href="admin_list.php" class="quick-action-card">
                        <div class="action-icon">👥</div>
                        <h3>관리자 관리</h3>
                        <p>관리자 계정을 추가하거나 수정하세요</p>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
