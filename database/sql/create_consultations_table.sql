-- 상담신청 테이블 생성
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT '이름',
    phone VARCHAR(20) NOT NULL COMMENT '전화번호',
    email VARCHAR(100) COMMENT '이메일',
    category VARCHAR(50) COMMENT '상담분야',
    content TEXT NOT NULL COMMENT '상담내용',
    status VARCHAR(20) DEFAULT 'pending' COMMENT '상태 (pending: 대기, processed: 처리완료)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '신청일시',
    processed_at TIMESTAMP NULL COMMENT '처리일시',
    admin_memo TEXT COMMENT '관리자 메모',
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='상담신청';
