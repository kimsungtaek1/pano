#!/bin/bash

# .env 파일 로드
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
else
    echo "Error: .env 파일이 없습니다."
    exit 1
fi

echo "=========================================="
echo "카페24 FTP 자동 배포 시작"
echo "=========================================="

# 제외 목록 생성
# uploads/ 폴더는 서버에서 직접 업로드되므로 동기화에서 제외 (삭제 방지)
EXCLUDE_OPTS="--exclude .git/ --exclude .env --exclude deploy.sh --exclude README.md --exclude .gitignore --exclude database/ --exclude uploads/"

# 한글이 포함된 모든 파일 및 디렉토리 제외
echo "한글 파일/디렉토리 제외 중..."
EXCLUDE_OPTS="$EXCLUDE_OPTS --exclude-glob '*[가-힣]*'"
echo "⚠️  한글이 포함된 모든 파일 및 디렉토리는 제외됩니다."

echo "업로드 시작..."

# FTP 서버에 파일 업로드
lftp -e "
set ssl:verify-certificate no
open ftp://${FTP_USERNAME}:${FTP_PASSWORD}@${FTP_HOST}
mirror --reverse --delete --verbose $EXCLUDE_OPTS ./ ${FTP_REMOTE_DIR}
bye
"

if [ $? -eq 0 ]; then
    echo "=========================================="
    echo "✅ 배포 완료!"
    echo "=========================================="
else
    echo "=========================================="
    echo "❌ 배포 실패!"
    echo "=========================================="
    exit 1
fi
