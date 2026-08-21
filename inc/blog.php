<?php
/**
 * Native blog presentation fields and rendering helpers.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_blog_fields() {
	return array(
		'_teznevise_kicker'          => array( 'label' => __( 'Kicker / eyebrow', 'teznevise' ), 'type' => 'text' ),
		'_teznevise_subtitle'        => array( 'label' => __( 'Subtitle / standfirst', 'teznevise' ), 'type' => 'textarea' ),
		'_teznevise_read_time'       => array( 'label' => __( 'Reading time override', 'teznevise' ), 'type' => 'text', 'placeholder' => '5 min read' ),
		'_teznevise_featured_label'  => array( 'label' => __( 'Featured label', 'teznevise' ), 'type' => 'text', 'placeholder' => 'Featured' ),
		'_teznevise_author_label'    => array( 'label' => __( 'Author label override', 'teznevise' ), 'type' => 'text' ),
		'_teznevise_hide_toc'        => array( 'label' => __( 'Hide table of contents', 'teznevise' ), 'type' => 'checkbox' ),
		'_teznevise_related_heading' => array( 'label' => __( 'Related posts heading', 'teznevise' ), 'type' => 'text' ),
	);
}

function teznevise_register_blog_meta_box() {
	add_meta_box( 'teznevise_blog_settings', __( 'Teznevise Blog Settings', 'teznevise' ), 'teznevise_render_blog_meta_box', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes_post', 'teznevise_register_blog_meta_box' );

function teznevise_render_blog_meta_box( $post ) {
	wp_nonce_field( 'teznevise_save_blog_fields', 'teznevise_blog_fields_nonce' );
	echo '<p>' . esc_html__( 'Optional presentation controls. Core title, content, excerpt, featured image, categories, tags, author, dates, status, revisions, and comments remain native WordPress fields.', 'teznevise' ) . '</p>';
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( teznevise_blog_fields() as $key => $field ) {
		echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		if ( 'textarea' === $field['type'] ) {
			printf( '<textarea class="large-text" rows="3" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $key ), esc_textarea( get_post_meta( $post->ID, $key, true ) ) );
		} elseif ( 'checkbox' === $field['type'] ) {
			printf( '<label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s> %3$s</label>', esc_attr( $key ), checked( get_post_meta( $post->ID, $key, true ), '1', false ), esc_html__( 'Disable the table of contents for this post.', 'teznevise' ) );
		} else {
			printf( '<input class="regular-text" type="text" id="%1$s" name="%1$s" value="%2$s" placeholder="%3$s">', esc_attr( $key ), esc_attr( get_post_meta( $post->ID, $key, true ) ), esc_attr( $field['placeholder'] ?? '' ) );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

function teznevise_save_blog_fields( $post_id ) {
	if ( ! isset( $_POST['teznevise_blog_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_blog_fields_nonce'] ) ), 'teznevise_save_blog_fields' ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || 'post' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( teznevise_blog_fields() as $key => $field ) {
		if ( 'checkbox' === $field['type'] ) {
			update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? '1' : '0' );
			continue;
		}
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$value = 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
		'' === $value ? delete_post_meta( $post_id, $key ) : update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post_post', 'teznevise_save_blog_fields' );

function teznevise_blog_field( $key, $post_id = 0, $fallback = '' ) {
	$value = get_post_meta( $post_id ? $post_id : get_the_ID(), '_teznevise_' . $key, true );
	return '' !== $value ? $value : $fallback;
}

function teznevise_read_time( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$custom  = teznevise_blog_field( 'read_time', $post_id );
	if ( $custom ) { return $custom; }
	$text  = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) ) );
	// Use Unicode-aware tokenization: split on whitespace and count non-empty tokens
	$words = $text ? count( preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY ) ) : 0;
	return max( 1, (int) ceil( $words / 200 ) ) . ' ' . __( 'دقیقه مطالعه', 'teznevise' );
}

function teznevise_post_heading_id( $text ) {
	$id = sanitize_title( wp_strip_all_tags( $text ) );
	return $id ? $id : 'section';
}

/**
 * Global heading ID tracker to ensure uniqueness across the entire content.
 *
 * @var array
 */
$teznevise_heading_ids = array();

function teznevise_prepare_post_content( $content ) {
	global $teznevise_heading_ids;
	$teznevise_heading_ids = array();
	
	return preg_replace_callback( '/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', function ( $match ) use ( &$teznevise_heading_ids ) {
		$level = $match[1];
		$attrs = $match[2];
		$inner = $match[3];
		$title = wp_strip_all_tags( $inner );
		
		// Extract existing ID if present
		$id = '';
		if ( preg_match( '/\bid\s*=\s*["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
			$id = sanitize_title( $id_match[1] );
		}
		
		// Generate ID if not present
		if ( ! $id ) {
			$id = teznevise_post_heading_id( $title );
		}
		
		// Ensure uniqueness - handle both explicit and generated IDs
		$base_id = $id;
		$counter = 2;
		while ( isset( $teznevise_heading_ids[ $id ] ) ) {
			$id = $base_id . '-' . $counter++;
		}
		
		// Mark this ID as used
		$teznevise_heading_ids[ $id ] = true;
		
		// Update or add the ID attribute
		if ( preg_match( '/\bid\s*=\s*["\'][^"\']*["\']/i', $attrs ) ) {
			$attrs = preg_replace( '/\bid\s*=\s*["\'][^"\']*["\']/i', 'id="' . esc_attr( $id ) . '"', $attrs, 1 );
		} else {
			$attrs .= ' id="' . esc_attr( $id ) . '"';
		}
		
		return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
	}, $content );
}

function teznevise_localize_on_this_page( $translation, $text, $domain = '' ) {
	unset( $domain );
	if ( 'On this page' === $text || 'On this page' === $translation ) {
		return 'در این مقاله';
	}
	return $translation;
}
add_filter( 'gettext', 'teznevise_localize_on_this_page', 10, 3 );

/**
 * Render table of contents from prepared content (with IDs already added).
 *
 * @param string $content Prepared post content with heading IDs
 * @return string HTML for TOC
 */
function teznevise_render_toc( $content ) {
	preg_match_all( '/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER );
	if ( empty( $matches ) ) { return ''; }
	
	$items = '<ul class="post-toc-grid">';
	foreach ( $matches as $match ) {
		// Extract ID from the heading attributes
		if ( ! preg_match( '/\bid\s*=\s*["\']([^"\']+)["\']/i', $match[2], $id_match ) ) {
			continue;
		}
		$heading_id = $id_match[1];
		$heading_text = wp_strip_all_tags( $match[3] );
		
		// Skip empty headings
		if ( empty( $heading_text ) ) {
			continue;
		}
		
		$items .= '<li><a class="post-toc-item post-toc-item--level-' . esc_attr( $match[1] ) . '" href="#' . esc_attr( $heading_id ) . '">' . esc_html( $heading_text ) . '</a></li>';
	}
	return $items . '</ul>';
}

function teznevise_related_posts( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
	if ( empty( $terms ) ) { return null; }
	return new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => array( $post_id ), 'category__in' => $terms, 'ignore_sticky_posts' => true, 'no_found_rows' => true, 'orderby' => 'date', 'order' => 'DESC' ) );
}
