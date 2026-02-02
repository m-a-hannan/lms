#!/usr/bin/env bash
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

git config core.hooksPath .githooks
echo "[OK] Git hooks path set to .githooks"
echo "[OK] pre-commit hook will run DB export before every commit."
