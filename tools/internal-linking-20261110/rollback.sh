#!/usr/bin/env bash
set -euo pipefail
if (( $# < 2 )); then
  echo 'Usage: rollback.sh WORDPRESS_ROOT PRIVATE_BACKUP_DIR [--apply]' >&2
  exit 2
fi
wp_root=$1
backup_dir=$2
shift 2
script_dir=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
wp --path="$wp_root" eval-file "$script_dir/wordpress.php" rollback "$backup_dir" "$@"
