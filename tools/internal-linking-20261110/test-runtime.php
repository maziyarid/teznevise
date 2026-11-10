<?php
/** Offline behavioral checks; no WordPress or network access. */
function add_action(...$args) {}
function add_filter(...$args) {}
function home_url($path = '') { return 'https://teznevise.ir/' . ltrim($path, '/'); }
define('OBJECT', 'OBJECT');
$GLOBALS['runtime_options'] = [];
$GLOBALS['runtime_permalink'] = 'https://teznevise.ir/unrelated/';
function get_option($key, $default = false) { return $GLOBALS['runtime_options'][$key] ?? $default; }
function is_admin() { return false; }
function is_feed() { return false; }
function is_preview() { return false; }
function is_singular() { return true; }
function get_queried_object_id() { return 123; }
function get_permalink($id) { return $GLOBALS['runtime_permalink']; }
function get_page_by_path(...$args) { return null; }
require __DIR__ . '/runtime.php';

function check($condition, $message) {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
function anchors($html, $href = null) {
    $doc = new DOMDocument();
    @$doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    $found = [];
    foreach ($doc->getElementsByTagName('a') as $a) {
        if ($href === null || $a->getAttribute('href') === $href) {
            $found[] = $a->textContent;
        }
    }
    return $found;
}
function main_text($html) {
    $doc = new DOMDocument();
    @$doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xp = new DOMXPath($doc);
    return $xp->query('//main')->item(0)->textContent;
}

$thesis = 'https://teznevise.ir/thesis/';
$master = 'https://teznevise.ir/thesis/master/';
$blue = 'https://bluethesis.ir/analysis/';
$keyword = 'انجام پایان نامه';
$source = [
    'id' => 123,
    'url' => 'https://teznevise.ir/online-calculation-tools/sample-size-calculator/',
    'type' => 'tool',
    'links' => [[
        'anchor' => $keyword,
        'target' => $thesis,
        'context' => 'برای هماهنگی حجم نمونه با روش پژوهش، مسیر {anchor} را بررسی کنید.',
        'activation' => 'immediate',
    ]],
];
$state = ['active' => true, 'master_ready' => false, 'remove_bluethesis' => false, 'blue_links' => []];
$html = '<!DOCTYPE html><html lang="fa"><head><title>ابزار</title></head><body><header><a href="' . $thesis . '">ناوبری</a></header><main><div class="article-content longcopy"><p>نتیجه محاسبه و روش استفاده از ابزار.</p></div></main><footer>پایان صفحه</footer></body></html>';

$inactive = $state;
$inactive['active'] = false;
check(tzil_transform($html, $source, $inactive) === $html, 'Inactive package must preserve the complete response byte for byte.');
$out = tzil_transform($html, $source, $state);
check(anchors($out, $thesis) === ['ناوبری', $keyword], 'Add one exact target anchor in content while leaving chrome alone.');
check(str_contains(main_text($out), 'حجم نمونه'), 'Contextual explanation belongs inside main content.');
check(!str_contains($out, '{anchor}'), 'Context placeholder must become the actual anchor.');
check(!str_contains($out, "\u{200C}"), 'New output must contain no half-space.');
check(tzil_transform($out, $source, $state) === $out, 'Rendering twice must be idempotent.');
check(tzil_transform('<html><body><header><p>No compatible content surface.</p></header></body></html>', $source, $state) === '<html><body><header><p>No compatible content surface.</p></header></body></html>', 'Missing content surface must fail closed without adding links to chrome.');
$fallback = tzil_transform('<html><body><main><p>شرح ابزار.</p></main></body></html>', $source, $state);
check(anchors($fallback, $thesis) === [$keyword], 'A compatible main element is an explicit fallback for pages without article classes.');

$existing = str_replace('نتیجه محاسبه و روش استفاده از ابزار.', '<a href="' . $thesis . '">راهنمای پژوهش</a> برای طراحی روش مفید است.', $html);
$out = tzil_transform($existing, $source, $state);
check(anchors($out, $thesis) === ['ناوبری', $keyword], 'Existing content target must acquire the exact anchor without adding another target link.');

$redirecting = str_replace('نتیجه محاسبه و روش استفاده از ابزار.', '<a href="http://teznevise.ir/thesis/">' . $keyword . '</a>', $html);
$out = tzil_transform($redirecting, $source, $state);
check(anchors($out, 'http://teznevise.ir/thesis/') === [], 'An existing HTTP target must be canonicalized to avoid a redirect.');
check(anchors($out, $thesis) === ['ناوبری', $keyword], 'Canonical target keeps the exact anchor.');

$duplicates = str_replace('نتیجه محاسبه و روش استفاده از ابزار.', '<a href="' . $thesis . '">راهنمای اول</a> و <a href="' . $thesis . '">راهنمای دوم</a> در اختیار شماست.', $html);
$out = tzil_transform($duplicates, $source, $state);
check(count(anchors($out, $thesis)) === 2, 'Duplicate content targets must collapse to one; chrome target remains.');
check(str_contains(main_text($out), 'راهنمای دوم'), 'Deduplication preserves the redundant link text.');

$source['links'][] = ['anchor' => 'انجام پایان نامه کارشناسی ارشد', 'target' => $master, 'context' => 'برای برنامه پژوهش ارشد، مسیر {anchor} را بررسی کنید.', 'activation' => 'master-published'];
$out = tzil_transform($html, $source, $state);
check(anchors($out, $master) === [], 'Draft master page must never receive a public link.');
$ready = $state;
$ready['master_ready'] = true;
$out = tzil_transform($html, $source, $ready);
check(anchors($out, $master) === ['انجام پایان نامه کارشناسی ارشد'], 'Master link activates only after explicit readiness.');

$blue_html = str_replace('نتیجه محاسبه و روش استفاده از ابزار.', '<a href="' . $blue . '">تحلیل آماری</a> و <a href="https://example.org/">منبع دیگر</a>', $html);
$state['blue_links'] = [['source_id' => 123, 'source_url' => $source['url'], 'anchor' => 'تحلیل آماری', 'target' => $blue]];
$out = tzil_transform($blue_html, $source, $state);
check(anchors($out, $blue) === ['تحلیل آماری'], 'Default run audits BlueThesis without touching it.');
$state['remove_bluethesis'] = true;
$out = tzil_transform($blue_html, $source, $state);
check(anchors($out, $blue) === [], 'Explicit removal flag unwraps the inventoried BlueThesis link.');
check(str_contains(main_text($out), 'تحلیل آماری'), 'Cross-site removal preserves visible anchor text.');
check(anchors($out, 'https://example.org/') === ['منبع دیگر'], 'Unrelated external links are untouched.');
$state['blue_links'][0]['source_id'] = 999;
$out = tzil_transform($blue_html, $source, $state);
check(anchors($out, $blue) === ['تحلیل آماری'], 'Removal is scoped to the inventoried source.');

$state['blue_links'][0]['source_id'] = 123;
$blue_only_source = $source;
$blue_only_source['links'] = [];
$out = tzil_transform($blue_html, $blue_only_source, $state);
check(anchors($out, $blue) === [], 'BlueThesis cleanup also covers inventoried sources without new internal links.');
$hero_blue = str_replace('<main>', '<main><div class="hero"><a href="' . $blue . '">تحلیل آماری</a></div>', $html);
$out = tzil_transform($hero_blue, $blue_only_source, $state);
check(anchors($out, $blue) === [], 'Flagged cleanup reaches inventoried hero links within main.');

$GLOBALS['runtime_options']['teznevise_internal_links_20261110'] = array_merge($state, ['sources'=>[$source]]);
$level = ob_get_level();
tzil_begin_render();
check(ob_get_level() === $level, 'Runtime refuses a source ID whose permalink has drifted.');
$GLOBALS['runtime_permalink'] = $source['url'];
tzil_begin_render();
check(ob_get_level() === $level + 1, 'Runtime activates only on the matching source identity.');
ob_end_clean();
$GLOBALS['runtime_options']['teznevise_onpage_20261110'] = ['enabled'=>true];
tzil_begin_render();
check(ob_get_level() === $level, 'Runtime remains dormant when PR #456 activates later.');

echo "PASS: runtime activation, placement, exact anchors, deduplication, master gate and flagged removals\n";
