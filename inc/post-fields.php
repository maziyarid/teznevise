<?php
/**
 * Blog post presentation fields.
 *
 * These native WordPress meta fields let editors control the post page
 * presentation without requiring a page builder or custom-field plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_register_post_fields() {
	add_meta_box(
		'teznevise_post_presentation',
		__( 'Teznevise Blog Settings', 'teznevise' ),
		'teznevise_render_post_fields',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_post', 'teznevise_register_post_fields' );

function teznevise_render_post_fields( $post ) {
	wp_nonce_field( 'teznevise_save_post_fields', 'teznevise_post_fields_nonce' );
	$fields = array(
		'_teznevise_kicker'          => array( 'label' => __( 'Kicker / eyebrow', 'teznevise' ), 'type' => 'text' ),
		'_teznevise_subtitle'        => array( 'label' => __( 'Subtitle / standfirst', 'teznevise' ), 'type' => 'textarea' ),
		'_teznevise_read_time'       => array( 'label' => __( 'Reading time', 'teznevise' ), 'type' => 'text', 'placeholder' => '5 min' ),
		'_teznevise_featured_label'  => array( 'label' => __( 'Featured label', 'teznevise' ), 'type' => 'text', 'placeholder' => 'Featured' ),
		'_teznevise_hide_toc'        => array( 'label' => __( 'Hide table of contents', 'teznevise' ), 'type' => 'checkbox' ),
		'_teznevise_related_heading' => array( 'label' => __( 'Related posts heading', 'teznevise' ), 'type' => 'text' ),
	);
	?>
	<p><?php esc_html_e( 'Optional presentation controls for the single blog page. Leave fields empty to use theme defaults.', 'teznevise' ); ?></p>
	<table class="form-table" role="presentation">
		<?php foreach ( $fields as $key => $field ) : ?>
			<tr>
				<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
				<td>
					<?php if ( 'textarea' === $field['type'] ) : ?>
						<textarea class="large-text" rows="3" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"><?php echo esc_textarea( get_post_meta( $post->ID, $key, true ) ); ?></textarea>
					<?php elseif ( 'checkbox' === $field['type'] ) : ?>
						<label><input type="checkbox" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( get_post_meta( $post->ID, $key, true ), '1' ); ?>> <?php esc_html_e( 'Disable this section for this post', 'teznevise' ); ?></label>
					<?php else : ?>
						<input class="regular-text" type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>" placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>">
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

function teznevise_save_post_fields( $post_id ) {
	if ( ! isset( $_POST['teznevise_post_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_post_fields_nonce'] ) ), 'teznevise_save_post_fields' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$keys = array( '_teznevise_kicker', '_teznevise_read_time', '_teznevise_featured_label', '_teznevise_related_heading' );
	foreach ( $keys as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
	$subtitle = isset( $_POST['_teznevise_subtitle'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_teznevise_subtitle'] ) ) : '';
	'' === $subtitle ? delete_post_meta( $post_id, '_teznevise_subtitle' ) : update_post_meta( $post_id, '_teznevise_subtitle', $subtitle );
	update_post_meta( $post_id, '_teznevise_hide_toc', isset( $_POST['_teznevise_hide_toc'] ) ? '1' : '0' );
}
add_action( 'save_post_post', 'teznevise_save_post_fields' );

function teznevise_post_field( $key, $post_id = 0, $fallback = '' ) {
	$value = get_post_meta( $post_id ? $post_id : get_the_ID(), '_teznevise_' . $key, true );
	return '' !== $value ? $value : $fallback;
}
