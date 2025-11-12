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
('최근 업무사례', '송동민 변호사, 중소기업 M&A 자문 성공적 완료', '파노 법률사무소 송동민 변호사는 최근 국내 중소기업의 M&A 거래에서 매수 측 법률 자문을 성공적으로 완료했습니다. 이번 거래는 IT 서비스 분야 기업 간 인수합병으로, 실사부터 계약 체결까지 전 과정에 걸쳐 법률 자문을 제공했습니다.\n\n송동민 변호사는 "기업 인수합병 과정에서 발생할 수 있는 각종 법률 리스크를 사전에 검토하고, 고객사의 이익을 최대한 보호할 수 있는 계약 조건을 도출했다"고 밝혔습니다.\n\n파노 법률사무소는 M&A, 기업 구조조정 등 기업법무 분야에서 축적된 전문성을 바탕으로 고객에게 최적의 법률 서비스를 제공하고 있습니다.', '파노 법률사무소 송동민 변호사가 중소기업 M&A 거래에서 매수 측 법률 자문을 성공적으로 완료하며, 실사부터 계약 체결까지 전 과정을 지원했습니다.', '2024-09-15'),
('최근 업무사례', '파노 법률사무소, 스타트업 투자 계약 자문 수행', '파노 법률사무소는 최근 유망 스타트업의 시리즈 A 투자 유치 과정에서 법률 자문을 제공했습니다. 송동민 변호사를 중심으로 한 자문팀은 투자계약서 검토, 주주간 계약 작성, 지식재산권 실사 등 투자 전반에 걸친 법률 서비스를 제공했습니다.\n\n이번 자문을 통해 투자자와 스타트업 모두가 만족하는 합리적인 계약 조건을 도출했으며, 향후 발생할 수 있는 분쟁 가능성을 최소화했습니다.\n\n파노 법률사무소는 스타트업 생태계에 대한 깊은 이해를 바탕으로 창업 초기 단계부터 성장 단계까지 필요한 법률 서비스를 제공하고 있습니다.', '파노 법률사무소가 유망 스타트업의 시리즈 A 투자 유치 과정에서 투자계약서 검토, 주주간 계약 작성 등 포괄적인 법률 자문을 제공했습니다.', '2024-07-22'),
('언론보도', '송동민 변호사, "계약서 작성 시 분쟁 예방 조항 필수"', '파노 법률사무소 송동민 변호사는 최근 인터뷰에서 "기업 간 거래에서 계약서 작성이 가장 중요하다"며 "특히 분쟁 발생 시 해결 방법을 미리 명시해 두는 것이 분쟁 예방의 핵심"이라고 강조했습니다.\n\n송 변호사는 "많은 기업들이 계약서를 형식적으로 작성하는 경우가 많은데, 실제 분쟁이 발생하면 계약서 내용이 결정적 증거가 된다"며 "전문가의 검토를 받아 정확하고 명확한 계약서를 작성하는 것이 중요하다"고 조언했습니다.\n\n파노 법률사무소는 각종 상업 계약서 작성 및 검토 서비스를 제공하고 있습니다.', '송동민 변호사가 기업 간 거래에서 계약서 작성의 중요성을 강조하며, 분쟁 예방을 위해 전문가 검토가 필수적이라고 조언했습니다.', '2024-06-10'),
('언론보도', '파노 법률사무소, 기업 법률 자문 전문 로펌으로 주목', '파노 법률사무소가 기업법무, 민사소송, 계약법 분야에서 전문성을 인정받으며 주목받고 있습니다. 송동민 변호사를 필두로 한 전문 변호사들이 중소기업부터 대기업까지 다양한 규모의 기업에 맞춤형 법률 서비스를 제공하고 있습니다.\n\n특히 계약서 작성 및 검토, 기업 간 분쟁 해결, 노무 관리, 지식재산권 보호 등 기업 운영 전반에 필요한 법률 자문을 원스톱으로 제공하는 것이 강점입니다.\n\n파노 법률사무소 관계자는 "고객의 입장에서 생각하고, 실질적인 해결책을 제시하는 것을 최우선 가치로 삼고 있다"고 밝혔습니다.', '파노 법률사무소가 기업법무 분야에서 전문성을 인정받으며, 계약서 검토부터 분쟁 해결까지 원스톱 법률 서비스를 제공하고 있습니다.', '2024-05-03');
