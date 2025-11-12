#!/bin/bash

# .env 파일 로드
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
else
    echo "Error: .env 파일이 없습니다."
    exit 1
fi

# 다운로드 디렉토리 생성
DOWNLOAD_DIR="./downloaded_images"
mkdir -p "$DOWNLOAD_DIR"

echo "=========================================="
echo "panolaw.com 이미지 다운로드 시작"
echo "=========================================="
echo "서버: ${FTP_HOST}"
echo "저장 위치: ${DOWNLOAD_DIR}"
echo "=========================================="

# FTP 서버에서 이미지 파일들 다운로드
lftp -e "
set ssl:verify-certificate no
open ftp://${FTP_USERNAME}:${FTP_PASSWORD}@${FTP_HOST}
cd ${FTP_REMOTE_DIR}
mget -O ${DOWNLOAD_DIR} *.png *.jpg *.jpeg *.gif *.svg *.ico 2>/dev/null
mirror --only-newer --verbose img/ ${DOWNLOAD_DIR}/img/
mirror --only-newer --verbose wp-content/uploads/ ${DOWNLOAD_DIR}/wp-content/uploads/
bye
"

if [ $? -eq 0 ]; then
    echo "=========================================="
    echo "✅ 이미지 다운로드 완료!"
    echo "=========================================="
    echo "다운로드된 이미지 개수:"
    find "$DOWNLOAD_DIR" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" -o -iname "*.gif" -o -iname "*.webp" -o -iname "*.svg" -o -iname "*.ico" \) | wc -l
else
    echo "=========================================="
    echo "❌ 다운로드 실패!"
    echo "=========================================="
    exit 1
fi
