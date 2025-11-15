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

# 한글 파일/디렉토리 찾기 (ASCII가 아닌 문자 포함)
echo "한글 파일/디렉토리 확인 중..."
KOREAN_FILES=$(find . -type f -name '*[가-힣]*' 2>/dev/null | sed 's|^\./||')
KOREAN_DIRS=$(find . -type d -name '*[가-힣]*' 2>/dev/null | sed 's|^\./||')

# 제외 목록 생성
EXCLUDE_OPTS="--exclude .git/ --exclude .env --exclude deploy.sh --exclude README.md --exclude .gitignore --exclude database/"

# 한글 파일 제외 추가
if [ ! -z "$KOREAN_FILES" ]; then
    echo "⚠️  다음 한글 파일들은 제외됩니다:"
    while IFS= read -r file; do
        if [ ! -z "$file" ]; then
            echo "   - $file"
            EXCLUDE_OPTS="$EXCLUDE_OPTS --exclude $(printf '%q' "$file")"
        fi
    done <<< "$KOREAN_FILES"
fi

# 한글 디렉토리 제외 추가
if [ ! -z "$KOREAN_DIRS" ]; then
    echo "⚠️  다음 한글 디렉토리들은 제외됩니다:"
    while IFS= read -r dir; do
        if [ ! -z "$dir" ]; then
            echo "   - $dir/"
            EXCLUDE_OPTS="$EXCLUDE_OPTS --exclude $(printf '%q' "$dir")/"
        fi
    done <<< "$KOREAN_DIRS"
fi

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
