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
THEME_VERSION="$(sed -n 's/^Version: \(.*\)$/\1/p' style.css | head -n 1)"

printf 'Teznevise release checks\nRoot: %s\n\n' "$ROOT"

if command -v php >/dev/null 2>&1; then
	while IFS= read -r -d '' file; do check "PHP syntax: ${file#./}" check_php "$file"; done < <(find . \( -path './.git' -o -path './react-app/node_modules' -o -path './react-app/.vercel' \) -prune -o -type f -name '*.php' -print0)
else
  printf 'PENDING PHP syntax: php executable not available\n'
fi

if command -v node >/dev/null 2>&1; then
	while IFS= read -r -d '' file; do check "JS syntax: ${file#./}" check_node "$file"; done < <(find . \( -path './.git' -o -path './react-app/node_modules' -o -path './react-app/.vercel' \) -prune -o -type f -name '*.js' -print0)
else
  printf 'PENDING JS syntax: node executable not available\n'
fi

for file in functions.php header.php front-page.php home.php archive.php single.php 404.php inc/blog.php inc/seo.php assets/css/blog.css; do
  check "required file: $file" test -f "$file"
done

for file in assets/css/redesign.css assets/css/layout-refinements.css assets/css/motion.css assets/css/batch-fixes.css assets/css/ui-round2.css assets/css/site-polish.css assets/css/service-thesis.css assets/css/service-statistics.css assets/css/service-simulation.css assets/css/modernization.css assets/js/redesign.js assets/vendor/fontawesome/css/all.min.css; do
  check "production asset: $file" test -s "$file"
done

# service-proposal styles are covered by shared service template + layout-refinements / redesign cascade
check "theme version is declared" test -n "$THEME_VERSION"
check "functions version matches theme header" grep -Fq "define( 'TEZNEVISE_VERSION', '$THEME_VERSION'" functions.php
check "readme stable tag matches theme header" grep -Fq "Stable tag: $THEME_VERSION" readme.txt
check "README version matches theme header" grep -Fq "**Version:** $THEME_VERSION" README.md
check "layout-refinements not truncated at 32KB" bash -c 'test "$(wc -c < assets/css/layout-refinements.css)" -gt 40000'
check "compat layer files" test -f inc/frontend-compat.php -a -s assets/css/wp-compat.css -a -s assets/css/legacy-wpcode.css -a -s inc/legacy-wpcode.php
check "frontend-compat loaded" grep -q "frontend-compat.php" functions.php
check "wp-compat stylesheet present" test -s assets/css/wp-compat.css
check "header-form stylesheet present" test -s assets/css/header-form.css
check "placeholder corruption absent" bash -c '! grep -R -n -E "^PLACEHOLDER[0-9]*$" --exclude-dir=.git --exclude-dir=node_modules --exclude-dir=.vercel --include="*.php" --include="*.css" --include="*.js" .'
check "no committed private keys" bash -c '! grep -R -n -E "BEGIN( (RSA|OPENSSH|EC))? PRIVATE KEY" --exclude-dir=.git --exclude-dir=node_modules --exclude-dir=.vercel --exclude="*.md" .'
check "GitHub deployment workflow remains non-deploying" bash -c '! grep -n -E "appleboy/ssh-action|scp-action|FTP_|CPANEL.*TOKEN|ssh.*private" .github/workflows/deploy-cpanel.yml'
check "cPanel excludes .git" grep -q -- "--exclude '.git'" .cpanel.yml
check "cPanel target is production theme" grep -q -- "/home/maziyarid/public_html/teznevise.ir/wp-content/themes/teznevise" .cpanel.yml

if [ "$fail" -ne 0 ]; then
  printf '\nRelease checks failed.\n'
  exit 1
fi
printf '\nRepository checks passed. Runtime/browser/VPS evidence is still required before release approval.\n'
