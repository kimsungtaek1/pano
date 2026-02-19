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

# 제외 목록
EXCLUDES=".env deploy.sh README.md .gitignore CLAUDE.md"

should_exclude() {
    local file="$1"
    for ex in $EXCLUDES; do
        if [ "$file" = "$ex" ]; then
            return 0
        fi
    done
    if [[ "$file" == database/* ]] || [[ "$file" == uploads/* ]]; then
        return 0
    fi
    if [[ "$file" =~ [가-힣] ]]; then
        return 0
    fi
    return 1
}

# --all 옵션: 전체 파일 배포
if [ "$1" = "--all" ]; then
    echo "전체 파일 업로드 중..."
    changed_files=$(find . -type f \
        -not -path './.git/*' \
        -not -name '.git' \
        | sed 's|^\./||')
else
    # 최근 커밋에서 변경된 파일만
    changed_files=$(git diff --name-only HEAD~1 HEAD 2>/dev/null)
fi

if [ -z "$changed_files" ]; then
    echo "변경된 파일이 없습니다."
    exit 0
fi

echo "변경된 파일 업로드 중..."

echo "$changed_files" | while read -r file; do
    if should_exclude "$file"; then
        continue
    fi

    # 삭제된 파일은 건너뛰기
    if [ ! -f "$file" ]; then
        echo "  - $file (삭제됨, 건너뜀)"
        continue
    fi

    # 원격 디렉토리 생성
    remote_dir=$(dirname "www/$file")
    sshpass -p "${FTP_PASSWORD}" ssh $SCP_OPTS "$REMOTE" "mkdir -p $remote_dir" 2>/dev/null

    # 파일 업로드
    sshpass -p "${FTP_PASSWORD}" scp $SCP_OPTS "$file" "${REMOTE}:www/$file" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "  ✓ $file"
    else
        echo "  ✗ $file"
    fi
done

echo "=========================================="
echo "✅ 배포 완료!"
echo "=========================================="
