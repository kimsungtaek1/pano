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

    // 처리중 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE status = 'processing'");
    $processing_consultations = $stmt->fetchColumn();

    // 완료 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE status = 'completed'");
    $completed_consultations = $stmt->fetchColumn();

    // 오늘 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE DATE(created_at) = CURDATE()");
    $today_consultations = $stmt->fetchColumn();

    // 이번주 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
    $week_consultations = $stmt->fetchColumn();

    // 이번달 상담신청 수
    $stmt = $pdo->query("SELECT COUNT(*) FROM consultations WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
    $month_consultations = $stmt->fetchColumn();

    // 최근 상담신청 10건
    $stmt = $pdo->query("SELECT * FROM consultations ORDER BY created_at DESC LIMIT 10");
    $recent_consultations = $stmt->fetchAll();

    // 미처리 상담신청 (긴급)
    $stmt = $pdo->query("SELECT * FROM consultations WHERE status = 'pending' ORDER BY created_at ASC LIMIT 5");
    $urgent_consultations = $stmt->fetchAll();

    // 최근 7일간 일별 상담신청 통계
    $stmt = $pdo->query("
        SELECT
            DATE(created_at) as date,
            COUNT(*) as count
        FROM consultations
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $daily_stats = $stmt->fetchAll();

} catch (PDOException $e) {
    $total_consultations = 0;
    $pending_consultations = 0;
    $processing_consultations = 0;
    $completed_consultations = 0;
    $today_consultations = 0;
    $week_consultations = 0;
    $month_consultations = 0;
    $recent_consultations = [];
    $urgent_consultations = [];
    $daily_stats = [];
}

// 상태 텍스트 배열
$status_labels = [
    'pending' => '미처리',
    'processing' => '처리중',
    'completed' => '완료'
];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>대시보드 - PANO 관리자</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .dashboard-col-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- 사이드바 -->
        <aside class="sidebar">
            <div class="logo">
                <h2>⚖️ PANO</h2>
                <p style="font-size: 12px; color: #95a5a6; margin-top: 5px;">법률사무소 관리</p>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php" class="active">
                    <span class="nav-icon">📊</span> 대시보드
                </a>
                <a href="consultation_list.php">
                    <span class="nav-icon">💬</span> 상담신청 관리
                </a>
                <a href="admin_list.php">
                    <span class="nav-icon">👥</span> 관리자 관리
                </a>
                <a href="logout.php">
                    <span class="nav-icon">🚪</span> 로그아웃
                </a>
            </nav>
            <div class="admin-info">
                <div style="padding: 15px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                    <p style="font-size: 13px; color: #ecf0f1; margin-bottom: 3px;">로그인:</p>
                    <p style="font-size: 14px; font-weight: 600; color: #fff;"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                </div>
            </div>
        </aside>

        <!-- 메인 컨텐츠 -->
        <main class="main-content">
            <!-- 헤더 -->
            <div class="content-header" style="margin-bottom: 30px;">
                <div>
                    <h1 style="margin-bottom: 5px;">📊 대시보드</h1>
                    <p style="color: #7f8c8d; font-size: 14px;">법률사무소 PANO 관리 시스템 - <?php echo date('Y년 m월 d일'); ?></p>
                </div>
            </div>

            <!-- 주요 통계 카드 (상단) -->
            <div class="stats-grid-main">
                <div class="stat-card-modern stat-card-total">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">📈</span>
                        <span class="stat-trend">전체</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">총 상담신청</p>
                        <p class="stat-number-large"><?php echo number_format($total_consultations); ?></p>
                    </div>
                </div>

                <div class="stat-card-modern stat-card-urgent">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">⚠️</span>
                        <span class="stat-trend urgent">긴급</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">미처리</p>
                        <p class="stat-number-large"><?php echo number_format($pending_consultations); ?></p>
                    </div>
                </div>

                <div class="stat-card-modern stat-card-processing">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">⏳</span>
                        <span class="stat-trend processing">진행중</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">처리중</p>
                        <p class="stat-number-large"><?php echo number_format($processing_consultations); ?></p>
                    </div>
                </div>

                <div class="stat-card-modern stat-card-completed">
                    <div class="stat-card-header">
                        <span class="stat-icon-large">✅</span>
                        <span class="stat-trend completed">완료</span>
                    </div>
                    <div class="stat-card-body">
                        <p class="stat-label">완료</p>
                        <p class="stat-number-large"><?php echo number_format($completed_consultations); ?></p>
                    </div>
                </div>
            </div>

            <!-- 기간별 통계 -->
            <div class="stats-period">
                <div class="period-card">
                    <div class="period-icon">📅</div>
                    <div class="period-info">
                        <p class="period-label">오늘</p>
                        <p class="period-number"><?php echo number_format($today_consultations); ?></p>
                    </div>
                </div>
                <div class="period-card">
                    <div class="period-icon">📆</div>
                    <div class="period-info">
                        <p class="period-label">이번주</p>
                        <p class="period-number"><?php echo number_format($week_consultations); ?></p>
                    </div>
                </div>
                <div class="period-card">
                    <div class="period-icon">📊</div>
                    <div class="period-info">
                        <p class="period-label">이번달</p>
                        <p class="period-number"><?php echo number_format($month_consultations); ?></p>
                    </div>
                </div>
            </div>

            <!-- 메인 그리드 -->
            <div class="dashboard-grid">
                <!-- 최근 상담신청 -->
                <div class="dashboard-section-modern">
                    <div class="section-header-modern">
                        <h2>💬 최근 상담신청</h2>
                        <a href="consultation_list.php" class="btn btn-secondary btn-sm">전체보기 →</a>
                    </div>

                    <div class="consultation-list">
                        <?php if (empty($recent_consultations)): ?>
                            <div class="empty-state">
                                <p style="font-size: 48px; margin-bottom: 10px;">📭</p>
                                <p style="color: #95a5a6;">상담신청이 없습니다.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_consultations as $consultation): ?>
                                <div class="consultation-item">
                                    <div class="consultation-status">
                                        <span class="status-badge status-<?php echo $consultation['status']; ?>">
                                            <?php echo $status_labels[$consultation['status']]; ?>
                                        </span>
                                    </div>
                                    <div class="consultation-content">
                                        <div class="consultation-header">
                                            <strong><?php echo htmlspecialchars($consultation['name']); ?></strong>
                                            <span class="consultation-time"><?php echo date('m/d H:i', strtotime($consultation['created_at'])); ?></span>
                                        </div>
                                        <div class="consultation-text">
                                            <?php echo htmlspecialchars(mb_substr($consultation['content'], 0, 80)) . (mb_strlen($consultation['content']) > 80 ? '...' : ''); ?>
                                        </div>
                                        <div class="consultation-meta">
                                            <span>📞 <?php echo htmlspecialchars($consultation['phone']); ?></span>
                                            <?php if (!empty($consultation['category'])): ?>
                                                <span>🏷️ <?php echo htmlspecialchars($consultation['category']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="consultation-action">
                                        <a href="consultation_view.php?id=<?php echo $consultation['id']; ?>" class="btn-view">보기</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 긴급 처리 필요 -->
                <div class="dashboard-section-modern">
                    <div class="section-header-modern">
                        <h2>⚡ 처리 필요</h2>
                        <span class="badge-count"><?php echo count($urgent_consultations); ?></span>
                    </div>

                    <div class="urgent-list">
                        <?php if (empty($urgent_consultations)): ?>
                            <div class="empty-state">
                                <p style="font-size: 36px; margin-bottom: 10px;">✅</p>
                                <p style="color: #27ae60; font-weight: 500;">모두 처리되었습니다!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($urgent_consultations as $urgent): ?>
                                <a href="consultation_view.php?id=<?php echo $urgent['id']; ?>" class="urgent-item">
                                    <div class="urgent-header">
                                        <strong><?php echo htmlspecialchars($urgent['name']); ?></strong>
                                        <span class="urgent-time">
                                            <?php
                                            $created = new DateTime($urgent['created_at']);
                                            $now = new DateTime();
                                            $diff = $now->diff($created);

                                            if ($diff->d > 0) {
                                                echo $diff->d . '일 전';
                                            } elseif ($diff->h > 0) {
                                                echo $diff->h . '시간 전';
                                            } else {
                                                echo $diff->i . '분 전';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="urgent-content">
                                        <?php echo htmlspecialchars(mb_substr($urgent['content'], 0, 50)) . '...'; ?>
                                    </div>
                                    <div class="urgent-footer">
                                        📞 <?php echo htmlspecialchars($urgent['phone']); ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 빠른 작업 -->
            <div class="dashboard-col-full">
                <div class="section-header-modern">
                    <h2>⚡ 빠른 작업</h2>
                </div>
                <div class="quick-actions-modern">
                    <a href="consultation_list.php?status=pending" class="quick-action-modern quick-action-pending">
                        <div class="quick-action-icon">⚠️</div>
                        <div class="quick-action-content">
                            <h3>미처리 상담 확인</h3>
                            <p><?php echo number_format($pending_consultations); ?>건의 미처리 상담이 있습니다</p>
                        </div>
                        <div class="quick-action-arrow">→</div>
                    </a>

                    <a href="consultation_list.php?status=processing" class="quick-action-modern quick-action-processing">
                        <div class="quick-action-icon">⏳</div>
                        <div class="quick-action-content">
                            <h3>처리중 상담 관리</h3>
                            <p><?php echo number_format($processing_consultations); ?>건 진행중</p>
                        </div>
                        <div class="quick-action-arrow">→</div>
                    </a>

                    <a href="consultation_list.php" class="quick-action-modern quick-action-all">
                        <div class="quick-action-icon">📋</div>
                        <div class="quick-action-content">
                            <h3>전체 상담 관리</h3>
                            <p>모든 상담신청 보기</p>
                        </div>
                        <div class="quick-action-arrow">→</div>
                    </a>

                    <a href="admin_list.php" class="quick-action-modern quick-action-admin">
                        <div class="quick-action-icon">👥</div>
                        <div class="quick-action-content">
                            <h3>관리자 관리</h3>
                            <p>관리자 계정 관리</p>
                        </div>
                        <div class="quick-action-arrow">→</div>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
