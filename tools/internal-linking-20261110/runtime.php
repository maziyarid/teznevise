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
function tzil_anchor_key( $url ) { return tzil_url_key(preg_split('/[?#]/', $url, 2)[0]); }
function tzil_unwrap( $node ) {
	$parent = $node->parentNode;
	while ( $node->firstChild ) { $parent->insertBefore( $node->firstChild, $node ); }
	$parent->removeChild( $node );
}
function tzil_anchor_label( $node, $label ) {
	if ( trim($node->textContent) === $label ) { return false; }
	$xpath = new DOMXPath($node->ownerDocument);
	$texts = iterator_to_array($xpath->query('.//text()', $node)); $first = null;
	foreach ($texts as $text) { if ($first === null && trim($text->nodeValue) !== '') { $first = $text; } }
	if ($first === null) { $node->appendChild($node->ownerDocument->createTextNode($label)); }
	else { foreach ($texts as $text) { $text->nodeValue = $text === $first ? $label : ''; } }
	return true;
}
function tzil_complex_anchor( $node ) {
	if ( count(preg_split('/\\s+/u', trim($node->textContent), -1, PREG_SPLIT_NO_EMPTY)) > 12 ) { return true; }
	$xpath = new DOMXPath($node->ownerDocument);
	$nonempty = 0;
	foreach ($xpath->query('.//text()', $node) as $text) { if (trim($text->nodeValue) !== '' && ++$nonempty > 1) { return true; } }
	foreach ($node->getElementsByTagName('*') as $child) { if (in_array(strtolower($child->tagName),array('p','div','h1','h2','h3','h4','h5','h6','ul','ol','li','table','img','picture'),true)) { return true; } }
	return false;
}
function tzil_append_context( $dom, $destination, $link, $anchor, $target, $primary ) {
	$parts=explode('{anchor}', $link['context'] ?? 'برای ادامه مسیر پژوهش، {anchor} را مرور کنید.');
	if(count($parts)!==2){return false;}
	$p=$dom->createElement('p');$p->setAttribute('data-tzil-target',tzil_url_key($target));$p->setAttribute('lang','fa');$p->setAttribute('dir','rtl');
	$p->appendChild($dom->createTextNode($parts[0]));$a=$dom->createElement('a');$a->setAttribute('href',$target);
	if($primary){$a->setAttribute('data-tzil-primary',tzil_url_key($target));}
	$a->appendChild($dom->createTextNode($anchor));$p->appendChild($a);$p->appendChild($dom->createTextNode($parts[1]));$destination->appendChild($p);return true;
}
function tzil_transform( $html, $source, $state ) {
	if ( empty( $state['active'] ) || empty($state['anchor_policy']) || ! class_exists( 'DOMDocument' ) || stripos( $html, '<html' ) === false ) { return $html; }
	$dom=new DOMDocument();$previous=libxml_use_internal_errors(true);
	$loaded=$dom->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING);libxml_clear_errors();libxml_use_internal_errors($previous);
	if(!$loaded){return $html;}$xpath=new DOMXPath($dom);$main=$xpath->query('//main')->item(0);if(!$main){return $html;}
	$roots=iterator_to_array($xpath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," article-content ") or contains(concat(" ",normalize-space(@class)," ")," longcopy ")]', $main));
	if(!$roots){$roots=array($main);}$anchors=iterator_to_array($main->getElementsByTagName('a'));$editorial=array();$destination=$roots[0];$longest=-1;
	foreach($roots as $root){$length=strlen(trim($root->textContent));if($length>$longest){$longest=$length;$destination=$root;}foreach($root->getElementsByTagName('a') as $a){$editorial[spl_object_id($a)]=true;}}
	$selected=array();foreach($source['links']??array() as $link){$selected[tzil_url_key($link['target'])]=$link;}
	$policies=array();foreach($state['anchor_policy']??array() as $policy){$policies[tzil_url_key($policy['target'])]=$policy;}
	$changed=false;
	foreach($policies as $key=>$policy){
		$found=array_values(array_filter($anchors,static fn($a)=>$a->parentNode&&tzil_anchor_key($a->getAttribute('href'))===$key));
		if(($policy['activation']??'immediate')==='master-published'&&empty($state['master_ready'])){foreach($found as $a){tzil_unwrap($a);$changed=true;}continue;}
		$link=$selected[$key]??null;$primary=null;
		if($link){
			foreach($found as $a){if(isset($editorial[spl_object_id($a)])&&!tzil_complex_anchor($a)&&$a->getAttribute('data-tzil-primary')===$key){$primary=$a;break;}}
			if(!$primary){foreach($found as $a){if(isset($editorial[spl_object_id($a)])&&!tzil_complex_anchor($a)&&trim($a->textContent)===$link['anchor']){$primary=$a;break;}}}
			if(!$primary){foreach($found as $a){if(isset($editorial[spl_object_id($a)])&&!tzil_complex_anchor($a)){$primary=$a;break;}}}
			if(!$primary&&$found){$primary=$found[0];}
		}
		$lsi=$link['lsi_phrases']??($policy['secondary_lsi_phrases']??$policy['lsi_phrases']);$index=(int)$source['id'];
		foreach($found as $a){
			$is_primary=$a===$primary;$label=$is_primary?$link['anchor']:(in_array(trim($a->textContent),$lsi,true)?trim($a->textContent):$lsi[$index%count($lsi)]);++$index;
			if(tzil_complex_anchor($a)||($is_primary&&!isset($editorial[spl_object_id($a)]))){tzil_unwrap($a);tzil_append_context($dom,$destination,$link??array(),$label,$policy['target'],$is_primary);$changed=true;continue;}
			if(tzil_anchor_label($a,$label)){$changed=true;}
			if($a->getAttribute('href')!==$policy['target']){$a->setAttribute('href',$policy['target']);$changed=true;}
			if($is_primary){if($a->getAttribute('data-tzil-primary')!==$key){$a->setAttribute('data-tzil-primary',$key);$changed=true;}}
			elseif($a->hasAttribute('data-tzil-primary')){$a->removeAttribute('data-tzil-primary');$changed=true;}
		}
		if($link&&!$primary){tzil_append_context($dom,$destination,$link,$link['anchor'],$policy['target'],true);$changed=true;}
	}
	// The three non-money verified relationships retain their exact single-link contract.
	foreach($selected as $key=>$link){
		if(isset($policies[$key])){continue;}
		if(($link['activation']??'immediate')==='master-published'&&empty($state['master_ready'])){continue;}
		$found=array();$keep=null;foreach($anchors as $a){if($a->parentNode&&tzil_anchor_key($a->getAttribute('href'))===$key){$found[]=$a;if(!$keep&&isset($editorial[spl_object_id($a)])&&trim($a->textContent)===$link['anchor']){$keep=$a;}}}
		foreach($found as $a){if($a!==$keep){tzil_unwrap($a);$changed=true;}}
		if($keep){if($keep->getAttribute('href')!==$link['target']){$keep->setAttribute('href',$link['target']);$changed=true;}}
		else{tzil_append_context($dom,$destination,$link,$link['anchor'],$link['target'],true);$changed=true;}
	}
	if(!empty($state['remove_bluethesis'])){foreach($anchors as $a){if(!$a->parentNode){continue;}foreach($state['blue_links']??array() as $row){if((int)$row['source_id']===(int)$source['id']&&html_entity_decode($a->getAttribute('href'),ENT_QUOTES|ENT_HTML5,'UTF-8')===$row['target']&&trim($a->textContent)===$row['anchor']){tzil_unwrap($a);$changed=true;break;}}}}
	if(!$changed){return $html;}foreach(iterator_to_array($dom->childNodes) as $child){if($child->nodeType===XML_PI_NODE){$dom->removeChild($child);}}return $dom->saveHTML();
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
