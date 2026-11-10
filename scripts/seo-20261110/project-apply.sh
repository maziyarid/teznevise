#!/usr/bin/env bash
# Project: Teznevise WordPress Theme | Author: MAZ//ID (Maziyar)
# Brand: MΛZ — https://github.com/maziyarid/M-Z
set -euo pipefail
script_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)
if [[ $# -lt 2 || $# -gt 3 ]]; then
  echo "Usage: $0 /absolute/wordpress/root /absolute/private/project-backup-dir [--dry-run]" >&2; exit 2
fi
if [[ $# == 3 ]]; then
  [[ "$3" == --dry-run ]] || { echo 'Unknown argument.' >&2; exit 2; }
  exec "$script_dir/gsc-apply.sh" "$1" "$2" --batch=project --dry-run
fi
exec "$script_dir/gsc-apply.sh" "$1" "$2" --batch=project
