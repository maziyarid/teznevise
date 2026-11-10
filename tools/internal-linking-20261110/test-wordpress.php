<?php
/** Isolated WordPress adapters: these tests never connect to WordPress. */
define('TZIL_TEST_MODE', true);
define('OBJECT', 'OBJECT');
$sandbox = sys_get_temp_dir() . '/tzil-contract-' . bin2hex(random_bytes(6));
mkdir($sandbox, 0700);
mkdir($sandbox . '/wordpress', 0700);
define('ABSPATH', $sandbox . '/wordpress/');
define('WP_CONTENT_DIR', $sandbox . '/wordpress/wp-content');
mkdir(WP_CONTENT_DIR . '/mu-plugins', 0700, true);
copy(__DIR__ . '/runtime.php', WP_CONTENT_DIR . '/mu-plugins/teznevise-internal-links-20261110.php');
$GLOBALS['options'] = [];
$GLOBALS['posts'] = [947 => (object)['ID'=>947,'post_type'=>'page','post_status'=>'publish','post_parent'=>0,'post_name'=>'thesis','post_title'=>'انجام پایان نامه','post_content'=>'Existing body','post_excerpt'=>'']];
$seed_manifest = json_decode(file_get_contents(dirname(__DIR__, 2) . '/data/internal-linking-20261110/manifest.json'), true);
$GLOBALS['source_paths'] = [947=>'/thesis/'];
foreach ($seed_manifest['sources'] as $source) {
    $id = (int)$source['id'];
    $GLOBALS['source_paths'][$id] = parse_url($source['url'], PHP_URL_PATH);
    if (!isset($GLOBALS['posts'][$id])) $GLOBALS['posts'][$id] = (object)['ID'=>$id,'post_type'=>$source['type']==='download'?'download':'page','post_status'=>'publish','post_parent'=>0,'post_name'=>basename(trim($GLOBALS['source_paths'][$id], '/')),'post_title'=>'Existing source','post_content'=>'Existing source content','post_excerpt'=>''];
}
$GLOBALS['markers'] = [];
$GLOBALS['writes'] = [];
$GLOBALS['http_calls'] = 0;
$GLOBALS['http_urls'] = [];
$GLOBALS['origin'] = 'https://teznevise.ir';
function home_url($path = '') { return rtrim($GLOBALS['origin'], '/') . '/' . ltrim($path, '/'); }
function add_action(...$args) {}
function get_option($key, $default = false) { return $GLOBALS['options'][$key] ?? $default; }
function add_option($key, $value, ...$args) { if (isset($GLOBALS['options'][$key])) return false; $GLOBALS['writes'][] = ['add_option', $key]; $GLOBALS['options'][$key] = $value; return true; }
function update_option($key, $value, ...$args) { $GLOBALS['writes'][] = ['update_option', $key]; $GLOBALS['options'][$key] = $value; return true; }
function delete_option($key) { $GLOBALS['writes'][] = ['delete_option', $key]; unset($GLOBALS['options'][$key]); return true; }
function get_post($id) { return $GLOBALS['posts'][(int)$id] ?? null; }
function get_permalink($id) {
    if (is_object($id)) $id = $id->ID;
    $path = $GLOBALS['source_paths'][(int)$id] ?? '/' . (get_post($id)->post_name ?? '') . '/';
    return home_url($path);
}
function get_post_meta($id, $key = '', $single = false) {
    if ($key === '') return array_map(static fn($value) => [$value], $GLOBALS['markers'][(int)$id] ?? []);
    return $GLOBALS['markers'][(int)$id][$key] ?? '';
}
function get_page_by_path($path, ...$args) { foreach ($GLOBALS['posts'] as $post) if ($post->post_name === 'master' && (int)$post->post_parent === 947) return $post; return null; }
function get_posts($args) {
    return array_values(array_filter($GLOBALS['posts'], static function($post) use ($args) {
        if (isset($args['post_parent'])) return (int)$post->post_parent === (int)$args['post_parent'];
        return $post->post_type === 'page';
    }));
}
function get_comments($args) { return 0; }
function wp_insert_post($data, $error = false) {
    $id = 10001;
    while (isset($GLOBALS['posts'][$id])) ++$id;
    $GLOBALS['writes'][] = ['wp_insert_post', $id];
    $GLOBALS['markers'][$id] = $data['meta_input'] ?? [];
    unset($data['meta_input']);
    $GLOBALS['posts'][$id] = (object)array_merge(['ID'=>$id,'post_excerpt'=>''], $data);
    return $id;
}
function wp_delete_post($id, $force = false) { $post = get_post($id); $GLOBALS['writes'][] = ['wp_delete_post', $id]; unset($GLOBALS['posts'][$id], $GLOBALS['markers'][$id]); return $post; }
function is_wp_error($value) { return false; }
function wp_remote_get($url, $args) {
    ++$GLOBALS['http_calls'];
    $GLOBALS['http_urls'][] = $url;
    $html = '<html><head><meta charset="UTF-8"><link rel="canonical" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"></head><body><main><div class="article-content longcopy"><p>آموزش استفاده از منبع.</p></div></main></body></html>';
    $state = get_option('teznevise_internal_links_20261110', []);
    $state['master_ready'] = $state['master_verified'] ?? false;
    foreach ($state['blue_links'] ?? [] as $blue) {
        if ($blue['source_url'] === $url) {
            $link = '<a href="' . htmlspecialchars($blue['target'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($blue['anchor'], ENT_QUOTES, 'UTF-8') . '</a>';
            $html = str_replace('</div></main>', $link . '</div></main>', $html);
        }
    }
    foreach ($state['sources'] ?? [] as $source) if ($source['url'] === $url) $html = tzil_transform($html, $source, $state);
    return ['code'=>200, 'body'=>$html];
}
function wp_remote_retrieve_response_code($response) { return $response['code']; }
function wp_remote_retrieve_body($response) { return $response['body']; }
require __DIR__ . '/wordpress.php';
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function refuses($callback, $fragment) {
    try { $callback(); } catch (RuntimeException $error) { check(str_contains($error->getMessage(), $fragment), 'Wrong rejection: ' . $error->getMessage()); return; }
    throw new RuntimeException('Expected rejection: ' . $fragment);
}
function remove_tree($path) { if (!is_dir($path)) return; foreach (new FilesystemIterator($path) as $item) { if ($item->isDir()) remove_tree($item->getPathname()); else unlink($item->getPathname()); } rmdir($path); }

try {
    $backup = $sandbox . '/backup';
    $original = serialize($GLOBALS['posts'][947]);
    $plan = tzil_dispatch('apply', [$backup]);
    check(!empty($plan['dry_run']) && $plan['writes'] === 0, 'Apply defaults to dry run.');
    check(!$GLOBALS['writes'] && !file_exists($backup), 'Dry run must write neither database nor backup directory.');
    $plan = tzil_dispatch('verify', [$backup]);
    check(!empty($plan['dry_run']) && $GLOBALS['http_calls'] === 0, 'Verify defaults to a non-mutating plan without live reads.');
    $source_id = (int)$seed_manifest['sources'][0]['id'];
    $GLOBALS['posts'][$source_id]->post_status = 'draft';
    refuses(static fn() => tzil_dispatch('apply', [$backup, '--apply']), 'Source ID, status or canonical path');
    $GLOBALS['posts'][$source_id]->post_status = 'publish';
    $source_path = $GLOBALS['source_paths'][$source_id];
    $GLOBALS['source_paths'][$source_id] = '/unrelated-source/';
    refuses(static fn() => tzil_dispatch('apply', [$backup, '--apply']), 'Source ID, status or canonical path');
    $GLOBALS['source_paths'][$source_id] = $source_path;
    $GLOBALS['source_paths'][947] = '/unrelated-parent/';
    refuses(static fn() => tzil_dispatch('apply', [$backup, '--apply']), 'Master parent ID');
    $GLOBALS['source_paths'][947] = '/thesis/';
    check(!$GLOBALS['writes'], 'Source and parent drift must fail before any write.');
    refuses(static fn() => tzil_dispatch('apply', [$backup, '--apply', '--activate-master-links']), 'publication');
    check(!$GLOBALS['writes'], 'A premature master-link request must fail before writes.');

    $result = tzil_dispatch('apply', [$backup, '--apply']);
    $id = $result['master_id'];
    check($GLOBALS['posts'][$id]->post_status === 'draft' && (int)$GLOBALS['posts'][$id]->post_parent === 947, 'Apply must create the hierarchical master page as a draft.');
    check(serialize($GLOBALS['posts'][947]) === $original, 'Existing page title, body, status and metadata remain untouched.');
    check(get_option(TZIL_OPTION)['remove_bluethesis'] === false, 'Cross-site removal is off by default.');
    check(is_file($backup . '/database.json'), 'Backup journal must exist after apply.');
    $before_repeat = count($GLOBALS['posts']);
    $repeat = tzil_dispatch('apply', [$backup, '--apply']);
    check(!empty($repeat['unchanged']) && count($GLOBALS['posts']) === $before_repeat, 'Repeat apply must not create another master draft.');
    $interrupted = json_decode(file_get_contents($backup . '/database.json'), true);
    $interrupted['phase'] = 'prepared';
    $interrupted['created_master_id'] = null;
    unset($interrupted['after_post_hash'], $interrupted['after_option_hash'], $interrupted['planned_option']);
    file_put_contents($backup . '/database.json', tzil_json($interrupted));
    unset($GLOBALS['options'][TZIL_OPTION]);
    tzil_dispatch('apply', [$backup, '--apply']);
    check(count($GLOBALS['posts']) === $before_repeat && get_option(TZIL_OPTION)['master_id'] === $id, 'Interrupted draft creation must recover the marked draft without creating a duplicate.');
    $verification = tzil_dispatch('verify', [$backup, '--check']);
    check(!empty($verification['verified']), 'Read-only verify accepts the planned rendered links and draft.');

    $state = get_option(TZIL_OPTION);
    $GLOBALS['options'][TZIL_OPTION]['editor_note'] = 'newer work';
    $before_refusal = count($GLOBALS['writes']);
    refuses(static fn() => tzil_dispatch('rollback', [$backup, '--apply']), 'Newer option');
    check(count($GLOBALS['writes']) === $before_refusal, 'Rollback rejects newer option edits before any write.');
    $GLOBALS['options'][TZIL_OPTION] = $state;
    $GLOBALS['posts'][$id]->post_content .= ' New editorial text';
    refuses(static fn() => tzil_dispatch('rollback', [$backup, '--apply']), 'Newer master');
    $journal = json_decode(file_get_contents($backup . '/database.json'), true);
    $GLOBALS['posts'][$id]->post_content = file_get_contents(dirname(__DIR__, 2) . '/data/internal-linking-20261110/master.html');
    $GLOBALS['markers'][$id]['editor_note'] = 'newer metadata';
    refuses(static fn() => tzil_dispatch('rollback', [$backup, '--apply']), 'Newer master');
    unset($GLOBALS['markers'][$id]['editor_note']);
    $GLOBALS['posts'][12345] = (object)['ID'=>12345,'post_type'=>'page','post_status'=>'draft','post_parent'=>$id,'post_name'=>'child','post_title'=>'Child','post_content'=>'','post_excerpt'=>''];
    refuses(static fn() => tzil_dispatch('rollback', [$backup, '--apply']), 'child content');
    unset($GLOBALS['posts'][12345]);
    $before_dry_rollback = count($GLOBALS['writes']);
    $plan = tzil_dispatch('rollback', [$backup]);
    check(!empty($plan['dry_run']) && count($GLOBALS['writes']) === $before_dry_rollback, 'Rollback defaults to a write-free plan.');
    tzil_dispatch('rollback', [$backup, '--apply']);
    check(!get_post($id) && get_option(TZIL_OPTION, null) === null, 'Rollback removes only owned draft and package activation.');
    check(serialize($GLOBALS['posts'][947]) === $original, 'Rollback preserves pre-existing pages.');
    check(!empty(tzil_dispatch('rollback', [$backup, '--apply'])['unchanged']), 'Repeat rollback is idempotent.');

    $flagged_backup = $sandbox . '/flagged-backup';
    tzil_dispatch('apply', [$flagged_backup, '--apply', '--remove-bluethesis']);
    check(get_option(TZIL_OPTION)['remove_bluethesis'] === true, 'Cross-site removal activates only when explicitly flagged.');
    refuses(static fn() => tzil_dispatch('apply', [$flagged_backup, '--apply']), 'keep --remove-bluethesis');
    tzil_dispatch('rollback', [$flagged_backup, '--apply']);
    check(get_option(TZIL_OPTION, null) === null, 'Flagged removal can be rolled back with the package.');

    $GLOBALS['origin'] = 'https://staging.example.org';
    $GLOBALS['http_urls'] = [];
    $staging_backup = $sandbox . '/staging-backup';
    tzil_dispatch('apply', [$staging_backup, '--apply']);
    $staging_state = get_option(TZIL_OPTION);
    foreach ($staging_state['sources'] as $source) {
        check(str_starts_with($source['url'], 'https://staging.example.org/'), 'Staging source URL must use the selected WordPress origin.');
        foreach ($source['links'] as $link) check(str_starts_with($link['target'], 'https://staging.example.org/'), 'Staging internal targets must use the selected WordPress origin.');
    }
    check(!empty(tzil_dispatch('verify', [$staging_backup, '--check'])['verified']), 'Staging verify must use local rendered links.');
    foreach ($GLOBALS['http_urls'] as $url) check(!str_starts_with($url, 'https://teznevise.ir/'), 'Staging apply and verify must never fetch production.');
    tzil_dispatch('rollback', [$staging_backup, '--apply']);
    $GLOBALS['origin'] = 'https://teznevise.ir';

    $GLOBALS['options']['teznevise_onpage_20261110'] = ['enabled'=>true];
    $before_conflict = count($GLOBALS['writes']);
    refuses(static fn() => tzil_dispatch('apply', [$sandbox . '/other-backup', '--apply']), 'PR #456');
    check(count($GLOBALS['writes']) === $before_conflict, 'Conflicting package refusal is write-free.');
    unset($GLOBALS['options']['teznevise_onpage_20261110']);
    $GLOBALS['options'][TZIL_OPTION] = false;
    refuses(static fn() => tzil_dispatch('apply', [$sandbox . '/other-backup', '--apply']), 'Unowned or newer option');
    check(count($GLOBALS['writes']) === $before_conflict, 'Even a false-valued unowned activation option must be preserved.');
    unset($GLOBALS['options'][TZIL_OPTION]);
    $GLOBALS['posts'][1234] = (object)['ID'=>1234,'post_type'=>'page','post_status'=>'draft','post_parent'=>0,'post_name'=>'other-master','post_title'=>'انجام پایان نامه کارشناسی ارشد','post_content'=>'','post_excerpt'=>''];
    refuses(static fn() => tzil_dispatch('apply', [$sandbox . '/other-backup', '--apply']), 'Another page owns');
    check(count($GLOBALS['writes']) === $before_conflict, 'Existing owner must block duplicate creation before writes.');
    echo "PASS: default dry runs, draft ownership, idempotence, read-only verification, rollback guards and PR #456 conflict\n";
} finally {
    remove_tree($sandbox);
}
