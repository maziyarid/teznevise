#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

fail=0
check() {
  local label="$1"; shift
  if "$@"; then printf 'PASS  %s\n' "$label"; else printf 'FAIL  %s\n' "$label"; fail=1; fi
}
check_php() { php -l "$1" >/dev/null; }
check_node() { node --check "$1" >/dev/null; }

printf 'Teznevise release checks\nRoot: %s\n\n' "$ROOT"

if command -v php >/dev/null 2>&1; then
  while IFS= read -r -d '' file; do check "PHP syntax: ${file#./}" check_php "$file"; done < <(find . -type f -name '*.php' -not -path './.git/*' -print0)
else
  printf 'PENDING PHP syntax: php executable not available\n'
fi

if command -v node >/dev/null 2>&1; then
  while IFS= read -r -d '' file; do check "JS syntax: ${file#./}" check_node "$file"; done < <(find . -type f -name '*.js' -not -path './.git/*' -print0)
else
  printf 'PENDING JS syntax: node executable not available\n'
fi

for file in functions.php header.php front-page.php home.php archive.php single.php 404.php inc/blog.php inc/seo.php assets/css/blog.css; do
  check "required file: $file" test -f "$file"
done

for file in assets/css/redesign.css assets/css/layout-refinements.css assets/css/motion.css assets/css/batch-fixes.css assets/css/ui-round2.css assets/css/site-polish.css assets/css/service-thesis.css assets/css/service-statistics.css assets/css/service-simulation.css assets/js/redesign.js assets/img/logo.jpg; do
  check "production asset: $file" test -s "$file"
done

# service-proposal styles are covered by shared service template + layout-refinements / redesign cascade
check "theme version 1.6.0" grep -q '^Version: 1\.6\.0$' style.css
check "functions version 1.6.0" grep -q "define( 'TEZNEVISE_VERSION', '1.6.0'" functions.php
check "placeholder corruption absent" bash -c '! grep -R -n -E "^PLACEHOLDER[0-9]*$" --include="*.php" --include="*.css" --include="*.js" .'
check "no obvious secret assignments" bash -c '! grep -R -n -E "(api[_-]?key|secret|password|private[_-]?key|BEGIN (RSA|OPENSSH|EC) PRIVATE KEY)\\s*[:=]" --exclude-dir=.git --exclude="*.md" .'
check "GitHub deployment workflow remains non-deploying" bash -c '! grep -n -E "appleboy/ssh-action|scp-action|FTP_|CPANEL.*TOKEN|ssh.*private" .github/workflows/deploy-cpanel.yml'
check "cPanel excludes .git" grep -q -- "--exclude '.git'" .cpanel.yml
check "cPanel target is production theme" grep -q -- "/home/maziyarid/public_html/teznevise.ir/wp-content/themes/teznevise" .cpanel.yml

if [ "$fail" -ne 0 ]; then
  printf '\nRelease checks failed.\n'
  exit 1
fi
printf '\nRepository checks passed. Runtime/browser/VPS evidence is still required before release approval.\n'
