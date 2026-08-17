<?php
/**
 * Theme-level technical SEO foundations.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function teznevise_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || function_exists( 'seopress_init' );
}

function teznevise_seo_description() {
	if ( is_singular() ) {
		$description = get_post_field( 'post_excerpt', get_queried_object_id() );
		if ( ! $description ) { $description = get_post_field( 'post_content', get_queried_object_id() ); }
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
	} else {
		$description = get_bloginfo( 'description' );
	}
	$description = wp_strip_all_tags( strip_shortcodes( (string) $description ) );
	return wp_trim_words( $description, 30, '…' );
}

function teznevise_seo_canonical() {
	if ( is_singular() ) { return get_permalink(); }
	if ( is_home() && ! is_front_page() ) { return get_permalink( get_option( 'page_for_posts' ) ); }
	if ( is_category() || is_tag() || is_tax() ) { return get_term_link( get_queried_object() ); }
	if ( is_post_type_archive() ) { return get_post_type_archive_link( get_query_var( 'post_type' ) ); }
	return home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
}

function teznevise_seo_output_head() {
	if ( teznevise_seo_plugin_active() ) { return; }
	$description = teznevise_seo_description();
	if ( $description ) : ?><meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<?php
	// WordPress core owns rel=canonical and robots. Keep the theme fallback
	// limited to description so it cannot emit duplicate core directives.
}
add_action( 'wp_head', 'teznevise_seo_output_head', 1 );

function teznevise_schema_data() {
	if ( teznevise_seo_plugin_active() ) { return array(); }
	$graph = array( array( '@type' => 'WebSite', '@id' => home_url( '/#website' ), 'url' => home_url( '/' ), 'name' => get_bloginfo( 'name' ), 'description' => get_bloginfo( 'description' ) ) );
	if ( is_singular( 'post' ) ) {
		$post_id = get_queried_object_id();
		$article = array( '@type' => 'Article', '@id' => get_permalink( $post_id ) . '#article', 'mainEntityOfPage' => get_permalink( $post_id ), 'headline' => get_the_title( $post_id ), 'datePublished' => get_the_date( DATE_W3C, $post_id ), 'dateModified' => get_the_modified_date( DATE_W3C, $post_id ), 'author' => array( '@type' => 'Person', 'name' => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ) ), 'publisher' => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ) ), 'description' => teznevise_seo_description() );
		if ( has_post_thumbnail( $post_id ) ) { $article['image'] = get_the_post_thumbnail_url( $post_id, 'full' ); }
		$graph[] = $article;
	}
	if ( is_singular() || is_category() || is_tag() || is_tax() ) {
		$graph[] = array( '@type' => 'BreadcrumbList', '@id' => home_url( '/#breadcrumbs' ), 'itemListElement' => array( array( '@type' => 'ListItem', 'position' => 1, 'name' => get_bloginfo( 'name' ), 'item' => home_url( '/' ) ), array( '@type' => 'ListItem', 'position' => 2, 'name' => wp_strip_all_tags( wp_get_document_title() ), 'item' => teznevise_seo_canonical() ) ) );
	}
	return array( '@context' => 'https://schema.org', '@graph' => $graph );
}

function teznevise_output_schema() {
	$data = teznevise_schema_data();
	if ( empty( $data ) ) { return; }
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'teznevise_output_schema', 20 );

function teznevise_robots_txt( $output, $public ) {
	if ( ! $public ) { return $output; }
	$sitemap = home_url( '/wp-sitemap.xml' );
	if ( false === strpos( $output, $sitemap ) ) { $output .= "\nSitemap: {$sitemap}\n"; }
	return $output;
}
add_filter( 'robots_txt', 'teznevise_robots_txt', 10, 2 );
