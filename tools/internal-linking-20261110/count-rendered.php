<?php
/** Reproducible, offline before/after counts on captured main-link fixtures. */
require_once __DIR__ . '/runtime.php';
$root = dirname(__DIR__, 2) . '/data/internal-linking-20261110/';
$manifest = json_decode(file_get_contents($root . 'manifest.json'), true, 512, JSON_THROW_ON_ERROR);
$fixtures = json_decode(file_get_contents($root . 'rendered-link-fixtures.json'), true, 512, JSON_THROW_ON_ERROR);
$sources = array_column($manifest['sources'], null, 'id');
function count_links($html, $target, $anchor = null) {
    $dom = new DOMDocument(); $old = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET); libxml_clear_errors(); libxml_use_internal_errors($old);
    $count = 0;
    foreach ($dom->getElementsByTagName('a') as $a) {
        $match = str_contains($target, 'bluethesis.ir') ? $a->getAttribute('href') === $target : tzil_url_key($a->getAttribute('href')) === tzil_url_key($target);
        if ($match && ($anchor === null || trim($a->textContent) === $anchor)) { ++$count; }
    }
    return $count;
}
$out = fopen('php://output', 'w');
fputcsv($out, ['source_url','anchor','target','before_target_links','before_exact_links','after_draft_stage','after_master_publication','after_optional_blue_removal'], ',', '"', '');
foreach ($fixtures as $fixture) {
    $source = $sources[$fixture['id']];
    $state = ['active'=>true,'master_ready'=>false,'remove_bluethesis'=>false,'blue_links'=>$manifest['blue_links']];
    $draft = tzil_transform($fixture['html'], $source, $state);
    $state['master_ready'] = true; $published = tzil_transform($fixture['html'], $source, $state);
    $state['remove_bluethesis'] = true; $removed = tzil_transform($fixture['html'], $source, $state);
    foreach ($source['links'] as $link) {
        $after = count_links($draft, $link['target']);
        $expected = $link['activation'] === 'master-published' ? 0 : 1;
        if ($after !== $expected || count_links($published, $link['target'], $link['anchor']) !== 1) { throw new RuntimeException('Link contract failed: ' . $source['url']); }
        if (tzil_transform($draft, $source, ['active'=>true,'master_ready'=>false,'remove_bluethesis'=>false,'blue_links'=>$manifest['blue_links']]) !== $draft) { throw new RuntimeException('Non-idempotent transform'); }
        fputcsv($out, [$source['url'],$link['anchor'],$link['target'],count_links($fixture['html'],$link['target']),count_links($fixture['html'],$link['target'],$link['anchor']),$after,count_links($published,$link['target']),count_links($removed,$link['target'])], ',', '"', '');
    }
    foreach ($manifest['blue_links'] as $blue) {
        if ($blue['source_id'] !== $fixture['id']) { continue; }
        $before = count_links($fixture['html'], $blue['target'], $blue['anchor']);
        if ($before !== 1 || count_links($draft,$blue['target'],$blue['anchor']) !== 1 || count_links($removed,$blue['target'],$blue['anchor']) !== 0) { throw new RuntimeException('Blue removal flag contract failed'); }
        fputcsv($out, [$source['url'],$blue['anchor'],$blue['target'],$before,$before,1,1,0], ',', '"', '');
    }
}
