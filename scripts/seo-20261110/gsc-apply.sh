#!/usr/bin/env bash
# Project: Teznevise WordPress Theme | Author: MAZ//ID (Maziyar)
# Brand: MΛZ — https://github.com/maziyarid/M-Z
set -euo pipefail
script_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)
if [[ $# -lt 3 || $# -gt 4 ]]; then
  echo "Usage: $0 /absolute/wordpress/root /absolute/private/backup-dir --batch=redirects|gone|indexing|links [--dry-run]" >&2; exit 2
fi
[[ "$1" = /* && "$2" = /* && "$3" = --batch=* ]] || { echo 'Use absolute paths and an explicit reviewed batch.' >&2; exit 2; }
batch=${3#--batch=}
mode=apply
if [[ ${4:-} == --dry-run ]]; then mode=apply-dry-run; elif [[ $# == 4 ]]; then echo 'Unknown argument.' >&2; exit 2; fi
exec wp --path="$1" --skip-plugins --skip-themes eval-file "$script_dir/gsc.php" "$mode" "$script_dir/../../seo/20261110" "$2" "$batch"
