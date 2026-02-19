#!/bin/bash

# .env 파일 로드
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
else
    echo "Error: .env 파일이 없습니다."
    exit 1
fi

echo "=========================================="
echo "카페24 SCP 자동 배포 시작"
echo "=========================================="

# 제외 목록 (rsync용)
EXCLUDE_OPTS="--exclude=.git/ --exclude=.env --exclude=deploy.sh --exclude=README.md --exclude=.gitignore --exclude=database/ --exclude=uploads/ --exclude=CLAUDE.md"

# 한글 파일/디렉토리 제외
echo "한글 파일/디렉토리 제외 중..."
EXCLUDE_OPTS="$EXCLUDE_OPTS --exclude=*[가-힣]*"
echo "⚠️  한글이 포함된 모든 파일 및 디렉토리는 제외됩니다."

echo "업로드 시작..."

# SCP 방식으로 rsync over SSH 업로드
sshpass -p "${FTP_PASSWORD}" rsync -avz --no-perms --no-owner --no-group \
    -e "ssh -o StrictHostKeyChecking=no" \
    $EXCLUDE_OPTS \
    ./ ${FTP_USERNAME}@${FTP_HOST}:www/ 2>&1

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
