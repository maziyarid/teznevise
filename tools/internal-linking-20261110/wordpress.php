<?php
/** Run with wp eval-file; no production invocation is performed by this PR. */
require_once __DIR__ . '/runtime.php';
const TZIL_OPTION = 'teznevise_internal_links_20261110';
function tzil_fail( $message ) { throw new RuntimeException( $message ); }
function tzil_json( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR ); }
function tzil_hash( $value ) { return hash( 'sha256', tzil_json( $value ) ); }
function tzil_local_url($url){$origin=function_exists('home_url')?rtrim(home_url('/'),'/'):'https://teznevise.ir';return preg_replace('#^https://teznevise\\.ir(?=/|$)#',$origin,$url);}
function tzil_local_manifest($manifest){foreach($manifest['sources'] as &$source){$source['url']=tzil_local_url($source['url']);foreach($source['links'] as &$link){$link['target']=tzil_local_url($link['target']);}unset($link);}unset($source);foreach($manifest['blue_links']??array() as $index=>$row){$manifest['blue_links'][$index]['source_url']=tzil_local_url($row['source_url']);}return $manifest;}
function tzil_post_snapshot( $id ) {
	$p = get_post( $id ); if ( ! $p ) { return null; }
	$out = get_object_vars($p);unset($out['filter']);ksort($out);
	$out['meta'] = get_post_meta( $id ); ksort($out['meta']); return $out;
}
function tzil_write_journal( $file, $value ) {
	$tmp = $file . '.tmp';
	if ( file_put_contents( $tmp, tzil_json( $value ), LOCK_EX ) === false ) { tzil_fail( 'Cannot write private journal.' ); }
	chmod( $tmp, 0600 ); if ( ! rename( $tmp, $file ) ) { tzil_fail( 'Cannot commit private journal.' ); }
}
function tzil_private_directory( $directory, $write ) {
	if ( $directory === '' || $directory[0] !== '/' || is_link( $directory ) ) { tzil_fail( 'Backup directory must be an absolute, non-symlink private path.' ); }
	$parent = realpath( dirname( $directory ) );
	if ( ! $parent ) { tzil_fail( 'Backup parent must already exist.' ); }
	$resolved = $parent . '/' . basename( $directory ); $webroot = realpath( ABSPATH );
	if ( $webroot && ( $resolved === $webroot || str_starts_with( $resolved . '/', $webroot . '/' ) ) ) { tzil_fail( 'Backup must be outside the WordPress webroot.' ); }
	if ( file_exists( $resolved ) && ( ! is_dir( $resolved ) || is_link( $resolved ) ) ) { tzil_fail( 'Unsafe backup path.' ); }
	if ( $write && ! is_dir( $resolved ) && ! mkdir( $resolved, 0700 ) ) { tzil_fail( 'Cannot create backup directory.' ); }
	if ( $write ) { chmod( $resolved, 0700 ); }
	return $resolved;
}
function tzil_direct_target( $url ) {
	$response = wp_remote_get( $url, array( 'timeout' => 25, 'redirection' => 0, 'limit_response_size' => 2000000 ) );
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) { tzil_fail( 'Target must return direct HTTP 200: ' . $url ); }
	$body = wp_remote_retrieve_body( $response );
	$dom = new DOMDocument(); $old = libxml_use_internal_errors( true ); $dom->loadHTML( $body, LIBXML_NONET ); libxml_clear_errors(); libxml_use_internal_errors( $old );
	$xpath = new DOMXPath( $dom ); $canonicals = $xpath->query( '//link[contains(concat(" ",normalize-space(@rel)," ")," canonical ")]/@href' );
	if ( $canonicals->length !== 1 || rtrim(html_entity_decode($canonicals->item(0)->nodeValue,ENT_QUOTES|ENT_HTML5,'UTF-8'),'/') !== rtrim($url,'/') ) { tzil_fail( 'Target needs one exact same-site self canonical: ' . $url ); }
	$private_staging=(string)get_option('blog_public',1)==='0' && strtolower((string)parse_url($url,PHP_URL_HOST))!=='teznevise.ir';
	$header=function_exists('wp_remote_retrieve_header')?(string)wp_remote_retrieve_header($response,'x-robots-tag'):'';
	if ( !$private_staging && (stripos($header,'noindex')!==false || preg_match( '/<meta[^>]+(?:name=["\x27]robots["\x27][^>]+content=["\x27][^"\x27]*noindex|content=["\x27][^"\x27]*noindex[^>]+name=["\x27]robots)/i', $body )) ) { tzil_fail( 'Target is noindex: ' . $url ); }
}
function tzil_validate_package( $manifest, $root ) {
	if ( empty( $manifest['sources'] ) || empty( $manifest['master'] ) ) { tzil_fail( 'Incomplete manifest.' ); }
	$known = array();
	foreach ( $manifest['sources'] as $source ) {
		if ( isset( $known[$source['id']] ) || ! tzil_url_key( $source['url'] ) ) { tzil_fail( 'Duplicate or invalid source.' ); } $known[$source['id']] = true;
		$targets = array();
		foreach ( $source['links'] as $link ) {
			$key = tzil_url_key( $link['target'] );
			if ( ! $key || isset( $targets[$key] ) || substr_count( $link['context'], '{anchor}' ) !== 1 || str_contains( tzil_json( $link ), "\u{200C}" ) ) { tzil_fail( 'Invalid link contract.' ); }
			$targets[$key] = true;
		}
	}
	$file = $manifest['master']['content_file'];
	if ( basename( $file ) !== $file || ! is_file( $root . '/' . $file ) ) { tzil_fail( 'Missing draft content file.' ); }
	$content = file_get_contents( $root . '/' . $file );
	if ( str_contains( $content, "\u{200C}" ) ) { tzil_fail( 'Draft contains U+200C.' ); }
	return $content;
}
function tzil_dispatch( $mode, $arguments ) {
	$apply = in_array( '--apply', $arguments, true );
	$remove = in_array( '--remove-bluethesis', $arguments, true );
	$master_links = in_array( '--activate-master-links', $arguments, true );
	$positionals = array_values( array_filter( $arguments, static fn( $a ) => ! str_starts_with( $a, '--' ) ) );
	if ( count( $positionals ) !== 1 ) { tzil_fail( 'Supply PRIVATE_BACKUP_DIR and optional --apply, --remove-bluethesis, --activate-master-links.' ); }
	foreach ( $arguments as $arg ) { if ( str_starts_with( $arg, '--' ) && ! in_array( $arg, array( '--apply','--dry-run','--remove-bluethesis','--activate-master-links','--check' ), true ) ) { tzil_fail( 'Unknown flag: ' . $arg ); } }
	if ( $apply && in_array( '--dry-run', $arguments, true ) ) { tzil_fail( 'Choose --apply or --dry-run.' ); }
	$directory = tzil_private_directory( $positionals[0], false ); $journal_file = $directory . '/database.json';
	$root = dirname( __DIR__, 2 ) . '/data/internal-linking-20261110';
	$manifest = json_decode( file_get_contents( $root . '/manifest.json' ), true, 512, JSON_THROW_ON_ERROR );
	$content = tzil_validate_package( $manifest, $root ); $package_hash = tzil_hash( array( $manifest, $content ) );
	$local_manifest=tzil_local_manifest($manifest);
	$state = get_option( TZIL_OPTION, null );
	$journal = is_file( $journal_file ) ? json_decode( file_get_contents( $journal_file ), true, 512, JSON_THROW_ON_ERROR ) : null;
	if ( $journal && $journal['package_hash'] !== $package_hash ) { tzil_fail( 'Package differs from immutable backup; use the reviewed original checkout.' ); }
	if ( tzil_conflicting_package() ) { tzil_fail( 'PR #456 package is active; reconcile ownership and anchors before applying either plan.' ); }
	$master = get_page_by_path( 'thesis/master', OBJECT, 'page' );
	if($journal && $master && empty($journal['created_master_id']) && ($journal['phase']??'')==='prepared' && get_post_meta($master->ID,'_tzil_created',true)===$package_hash){$journal['created_master_id']=(int)$master->ID;$journal['after_post_hash']=tzil_hash(tzil_post_snapshot($master->ID));$journal['phase']='draft-created';}
	if($journal && $state !== null && ($journal['phase']??'')==='activating' && isset($journal['planned_option']) && tzil_hash($state)===tzil_hash($journal['planned_option'])){$journal['after_option_hash']=tzil_hash($state);$journal['phase']='applied';}
	if ( $mode === 'verify' ) {
		if ( ! in_array( '--check', $arguments, true ) ) { return array( 'dry_run'=>true,'plan'=>'Check active option, draft fingerprint, target canonicals and editorial links; add --check to perform read-only checks.' ); }
		if ( ! $journal || ! $state || $journal['after_option_hash'] !== tzil_hash( $state ) || $state['package_hash'] !== $package_hash ) { tzil_fail( 'Installed state does not match this package and backup.' ); }
		if ( ! $master || (int) $master->ID !== (int) $state['master_id'] || ( ! $state['master_verified'] && $master->post_status !== 'draft' ) ) { tzil_fail( 'Master draft state differs from reviewed plan.' ); }
		if ( ! class_exists( 'DOMDocument' ) ) { tzil_fail( 'PHP DOM extension required.' ); }
		$checked = 0;
		$blue_seen=0;$blue_expected=count($state['blue_links']);$targets=array();
		foreach ( $state['sources'] as $source ) {
			$r = wp_remote_get( $source['url'], array( 'redirection'=>0,'timeout'=>25 ) );
			if ( is_wp_error($r) || wp_remote_retrieve_response_code($r) !== 200 ) { tzil_fail( 'Source not direct 200: '.$source['url'] ); }
			$d = new DOMDocument(); $old = libxml_use_internal_errors(true); $d->loadHTML(wp_remote_retrieve_body($r),LIBXML_NONET); libxml_clear_errors();libxml_use_internal_errors($old);$x=new DOMXPath($d);
			$nodes=$x->query('//main//a');
			foreach($source['links'] as $link){$expected=($link['activation']??'immediate')!=='master-published'||!empty($state['master_verified']);if(!$expected){continue;}$targets[$link['target']]=true;$count=0;foreach($nodes as $a){if(tzil_url_key($a->getAttribute('href'))===tzil_url_key($link['target'])){if(trim($a->textContent)!==$link['anchor']){tzil_fail('Wrong anchor: '.$source['url']);}++$count;}}if($count!==1){tzil_fail('Expected one editorial target: '.$source['url'].' -> '.$link['target']);}++$checked;}
			foreach($x->query('//main//a') as $a){foreach($state['blue_links'] as $blue){if((int)$blue['source_id']===(int)$source['id']&&$a->getAttribute('href')===$blue['target']&&trim($a->textContent)===$blue['anchor']){++$blue_seen;break;}}}
		}
		if($blue_seen!==(!empty($state['remove_bluethesis'])?0:$blue_expected)){tzil_fail('BlueThesis count differs from captured audit/flag: '.$blue_seen);}
		foreach(array_keys($targets) as $target){tzil_direct_target($target);}
		return array('verified'=>true,'editorial_links_checked'=>$checked,'blue_links'=>$blue_seen,'master_status'=>$master->post_status,'remove_bluethesis'=>$state['remove_bluethesis']);
	}
	if ( $mode === 'rollback' ) {
		if ( ! $journal ) { tzil_fail( 'No backup journal.' ); }
		if ( ( $journal['phase'] ?? '' ) === 'rolled-back' ) { return array( 'unchanged'=>true,'phase'=>'rolled-back' ); }
		$partial = $state === null && in_array($journal['phase']??'',array('prepared','draft-created'),true);
		if ( !$partial && ( $state === null || ($journal['after_option_hash']??'') !== tzil_hash( $state ) ) ) { tzil_fail( 'Newer option edits detected; refusing rollback.' ); }
		if($partial && empty($journal['created_master_id'])){if($apply){$journal['phase']='rolled-back';tzil_write_journal($journal_file,$journal);}return array('dry_run'=>!$apply,'unchanged'=>true,'phase'=>'prepared-with-no-writes');}
		if ( tzil_hash( tzil_post_snapshot( $journal['created_master_id'] ) ) !== $journal['after_post_hash'] ) { tzil_fail( 'Newer master page edits detected; refusing rollback.' ); }
		if ( get_posts( array('post_type'=>'any','post_status'=>'any','post_parent'=>$journal['created_master_id'],'numberposts'=>1) ) || get_comments( array('post_id'=>$journal['created_master_id'],'count'=>true) ) ) { tzil_fail( 'Master has child content or comments; refusing deletion.' ); }
		if ( ! $apply ) { return array( 'dry_run'=>true,'plan'=>'Remove package option and delete only the unchanged package-created draft. Runtime files use installer rollback separately.' ); }
		if($state !== null){delete_option( TZIL_OPTION );}
		if ( ! wp_delete_post( $journal['created_master_id'], true ) ) { if($state!==null){update_option( TZIL_OPTION, $state, false );} tzil_fail( 'Could not remove owned draft; option restored.' ); }
		$journal['phase']='rolled-back'; tzil_write_journal($journal_file,$journal); return array('rolled_back'=>true);
	}
	if ( $mode !== 'apply' ) { tzil_fail( 'Unknown operation.' ); }
	if ( ! class_exists( 'DOMDocument' ) ) { tzil_fail( 'PHP DOM extension required.' ); }
	$parent=get_post((int)$manifest['master']['parent_id']);
	if(!$parent||$parent->post_type!=='page'||$parent->post_status!=='publish'||rtrim(get_permalink($parent->ID),'/')!==rtrim(tzil_local_url('https://teznevise.ir/thesis/'),'/')){tzil_fail('Master parent ID no longer identifies the published thesis page.');}
	foreach($local_manifest['sources'] as $source){$post=get_post((int)$source['id']);if(!$post||$post->post_status!=='publish'||rtrim(get_permalink($post->ID),'/')!==rtrim($source['url'],'/')){tzil_fail('Source ID, status or canonical path drifted: '.$source['url']);}}
	if ( $journal && ($journal['phase']??'') === 'rolled-back' ) { tzil_fail( 'Use a fresh private backup directory after rollback.' ); }
	if ( $state !== null && ( ! $journal || !is_array($state) || ($state['package_hash']??'') !== $package_hash || ($journal['after_option_hash']??'') !== tzil_hash($state) ) ) { tzil_fail( 'Unowned or newer option state.' ); }
	if ( $master && ( ! $journal || (int)$master->ID !== (int)($journal['created_master_id']??0) ) ) { tzil_fail( 'Existing master page: consolidate after editorial review; do not create a duplicate.' ); }
	foreach ( get_posts( array('post_type'=>'page','post_status'=>array('publish','draft','private','pending'),'numberposts'=>-1) ) as $page ) {
		if ( $master && (int)$page->ID === (int)$master->ID ) { continue; }
		if ( str_contains( str_replace("\u{200C}",' ',$page->post_title), 'انجام پایان نامه کارشناسی ارشد' ) ) { tzil_fail('Another page owns the master keyword: '.$page->ID.'; consolidate instead.'); }
	}
	if ( $state && !$remove && !empty($state['remove_bluethesis']) ) { tzil_fail('Flagged removal already active; keep --remove-bluethesis on repeat apply or roll back first.'); }
	if ( $master_links ) { if (!$master || $master->post_status!=='publish') { tzil_fail('Master links require separate editorial publication first; this script never publishes.'); } tzil_direct_target(tzil_local_url('https://teznevise.ir/thesis/master/')); }
	if ( ! $apply ) { return array('dry_run'=>true,'sources'=>count($manifest['sources']),'create_master_draft'=>!$master,'remove_bluethesis'=>$remove,'activate_master_links'=>$master_links,'writes'=>0); }
	$runtime_file=WP_CONTENT_DIR.'/mu-plugins/teznevise-internal-links-20261110.php';
	if(!is_file($runtime_file)||hash_file('sha256',$runtime_file)!==hash_file('sha256',__DIR__.'/runtime.php')){tzil_fail('Install the exact dormant MU runtime with install.sh before activating data.');}
	$targets=array();foreach($local_manifest['sources'] as $source){foreach($source['links'] as $link){if(($link['activation']??'immediate')==='immediate'){$targets[$link['target']]=true;}}}
	foreach(array_keys($targets) as $target){tzil_direct_target($target);}
	if ( ! add_option('tzil_20261110_lock',array('time'=>time()),'','no') ) { tzil_fail('Package lock exists; inspect interrupted run before continuing.'); }
	try {
		$directory=tzil_private_directory($positionals[0],true);
		if(!$journal){$journal=array('package_hash'=>$package_hash,'phase'=>'prepared','before_option'=>null,'created_master_id'=>null);tzil_write_journal($journal_file,$journal);}
		if(!$master){
			$id=wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_parent'=>(int)$manifest['master']['parent_id'],'post_name'=>'master','post_title'=>$manifest['master']['title'],'post_content'=>$content,'meta_input'=>array('_tzil_created'=>$package_hash)),true);
			if(is_wp_error($id)){tzil_fail($id->get_error_message());}
			$journal['created_master_id']=(int)$id;$journal['after_post_hash']=tzil_hash(tzil_post_snapshot($id));$journal['phase']='draft-created';tzil_write_journal($journal_file,$journal);$master=get_post($id);
		}
		$next=array('active'=>true,'package_hash'=>$package_hash,'sources'=>$local_manifest['sources'],'blue_links'=>$local_manifest['blue_links']??array(),'remove_bluethesis'=>$remove,'master_id'=>(int)$master->ID,'master_verified'=>$master_links||!empty($state['master_verified']));
		if($state && tzil_hash($next)===tzil_hash($state)){tzil_write_journal($journal_file,$journal);return array('unchanged'=>true,'master_status'=>$master->post_status);}
		$journal['planned_option']=$next;$journal['phase']='activating';tzil_write_journal($journal_file,$journal);
		update_option(TZIL_OPTION,$next,false);
		if(tzil_hash(get_option(TZIL_OPTION))!==tzil_hash($next)){tzil_fail('Option readback failed; retain journal.');}
		$journal['after_option_hash']=tzil_hash($next);$journal['phase']='applied';tzil_write_journal($journal_file,$journal);
		return array('applied'=>true,'master_id'=>(int)$master->ID,'master_status'=>$master->post_status,'sources'=>count($next['sources']),'remove_bluethesis'=>$remove);
	} finally { delete_option('tzil_20261110_lock'); }
}
if ( ! defined('TZIL_TEST_MODE') ) {
	try { $operation = array_shift($args); WP_CLI::log(tzil_json(tzil_dispatch($operation,$args))); }
	catch (Throwable $error) { WP_CLI::error($error->getMessage()); }
}
