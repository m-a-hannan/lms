#!/usr/bin/env bash
set -euo pipefail

# -----------------------------
# Config (you can edit safely)
# -----------------------------
TZ_NAME="Asia/Dhaka"
EXPORT_DIR="/home/db-export"
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PROJECT_DB_DIR="${PROJECT_ROOT}/DB"
ENV_FILE="${PROJECT_ROOT}/.env"

# -----------------------------
# Helpers
# -----------------------------
err() { echo "[ERROR] $*" >&2; }
info() { echo "[INFO]  $*"; }

require_cmd() {
  command -v "$1" >/dev/null 2>&1 || { err "Missing command: $1"; exit 1; }
}

# Load .env safely (expects simple KEY=VALUE lines)
load_env() {
  [[ -f "$ENV_FILE" ]] || { err ".env not found at: $ENV_FILE"; exit 1; }

  # shellcheck disable=SC2046
  export $(grep -E '^[A-Za-z_][A-Za-z0-9_]*=' "$ENV_FILE" | sed 's/\r$//' | xargs) || true
}

# Find mysqldump/mysql (XAMPP or system)
detect_mysql_bins() {
  if [[ -x "/opt/lampp/bin/mysqldump" && -x "/opt/lampp/bin/mysql" ]]; then
    MYSQLDUMP="/opt/lampp/bin/mysqldump"
    MYSQL="/opt/lampp/bin/mysql"
  else
    require_cmd mysqldump
    require_cmd mysql
    MYSQLDUMP="$(command -v mysqldump)"
    MYSQL="$(command -v mysql)"
  fi
}

# -----------------------------
# Main
# -----------------------------
main() {
  info "Loading DB credentials from .env..."
  load_env

  : "${DB_HOST:?Missing DB_HOST in .env}"
  : "${DB_USER:?Missing DB_USER in .env}"
  : "${DB_PASS:?Missing DB_PASS in .env}"
  : "${DB_NAME:?Missing DB_NAME in .env}"

  detect_mysql_bins

  mkdir -p "$EXPORT_DIR" "$PROJECT_DB_DIR"

  TS="$(TZ="$TZ_NAME" date +"%Y%m%d-%H%M%S")"
  DUMP_NAME="lms_db-backup-${TS}.sql"
  DUMP_PATH="${EXPORT_DIR}/${DUMP_NAME}"

  info "Exporting full DB (schema + data) to: $DUMP_PATH"

  # Quick connection test first (so we fail fast with a clear message)
  if ! "$MYSQL" -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "SELECT 1;" >/dev/null 2>&1; then
    err "Cannot connect to MySQL using .env credentials (DB_HOST/DB_USER/DB_PASS). Commit blocked."
    exit 1
  fi

  # Full dump includes schema + data + routines/triggers/events
  # --databases adds CREATE DATABASE and USE, helpful for full recreate on server
  # --add-drop-database/table makes re-import safe
  if ! "$MYSQLDUMP" \
      -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" \
      --databases "$DB_NAME" \
      --single-transaction \
      --routines --triggers --events \
      --add-drop-database --add-drop-table \
      --set-gtid-purged=OFF \
      > "$DUMP_PATH" 2> "${DUMP_PATH}.err"
  then
    err "mysqldump failed. Commit blocked."
    err "---- mysqldump error output ----"
    sed -n '1,200p' "${DUMP_PATH}.err" >&2 || true
    exit 1
  fi
  rm -f "${DUMP_PATH}.err" || true

  info "Dump created successfully."

  # Copy latest dump into repo DB/ so it will be committed and deployed
  LATEST_IN_REPO="${PROJECT_DB_DIR}/${DUMP_NAME}"
  info "Copying latest dump into repo: $LATEST_IN_REPO"
  cp -f "$DUMP_PATH" "$LATEST_IN_REPO"

  # Optional convenience pointer file
  info "Updating DB/LATEST_DB_EXPORT.txt"
  echo "$DUMP_NAME" > "${PROJECT_DB_DIR}/LATEST_DB_EXPORT.txt"

  info "Done. Commit can proceed."
}

main "$@"
