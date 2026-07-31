#!/bin/bash

set -euo pipefail

# .env 파일 로드
if [ -f .env ]; then
    set -a
    . ./.env
    set +a
else
    echo "Error: .env 파일이 없습니다."
    exit 1
fi

FTP_HOST="${FTP_HOST:-}"
FTP_USERNAME="${FTP_USERNAME:-}"
FTP_PASSWORD="${FTP_PASSWORD:-}"
REMOTE_DIR="${FTP_REMOTE_DIR:-/www}"

if [ -z "$FTP_HOST" ] || [ -z "$FTP_USERNAME" ] || [ -z "$FTP_PASSWORD" ]; then
    echo "Error: FTP_HOST/FTP_USERNAME/FTP_PASSWORD 값이 .env에 없습니다."
    exit 1
fi

if ! command -v lftp >/dev/null 2>&1; then
    echo "Error: lftp가 설치되어 있지 않습니다."
    exit 1
fi

EXCLUDES=".env .pano-config.php deploy.sh README.md .gitignore CLAUDE.md .DS_Store"

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
    if [[ "$file" == .agent/* ]] || [[ "$file" == .github/* ]] || [[ "$file" == .serena/* ]]; then
        return 0
    fi
    if [ "$file" = "codex_write_test.txt" ] || [ "$file" = "database.zip" ]; then
        return 0
    fi
    if [[ "$file" =~ [가-힣] ]]; then
        return 0
    fi
    return 1
}

normalize_remote_dir() {
    local dir="$1"
    dir="${dir%/}"
    if [ -z "$dir" ]; then
        dir="/"
    fi
    dir=$(echo "$dir" | sed 's#/\{2,\}#/#g')
    echo "$dir"
}

# 기본은 최신 커밋 변경 파일만 업로드 (post-commit 훅용)
if [ "${1:-}" = "--all" ]; then
    # 전체 파일 배포(필요할 때만)
    echo "전체 파일 업로드 중..."
    changed_files=$(find . -type f \
        -not -path './.git/*' \
        -not -name '.git' \
        | sed 's|^\./||')
elif [ "${1:-}" = "--worktree" ]; then
    # 작업트리 + 스테이징 포함 변경 파일 배포
    echo "작업트리 변경 파일 업로드 중..."
    changed_files=$(git diff --name-only HEAD 2>/dev/null)
else
    # 기본: 가장 최근 커밋 변경 파일만 업로드
    changed_files=$(git show --pretty="" --name-only --no-renames HEAD 2>/dev/null)
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

    file_dir=$(dirname "$file")
    if [ "$file_dir" = "." ]; then
        remote_dir="$REMOTE_DIR"
    else
        remote_dir="$REMOTE_DIR/$file_dir"
    fi
    remote_dir=$(normalize_remote_dir "$remote_dir")

    if lftp -u "$FTP_USERNAME,$FTP_PASSWORD" "ftp://$FTP_HOST" \
        -e "set ftp:passive-mode true; set ssl:verify-certificate no; mkdir -p '$remote_dir'; cd '$remote_dir'; put '$file'; bye" >/dev/null 2>&1; then
        echo "  ✓ $file"
    else
        echo "  ✗ $file"
    fi
done

echo "=========================================="
echo "✅ 배포 완료!"
echo "=========================================="
