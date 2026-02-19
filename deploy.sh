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

# 제외 패턴
EXCLUDES=".git .env deploy.sh README.md .gitignore database uploads CLAUDE.md"

should_exclude() {
    local file="$1"
    # 제외 목록 체크
    for ex in $EXCLUDES; do
        if [[ "$file" == "$ex"* ]]; then
            return 0
        fi
    done
    # 한글 포함 체크
    if [[ "$file" =~ [가-힣] ]]; then
        return 0
    fi
    return 1
}

echo "업로드 시작..."

# 변경된 파일 목록 생성 및 업로드
find . -type f | while read -r file; do
    # ./ 제거
    rel="${file#./}"

    if should_exclude "$rel"; then
        continue
    fi

    # 원격 디렉토리 생성 후 파일 업로드
    remote_dir=$(dirname "www/$rel")
    sshpass -p "${FTP_PASSWORD}" ssh -o StrictHostKeyChecking=no ${FTP_USERNAME}@${FTP_HOST} "mkdir -p $remote_dir" 2>/dev/null
    sshpass -p "${FTP_PASSWORD}" scp -o StrictHostKeyChecking=no "$file" "${FTP_USERNAME}@${FTP_HOST}:www/$rel" 2>/dev/null

    if [ $? -eq 0 ]; then
        echo "  ✓ $rel"
    else
        echo "  ✗ $rel (실패)"
    fi
done

echo "=========================================="
echo "✅ 배포 완료!"
echo "=========================================="
