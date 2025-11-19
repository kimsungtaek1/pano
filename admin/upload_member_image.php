<?php
// 오류를 로그에만 기록하고 화면에는 표시하지 않음 (JSON 응답을 깨뜨리지 않기 위해)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 응답 헤더 설정 (가장 먼저)
header('Content-Type: application/json; charset=utf-8');

try {
    session_start();

    // 로그인 체크
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => '권한이 없습니다.']);
        exit;
    }

    // 파일 업로드 체크
    if (!isset($_FILES['image'])) {
        echo json_encode(['success' => false, 'error' => '파일이 전송되지 않았습니다.']);
        exit;
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error_message = '파일 업로드 오류: ';
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message .= '파일 크기가 너무 큽니다.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message .= '파일이 부분적으로만 업로드되었습니다.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message .= '파일이 업로드되지 않았습니다.';
                break;
            default:
                $error_message .= '알 수 없는 오류 (코드: ' . $_FILES['image']['error'] . ')';
        }
        echo json_encode(['success' => false, 'error' => $error_message]);
        exit;
    }

    $file = $_FILES['image'];
    $upload_dir = '../images/person/';

    // 업로드 디렉토리 생성
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('디렉토리 생성 실패');
        }
    }

    // 파일 확장자 체크 (PNG만 허용)
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file_extension !== 'png') {
        echo json_encode(['success' => false, 'error' => 'PNG 형식의 이미지만 업로드 가능합니다.']);
        exit;
    }

    // 파일 크기 체크 (5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => '파일 크기는 5MB 이하여야 합니다.']);
        exit;
    }

    // member_id 받기 (POST 또는 GET)
    $member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : null;

    // member_id가 없으면 DB에서 다음 ID 가져오기
    if (!$member_id) {
        require_once '../includes/db.php';
        $stmt = $pdo->query("SELECT COALESCE(MAX(id), 0) + 1 as next_id FROM members");
        $result = $stmt->fetch();
        $member_id = $result['next_id'];
    }

    // person{id}.png 형식으로 파일명 생성
    $new_filename = 'person' . $member_id . '.png';
    $upload_path = $upload_dir . $new_filename;

    // 디버깅 로그
    error_log("Upload attempt - Member ID: $member_id, Filename: $new_filename, Path: $upload_path");
    error_log("Temp file: " . $file['tmp_name'] . ", Exists: " . (file_exists($file['tmp_name']) ? 'yes' : 'no'));
    error_log("Upload dir exists: " . (is_dir($upload_dir) ? 'yes' : 'no') . ", Writable: " . (is_writable($upload_dir) ? 'yes' : 'no'));

    // 파일 이동
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // 파일 권한 설정
        chmod($upload_path, 0644);

        // 상대 URL 반환
        $file_url = '/images/person/' . $new_filename;

        error_log("Upload success - File saved: $upload_path");

        echo json_encode([
            'success' => true,
            'url' => $file_url,
            'filename' => $new_filename
        ]);
    } else {
        $error_detail = error_get_last();
        error_log("Upload failed - " . print_r($error_detail, true));
        throw new Exception('파일 저장에 실패했습니다. 디렉토리 권한을 확인하세요.');
    }

} catch (Exception $e) {
    error_log("Exception in upload: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
