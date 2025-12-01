-- news 테이블에 유형(case_type)과 소제목(subtitle) 컬럼 추가
-- 실행 방법: phpMyAdmin에서 이 SQL을 실행하거나,
-- 카페24 호스팅 관리자 > DB 관리에서 실행

ALTER TABLE news ADD COLUMN case_type VARCHAR(50) DEFAULT NULL AFTER summary;
ALTER TABLE news ADD COLUMN subtitle VARCHAR(255) DEFAULT NULL AFTER case_type;

-- 확인용: 테이블 구조 조회
-- DESCRIBE news;

-- ============================================
-- 2024-12 카테고리명 변경 및 view_count 추가
-- ============================================

-- 1. 카테고리명 변경: '최근 업무사례' -> '파노 성공사례'
UPDATE news SET category = '파노 성공사례' WHERE category = '최근 업무사례';

-- 2. 한경BUSINESS 카테고리를 언론보도로 변경 (또는 삭제하려면 아래 주석 해제)
UPDATE news SET category = '언론보도' WHERE category = '한경BUSINESS';
-- DELETE FROM news WHERE category = '한경BUSINESS';

-- 3. 조회수(view_count) 컬럼 추가
ALTER TABLE news ADD COLUMN view_count INT DEFAULT 0 AFTER is_published;

-- 확인용: 카테고리별 개수 조회
-- SELECT category, COUNT(*) as cnt FROM news GROUP BY category;
