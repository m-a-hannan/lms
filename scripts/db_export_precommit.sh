#!/usr/bin/env bash
set -euo pipefail

# -----------------------------
# Config (you can edit safely)
# -----------------------------
TZ_NAME="Asia/Dhaka"
EXPORT_DIR="/home/hannan/db-export"
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

# Load .env safely (supports quoted values + special characters)
load_env() {
  [[ -f "$ENV_FILE" ]] || { err ".env not found at: $ENV_FILE"; exit 1; }

  while IFS= read -r line || [[ -n "$line" ]]; do
    # Strip CR (Windows line endings)
    line="${line%$'\r'}"
    # Trim leading/trailing whitespace
    line="${line#"${line%%[![:space:]]*}"}"
    line="${line%"${line##*[![:space:]]}"}"

    [[ -z "$line" ]] && continue
    [[ "$line" == \#* || "$line" == \;* ]] && continue

    # Allow optional "export " prefix
    if [[ "$line" == export\ * ]]; then
      line="${line#export }"
      line="${line#"${line%%[![:space:]]*}"}"
    fi

    [[ "$line" =~ ^[A-Za-z_][A-Za-z0-9_]*= ]] || continue
    key="${line%%=*}"
    val="${line#*=}"
    # Trim whitespace around value (keep inner spaces and special chars)
    val="${val#"${val%%[![:space:]]*}"}"
    val="${val%"${val##*[![:space:]]}"}"

    # Strip surrounding quotes if present
    if [[ ( "$val" == \"*\" && "$val" == *\" ) || ( "$val" == \'*\' && "$val" == *\' ) ]]; then
      quote="${val:0:1}"
      val="${val:1:-1}"
      if [[ "$quote" == '"' ]]; then
        val="${val//\\\"/\"}"
        val="${val//\\\\/\\}"
      fi
    fi

    printf -v "$key" '%s' "$val"
    export "$key"
  done < "$ENV_FILE"
}

# Find mysqldump/mysql (XAMPP or system)
detect_mysql_bins() {
  local candidates=()

  if [[ -x "/opt/lampp/bin/mysql" && -x "/opt/lampp/bin/mysqldump" ]]; then
    candidates+=("/opt/lampp/bin")
  fi

  if command -v mysql >/dev/null 2>&1 && command -v mysqldump >/dev/null 2>&1; then
    candidates+=("system")
  fi

  if [[ ${#candidates[@]} -eq 0 ]]; then
    require_cmd mysql
    require_cmd mysqldump
  fi

  for candidate in "${candidates[@]}"; do
    if [[ "$candidate" == "system" ]]; then
      MYSQL="$(command -v mysql)"
      MYSQLDUMP="$(command -v mysqldump)"
    else
      MYSQL="$candidate/mysql"
      MYSQLDUMP="$candidate/mysqldump"
    fi

    # Quick connection test to pick the right binary pair.
    if MYSQL_PWD="$DB_PASS" "$MYSQL" "${MYSQL_ARGS[@]}" -e "SELECT 1;" >/dev/null 2>&1; then
      return 0
    fi
  done
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

  # Prefer local socket when available (supports XAMPP/MariaDB).
  if [[ -z "${DB_SOCKET:-}" ]] && [[ "$DB_HOST" == "localhost" || "$DB_HOST" == "127.0.0.1" ]]; then
    if [[ -S "/opt/lampp/var/mysql/mysql.sock" ]]; then
      DB_SOCKET="/opt/lampp/var/mysql/mysql.sock"
    fi
  fi

  MYSQL_ARGS=()
  if [[ -n "${DB_SOCKET:-}" ]]; then
    MYSQL_ARGS+=(--protocol=socket --socket="$DB_SOCKET")
  else
    MYSQL_ARGS+=(-h"$DB_HOST")
    if [[ -n "${DB_PORT:-}" ]]; then
      MYSQL_ARGS+=(-P"$DB_PORT")
    fi
  fi
  MYSQL_ARGS+=(-u"$DB_USER")

  detect_mysql_bins

  mkdir -p "$EXPORT_DIR" "$PROJECT_DB_DIR"

  TS="$(TZ="$TZ_NAME" date +"%Y%m%d-%H%M%S")"
  DUMP_NAME="lms_db-backup-${TS}.sql"
  DUMP_PATH="${EXPORT_DIR}/${DUMP_NAME}"

  info "Exporting full DB (schema + data) to: $DUMP_PATH"

  # Quick connection test first (so we fail fast with a clear message)
  if ! MYSQL_PWD="$DB_PASS" "$MYSQL" "${MYSQL_ARGS[@]}" -e "SELECT 1;" >/dev/null 2>&1; then
    err "Cannot connect to MySQL using .env credentials (DB_HOST/DB_USER/DB_PASS). Commit blocked."
    exit 1
  fi

  # Full dump includes schema + data + routines/triggers/events
  # --databases adds CREATE DATABASE and USE, helpful for full recreate on server
  # --add-drop-database/table makes re-import safe
  GTID_FLAG=()
  if "$MYSQLDUMP" --help 2>/dev/null | grep -q "set-gtid-purged"; then
    GTID_FLAG=(--set-gtid-purged=OFF)
  fi
  DEFINER_FLAG=()
  if "$MYSQLDUMP" --help 2>/dev/null | grep -q "skip-definer"; then
    DEFINER_FLAG=(--skip-definer)
  fi

  if ! MYSQL_PWD="$DB_PASS" "$MYSQLDUMP" \
      "${MYSQL_ARGS[@]}" \
      --databases "$DB_NAME" \
      --single-transaction \
      --triggers \
      --add-drop-database --add-drop-table \
      "${GTID_FLAG[@]}" \
      "${DEFINER_FLAG[@]}" \
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

  info "Removing older DB exports from repo DB/..."
  find "$PROJECT_DB_DIR" -maxdepth 1 -type f -name "lms_db-backup-*.sql" ! -name "$DUMP_NAME" -print -delete || true

  # Optional convenience pointer file
  info "Updating DB/LATEST_DB_EXPORT.txt"
  echo "$DUMP_NAME" > "${PROJECT_DB_DIR}/LATEST_DB_EXPORT.txt"

  info "Done. Commit can proceed."
}

main "$@"
