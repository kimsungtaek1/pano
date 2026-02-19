#!/bin/bash

# .env 파일 로드
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
else
    echo "Error: .env 파일이 없습니다."
    exit 1
fi

SCP_OPTS="-o StrictHostKeyChecking=no"
REMOTE="${FTP_USERNAME}@${FTP_HOST}"

echo "=========================================="
echo "카페24 SCP 자동 배포 시작"
echo "=========================================="

echo "업로드 시작..."

# 업로드 대상 파일 목록 생성 (제외 항목 적용)
file_list=$(find . -type f \
    -not -path './.git/*' \
    -not -path './.git' \
    -not -name '.env' \
    -not -name 'deploy.sh' \
    -not -name 'README.md' \
    -not -name '.gitignore' \
    -not -path './database/*' \
    -not -path './uploads/*' \
    -not -name 'CLAUDE.md' \
    | grep -v '[가-힣]')

# 필요한 디렉토리 먼저 생성
dirs=$(echo "$file_list" | xargs -I{} dirname {} | sort -u | sed 's|^\./||')
if [ -n "$dirs" ]; then
    dir_cmds=""
    for d in $dirs; do
        dir_cmds="${dir_cmds}mkdir -p www/$d;"
    done
    sshpass -p "${FTP_PASSWORD}" ssh $SCP_OPTS "$REMOTE" "$dir_cmds" 2>/dev/null
fi

# 파일 업로드
echo "$file_list" | while read -r file; do
    rel="${file#./}"
    sshpass -p "${FTP_PASSWORD}" scp $SCP_OPTS "$file" "${REMOTE}:www/$rel" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "  ✓ $rel"
    else
        echo "  ✗ $rel"
    fi
done

echo "=========================================="
echo "✅ 배포 완료!"
echo "=========================================="
