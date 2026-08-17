<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: maziyarid/M-Z — A brand new repository with my complete brand identity, story, and website prototype.
 * https://github.com/maziyarid/M-Z
 *
 * Theme helpers.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fallback primary menu when no menu is assigned.
 */
function teznevise_fallback_primary_menu() {
	$items = array(
		array( 'url' => home_url( '/' ), 'label' => __( 'خانه', 'teznevise' ), 'active' => is_front_page() ),
		array( 'url' => home_url( '/service-thesis/' ), 'label' => __( 'خدمات', 'teznevise' ), 'active' => is_page( array( 'service-thesis', 'service-proposal', 'service-statistics', 'service-simulation' ) ) ),
		array( 'url' => home_url( '/tools/' ), 'label' => __( 'ابزارها', 'teznevise' ), 'active' => is_page( 'tools' ) || is_page_template( 'page-tool.php' ) ),
		array( 'url' => home_url( '/blog/' ), 'label' => __( 'بلاگ', 'teznevise' ), 'active' => is_home() || is_singular( 'post' ) ),
		array( 'url' => home_url( '/about/' ), 'label' => __( 'درباره ما', 'teznevise' ), 'active' => is_page( 'about' ) ),
	);
	echo '<ul class="nav-links">';
	foreach ( $items as $item ) {
		$cls = $item['active'] ? ' class="active"' : '';
		printf(
			'<li><a%s href="%s">%s</a></li>',
			$cls,
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Safe theme asset URL helper.
 *
 * @param string $relative_path Relative path.
 * @return string
 */
function teznevise_asset( $relative_path ) {
	$resolved = teznevise_resolve_asset( $relative_path );
	return $resolved ? $resolved['url'] : ( TEZNEVISE_URI . '/' . ltrim( $relative_path, '/' ) );
}

/**
 * Parse multiline structured lists: title|url|desc|icon
 * Empty lines skipped. Missing parts get empty string.
 *
 * @param string $raw Raw textarea.
 * @param int    $cols Expected columns (default 4).
 * @return array<int,array<int,string>>
 */
function teznevise_parse_pipe_list( $raw, $cols = 4 ) {
	$out = array();
	$raw = (string) $raw;
	if ( '' === trim( $raw ) ) {
		return $out;
	}
	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line || 0 === strpos( $line, '#' ) ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line ) );
		while ( count( $parts ) < $cols ) {
			$parts[] = '';
		}
		$out[] = array_slice( $parts, 0, $cols );
	}
	return $out;
}

/**
 * Echo escaped HTML or a safe fallback when empty.
 *
 * @param string $value Value.
 * @param string $fallback Fallback.
 * @param string $tag Optional wrap tag (e.g. p, span). Empty = raw text only.
 */
function teznevise_echo_or( $value, $fallback = '', $tag = '' ) {
	$text = ( '' !== trim( (string) $value ) ) ? $value : $fallback;
	if ( '' === trim( (string) $text ) ) {
		return;
	}
	if ( $tag ) {
		printf( '<%1$s>%2$s</%1$s>', tag_escape( $tag ), esc_html( $text ) );
		return;
	}
	echo esc_html( $text );
}

/**
 * Convert legacy .html links in content to WordPress paths when possible.
 *
 * @param string $content Content.
 * @return string
 */
function teznevise_rewrite_static_links( $content ) {
	$map = array(
		'index.html'                      => '/',
		'about.html'                      => '/about/',
		'contact.html'                    => '/contact/',
		'privacy.html'                    => '/privacy/',
		'team.html'                       => '/team/',
		'tools.html'                      => '/tools/',
		'downloads.html'                  => '/downloads/',
		'inquiry.html'                    => '/inquiry/',
		'blog.html'                       => '/blog/',
		'service-thesis.html'             => '/service-thesis/',
		'service-proposal.html'           => '/service-proposal/',
		'service-statistics.html'         => '/service-statistics/',
		'service-simulation.html'         => '/service-simulation/',
		'tool-descriptive-statistics.html'=> '/tool-descriptive-statistics/',
	);
	foreach ( $map as $html => $path ) {
		$content = str_replace( array( 'href="' . $html . '"', "href='" . $html . "'" ), 'href="' . esc_url( home_url( $path ) ) . '"', $content );
		$content = str_replace( array( 'href="./' . $html . '"', 'href="/' . $html . '"' ), 'href="' . esc_url( home_url( $path ) ) . '"', $content );
	}
	return $content;
}
add_filter( 'the_content', 'teznevise_rewrite_static_links', 12 );

/**
 * Discourage public indexing of legacy static HTML under teznevise_work.
 *
 * @param array $robots Robots.
 * @return array
 */
function teznevise_robots_noindex_work( $robots ) {
	if ( isset( $_SERVER['REQUEST_URI'] ) && false !== strpos( (string) $_SERVER['REQUEST_URI'], '/teznevise_work/' ) ) {
		$robots['noindex'] = true;
		$robots['nofollow'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'teznevise_robots_noindex_work' );
