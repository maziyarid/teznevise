#!/usr/bin/env bash
# Project: Teznevise WordPress Theme | Author: MAZ//ID (Maziyar)
# Brand: MΛZ — https://github.com/maziyarid/M-Z
set -euo pipefail
script_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)
if [[ $# -lt 2 || $# -gt 3 ]]; then
  echo "Usage: $0 /absolute/wordpress/root /absolute/private/backup-dir [--dry-run]" >&2; exit 2
fi
[[ "$1" = /* && "$2" = /* ]] || { echo 'Both paths must be absolute.' >&2; exit 2; }
mode=rollback
if [[ ${3:-} == --dry-run ]]; then mode=rollback-dry-run; elif [[ $# == 3 ]]; then echo 'Unknown argument.' >&2; exit 2; fi
expected_batch=${TZGSC_EXPECTED_BATCH:-from-backup}
exec wp --path="$1" --skip-plugins --skip-themes eval-file "$script_dir/gsc.php" "$mode" "$script_dir/../../seo/20261110" "$2" "$expected_batch"
