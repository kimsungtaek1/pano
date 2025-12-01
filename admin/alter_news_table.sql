-- news 테이블에 유형(case_type)과 소제목(subtitle) 컬럼 추가
-- 실행 방법: phpMyAdmin에서 이 SQL을 실행하거나,
-- 카페24 호스팅 관리자 > DB 관리에서 실행

ALTER TABLE news ADD COLUMN case_type VARCHAR(50) DEFAULT NULL AFTER summary;
ALTER TABLE news ADD COLUMN subtitle VARCHAR(255) DEFAULT NULL AFTER case_type;

-- 확인용: 테이블 구조 조회
-- DESCRIBE news;
