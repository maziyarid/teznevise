<?php
/**
 * Technical SEO foundations with plugin-aware fallbacks.
 *
 * WordPress core remains the owner of title tags, canonical markup, and
 * the XML sitemap. The theme supplies contextual descriptions, social
 * metadata, robots controls, and schema only when a major SEO plugin is absent.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || function_exists( 'seopress_init' );
}

function teznevise_seo_description() {
	$description = '';
	if ( is_front_page() ) {
		$description = function_exists( 'teznevise_mod' ) ? teznevise_mod( 'hero_text', get_bloginfo( 'description' ) ) : get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$post_id = get_queried_object_id();
		$description = get_post_field( 'post_excerpt', $post_id );
		if ( ! $description ) {
			$description = get_post_field( 'post_content', $post_id );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
	} elseif ( is_home() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		$description = $posts_page ? get_post_field( 'post_excerpt', $posts_page ) : get_bloginfo( 'description' );
	} elseif ( is_search() ) {
		$description = sprintf( __( 'Search results for %s', 'teznevise' ), get_search_query() );
	} else {
		$description = get_bloginfo( 'description' );
	}
	$description = wp_strip_all_tags( strip_shortcodes( (string) $description ) );
	$description = trim( preg_replace( '/\s+/u', ' ', $description ) );
	return function_exists( 'mb_substr' ) ? mb_substr( $description, 0, 160 ) : substr( $description, 0, 160 );
}

function teznevise_seo_canonical() {
	if ( is_search() || is_404() ) {
		return false;
	}
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_home() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		return $posts_page ? get_permalink( $posts_page ) : home_url( '/' );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? false : $link;
	}
	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_query_var( 'post_type' ) );
	}
	if ( is_paged() ) {
		return get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
	}
	return false;
}

function teznevise_seo_output_head() {
	if ( is_admin() || teznevise_seo_plugin_active() ) {
		return;
	}
	$description = teznevise_seo_description();
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	$title = wp_get_document_title();
	$url   = teznevise_seo_canonical();
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
	}
	if ( ! $image && function_exists( 'teznevise_logo_url' ) ) {
		$image = teznevise_logo_url();
	}
	echo '<meta property="og:locale" content="fa_IR">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'teznevise_seo_output_head', 1 );

function teznevise_seo_canonical_filter( $canonical ) {
	if ( teznevise_seo_plugin_active() ) {
		return $canonical;
	}
	$theme_canonical = teznevise_seo_canonical();
	return $theme_canonical ? esc_url_raw( $theme_canonical ) : false;
}
add_filter( 'get_canonical_url', 'teznevise_seo_canonical_filter' );

/**
 * 301 competing URLs onto one canonical slug (TZ-004).
 */
function teznevise_alias_redirects() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_cron() ) {
		return;
	}
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$path = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
	$path = untrailingslashit( $path );
	$map  = array(
		'/contact'            => array( 'contact-us' ),
		'/team'               => array( 'our-team' ),
		'/privacy-policy'     => array( 'privacy' ),
		'/service-thesis'     => array( 'thesis' ),
		'/service-proposal'   => array( 'proposal' ),
		'/statistics'         => array( 'service-statistics' ),
		'/posts'              => array( 'blog' ),
		'/tools'              => array( 'online-calculation-tools' ),
	);
	if ( ! isset( $map[ $path ] ) ) {
		return;
	}
	foreach ( $map[ $path ] as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' === $page->post_status && $page->post_name !== trim( $path, '/' ) ) {
			wp_safe_redirect( get_permalink( $page ), 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'teznevise_alias_redirects', 1 );

function teznevise_schema_data() {
	if ( teznevise_seo_plugin_active() ) {
		return array();
	}
	$graph = array(
		array(
			'@type'       => 'WebSite',
			'@id'         => home_url( '/#website' ),
			'url'         => home_url( '/' ),
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
		),
	);
	if ( is_singular( 'post' ) ) {
		$post_id = get_queried_object_id();
		$author_id = (int) get_post_field( 'post_author', $post_id );
		$publisher = array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) );
		if ( function_exists( 'teznevise_logo_url' ) && teznevise_logo_url() ) {
			$publisher['logo'] = array( '@type' => 'ImageObject', 'url' => teznevise_logo_url() );
		}
		$article = array(
			'@type'            => 'Article',
			'@id'              => get_permalink( $post_id ) . '#article',
			'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink( $post_id ) ),
			'headline'         => wp_strip_all_tags( get_the_title( $post_id ) ),
			'datePublished'    => get_the_date( DATE_W3C, $post_id ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
			'author'           => array( '@type' => 'Organization', 'name' => function_exists( 'teznevise_public_author_name' ) ? teznevise_public_author_name( $author_id ) : get_bloginfo( 'name' ) ),
			'publisher'        => $publisher,
			'description'      => teznevise_seo_description(),
		);
		if ( has_post_thumbnail( $post_id ) ) {
			$article['image'] = get_the_post_thumbnail_url( $post_id, 'full' );
		}
		$graph[] = $article;
	}
	if ( ! is_front_page() && ! is_search() && ! is_404() ) {
		$canonical = teznevise_seo_canonical();
		if ( $canonical ) {
			$graph[] = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $canonical . '#breadcrumbs',
				'itemListElement' => array(
					array( '@type' => 'ListItem', 'position' => 1, 'name' => get_bloginfo( 'name' ), 'item' => home_url( '/' ) ),
					array( '@type' => 'ListItem', 'position' => 2, 'name' => wp_strip_all_tags( wp_get_document_title() ), 'item' => $canonical ),
				),
			);
		}
	}
	return array( '@context' => 'https://schema.org', '@graph' => $graph );
}

function teznevise_output_schema() {
	$data = teznevise_schema_data();
	if ( empty( $data ) ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'teznevise_output_schema', 20 );

function teznevise_robots( $robots ) {
	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'teznevise_robots' );

function teznevise_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	$sitemap = home_url( '/wp-sitemap.xml' );
	if ( false === strpos( $output, $sitemap ) ) {
		$output .= "\nSitemap: {$sitemap}\n";
	}
	return $output;
}
add_filter( 'robots_txt', 'teznevise_robots_txt', 10, 2 );

function teznevise_language_attributes( $output ) {
	if ( stripos( $output, 'lang=' ) === false ) {
		$output .= ' lang="fa-IR"';
	}
	return $output;
}
add_filter( 'language_attributes', 'teznevise_language_attributes' );
