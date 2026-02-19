<?php
// API 인증 키 (pano_landing_2와 동일해야 함)
define('API_SECRET_KEY', 'pano2_to_panolaw_9f8x7k2m4v6b');

// DB 설정
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'lez0628');
define('DB_USERNAME', 'lez0628');
define('DB_PASSWORD', 'vkshdb*0628');

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '잘못된 요청'], JSON_UNESCAPED_UNICODE);
    exit;
}

// API 키 검증
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($apiKey !== API_SECRET_KEY) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '인증 실패'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 데이터 받기
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => '데이터 없음'], JSON_UNESCAPED_UNICODE);
    exit;
}

$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$content = trim($input['content'] ?? '');
$utm_source = trim($input['utm_source'] ?? '');
$utm_medium = trim($input['utm_medium'] ?? '');
$utm_campaign = trim($input['utm_campaign'] ?? '');
$utm_content = trim($input['utm_content'] ?? '');
$utm_term = trim($input['utm_term'] ?? '');
$fbclid = trim($input['fbclid'] ?? '');
$ip_address = trim($input['ip_address'] ?? '');
$user_agent = trim($input['user_agent'] ?? '');
$domain = trim($input['domain'] ?? '');

// IP 기반 국가코드 조회
$country = null;
if ($ip_address) {
    $ch = curl_init("http://ip-api.com/json/{$ip_address}?fields=countryCode");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
    ]);
    $geoData = curl_exec($ch);
    curl_close($ch);
    if ($geoData) {
        $country = json_decode($geoData, true)['countryCode'] ?? null;
    }
}

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => '필수값 누락'], JSON_UNESCAPED_UNICODE);
    exit;
}

// DB 저장
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    $sql = "INSERT INTO consultations (name, phone, content, utm_source, utm_medium, utm_campaign, utm_content, utm_term, fbclid, ip_address, user_agent, country, domain)
            VALUES (:name, :phone, :content, :utm_source, :utm_medium, :utm_campaign, :utm_content, :utm_term, :fbclid, :ip_address, :user_agent, :country, :domain)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $name,
        'phone' => $phone,
        'content' => $content,
        'utm_source' => $utm_source ?: null,
        'utm_medium' => $utm_medium ?: null,
        'utm_campaign' => $utm_campaign ?: null,
        'utm_content' => $utm_content ?: null,
        'utm_term' => $utm_term ?: null,
        'fbclid' => $fbclid ?: null,
        'ip_address' => $ip_address ?: null,
        'user_agent' => $user_agent ?: null,
        'country' => $country,
        'domain' => $domain ?: null
    ]);

    echo json_encode(['success' => true, 'message' => '저장 완료', 'data' => ['id' => $pdo->lastInsertId()]], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log("Receive API DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => '저장 실패'], JSON_UNESCAPED_UNICODE);
}
