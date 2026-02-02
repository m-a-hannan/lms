#!/usr/bin/env bash
set -euo pipefail

TZ_NAME="Asia/Dhaka"
WEBROOT="/var/www/html"
ENV_FILE="${WEBROOT}/.env"
DB_DIR="${WEBROOT}/DB"

DEPLOY_LOG_DIR="/var/log/lms-deploy"
DB_ARCHIVE_DIR="/var/backups/lms-db"
STATUS_DIR="${WEBROOT}/deploy"
STATUS_JSON="${STATUS_DIR}/status.json"

err(){ echo "[ERROR] $*" >&2; }
info(){ echo "[INFO]  $*"; }

mkdir -p "$DEPLOY_LOG_DIR" "$DB_ARCHIVE_DIR" "$STATUS_DIR"

# Load env from server .env (you already preserve it during rsync)
load_env() {
  [[ -f "$ENV_FILE" ]] || { err "Server .env not found at $ENV_FILE"; exit 1; }
  # shellcheck disable=SC2046
  export $(grep -E '^[A-Za-z_][A-Za-z0-9_]*=' "$ENV_FILE" | sed 's/\r$//' | xargs) || true
}

pick_latest_dump() {
  # Prefer your pointer file if exists
  if [[ -f "${DB_DIR}/LATEST_DB_EXPORT.txt" ]]; then
    local name
    name="$(cat "${DB_DIR}/LATEST_DB_EXPORT.txt" | tr -d '\r\n')"
    if [[ -n "$name" && -f "${DB_DIR}/${name}" ]]; then
      echo "${DB_DIR}/${name}"
      return 0
    fi
  fi

  # Fallback: pick newest matching file
  ls -1t "${DB_DIR}"/lms_db-backup-*.sql 2>/dev/null | head -n 1 || true
}

trim_last_15() {
  local dir="$1"
  # delete older than last 15 by mtime
  ls -1t "$dir" 2>/dev/null | tail -n +16 | while read -r old; do
    rm -rf "$dir/$old" || true
  done
}

main() {
  load_env

  : "${DB_HOST:?Missing DB_HOST in server .env}"
  : "${DB_USER:?Missing DB_USER in server .env}"
  : "${DB_PASS:?Missing DB_PASS in server .env}"
  : "${DB_NAME:?Missing DB_NAME in server .env}"

  local dump
  dump="$(pick_latest_dump)"
  [[ -n "${dump:-}" && -f "$dump" ]] || { err "No DB dump found in ${DB_DIR}. Deployment must include DB dump."; exit 1; }

  local dump_file
  dump_file="$(basename "$dump")"

  local sha="${GITHUB_SHA:-unknown}"
  local msg="${GITHUB_COMMIT_MESSAGE:-unknown}"
  local ts
  ts="$(TZ="$TZ_NAME" date -Is)"

  local log_file="${DEPLOY_LOG_DIR}/deploy-${ts//:/-}-${sha}.log"

  {
    echo "time=$ts"
    echo "sha=$sha"
    echo "message=$msg"
    echo "dump=$dump_file"
    echo "db_host=$DB_HOST"
    echo "db_name=$DB_NAME"
    echo "------------------------------"

    info "Archiving dump for this deploy..."
    cp -f "$dump" "${DB_ARCHIVE_DIR}/${ts//:/-}-${sha}-${dump_file}"

    info "Dropping & recreating database: ${DB_NAME}"
    mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS \`${DB_NAME}\`; CREATE DATABASE \`${DB_NAME}\`;" 2>&1

    info "Importing dump..."
    # Strip DEFINER clauses to avoid SUPER/SET_USER_ID requirement on import.
    sed -E 's/\/\*![0-9]+ DEFINER=[^*]*\*\///g; s/DEFINER=`[^`]+`@`[^`]+`//g' "$dump" \
      | mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" 2>&1

    info "Writing status JSON for UI..."
    cat > "$STATUS_JSON" <<EOF
{
  "time": "$(TZ="$TZ_NAME" date -Is)",
  "sha": "$sha",
  "message": "$(echo "$msg" | python3 -c 'import json,sys; print(json.dumps(sys.stdin.read().strip())[1:-1])' 2>/dev/null || echo "$msg")",
  "dump": "$dump_file",
  "result": "success"
}
EOF

    info "Done."
  } | tee -a "$log_file"

  # Keep last 15 logs + last 15 archived dumps
  trim_last_15 "$DEPLOY_LOG_DIR"
  trim_last_15 "$DB_ARCHIVE_DIR"
}

main "$@"
