#!/usr/bin/env bash
# The repository-owned copy entry point; the version check precedes rsync.
set -euo pipefail
sync_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
sync_target="${1:?Installed theme directory is required}"
python3 "$sync_root/scripts/seo-20261110/check_deploy_version.py" \
  --candidate "$sync_root/style.css" --installed "$sync_target/style.css"
exec /bin/rsync -avz --exclude '.git' "$sync_root/" "$sync_target/"
