<?php
/**
 * Basic SEO helpers — meta description, Open Graph, language.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output common head meta.
 */
function teznevise_seo_head() {
	if ( is_admin() ) {
		return;
	}

	$description = '';
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && ! empty( $post->post_excerpt ) ) {
			$description = wp_strip_all_tags( $post->post_excerpt );
		} elseif ( $post ) {
			$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 32, '…' );
		}
	} elseif ( is_front_page() ) {
		$description = function_exists( 'teznevise_mod' ) ? teznevise_mod( 'hero_text', get_bloginfo( 'description' ) ) : get_bloginfo( 'description' );
	} else {
		$description = get_bloginfo( 'description' );
	}

	$description = mb_substr( trim( preg_replace( '/\s+/', ' ', $description ) ), 0, 160 );
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}

	$title = wp_get_document_title();
	$url   = home_url( '/' );
	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_home() ) {
		$posts_page = (int) get_option( 'page_for_posts' );
		$url        = $posts_page ? get_permalink( $posts_page ) : home_url( '/' );
	}

	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'large' );
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
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action( 'wp_head', 'teznevise_seo_head', 1 );

/**
 * Ensure HTML language attributes include fa-IR when missing.
 *
 * @param string $output Language attributes.
 * @return string
 */
function teznevise_language_attributes( $output ) {
	if ( stripos( $output, 'lang=' ) === false ) {
		$output .= ' lang="fa-IR"';
	}
	return $output;
}
add_filter( 'language_attributes', 'teznevise_language_attributes' );
