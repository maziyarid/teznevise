<?php
/**
 * Page custom fields — registered post meta + admin meta boxes.
 *
 * Edit any Page in WP Admin → box «فیلدهای تزنویسه».
 * Fields are also available in REST / block editor Custom Fields when enabled.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Schema of page meta fields.
 *
 * @return array
 */
function teznevise_page_meta_schema() {
	return array(
		'eyebrow' => array(
			'label'       => __( 'ابرو (Eyebrow)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'متن کوچک بالای عنوان صفحه', 'teznevise' ),
		),
		'subtitle' => array(
			'label'       => __( 'زیرعنوان', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'توضیح کوتاه زیر عنوان', 'teznevise' ),
		),
		'cta_text' => array(
			'label'   => __( 'متن دکمه CTA', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
		),
		'cta_url' => array(
			'label'   => __( 'لینک دکمه CTA', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
		),
		'hero_note' => array(
			'label'       => __( 'یادداشت هیرو / بنر', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'متن کمکی کنار عنوان', 'teznevise' ),
		),
		'service_icon' => array(
			'label'       => __( 'آیکون خدمت (کلاس Font Awesome)', 'teznevise' ),
			'type'        => 'string',
			'default'     => 'fa-solid fa-graduation-cap',
			'description' => __( 'مثال: fa-solid fa-chart-line', 'teznevise' ),
		),
		'service_color' => array(
			'label'       => __( 'کلاس رنگ آیکون', 'teznevise' ),
			'type'        => 'string',
			'default'     => 'icon-teal',
			'description' => __( 'مثال: icon-indigo, icon-teal, icon-cyan', 'teznevise' ),
		),
		'features' => array(
			'label'       => __( 'ویژگی‌ها (هر خط یک مورد)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'برای صفحات خدمت — هر خط یک بولت', 'teznevise' ),
			'ui'          => 'textarea',
		),
		'price_note' => array(
			'label'   => __( 'یادداشت قیمت / زمان', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
		),
		'secondary_cta_text' => array(
			'label'   => __( 'متن دکمه فرعی', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
		),
		'secondary_cta_url' => array(
			'label'   => __( 'لینک دکمه فرعی', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
		),
		'hide_title' => array(
			'label'       => __( 'مخفی کردن عنوان پیش‌فرض', 'teznevise' ),
			'type'        => 'boolean',
			'default'     => false,
			'description' => __( 'اگر فعال باشد عنوان H1 از قالب نشان داده نمی‌شود', 'teznevise' ),
			'ui'          => 'checkbox',
		),
	);
}

/**
 * Register post meta for pages (REST + Custom Fields API).
 */
function teznevise_register_page_meta() {
	$schema = teznevise_page_meta_schema();
	foreach ( $schema as $key => $args ) {
		$type = isset( $args['type'] ) ? $args['type'] : 'string';
		$default = isset( $args['default'] ) ? $args['default'] : ( 'boolean' === $type ? false : '' );
		register_post_meta(
			'page',
			'_teznevise_' . $key,
			array(
				'type'              => $type,
				'description'       => isset( $args['description'] ) ? $args['description'] : $args['label'],
				'single'            => true,
				'default'           => $default,
				'show_in_rest'      => true,
				'auth_callback'     => function () {
					return current_user_can( 'edit_pages' );
				},
				'sanitize_callback' => function ( $value ) use ( $type ) {
					if ( 'boolean' === $type ) {
						return (bool) $value;
					}
					if ( is_string( $value ) && strlen( $value ) > 500 ) {
						return sanitize_textarea_field( $value );
					}
					return sanitize_text_field( $value );
				},
			)
		);
	}
}
add_action( 'init', 'teznevise_register_page_meta' );

/**
 * Add meta box on page edit screen.
 */
function teznevise_add_page_meta_box() {
	add_meta_box(
		'teznevise_page_fields',
		__( 'فیلدهای تزنویسه', 'teznevise' ),
		'teznevise_render_page_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'teznevise_add_page_meta_box' );

/**
 * Render meta box fields.
 *
 * @param WP_Post $post Current post.
 */
function teznevise_render_page_meta_box( $post ) {
	wp_nonce_field( 'teznevise_save_page_meta', 'teznevise_page_meta_nonce' );
	$schema = teznevise_page_meta_schema();
	echo '<p style="margin-top:0;color:#646970;">' . esc_html__( 'این فیلدها محتوای قابل‌ویرایش قالب صفحه هستند. برای صفحه اصلی از سفارشی‌سازی قالب (Customizer) استفاده کنید.', 'teznevise' ) . '</p>';
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $schema as $key => $args ) {
		$meta_key = '_teznevise_' . $key;
		$value    = get_post_meta( $post->ID, $meta_key, true );
		$ui       = isset( $args['ui'] ) ? $args['ui'] : ( 'boolean' === $args['type'] ? 'checkbox' : 'text' );
		$id       = 'teznevise_field_' . $key;
		echo '<tr>';
		echo '<th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $args['label'] ) . '</label></th>';
		echo '<td>';
		if ( 'checkbox' === $ui ) {
			printf(
				'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>',
				esc_attr( $id ),
				esc_attr( $meta_key ),
				checked( (bool) $value, true, false ),
				esc_html__( 'فعال', 'teznevise' )
			);
		} elseif ( 'textarea' === $ui ) {
			printf(
				'<textarea class="large-text" rows="5" id="%1$s" name="%2$s">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $meta_key ),
				esc_textarea( (string) $value )
			);
		} else {
			printf(
				'<input type="text" class="large-text" id="%1$s" name="%2$s" value="%3$s" />',
				esc_attr( $id ),
				esc_attr( $meta_key ),
				esc_attr( (string) $value )
			);
		}
		if ( ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Save page meta box fields.
 *
 * @param int $post_id Post ID.
 */
function teznevise_save_page_meta( $post_id ) {
	if ( ! isset( $_POST['teznevise_page_meta_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_page_meta_nonce'] ) ), 'teznevise_save_page_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( get_post_type( $post_id ) !== 'page' ) {
		return;
	}
	$schema = teznevise_page_meta_schema();
	foreach ( $schema as $key => $args ) {
		$meta_key = '_teznevise_' . $key;
		$type     = $args['type'];
		if ( 'boolean' === $type ) {
			$val = isset( $_POST[ $meta_key ] ) ? 1 : 0;
			update_post_meta( $post_id, $meta_key, $val );
			continue;
		}
		if ( ! isset( $_POST[ $meta_key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $meta_key ] );
		if ( is_string( $raw ) && ( ( isset( $args['ui'] ) && 'textarea' === $args['ui'] ) || strlen( $raw ) > 200 ) ) {
			$val = sanitize_textarea_field( $raw );
		} else {
			$val = sanitize_text_field( $raw );
		}
		update_post_meta( $post_id, $meta_key, $val );
	}
}
add_action( 'save_post_page', 'teznevise_save_page_meta' );
