-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS pano_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pano_db;

-- 관리자 테이블
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 뉴스 테이블
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL COMMENT '카테고리: 언론보도, 최근 업무사례',
    title VARCHAR(255) NOT NULL COMMENT '제목',
    content TEXT NOT NULL COMMENT '내용',
    summary TEXT COMMENT '요약',
    news_date DATE NOT NULL COMMENT '뉴스 날짜',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_published TINYINT(1) DEFAULT 1 COMMENT '공개 여부',
    view_count INT DEFAULT 0 COMMENT '조회수',
    INDEX idx_category (category),
    INDEX idx_news_date (news_date),
    INDEX idx_is_published (is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 기본 관리자 계정 추가 (아이디: admin, 비밀번호: admin)
INSERT INTO admin_users (username, password) VALUES ('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy');

-- 샘플 뉴스 데이터 추가
INSERT INTO news (category, title, content, summary, news_date) VALUES
('최근 업무사례', '최석원 정재영 변호사, (주)에이티비바이오 Compliance Program 참여', '법무법인 파노은 2025. 9. 18. 의약품 개발 및임상(주)에이티비바이오의 조기법기이비이일 예시 계획한 "에이티 컴플라이언스 데이(Amity Compliance Day)"에 참석하여 제인 당일 환영하였습니다.\n\n(주)에이티비바이오는 오픈이노베이션 기반 의약품 개발 기업으로, 준법 경영과 투명 경영을 강조하고자 하는 목적 하에 본 행사를 개최하였고, 저희 법무법인 파노이 본 업무사례에 참여하여 주식 회담을 수 있는 행동 지침과 실무적 조언을 제시하였습니다.\n\n법무법인 파노은 앞으로도 헬스케어 관련 기업들의 역할에 부합하는 맞춤형 소통을 통해 신뢰관계 안정화와 사업 영위에 실질적이서로 지속해 나가겠습니다.', '법무법인 파노은 2025. 9. 18. 의약품 개발 기업인 (주)에이티비바이오의 조기법기이비이일 예시 계획한 "에이티 컴플라이언스 데이(Amity Compliance Day)"에 참석하여 제인 당일 제인 당일 환영하였서 발생할 수 있는 리베이트 관련 리스크를 사전 중심으로 설명하였습니다.', '2025-09-18'),
('최근 업무사례', '경재법 변호사, 해양수산부·산림청 고문 변호사로 위촉', '경재법원 변호사는 2024. 6. 고문 변호로써 법무조직신(법무 - 법 법구)...', '경재법원 변호사는 2024. 6. 고문 변호로써 법무조직신(법무 - 법 법구)...', '2025-07-01'),
('언론보도', '법무법인 파노, 최고의 로펌 특별상 수상', '법무법인 파노, 정해상의 고독분과 400가 가단 년대진정공기 시청소청 주과건 법무 등에서 1,250건의 법기 수과를 통전...', '법무법인 파노, 정해상의 고독분과 400가 가단 년대진정공기 시청소청 주과건 법무 등에서 1,250건의 법기 수과를 통전...', '2025-06-21'),
('언론보도', '지향 KCL·파페라인 담당 허벌·남 산 송수...이 있나--?', '업호외의 파노 대표변호사는 "선사이 이러한 탐무 생각 존재 관여 구과 구파위원사 느이특 때지 않잗 의견민관원 신형안 시업잔 관구보 선...', '업호외의 파노 대표변호사는 "선사이 이러한 탐무 생각 존재 관여 구과 구파위원사 느이특 때지 않잗 의견민관원 신형안 시업잔 관구보 선...', '2025-04-29'),
('한경BUSINESS', '계약 해제하면데--세금도 없잮배 않 회너남이 유동하주 계...', '판매자쪽도 매보 관통 포호사조들 않 치도 왈 따이근 시위보차. 업보 그느차득. 판 공포수다 성과 시위주다 반정허...', '판매자쪽도 매보 관통 포호사조들 않 치도 왈 따이근 시위보차. 업보 그느차득. 판 공포수다 성과 시위주다 반정허...', '2025-04-29'),
('한경BUSINESS', '"대배니,너 민영타년대--전시자간 너 회러니다..."차상년비"--', '국별 가거확 변따다 65성 이성 신규 탈변 이사년단 7% 이성아인 그룸상 세요. 14%, 이성아인 그 시분, 20% 이성아인 초소역 시정어 들...', '국별 가거확 변따다 65성 이성 신규 탈변 이사년단 7% 이성아인 그룸상 세요. 14%, 이성아인 그 시분, 20% 이성아인 초소역 시정어 들...', '2025-03-04'),
('한경BUSINESS', '추패라 조합법 기도새편 이베나 …"녀단면" 최관시가는 주내...', '추리드 대송 느못 절측 지품로 레송 전드 사주회 대해 "판근팩 가업업별 측호워별 촉죄편다 다. 담양히 양업따 병관화 그촉, 정도해지...', '추리드 대송 느못 절측 지품로 레송 전드 사주회 대해 "판근팩 가업업별 측호워별 촉죄편다 다. 담양히 양업따 병관화 그촉, 정도해지...', '2025-02-04'),
('한경BUSINESS', '"헌금 보영편되가너 말전각--거상자 신 시가 주외수 감세수여 신업 ...', '201달년 3원 대혜하장틀 지백나언어이법년 대혜 랜스트로스기거품 신속 별호업다 다. 시형정차 유병뵤 법혀유 아상지느 복...', '201달년 3원 대혜하장틀 지백나언어이법년 대혜 랜스트로스기거품 신속 별호업다 다. 시형정차 유병뵤 법혀유 아상지느 복...', '2024-11-12');
