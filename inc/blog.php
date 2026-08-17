<?php
/**
 * Native blog presentation fields and rendering helpers.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function teznevise_blog_fields() {
	return array(
		'_teznevise_kicker'          => array( 'label' => __( 'Kicker / eyebrow', 'teznevise' ), 'type' => 'text' ),
		'_teznevise_subtitle'        => array( 'label' => __( 'Subtitle / standfirst', 'teznevise' ), 'type' => 'textarea' ),
		'_teznevise_read_time'       => array( 'label' => __( 'Reading time', 'teznevise' ), 'type' => 'text', 'placeholder' => '5 min' ),
		'_teznevise_featured_label'  => array( 'label' => __( 'Featured label', 'teznevise' ), 'type' => 'text', 'placeholder' => 'Featured' ),
		'_teznevise_author_label'    => array( 'label' => __( 'Author label override', 'teznevise' ), 'type' => 'text' ),
		'_teznevise_hide_toc'        => array( 'label' => __( 'Hide table of contents', 'teznevise' ), 'type' => 'checkbox' ),
		'_teznevise_related_heading' => array( 'label' => __( 'Related posts heading', 'teznevise' ), 'type' => 'text' ),
	);
}

function teznevise_register_blog_meta_box() { add_meta_box( 'teznevise_blog_settings', __( 'Teznevise Blog Settings', 'teznevise' ), 'teznevise_render_blog_meta_box', 'post', 'normal', 'high' ); }
add_action( 'add_meta_boxes_post', 'teznevise_register_blog_meta_box' );

function teznevise_render_blog_meta_box( $post ) {
	wp_nonce_field( 'teznevise_save_blog_fields', 'teznevise_blog_fields_nonce' );
	?><p><?php esc_html_e( 'Optional presentation controls. Standard title, content, excerpt, featured image, category, tags, author, and date remain native WordPress fields.', 'teznevise' ); ?></p>
	<table class="form-table" role="presentation">
	<?php foreach ( teznevise_blog_fields() as $key => $field ) : ?>
		<tr><th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th><td>
		<?php if ( 'textarea' === $field['type'] ) : ?>
			<textarea class="large-text" rows="3" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"><?php echo esc_textarea( get_post_meta( $post->ID, $key, true ) ); ?></textarea>
		<?php elseif ( 'checkbox' === $field['type'] ) : ?>
			<label><input type="checkbox" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( get_post_meta( $post->ID, $key, true ), '1' ); ?>> <?php esc_html_e( 'Disable this section for this post', 'teznevise' ); ?></label>
		<?php else : ?>
			<input class="regular-text" type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>">
		<?php endif; ?>
		</td></tr>
	<?php endforeach; ?>
	</table><?php
}

function teznevise_save_blog_fields( $post_id ) {
	if ( ! isset( $_POST['teznevise_blog_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_blog_fields_nonce'] ) ), 'teznevise_save_blog_fields' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) { return; }
	foreach ( teznevise_blog_fields() as $key => $field ) {
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$value = 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : ( 'checkbox' === $field['type'] ? ( isset( $_POST[ $key ] ) ? '1' : '0' ) : sanitize_text_field( $value ) );
		if ( '' === $value && 'checkbox' !== $field['type'] ) { delete_post_meta( $post_id, $key ); } else { update_post_meta( $post_id, $key, $value ); }
	}
}
add_action( 'save_post_post', 'teznevise_save_blog_fields' );

function teznevise_blog_field( $key, $post_id = 0, $fallback = '' ) {
	$value = get_post_meta( $post_id ? $post_id : get_the_ID(), '_teznevise_' . $key, true );
	return '' !== $value ? $value : $fallback;
}

function teznevise_read_time( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$custom = teznevise_blog_field( 'read_time', $post_id );
	if ( $custom ) { return $custom; }
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) ) );
	$words = $text ? count( preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY ) ) : 0;
	return max( 1, (int) ceil( $words / 200 ) ) . ' ' . __( 'min read', 'teznevise' );
}

function teznevise_post_heading_id( $text ) { return sanitize_title( wp_strip_all_tags( $text ) ); }

function teznevise_prepare_post_content( $content ) {
	$used = array();
	return preg_replace_callback( '/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', function ( $match ) use ( &$used ) {
		$attrs = $match[2];
		$title = wp_strip_all_tags( $match[3] );
		if ( preg_match( '/\bid\s*=\s*["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
			$id = $id_match[1];
		} else {
			$id = teznevise_post_heading_id( $title );
			$base = $id;
			$count = 2;
			while ( isset( $used[ $id ] ) ) { $id = $base . '-' . $count++; }
			$attrs .= ' id="' . esc_attr( $id ) . '"';
		}
		$used[ $id ] = true;
		return '<h' . $match[1] . $attrs . '>' . $match[3] . '</h' . $match[1] . '>';
	}, $content );
}

function teznevise_render_toc( $content ) {
	if ( ! $content ) { return ''; }
	preg_match_all( '/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER );
	if ( empty( $matches ) ) { return ''; }
	$used = array();
	$items = '<ul class="blog-toc__list">';
	foreach ( $matches as $match ) {
		$title = wp_strip_all_tags( $match[3] );
		if ( preg_match( '/\bid\s*=\s*["\']([^"\']+)["\']/i', $match[2], $id_match ) ) {
			$id = $id_match[1];
		} else {
			$id = teznevise_post_heading_id( $title );
			$base = $id;
			$count = 2;
			while ( isset( $used[ $id ] ) ) { $id = $base . '-' . $count++; }
		}
		$used[ $id ] = true;
		$items .= '<li class="blog-toc__item blog-toc__item--level-' . esc_attr( $match[1] ) . '"><a href="#' . esc_attr( $id ) . '">' . esc_html( $title ) . '</a></li>';
	}
	return $items . '</ul>';
}

function teznevise_related_posts( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
	return new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => array( $post_id ), 'category__in' => $terms, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
}
