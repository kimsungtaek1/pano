<?php
// 오류 출력 활성화 (개발 중)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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

    // 약력 데이터 로드
    $stmt = $pdo->prepare("SELECT * FROM member_careers WHERE member_id = ? ORDER BY display_order ASC, id ASC");
    $stmt->execute([$id]);
    $careers = $stmt->fetchAll();
}

// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $profile_image = trim($_POST['profile_image'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $careers_data = $_POST['careers'] ?? [];
    
    // 디버깅: POST 데이터 로그
    error_log("POST careers data: " . print_r($careers_data, true));

    // 유효성 검사
    if (empty($name)) {
        $error = '이름은 필수 항목입니다.';
    } else {
        try {
            $pdo->beginTransaction();

            if ($is_edit) {
                // 수정
                $stmt = $pdo->prepare("UPDATE members SET name = ?, position = ?, profile_image = ?, display_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$name, $position, $profile_image, $display_order, $is_active, $id]);

                // 기존 약력 삭제
                $stmt = $pdo->prepare("DELETE FROM member_careers WHERE member_id = ?");
                $stmt->execute([$id]);

                // 새 약력 추가
                if (!empty($careers_data)) {
                    $stmt = $pdo->prepare("INSERT INTO member_careers (member_id, career, display_order) VALUES (?, ?, ?)");
                    foreach ($careers_data as $index => $career) {
                        $career_text = trim($career);
                        if (!empty($career_text)) {
                            $stmt->execute([$id, $career_text, $index]);
                        }
                    }
                }

                $pdo->commit();
                
                // 수정 완료 후 리스트 페이지로 이동
                header("Location: member_list.php");
                exit;
            } else {
                // 새 구성원 추가
                $stmt = $pdo->prepare("INSERT INTO members (name, position, profile_image, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $position, $profile_image, $display_order, $is_active]);
                $id = $pdo->lastInsertId();

                // 약력 추가
                if (!empty($careers_data)) {
                    $stmt = $pdo->prepare("INSERT INTO member_careers (member_id, career, display_order) VALUES (?, ?, ?)");
                    foreach ($careers_data as $index => $career) {
                        $career_text = trim($career);
                        if (!empty($career_text)) {
                            $stmt->execute([$id, $career_text, $index]);
                        }
                    }
                }

                $pdo->commit();
                
                // 등록 완료 후 리스트 페이지로 이동
                header("Location: member_list.php");
                exit;
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = '저장 중 오류가 발생했습니다: ' . $e->getMessage();
        }
    }
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
                        <label for="profile_image">프로필 이미지</label>

                        <!-- 이미지 업로드 -->
                        <div style="margin-bottom: 15px;">
                            <input type="file" id="image_file" accept="image/png" style="margin-bottom: 10px;">
                            <button type="button" id="upload_btn" class="btn btn-secondary btn-sm" style="background: #e74c3c; color: white;">이미지 업로드</button>
                            <small style="display: block; margin-top: 5px; color: #666;">또는 아래에 이미지 URL을 직접 입력하세요 (최대 5MB, PNG만 가능)</small>
                        </div>

                        <!-- URL 입력 -->
                        <input type="text" id="profile_image" name="profile_image" value="<?php echo htmlspecialchars($member['profile_image'] ?? ''); ?>" placeholder="https://example.com/image.jpg 또는 /images/person/xxx.jpg">

                        <!-- 이미지 미리보기 -->
                        <div id="image_preview_container" style="margin-top: 10px; <?php echo empty($member['profile_image']) ? 'display:none;' : ''; ?>">
                            <img id="image_preview" src="<?php echo htmlspecialchars($member['profile_image'] ?? ''); ?>" alt="프로필 미리보기" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                            <button type="button" id="remove_image_btn" class="btn btn-sm" style="display: block; margin-top: 5px; background: #e74c3c; color: white;">이미지 제거</button>
                        </div>

                        <!-- 업로드 진행 상태 -->
                        <div id="upload_status" style="margin-top: 10px; display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label>약력</label>
                        <div id="careers_container">
                            <?php if ($is_edit && !empty($careers)): ?>
                                <?php foreach ($careers as $index => $career): ?>
                                    <div class="career-item" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                        <input type="text" name="careers[]" value="<?php echo htmlspecialchars($career['career']); ?>" placeholder="약력을 입력하세요" style="flex: 1;">
                                        <button type="button" class="btn btn-sm remove-career-btn" style="background: #e74c3c; color: white; padding: 5px 15px;">삭제</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="career-item" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <input type="text" name="careers[]" placeholder="약력을 입력하세요" style="flex: 1;">
                                    <button type="button" class="btn btn-sm remove-career-btn" style="background: #e74c3c; color: white; padding: 5px 15px;">삭제</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add_career_btn" class="btn btn-secondary btn-sm" style="margin-top: 10px;">약력 추가</button>
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
    <script>
    // 이미지 업로드 기능
    document.addEventListener('DOMContentLoaded', function() {
        const uploadBtn = document.getElementById('upload_btn');
        const imageFile = document.getElementById('image_file');
        const profileImageInput = document.getElementById('profile_image');
        const imagePreview = document.getElementById('image_preview');
        const imagePreviewContainer = document.getElementById('image_preview_container');
        const uploadStatus = document.getElementById('upload_status');
        const removeImageBtn = document.getElementById('remove_image_btn');

        // 업로드 버튼 클릭
        uploadBtn.addEventListener('click', function() {
            if (!imageFile.files || !imageFile.files[0]) {
                alert('업로드할 이미지를 선택해주세요.');
                return;
            }

            const file = imageFile.files[0];

            // 파일 크기 체크 (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('파일 크기는 5MB 이하여야 합니다.');
                return;
            }

            // 파일 형식 체크 (PNG만 허용)
            if (file.type !== 'image/png') {
                alert('PNG 형식의 이미지만 업로드 가능합니다.');
                return;
            }

            // FormData 생성
            const formData = new FormData();
            formData.append('image', file);
            <?php if ($is_edit): ?>
            formData.append('member_id', '<?php echo $id; ?>');
            <?php endif; ?>

            // 업로드 상태 표시
            uploadStatus.style.display = 'block';
            uploadStatus.innerHTML = '<span style="color: #3498db;">업로드 중...</span>';
            uploadBtn.disabled = true;

            // AJAX 업로드
            fetch('upload_member_image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // URL 입력란에 업로드된 이미지 경로 설정
                    profileImageInput.value = data.url;

                    // 미리보기 표시
                    imagePreview.src = data.url;
                    imagePreviewContainer.style.display = 'block';

                    uploadStatus.innerHTML = '<span style="color: #27ae60;">✓ 업로드 완료!</span>';
                    setTimeout(() => {
                        uploadStatus.style.display = 'none';
                    }, 3000);

                    // 파일 입력 초기화
                    imageFile.value = '';
                } else {
                    uploadStatus.innerHTML = '<span style="color: #e74c3c;">✗ ' + data.error + '</span>';
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                uploadStatus.innerHTML = '<span style="color: #e74c3c;">✗ 업로드 중 오류가 발생했습니다.</span>';
            })
            .finally(() => {
                uploadBtn.disabled = false;
            });
        });

        // URL 입력란 변경 시 미리보기 업데이트
        profileImageInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                imagePreview.src = url;
                imagePreviewContainer.style.display = 'block';
            } else {
                imagePreviewContainer.style.display = 'none';
            }
        });

        // 이미지 제거 버튼
        removeImageBtn.addEventListener('click', function() {
            if (confirm('이미지를 제거하시겠습니까?')) {
                profileImageInput.value = '';
                imagePreviewContainer.style.display = 'none';
                imageFile.value = '';
            }
        });

        // 약력 추가/삭제 기능
        const careersContainer = document.getElementById('careers_container');
        const addCareerBtn = document.getElementById('add_career_btn');

        // 약력 추가
        addCareerBtn.addEventListener('click', function() {
            const careerItem = document.createElement('div');
            careerItem.className = 'career-item';
            careerItem.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
            careerItem.innerHTML = `
                <input type="text" name="careers[]" placeholder="약력을 입력하세요" style="flex: 1;">
                <button type="button" class="btn btn-sm remove-career-btn" style="background: #e74c3c; color: white; padding: 5px 15px;">삭제</button>
            `;
            careersContainer.appendChild(careerItem);
        });

        // 약력 삭제 (이벤트 위임)
        careersContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-career-btn')) {
                const careerItems = careersContainer.querySelectorAll('.career-item');
                if (careerItems.length > 1) {
                    e.target.closest('.career-item').remove();
                } else {
                    alert('최소 1개의 약력 입력란은 유지되어야 합니다.');
                }
            }
        });
    });
    </script>
</body>
</html>
