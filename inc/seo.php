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
		$post_id     = get_queried_object_id();
		$description = get_post_field( 'post_excerpt', $post_id );
		if ( ! $description ) {
			$description = get_post_field( 'post_content', $post_id );
		}
		$plain = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( (string) $description ) ) ) );
		if ( '' === $plain && function_exists( 'teznevise_page_classic_source' ) ) {
			$description = teznevise_page_classic_source( $post_id );
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
 * Canonical path for a competing slug (TZ-004). Short/alias → long published URL.
 *
 * @return array<string,string> Untrailingslashed request path => canonical path with trailing slash.
 */
function teznevise_request_alias_map() {
	return array(
		'/about'                       => '/about-us/',
		'/team'                        => '/our-team/',
		'/contact'                     => '/contact-us/',
		'/privacy'                     => '/privacy-policy/',
		'/terms'                       => '/terms-and-conditions/',
		'/cookies'                     => '/cookie-policy/',
		'/refund'                      => '/refund-policy/',
		'/tools'                       => '/online-calculation-tools/',
		'/service-thesis'              => '/thesis/',
		'/service-proposal'            => '/proposal/',
		'/statistics'                  => '/service-statistics/',
		'/order'                       => '/inquiry/',
		'/posts'                       => '/blog/',
		'/download'                    => '/downloads/',
		'/tool-pearson-correlation'    => '/online-calculation-tools/pearson-correlation-calculator/',
		'/tool-spearman'               => '/online-calculation-tools/spearman-correlation-calculator/',
		'/tool-ttest'                  => '/online-calculation-tools/t-test-calculator/',
		'/tool-anova'                  => '/online-calculation-tools/anova-calculator/',
		'/tool-chi-square'             => '/online-calculation-tools/chi-square-calculator/',
		'/tool-regression'             => '/online-calculation-tools/regression-calculator/',
		'/tool-cronbach-alpha'         => '/online-calculation-tools/cronbachs-alpha-calculator/',
		'/tool-sample-size'            => '/online-calculation-tools/sample-size-calculator/',
		'/tool-power-analysis'         => '/online-calculation-tools/power-analysis-calculator/',
		'/tool-content-validity'       => '/online-calculation-tools/content-validity-calculator/',
		'/tool-mann-whitney'           => '/online-calculation-tools/mann-whitney-calculator/',
		'/tool-wilcoxon'               => '/online-calculation-tools/wilcoxon-calculator/',
		'/tool-kruskal-wallis'         => '/online-calculation-tools/kruskal-wallis-calculator/',
		'/tool-descriptive-statistics' => '/online-calculation-tools/descriptive-statistics-calculator/',
	);
}

/**
 * Page slugs that must 301 onto a different canonical path.
 *
 * @return array<string,string>
 */
function teznevise_alias_page_slugs() {
	$map = array();
	foreach ( teznevise_request_alias_map() as $from => $to ) {
		$map[ trim( $from, '/' ) ] = $to;
	}
	return $map;
}

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
	if ( '' === $path || '/' === $path ) {
		return;
	}

	$target = '';

	if ( '/posts' === $path ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		$target     = $posts_page ? get_permalink( $posts_page ) : home_url( '/blog/' );
	}

	$map = teznevise_request_alias_map();
	if ( ! $target && isset( $map[ $path ] ) ) {
		$canon  = $map[ $path ];
		$page   = get_page_by_path( trim( $canon, '/' ) );
		$target = ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : home_url( $canon );
	}

	if ( ! $target && preg_match( '#^/([a-z0-9-]+-calculator)$#', $path, $m ) && 'price-calculator' !== $m[1] ) {
		$nested = 'online-calculation-tools/' . $m[1];
		$page   = get_page_by_path( $nested );
		$target = ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : home_url( '/' . $nested . '/' );
	}

	if ( $target ) {
		$dest_path = untrailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) );
		if ( $dest_path && $dest_path !== $path ) {
			wp_safe_redirect( $target, 301 );
			exit;
		}
	}
}
add_action( 'after_setup_theme', 'teznevise_alias_redirects', 0 );
add_action( 'init', 'teznevise_alias_redirects', -999 );
add_action( 'template_redirect', 'teznevise_alias_redirects', 0 );
if ( ! is_admin() && function_exists( 'wp_safe_redirect' ) ) {
	teznevise_alias_redirects();
}

/**
 * Attachment URLs and alias pages that survived the REQUEST_URI pass.
 */
function teznevise_template_seo_redirects() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}
	if ( is_attachment() ) {
		$parent = (int) get_post_field( 'post_parent', get_queried_object_id() );
		$dest   = $parent ? get_permalink( $parent ) : home_url( '/' );
		if ( $dest ) {
			wp_safe_redirect( $dest, 301 );
			exit;
		}
	}
	if ( ! is_page() ) {
		return;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$map  = teznevise_alias_page_slugs();
	if ( isset( $map[ $slug ] ) ) {
		$page = get_page_by_path( trim( $map[ $slug ], '/' ) );
		$dest = ( $page && (int) $page->ID !== (int) get_queried_object_id() ) ? get_permalink( $page ) : home_url( $map[ $slug ] );
		if ( $dest ) {
			wp_safe_redirect( $dest, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'teznevise_template_seo_redirects', 1 );

/**
 * Yoast SEO Premium 301s /posts/ → /thesis-journey/ on plugins_loaded,
 * before the theme runs. Remove stored rules that fight the canonical map.
 */
function teznevise_purge_yoast_posts_redirect() {
	if ( '1.9.31' === get_option( 'teznevise_purged_yoast_posts' ) ) {
		return;
	}
	$origins = array_keys( teznevise_request_alias_map() );
	$origins[] = '/posts';
	$opts      = array(
		'wpseo-premium-redirects-base',
		'wpseo-premium-redirects-export-plain',
		'wpseo-premium-redirects-export-regex',
	);
	foreach ( $opts as $opt ) {
		$val = get_option( $opt );
		if ( ! is_array( $val ) || ! $val ) {
			continue;
		}
		$changed = false;
		foreach ( $val as $origin => $row ) {
			$norm = untrailingslashit( '/' . ltrim( (string) $origin, '/' ) );
			if ( in_array( $norm, $origins, true ) ) {
				unset( $val[ $origin ] );
				$changed = true;
			}
		}
		if ( $changed ) {
			update_option( $opt, $val, false );
		}
	}
	update_option( 'teznevise_purged_yoast_posts', '1.9.31', false );
}
add_action( 'init', 'teznevise_purge_yoast_posts_redirect', -1000 );

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
		$post_id   = get_queried_object_id();
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

/**
 * Word count that is not Latin-only (Persian pages otherwise look empty).
 *
 * @param int $post_id Post ID.
 * @return int
 */
function teznevise_plain_word_count( $post_id ) {
	$html  = (string) get_post_field( 'post_content', $post_id );
	$plain = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( $html ) ) ) );
	if ( '' === $plain ) {
		return 0;
	}
	$parts = preg_split( '/\s+/u', $plain );
	return is_array( $parts ) ? count( $parts ) : 0;
}

/**
 * Explicit policy slugs that must be noindex (robots + sitemap).
 * Single source of truth for the nine support/policy pages.
 *
 * @return string[]
 */
function teznevise_explicit_noindex_slugs() {
	return array(
		'corporate-social-responsibility',
		'account',
		'join-us',
		'careers',
		'fair-use-policy',
		'revision-policy',
		'originality-guarantee',
		'service-commitments',
		'achievements',
	);
}

/**
 * Thin or competing URLs that must not be indexed.
 *
 * @return bool
 */
function teznevise_should_noindex() {
	if ( is_search() || is_404() || is_attachment() ) {
		return true;
	}
	if ( ! is_singular() ) {
		return false;
	}
	$post_id = get_queried_object_id();
	$slug    = get_post_field( 'post_name', $post_id );
	if ( isset( teznevise_alias_page_slugs()[ $slug ] ) ) {
		return true;
	}
	if ( in_array( $slug, teznevise_explicit_noindex_slugs(), true ) ) {
		return true;
	}
	$path = (string) wp_parse_url( (string) get_permalink( $post_id ), PHP_URL_PATH );
	if ( false !== strpos( $path, '/project/assignments/' ) && teznevise_plain_word_count( $post_id ) < 400 ) {
		return true;
	}
	return false;
}

function teznevise_robots( $robots ) {
	if ( teznevise_should_noindex() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'teznevise_robots' );
add_filter( 'wpseo_robots', 'teznevise_wpseo_robots_string', 20 );

/**
 * Yoast robots string: force noindex,follow on thin/alias URLs.
 *
 * @param string $robots Robots content.
 * @return string
 */
function teznevise_wpseo_robots_string( $robots ) {
	if ( ! teznevise_should_noindex() ) {
		return $robots;
	}
	return 'noindex, follow';
}

function teznevise_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	if ( defined( 'WPSEO_VERSION' ) || false !== strpos( (string) $output, 'sitemap_index.xml' ) ) {
		return $output;
	}
	$sitemap = home_url( '/wp-sitemap.xml' );
	if ( false === strpos( $output, $sitemap ) ) {
		$output .= "\nSitemap: {$sitemap}\n";
	}
	return $output;
}
add_filter( 'robots_txt', 'teznevise_robots_txt', 10, 2 );

/**
 * Drop image locs, attachment URLs, and alias pages from Yoast sitemaps.
 *
 * @param array|false $url    Sitemap entry.
 * @param string      $type   Object type.
 * @param object      $object Post/term object.
 * @return array|false
 */
function teznevise_wpseo_sitemap_entry( $url, $type, $object ) {
	if ( false === $url || ! is_array( $url ) ) {
		return $url;
	}
	$loc = isset( $url['loc'] ) ? (string) $url['loc'] : '';
	if ( false !== strpos( $loc, '/wp-content/uploads/' ) ) {
		return false;
	}
	if ( $object instanceof WP_Post ) {
		if ( 'attachment' === $object->post_type ) {
			return false;
		}
		$slug = $object->post_name;
		if ( isset( teznevise_alias_page_slugs()[ $slug ] ) ) {
			return false;
		}
		if ( in_array( $slug, teznevise_explicit_noindex_slugs(), true ) ) {
			return false;
		}
		$path = (string) wp_parse_url( $loc, PHP_URL_PATH );
		if ( false !== strpos( $path, '/project/assignments/' ) && teznevise_plain_word_count( $object->ID ) < 400 ) {
			return false;
		}
		if ( preg_match( '#^/(tool-[a-z0-9-]+|[a-z0-9-]+-calculator)/?$#', (string) untrailingslashit( $path ) ) && false === strpos( $path, '/online-calculation-tools/' ) && false === strpos( $path, 'price-calculator' ) ) {
			return false;
		}
	}
	if ( isset( $url['images'] ) ) {
			unset( $url['images'] );
	}
	return $url;
}
add_filter( 'wpseo_sitemap_entry', 'teznevise_wpseo_sitemap_entry', 20, 3 );
add_filter( 'wpseo_xml_sitemap_img', '__return_false' );

/**
 * Never list attachments as a sitemap post type.
 *
 * @param bool   $exclude Whether to exclude.
 * @param string $type    Post type.
 * @return bool
 */
function teznevise_wpseo_exclude_attachment_sitemap( $exclude, $type ) {
	if ( 'attachment' === $type ) {
		return true;
	}
	return $exclude;
}
add_filter( 'wpseo_sitemap_exclude_post_type', 'teznevise_wpseo_exclude_attachment_sitemap', 10, 2 );

/**
 * Point Yoast canonical at the mapped URL when an alias still renders.
 *
 * @param string $canonical Canonical URL.
 * @return string
 */
function teznevise_wpseo_canonical( $canonical ) {
	if ( ! is_page() ) {
		return $canonical;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$map  = teznevise_alias_page_slugs();
	if ( ! isset( $map[ $slug ] ) ) {
		return $canonical;
	}
	$page = get_page_by_path( trim( $map[ $slug ], '/' ) );
	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return home_url( $map[ $slug ] );
}
add_filter( 'wpseo_canonical', 'teznevise_wpseo_canonical', 20 );

function teznevise_language_attributes( $output ) {
	if ( stripos( $output, 'lang=' ) === false ) {
		$output .= ' lang="fa-IR"';
	}
	return $output;
}
add_filter( 'language_attributes', 'teznevise_language_attributes' );

/**
 * Replace one-word titles Yoast inherited from the page title.
 *
 * @param string $title Title.
 * @return string
 */
function teznevise_filter_document_title( $title ) {
	if ( ! is_string( $title ) || '' === $title ) {
		return $title;
	}
	$site  = get_bloginfo( 'name' );
	$core  = trim( (string) preg_replace( '/\s*[-|–]\s*' . preg_quote( $site, '/' ) . '\s*$/u', '', $title ) );
	$short = ( function_exists( 'mb_strlen' ) ? mb_strlen( $core ) : strlen( $core ) ) < 10;
	if ( is_front_page() && ( $short || 'خانه' === $core ) ) {
		return 'مشاوره پایان‌نامه، پروپوزال و تحلیل آماری | ' . $site;
	}
	if ( is_home() && ( $short || 'بلاگ' === $core ) ) {
		return 'راهنماها و آموزش‌های پژوهشی | بلاگ ' . $site;
	}
	$map = array(
		'ثبت سفارش'     => 'ثبت درخواست مشاوره پژوهشی',
		'آزمون t'       => 'محاسبه‌گر آزمون t آنلاین',
		'رگرسیون'       => 'محاسبه‌گر تحلیل رگرسیون آنلاین',
		'تحلیل کیفی'    => 'مشاوره تحلیل کیفی پژوهش',
		'تحلیل آماری'   => 'تحلیل آماری تخصصی پژوهش',
		'شبیه‌سازی'     => 'شبیه‌سازی تخصصی مهندسی و پژوهش',
		'دانلودها'      => 'دانلود قالب و فایل‌های پژوهشی',
		'درباره ما'     => 'درباره تزنویسه، همراه پژوهشی دانشجویان',
		'انجام پروژه دانشجویی' => 'مشاوره پروژه دانشجویی',
		'انجام مقاله'   => 'مشاوره نگارش مقاله',
	);
	if ( isset( $map[ $core ] ) ) {
		return $map[ $core ] . ' | ' . $site;
	}
	return $title;
}
add_filter( 'wpseo_title', 'teznevise_filter_document_title', 20 );
add_filter( 'pre_get_document_title', 'teznevise_filter_document_title', 20 );

/**
 * Guarantee a meta description on the homepage even when Yoast left name=description empty.
 *
 * @param string $desc Description.
 * @return string
 */
function teznevise_filter_metadesc( $desc ) {
	$desc = is_string( $desc ) ? trim( $desc ) : '';
	if ( '' !== $desc ) {
		return $desc;
	}
	if ( is_front_page() ) {
		return 'تزنویسه همراه پژوهشی دانشجویان است: مشاوره پایان‌نامه و پروپوزال، تحلیل آماری و ابزارهای آنلاین رایگان.';
	}
	if ( is_home() ) {
		return 'راهنماها و آموزش‌های پژوهشی تزنویسه: موضوع، پروپوزال، فصل‌های پایان‌نامه و روش تحقیق.';
	}
	if ( is_singular() ) {
		$id      = get_queried_object_id();
		$excerpt = trim( wp_strip_all_tags( (string) get_post_field( 'post_excerpt', $id ) ) );
		if ( '' === $excerpt ) {
			$excerpt = trim( wp_strip_all_tags( (string) get_post_field( 'post_content', $id ) ) );
		}
		if ( function_exists( 'teznevise_consult_copy' ) ) {
			$excerpt = teznevise_consult_copy( $excerpt );
		}
		$excerpt = preg_replace( '/\s+/u', ' ', $excerpt );
		if ( is_string( $excerpt ) && ( function_exists( 'mb_strlen' ) ? mb_strlen( $excerpt ) : strlen( $excerpt ) ) >= 40 ) {
			return wp_trim_words( $excerpt, 32, '' );
		}
		$fallback = trim( wp_strip_all_tags( (string) get_the_title( $id ) ) );
		if ( function_exists( 'teznevise_consult_copy' ) ) {
			$fallback = teznevise_consult_copy( $fallback );
		}
		if ( '' !== $fallback ) {
			return $fallback . ' | ' . get_bloginfo( 'name' );
		}
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		$desc = ( $term && ! empty( $term->description ) ) ? trim( wp_strip_all_tags( (string) $term->description ) ) : '';
		if ( '' !== $desc ) {
			return wp_trim_words( $desc, 32, '' );
		}
		$name = trim( wp_strip_all_tags( (string) single_term_title( '', false ) ) );
		if ( '' !== $name ) {
			return $name . ' | ' . get_bloginfo( 'name' );
		}
	}
	return $desc;
}
add_filter( 'wpseo_metadesc', 'teznevise_filter_metadesc', 20 );

/**
 * Collected FAQ pairs for JSON-LD (printed in wp_footer so shortcodes can contribute).
 *
 * @param string $q Question.
 * @param string $a Answer.
 */
function teznevise_faq_collect( $q, $a ) {
	$q = trim( wp_strip_all_tags( (string) $q ) );
	$a = trim( wp_strip_all_tags( (string) $a ) );
	if ( '' === $q || '' === $a ) {
		return;
	}
	$GLOBALS['teznevise_faq_pairs']   = isset( $GLOBALS['teznevise_faq_pairs'] ) && is_array( $GLOBALS['teznevise_faq_pairs'] ) ? $GLOBALS['teznevise_faq_pairs'] : array();
	$GLOBALS['teznevise_faq_pairs'][] = array( 'q' => $q, 'a' => $a );
}

/**
 * Whether a string looks like a question (not a service blurb).
 *
 * @param string $title Title.
 * @return bool
 */
function teznevise_text_is_question( $title ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	if ( '' === $title ) {
		return false;
	}
	if ( false !== strpos( $title, '؟' ) || false !== strpos( $title, '?' ) ) {
		return true;
	}
	return (bool) preg_match( '/^(آیا|چگونه|چطور|چرا|چقدر|کدام|چه )/u', $title );
}

/**
 * Accordion markup for a list of q/a pairs. Visible question is a button, never CSS-hidden.
 *
 * @param array $items Items with q/a.
 * @return string
 */
function teznevise_faq_items_markup( $items ) {
	if ( ! $items ) {
		return '';
	}
	ob_start();
	echo '<ul class="faq-grid tz-faq-grid">';
	$i    = 0;
	$seen = array();
	foreach ( $items as $item ) {
		$q = isset( $item['q'] ) ? (string) $item['q'] : '';
		$a = isset( $item['a'] ) ? (string) $item['a'] : '';
		if ( '' === $q || '' === $a ) {
			continue;
		}
		$key = md5( $q );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;
		++$i;
		teznevise_faq_collect( $q, $a );
		$tone = ' tone-' . ( ( ( $i - 1 ) % 9 ) + 1 );
		echo '<li class="faq-card faq-item' . esc_attr( $tone ) . '">';
		echo '<button type="button" class="faq-q" aria-expanded="false">';
		echo '<span class="faq-num" aria-hidden="true">' . esc_html( number_format_i18n( $i ) ) . '</span>';
		echo '<span class="faq-q__text">' . esc_html( $q ) . '</span>';
		echo '<span class="faq-q__mark" aria-hidden="true"></span>';
		echo '</button>';
		echo '<div class="faq-a"><p>' . esc_html( $a ) . '</p></div>';
		echo '</li>';
	}
	echo '</ul>';
	return (string) ob_get_clean();
}

/**
 * Parse a UL of <li><strong>Q</strong> — A</li> into q/a pairs. Returns empty if not questions.
 *
 * @param string $ul Ul HTML.
 * @return array<int,array{q:string,a:string}>
 */
function teznevise_parse_faq_ul( $ul ) {
	$out  = array();
	$seen = array();
	if ( ! preg_match_all( '/<li\b[^>]*>(.*?)<\/li>/is', (string) $ul, $lis ) ) {
		return $out;
	}
	foreach ( $lis[1] as $li ) {
		$q = '';
		$a = '';
		if ( preg_match( '/<strong\b[^>]*>(.*?)<\/strong>/is', $li, $sm ) ) {
			$q = trim( wp_strip_all_tags( $sm[1] ) );
			$a = trim( wp_strip_all_tags( str_replace( $sm[0], '', $li ) ) );
			$a = ltrim( $a, " \t\n\r\0\x0B—–-▾▼" );
		} else {
			$plain = trim( wp_strip_all_tags( $li ) );
			if ( preg_match( '/^(.+?[؟?])\s*[—–\-]\s*(.+)$/u', $plain, $pm ) ) {
				$q = trim( $pm[1] );
				$a = trim( $pm[2] );
			}
		}
		$q = is_string( $q ) ? preg_replace( '/[\s\x{00A0}]*[▼▽▾▴▸▹◁◀▶＋+]+$/u', '', $q ) : '';
		$q = is_string( $q ) ? trim( $q ) : '';
		if ( '' === $q || '' === $a || ! teznevise_text_is_question( $q ) ) {
			continue;
		}
		$key = md5( $q );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;
		$out[] = array( 'q' => $q, 'a' => $a );
	}
	return $out;
}

/**
 * Cut FAQ heading+list out of HTML and return a visible accordion section.
 *
 * @param string $html HTML.
 * @return array{0:string,1:string} Remaining HTML, FAQ section HTML.
 */
function teznevise_split_faq_blocks( $html ) {
	$html     = (string) $html;
	$sections = '';
	$html     = preg_replace_callback(
		'#(<h2\b[^>]*>[^<]*(?:سوالات|پرسش)[^<]*</h2>)(\s*<p\b[^>]*>[\s\S]*?</p>)?\s*(<ul\b[^>]*>[\s\S]*?</ul>)#iu',
		static function ( $m ) use ( &$sections ) {
			$items = teznevise_parse_faq_ul( $m[3] );
			if ( count( $items ) < 2 ) {
				return $m[0];
			}
			$head      = $m[1];
			$lead      = isset( $m[2] ) ? $m[2] : '';
			$sections .= '<section class="section tz-faq-band" aria-label="' . esc_attr__( 'سوالات متداول', 'teznevise' ) . '"><div class="container">';
			$sections .= $head;
			$sections .= $lead;
			$sections .= teznevise_faq_items_markup( $items );
			$sections .= '</div></section>';
			return '';
		},
		$html
	);
	return array( is_string( $html ) ? $html : '', $sections );
}

/**
 * FAQPage JSON-LD from collected pairs. Always emitted (Yoast does not add this).
 */
function teznevise_output_faq_schema() {
	$pairs = isset( $GLOBALS['teznevise_faq_pairs'] ) && is_array( $GLOBALS['teznevise_faq_pairs'] ) ? $GLOBALS['teznevise_faq_pairs'] : array();
	if ( count( $pairs ) < 2 ) {
		return;
	}
	$seen = array();
	$ent  = array();
	foreach ( $pairs as $row ) {
		$key = md5( $row['q'] );
		if ( isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;
		$ent[]        = array(
			'@type'          => 'Question',
			'name'           => $row['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $row['a'],
			),
		);
	}
	if ( count( $ent ) < 2 ) {
		return;
	}
	$payload = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $ent,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'teznevise_output_faq_schema', 22 );

/**
 * Service JSON-LD on consulting landings. Review schema is omitted (testimonials use initials).
 */
function teznevise_output_service_schema() {
	if ( ! is_page() ) {
		return;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$map  = array(
		'thesis'              => array( 'name' => 'مشاوره انجام پایان‌نامه', 'url' => home_url( '/thesis/' ) ),
		'proposal'            => array( 'name' => 'مشاوره انجام پروپوزال', 'url' => home_url( '/proposal/' ) ),
		'service-statistics'  => array( 'name' => 'تحلیل آماری', 'url' => home_url( '/service-statistics/' ) ),
		'service-simulation'  => array( 'name' => 'شبیه‌سازی', 'url' => home_url( '/service-simulation/' ) ),
		'service-qualitative' => array( 'name' => 'تحلیل کیفی', 'url' => home_url( '/service-qualitative/' ) ),
		'service-project'     => array( 'name' => 'مشاوره پروژه دانشجویی', 'url' => home_url( '/service-project/' ) ),
		'service-article'     => array( 'name' => 'مشاوره نگارش مقاله', 'url' => home_url( '/service-article/' ) ),
	);
	if ( ! isset( $map[ $slug ] ) ) {
		return;
	}
	$row     = $map[ $slug ];
	$payload = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'name'        => $row['name'],
		'url'         => $row['url'],
		'provider'    => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		'areaServed'  => 'IR',
		'serviceType' => $row['name'],
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'teznevise_output_service_schema', 23 );

/**
 * Fill empty img alt from title, filename, or post title.
 *
 * @param string $html HTML.
 * @return string
 */
function teznevise_fill_empty_img_alt( $html ) {
	if ( ! is_string( $html ) || false === stripos( $html, '<img' ) ) {
		return $html;
	}
	return preg_replace_callback(
		'/<img\b[^>]*>/i',
		static function ( $m ) {
			$tag = $m[0];
			if ( preg_match( '/\balt=("|\')(.*?)\1/i', $tag, $am ) && '' !== trim( $am[2] ) ) {
				return $tag;
			}
			$alt = '';
			if ( preg_match( '/\btitle=("|\')(.*?)\1/i', $tag, $tm ) ) {
				$alt = trim( wp_strip_all_tags( $tm[2] ) );
			}
			if ( '' === $alt && preg_match( '/\bsrc=("|\')(.*?)\1/i', $tag, $sm ) ) {
				$base = (string) pathinfo( (string) wp_parse_url( $sm[2], PHP_URL_PATH ), PATHINFO_FILENAME );
				$base = preg_replace( '/[-_]+/', ' ', rawurldecode( $base ) );
				$base = preg_replace( '/\d{5,}/', '', (string) $base );
				$alt  = trim( (string) $base );
			}
			if ( '' === $alt && is_singular() ) {
				$alt = wp_strip_all_tags( get_the_title() );
			}
			if ( '' === $alt ) {
				$alt = get_bloginfo( 'name' );
			}
			if ( preg_match( '/\balt=/i', $tag ) ) {
				return preg_replace( '/\balt=("|\')(.*?)\1/i', 'alt="' . esc_attr( $alt ) . '"', $tag, 1 );
			}
			return preg_replace( '/<img\b/i', '<img alt="' . esc_attr( $alt ) . '"', $tag, 1 );
		},
		$html
	);
}
add_filter( 'the_content', 'teznevise_fill_empty_img_alt', 20 );
add_filter( 'post_thumbnail_html', 'teznevise_fill_empty_img_alt', 20 );

/**
 * Lead nonce field with a unique id so two forms on one page do not duplicate DOM ids.
 *
 * @param string $uid Suffix.
 */
function teznevise_lead_nonce_field( $uid = '' ) {
	$html = wp_nonce_field( 'teznevise_lead', 'teznevise_lead_nonce', true, false );
	$uid  = sanitize_html_class( (string) $uid );
	if ( '' !== $uid ) {
		$html = str_replace( 'id="teznevise_lead_nonce"', 'id="teznevise_lead_nonce-' . $uid . '"', $html );
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
