#!/usr/bin/env bash
# Project: Teznevise WordPress Theme | Author: MAZ//ID (Maziyar)
# Brand: MΛZ — https://github.com/maziyarid/M-Z
set -euo pipefail
script_dir=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)
export TZGSC_EXPECTED_BATCH=project
exec "$script_dir/gsc-rollback.sh" "$@"
