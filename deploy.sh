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

# tar로 묶어서 한번에 전송 (제외 항목 적용)
TAR_FILE="/tmp/pano_deploy.tar.gz"

echo "파일 압축 중..."
tar czf "$TAR_FILE" \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='deploy.sh' \
    --exclude='README.md' \
    --exclude='.gitignore' \
    --exclude='database' \
    --exclude='uploads' \
    --exclude='CLAUDE.md' \
    .

echo "업로드 중..."
sshpass -p "${FTP_PASSWORD}" scp -o StrictHostKeyChecking=no "$TAR_FILE" "${FTP_USERNAME}@${FTP_HOST}:www/pano_deploy.tar.gz"

echo "서버에서 압축 해제 중..."
sshpass -p "${FTP_PASSWORD}" ssh -o StrictHostKeyChecking=no ${FTP_USERNAME}@${FTP_HOST} "cd www && tar xzf pano_deploy.tar.gz && rm pano_deploy.tar.gz"

# 임시 파일 삭제
rm -f "$TAR_FILE"

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
