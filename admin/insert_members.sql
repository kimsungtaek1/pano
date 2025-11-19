-- 구성원 데이터 삽입

-- 1. 강홍구 세무사
INSERT INTO members (name, position, display_order, is_active)
VALUES ('강홍구', '세무사', 1, 1);
SET @member_id1 = LAST_INSERT_ID();

INSERT INTO member_careers (member_id, career, display_order) VALUES
(@member_id1, '한국세무사회 법제위원', 1),
(@member_id1, '한국세무사고시회 상임이사', 2),
(@member_id1, '세무법인 엑스퍼트 성수점 대표세무사', 3);

-- 2. 이혁호 응급의학과 전문의
INSERT INTO members (name, position, display_order, is_active)
VALUES ('이혁호', '응급의학과 전문의', 2, 1);
SET @member_id2 = LAST_INSERT_ID();

INSERT INTO member_careers (member_id, career, display_order) VALUES
(@member_id2, '대한응급의학회 정회원', 1),
(@member_id2, '대한재난의학회 정회원', 2),
(@member_id2, '인천힘찬종합병원 응급의학과 과장', 3);

-- 3. 최근석 마취통증의학과 전문의
INSERT INTO members (name, position, display_order, is_active)
VALUES ('최근석', '마취통증의학과 전문의', 3, 1);
SET @member_id3 = LAST_INSERT_ID();

INSERT INTO member_careers (member_id, career, display_order) VALUES
(@member_id3, '대한통증학회 정회원', 1),
(@member_id3, '전) 충남대학교 통증클리닉 임상교수', 2),
(@member_id3, '코끼리통증의학과 대표원장', 3);

-- 4. 박영수 통합치의학과 전문의
INSERT INTO members (name, position, display_order, is_active)
VALUES ('박영수', '통합치의학과 전문의', 4, 1);
SET @member_id4 = LAST_INSERT_ID();

INSERT INTO member_careers (member_id, career, display_order) VALUES
(@member_id4, '대한턱관절교합학회 정회원', 1),
(@member_id4, '국제임플란트학회 정회원', 2),
(@member_id4, '착한미소치과 대표원장', 3);

-- 5. 문용진 한의사
INSERT INTO members (name, position, display_order, is_active)
VALUES ('문용진', '한의사', 5, 1);
SET @member_id5 = LAST_INSERT_ID();

INSERT INTO member_careers (member_id, career, display_order) VALUES
(@member_id5, '더불어민주당 보건의료특별위원회 부위원장', 1),
(@member_id5, '법무부 청소년범죄예방위원 목포지역 협의회 부회장', 2),
(@member_id5, '부부요양병원 대표원장', 3);
