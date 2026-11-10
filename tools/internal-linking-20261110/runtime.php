<?php
/** Dormant, independently installed Teznevise internal-link renderer. */
if ( ! function_exists( 'tzil_transform' ) ) {
function tzil_url_key( $url ) {
	$p = parse_url( html_entity_decode( $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	if ( false === $p || isset( $p['query'] ) || isset( $p['fragment'] ) ) { return ''; }
	if ( isset( $p['scheme'] ) && ! in_array( strtolower($p['scheme']), array('http','https'), true ) ) { return ''; }
	if ( !isset($p['host']) && !str_starts_with($url,'/') ) { return ''; }
	$hosts=array('teznevise.ir');if(function_exists('home_url')){$hosts[]=strtolower((string)parse_url(home_url('/'),PHP_URL_HOST));}
	if ( isset( $p['host'] ) && !in_array(strtolower($p['host']),$hosts,true) ) { return ''; }
	return '/' . trim( $p['path'] ?? '/', '/' ) . '/';
}
function tzil_unwrap( $node ) {
	$parent = $node->parentNode;
	while ( $node->firstChild ) { $parent->insertBefore( $node->firstChild, $node ); }
	$parent->removeChild( $node );
}
function tzil_transform( $html, $source, $state ) {
	if ( empty( $state['active'] ) || ! class_exists( 'DOMDocument' ) || stripos( $html, '<html' ) === false ) { return $html; }
	$dom = new DOMDocument(); $previous = libxml_use_internal_errors( true );
	$loaded = $dom->loadHTML( '<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors(); libxml_use_internal_errors( $previous );
	if ( ! $loaded ) { return $html; }
	$xpath = new DOMXPath( $dom );
	$roots = iterator_to_array($xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " article-content ") or contains(concat(" ", normalize-space(@class), " "), " longcopy ")]' ));
	$main = $xpath->query('//main')->item(0);
	if ( !$roots && !$main ) { return $html; }
	if ( !$roots ) { $roots=array($main); }
	$anchors = array(); $editorial = array(); $destination = $roots[0]; $longest = -1;
	foreach ( $roots as $root ) {
		$length = strlen( trim( $root->textContent ) );
		if ( $length > $longest ) { $longest = $length; $destination = $root; }
		foreach ( $root->getElementsByTagName( 'a' ) as $a ) { $editorial[ spl_object_id( $a ) ] = $a; }
	}
	if ( $main ) { foreach($main->getElementsByTagName('a') as $a) { $anchors[spl_object_id($a)]=$a; } }
	else { $anchors=$editorial; }
	$changed = false;
	foreach ( $source['links'] ?? array() as $link ) {
		if ( ( $link['activation'] ?? 'immediate' ) === 'master-published' && empty( $state['master_ready'] ) ) { continue; }
		$key = tzil_url_key( $link['target'] ); if ( ! $key ) { continue; }
		$found = array(); $keep = null;
		foreach ( $anchors as $a ) {
			if ( $a->parentNode && tzil_url_key( $a->getAttribute( 'href' ) ) === $key ) {
				$found[] = $a;
				if ( null === $keep && isset($editorial[spl_object_id($a)]) && trim( $a->textContent ) === $link['anchor'] ) { $keep = $a; }
			}
		}
		foreach ( $found as $a ) { if ( $a !== $keep ) { tzil_unwrap( $a ); $changed = true; } }
		if ( null !== $keep ) { if($keep->getAttribute('href')!==$link['target']){$keep->setAttribute('href',$link['target']);$changed=true;} continue; }
		$parts = explode( '{anchor}', $link['context'] );
		if ( count( $parts ) !== 2 ) { continue; }
		$p = $dom->createElement( 'p' ); $p->setAttribute( 'data-tzil-target', $key ); $p->setAttribute('lang','fa');$p->setAttribute('dir','rtl');
		$p->appendChild( $dom->createTextNode( $parts[0] ) );
		$a = $dom->createElement( 'a' ); $a->setAttribute( 'href', $link['target'] );
		$a->appendChild( $dom->createTextNode( $link['anchor'] ) ); $p->appendChild( $a );
		$p->appendChild( $dom->createTextNode( $parts[1] ) ); $destination->appendChild( $p ); $changed = true;
	}
	if ( ! empty( $state['remove_bluethesis'] ) ) {
		foreach ( $anchors as $a ) {
			if ( ! $a->parentNode ) { continue; }
			foreach ( $state['blue_links'] ?? array() as $row ) {
				if ( (int) $row['source_id'] !== (int) $source['id'] ) { continue; }
				if ( html_entity_decode( $a->getAttribute( 'href' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) === $row['target'] && trim( $a->textContent ) === $row['anchor'] ) {
					tzil_unwrap( $a ); $changed = true; break;
				}
			}
		}
	}
	if ( ! $changed ) { return $html; }
	foreach ( iterator_to_array( $dom->childNodes ) as $child ) { if ( $child->nodeType === XML_PI_NODE ) { $dom->removeChild( $child ); } }
	return $dom->saveHTML();
}
function tzil_conflicting_package() {
	foreach ( array( 'teznevise_onpage_20261110', 'teznevise_gsc_20261110', 'teznevise_gsc_remediation_20261110' ) as $key ) {
		$value = get_option( $key, false );
		if(is_array($value)){if(!empty($value['enabled'])||!empty($value['active'])||($value&&!array_key_exists('enabled',$value)&&!array_key_exists('active',$value))){return $key;}}
		elseif($value){return $key;}
	}
	return false;
}
function tzil_begin_render() {
	if ( is_admin() || is_feed() || is_preview() || ! is_singular() || tzil_conflicting_package() ) { return; }
	$state = get_option( 'teznevise_internal_links_20261110', array() );
	if ( empty( $state['active'] ) ) { return; }
	$id = (int) get_queried_object_id(); $source = null;
	foreach ( $state['sources'] ?? array() as $row ) { if ( (int) $row['id'] === $id ) { $source = $row; break; } }
	if ( ! $source && !empty($state['remove_bluethesis']) ) { foreach($state['blue_links']??array() as $row){if((int)$row['source_id']===$id){$source=array('id'=>$id,'links'=>array());break;}} }
	if ( ! $source ) { return; }
	if(tzil_url_key(get_permalink($id))!==tzil_url_key($source['url']??get_permalink($id))){return;}
	$master = get_page_by_path( 'thesis/master', OBJECT, 'page' );
	$state['master_ready'] = ! empty( $state['master_verified'] ) && $master && $master->post_status === 'publish' && (int) $master->ID === (int) ( $state['master_id'] ?? 0 );
	ob_start( static function ( $html ) use ( $source, $state ) { return tzil_transform( $html, $source, $state ); } );
}
if ( function_exists( 'add_action' ) ) { add_action( 'template_redirect', 'tzil_begin_render', 999 ); }
}
