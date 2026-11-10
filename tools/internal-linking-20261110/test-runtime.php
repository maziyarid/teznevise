<?php
/** Offline behavior tests; no WordPress, browser or network access. */
function add_action(...$args) {}
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
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function parsed($html) {
    $doc = new DOMDocument(); @$doc->loadHTML('<?xml encoding="UTF-8">' . $html); return $doc;
}
function anchors($html, $href = null, $main_only = false) {
    $doc = parsed($html); $xp = new DOMXPath($doc); $found = [];
    foreach ($xp->query($main_only ? '//main//a' : '//a') as $a) {
        if ($href === null || $a->getAttribute('href') === $href) $found[] = trim($a->textContent);
    }
    return $found;
}
function main_text($html) { return (new DOMXPath(parsed($html)))->query('//main')->item(0)->textContent; }
function contract($out, $href, $exact, $variants, $expected_exact, $total) {
    $texts = anchors($out, $href, true);
    check(count($texts) === $total, 'Existing target occurrences must be preserved, without manufactured secondary links.');
    check(count(array_filter($texts, static fn($a) => $a === $exact)) === $expected_exact, 'Only the designated primary may have the exact keyword.');
    foreach ($texts as $text) check($text === $exact || in_array($text, $variants, true), 'Every other target link must use an approved LSI phrase.');
}
$thesis = 'https://teznevise.ir/thesis/'; $master = 'https://teznevise.ir/thesis/master/';
$proposal = 'https://teznevise.ir/proposal/'; $blue = 'https://bluethesis.ir/analysis/';
$keyword = 'انجام پایان نامه'; $variants = ['نگارش پایان نامه','سفارش پایان نامه','خدمات پایان نامه','تدوین پایان نامه','راهنمای پایان نامه'];
$proposal_keyword = 'انجام پروپوزال'; $proposal_variants = ['نگارش پروپوزال','تدوین پروپوزال','نوشتن پروپوزال','طراحی پروپوزال','راهنمای پروپوزال'];
$source = ['id'=>123,'url'=>'https://teznevise.ir/online-calculation-tools/sample-size-calculator/','type'=>'tool','links'=>[
    ['anchor'=>$keyword,'exact_keyword'=>$keyword,'anchor_role'=>'exact-primary','lsi_phrases'=>$variants,'target'=>$thesis,'context'=>'برای هماهنگی حجم نمونه با روش پژوهش، مسیر {anchor} را بررسی کنید.','activation'=>'immediate']
]];
$policy = [
    ['keyword'=>$keyword,'target'=>$thesis,'lsi_phrases'=>$variants,'designated_exact_source_ids'=>[123],'activation'=>'immediate'],
    ['keyword'=>$proposal_keyword,'target'=>$proposal,'lsi_phrases'=>$proposal_variants,'designated_exact_source_ids'=>[999],'activation'=>'immediate'],
];
$state = ['active'=>true,'master_ready'=>false,'remove_bluethesis'=>false,'blue_links'=>[],'anchor_policy'=>$policy];
$html = '<!DOCTYPE html><html lang="fa"><head><title>ابزار</title></head><body><header><a href="'.$thesis.'">ناوبری</a></header><main><div class="article-content longcopy"><p>نتیجه محاسبه و روش استفاده از ابزار.</p></div></main><footer>پایان صفحه</footer></body></html>';
$inactive = $state; $inactive['active'] = false;
check(tzil_transform($html,$source,$inactive) === $html, 'Inactive renderer must preserve the whole response byte for byte.');
$out = tzil_transform($html,$source,$state);
contract($out,$thesis,$keyword,$variants,1,1);
check(anchors($out,$thesis) === ['ناوبری',$keyword], 'Chrome must remain unchanged.');
check(str_contains(main_text($out),'حجم نمونه'), 'A new primary needs its natural authored context.');
check(!str_contains($out,'{anchor}') && !str_contains($out,"\u{200C}"), 'Output resolves placeholders and contains no half-space.');
check(tzil_transform($out,$source,$state) === $out, 'Repeated rendering must be idempotent.');
$missing = '<html><body><header><p>No compatible content surface.</p></header></body></html>';
check(tzil_transform($missing,$source,$state) === $missing, 'Missing main/editorial surface must fail closed.');
$fallback = tzil_transform('<html><body><main><p>شرح ابزار.</p></main></body></html>',$source,$state);
contract($fallback,$thesis,$keyword,$variants,1,1);
$existing = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="'.$thesis.'">راهنمای پژوهش</a> برای طراحی روش مفید است.',$html);
$out = tzil_transform($existing,$source,$state); contract($out,$thesis,$keyword,$variants,1,1);
$duplicates = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="'.$thesis.'">'.$keyword.'</a> و <a href="'.$thesis.'">'.$keyword.'</a> و <a href="'.$thesis.'">عبارت قدیمی</a>',$html);
$out = tzil_transform($duplicates,$source,$state); contract($out,$thesis,$keyword,$variants,1,3);
check(tzil_transform($out,$source,$state) === $out, 'Secondary LSI choices must stay stable across repeated renders.');
$redirecting = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="http://teznevise.ir/thesis/">'.$keyword.'</a>',$html);
$out = tzil_transform($redirecting,$source,$state);
check(anchors($out,'http://teznevise.ir/thesis/',true) === [], 'Existing HTTP target must acquire the canonical HTTPS href.');
contract($out,$thesis,$keyword,$variants,1,1);
$lsi_source = $source; $lsi_source['id'] = 124; $lsi_source['links'][0]['anchor'] = $variants[0]; $lsi_source['links'][0]['anchor_role'] = 'lsi';
$out = tzil_transform($duplicates,$lsi_source,$state); contract($out,$thesis,$keyword,$variants,0,3);
$unselected = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="'.$proposal.'">'.$proposal_keyword.'</a> و <a href="'.$proposal.'">مشاوره و طراحی پروپوزال</a>',$html);
$out = tzil_transform($unselected,$source,$state); contract($out,$proposal,$proposal_keyword,$proposal_variants,0,2);
contract($out,$thesis,$keyword,$variants,1,1);
$description = 'این توضیح درباره مسئله پژوهش و برنامه اجرا باید بدون حذف شدن در کارت باقی بماند.';
$card = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a class="service-card" href="'.$thesis.'"><h3>راهنمای پژوهش</h3><p>'.$description.'</p></a>',$html);
$out = tzil_transform($card,$source,$state); contract($out,$thesis,$keyword,$variants,1,1);
check(str_contains(main_text($out),$description), 'Shortening a linked card must preserve its description.');
check((new DOMXPath(parsed($out)))->query('//main//h3')->length === 1, 'Shortening a linked card must preserve nested heading markup.');
check(tzil_transform($out,$source,$state) === $out, 'Card replacement must be idempotent.');
$short_card = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a class="service-card" href="'.$thesis.'"><span class="card-title">پژوهش</span><span class="card-description">روش و برنامه اجرا</span></a>',$html);
$out = tzil_transform($short_card,$source,$state); contract($out,$thesis,$keyword,$variants,1,1);
$short_xpath = new DOMXPath(parsed($out));
check($short_xpath->query('//main//span[@class="card-title"]')->item(0)->textContent === 'پژوهش', 'A short span card keeps its original title.');
check($short_xpath->query('//main//span[@class="card-description"]')->item(0)->textContent === 'روش و برنامه اجرا', 'A short span card keeps its description and span markup.');
check(tzil_transform($out,$source,$state) === $out, 'Short span card replacement must be idempotent.');
$image_card = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="'.$thesis.'"><img src="/research.png" alt="پژوهش">پژوهش</a>',$html);
$out = tzil_transform($image_card,$source,$state); contract($out,$thesis,$keyword,$variants,1,1);
check((new DOMXPath(parsed($out)))->query('//main//img[@src="/research.png"]')->length === 1, 'Image links retain their image when the contextual anchor is shortened.');
check(str_contains(main_text($out),'پژوهش'), 'Image link replacement keeps the original caption.');
check(tzil_transform($out,$source,$state) === $out, 'Image link replacement must be idempotent.');
$master_keyword = 'انجام پایان نامه کارشناسی ارشد'; $master_variants = ['نگارش پایان نامه ارشد','پایان نامه کارشناسی ارشد','تدوین پایان نامه ارشد','راهنمای پایان نامه ارشد','مراحل پایان نامه ارشد'];
$master_source = $source; $master_source['id'] = 125; $master_source['links'] = [['anchor'=>$master_keyword,'exact_keyword'=>$master_keyword,'anchor_role'=>'exact-primary','lsi_phrases'=>$master_variants,'target'=>$master,'context'=>'برای برنامه پژوهش ارشد، مسیر {anchor} را بررسی کنید.','activation'=>'master-published']];
$state['anchor_policy'][] = ['keyword'=>$master_keyword,'target'=>$master,'lsi_phrases'=>$master_variants,'designated_exact_source_ids'=>[125],'activation'=>'master-published'];
$existing_master = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="'.$master.'">راهنمای ارشد</a>',$html);
$out = tzil_transform($existing_master,$master_source,$state);
check(anchors($out,$master,true) === [], 'Draft gating must also unwrap a legacy link to the unpublished master.');
check(str_contains(main_text($out),'راهنمای ارشد'), 'Removing a draft destination must preserve its visible label.');
$ready = $state; $ready['master_ready'] = true;
$out = tzil_transform($html,$master_source,$ready); contract($out,$master,$master_keyword,$master_variants,1,1);
$chapter = 'https://teznevise.ir/thesis/chapter-four/'; $chapter_keyword = 'انجام فصل چهارم پایان نامه';
$nonmoney = $source; $nonmoney['links'] = [['anchor'=>$chapter_keyword,'target'=>$chapter,'context'=>'برای گزارش یافته ها، راهنمای {anchor} را بخوانید.','activation'=>'immediate']];
$two_chapter = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="'.$chapter.'">راهنمای اول</a> و <a href="'.$chapter.'">راهنمای دوم</a>',$html);
$out = tzil_transform($two_chapter,$nonmoney,$state);
check(anchors($out,$chapter,true) === [$chapter_keyword], 'The three non-money verified maps retain their one-exact-target contract.');
$blue_html = str_replace('نتیجه محاسبه و روش استفاده از ابزار.','<a href="'.$blue.'">تحلیل آماری</a> و <a href="https://example.org/">منبع دیگر</a>',$html);
$state['blue_links'] = [['source_id'=>123,'source_url'=>$source['url'],'anchor'=>'تحلیل آماری','target'=>$blue]];
$out = tzil_transform($blue_html,$source,$state); check(anchors($out,$blue) === ['تحلیل آماری'], 'BlueThesis must remain unchanged by default.');
$state['remove_bluethesis'] = true;
$out = tzil_transform($blue_html,$source,$state);
check(anchors($out,$blue) === [] && str_contains(main_text($out),'تحلیل آماری'), 'Flagged removal preserves the inventoried anchor text.');
check(anchors($out,'https://example.org/') === ['منبع دیگر'], 'Unrelated external links remain untouched.');
$state['blue_links'][0]['source_id'] = 999;
check(anchors(tzil_transform($blue_html,$source,$state),$blue) === ['تحلیل آماری'], 'Removal stays scoped to the inventoried source.');
$state['blue_links'][0]['source_id'] = 123;
$blue_only = $source; $blue_only['links'] = [];
check(anchors(tzil_transform($blue_html,$blue_only,$state),$blue) === [], 'Flagged removal also covers sources without new primary links.');
$hero_blue = str_replace('<main>','<main><div class="hero"><a href="'.$blue.'">تحلیل آماری</a></div>',$html);
check(anchors(tzil_transform($hero_blue,$blue_only,$state),$blue) === [], 'Removal reaches inventoried hero links within main.');
$GLOBALS['runtime_options']['teznevise_internal_links_20261110'] = array_merge($state,['sources'=>[$source]]);
$level = ob_get_level(); tzil_begin_render();
check(ob_get_level() === $level, 'Runtime must refuse an enrolled source whose permalink has drifted.');
$GLOBALS['runtime_permalink'] = $source['url']; tzil_begin_render();
check(ob_get_level() === $level+1, 'Runtime starts only for the matching source identity.'); ob_end_clean();
$GLOBALS['runtime_options']['teznevise_onpage_20261110'] = ['enabled'=>true]; tzil_begin_render();
check(ob_get_level() === $level, 'Late activation of PR #456 disables this renderer.');
echo "PASS: mixed exact/LSI anchors, preserved secondary links/cards, canonical targets, draft gates and guarded removal\n";
