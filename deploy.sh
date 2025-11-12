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

# FTP 서버에 파일 업로드
lftp -e "
set ssl:verify-certificate no
open ftp://${FTP_USERNAME}:${FTP_PASSWORD}@${FTP_HOST}
mirror --reverse --delete --verbose --exclude .git/ --exclude .env --exclude deploy.sh --exclude README.md --exclude .gitignore --exclude database/ --exclude images/ --exclude-glob '*.png' --exclude-glob '*.jpg' --exclude-glob '*.jpeg' --exclude-glob '*.gif' --exclude-glob '*.svg' --exclude-glob '*.webp' ./ ${FTP_REMOTE_DIR}
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
