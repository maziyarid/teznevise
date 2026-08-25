<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: maziyarid/M-Z — A brand new repository with my complete brand identity, story, and website prototype.
 *
 * Extra page meta fields for About / Team / Tools / Downloads.
 * Registers additional meta and extends the admin meta box.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extra schema keys.
 *
 * @return array
 */
function teznevise_page_meta_schema_extra() {
	return array(
		'timeline' => array(
			'label'       => __( 'خط زمانی (سال|عنوان|توضیح)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'هر خط: ۱۳۹۸|شروع مشاوره|توضیح کوتاه', 'teznevise' ),
			'ui'          => 'textarea',
			'group'       => 'about',
		),
		'policy_points' => array(
			'label'       => __( 'سیاست کاری (هر خط یک مورد)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'ui'          => 'textarea',
			'group'       => 'about',
		),
		'team_stats' => array(
			'label'       => __( 'آمار تیم (عدد|برچسب)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'هر خط: ۲۷+|پژوهشگر متخصص', 'teznevise' ),
			'ui'          => 'textarea',
			'group'       => 'team',
		),
		'team_members' => array(
			'label'       => __( 'اعضای تیم (نام|نقش|حوزه|بیو)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'اختیاری — در غیر این صورت از محتوای صفحه استفاده شود', 'teznevise' ),
			'ui'          => 'textarea',
			'group'       => 'team',
		),
		'tools_list' => array(
			'label'       => __( 'فهرست ابزارها (عنوان|لینک|توضیح|آیکون)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'هر خط یک ابزار. مثال: آمار توصیفی|/tool-descriptive-statistics/|میانگین و واریانس|fa-solid fa-chart-simple', 'teznevise' ),
			'ui'          => 'textarea',
			'group'       => 'tools',
		),
		'downloads_list' => array(
			'label'       => __( 'فهرست دانلودها (عنوان|لینک|توضیح)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'هر خط یک فایل. لینک می‌تواند Media Library URL باشد.', 'teznevise' ),
			'ui'          => 'textarea',
			'group'       => 'downloads',
		),
	);
}

/**
 * Register extra page meta.
 */
function teznevise_register_page_meta_extra() {
	foreach ( teznevise_page_meta_schema_extra() as $key => $args ) {
		$type    = isset( $args['type'] ) ? $args['type'] : 'string';
		$default = isset( $args['default'] ) ? $args['default'] : '';
		register_post_meta(
			'page',
			'_teznevise_' . $key,
			array(
				'type'              => $type,
				'description'       => isset( $args['description'] ) ? $args['description'] : $args['label'],
				'single'            => true,
				'default'           => $default,
				'show_in_rest'      => true,
				'auth_callback'     => function ( $allowed, $meta_key, $object_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
					return current_user_can( 'edit_page', (int) $object_id );
				},
				'sanitize_callback' => function ( $value ) {
					return is_string( $value ) ? sanitize_textarea_field( $value ) : '';
				},
			)
		);
	}
}
add_action( 'init', 'teznevise_register_page_meta_extra', 20 );

/**
 * Extend admin groups.
 *
 * @param array $groups Groups.
 * @return array
 */
function teznevise_page_meta_groups_extra( $groups ) {
	$groups['about']     = __( 'درباره ما', 'teznevise' );
	$groups['team']      = __( 'تیم', 'teznevise' );
	$groups['tools']     = __( 'ابزارها', 'teznevise' );
	$groups['downloads'] = __( 'دانلودها', 'teznevise' );
	return $groups;
}
add_filter( 'teznevise_page_meta_groups', 'teznevise_page_meta_groups_extra' );

/**
 * Second meta box for section-specific lists.
 */
function teznevise_add_page_meta_box_extra() {
	add_meta_box(
		'teznevise_page_fields_extra',
		__( 'فهرست‌ها و بخش‌های صفحه (تزنویسه)', 'teznevise' ),
		'teznevise_render_page_meta_box_extra',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'teznevise_add_page_meta_box_extra' );

/**
 * Render extra meta box.
 *
 * @param WP_Post $post Post.
 */
function teznevise_render_page_meta_box_extra( $post ) {
	wp_nonce_field( 'teznevise_save_page_meta_extra', 'teznevise_page_meta_extra_nonce' );
	$schema = teznevise_page_meta_schema_extra();
	$groups = array(
		'about'     => __( 'درباره ما', 'teznevise' ),
		'team'      => __( 'تیم', 'teznevise' ),
		'tools'     => __( 'ابزارها', 'teznevise' ),
		'downloads' => __( 'دانلودها', 'teznevise' ),
	);
	echo '<p style="color:#646970;">' . esc_html__( 'این فیلدها برای قالب‌های About / Team / Tools / Downloads است. اگر صفحه‌ساز را پر کرده‌اید نیازی به پر کردن همه نیست.', 'teznevise' ) . '</p>';
	$current = '';
	echo '<details class="tez-meta-advanced"><summary>' . esc_html__( 'فهرست‌های پیشرفته صفحه', 'teznevise' ) . '</summary>';
	echo '<table class="form-table"><tbody>';
	foreach ( $schema as $key => $args ) {
		$group = isset( $args['group'] ) ? $args['group'] : 'about';
		if ( $group !== $current ) {
			$current = $group;
			$label   = isset( $groups[ $group ] ) ? $groups[ $group ] : $group;
			echo '<tr><td colspan="2"><div class="tez-meta-group">' . esc_html( $label ) . '</div></td></tr>';
		}
		$meta_key = '_teznevise_' . $key;
		$value    = get_post_meta( $post->ID, $meta_key, true );
		$id       = 'teznevise_extra_' . $key;
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $args['label'] ) . '</label></th><td>';
		printf(
			'<textarea class="large-text" rows="5" id="%1$s" name="%2$s">%3$s</textarea>',
			esc_attr( $id ),
			esc_attr( $meta_key ),
			esc_textarea( (string) $value )
		);
		if ( ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table></details>';
}

/**
 * Save extra meta.
 *
 * @param int $post_id Post ID.
 */
function teznevise_save_page_meta_extra( $post_id ) {
	if ( ! isset( $_POST['teznevise_page_meta_extra_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_page_meta_extra_nonce'] ) ), 'teznevise_save_page_meta_extra' ) ) {
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
	$changed = false;
	foreach ( array_keys( teznevise_page_meta_schema_extra() ) as $key ) {
		$meta_key = '_teznevise_' . $key;
		if ( ! isset( $_POST[ $meta_key ] ) ) {
			continue;
		}
		$val     = sanitize_textarea_field( wp_unslash( $_POST[ $meta_key ] ) );
		$current = get_post_meta( $post_id, $meta_key, true );
		if ( (string) $current === (string) $val ) {
			continue;
		}
		update_post_meta( $post_id, $meta_key, $val );
		$changed = true;
	}
	if ( $changed && function_exists( 'teznevise_stamp_manual_page_ownership' ) ) {
		teznevise_stamp_manual_page_ownership( $post_id );
	}
}
add_action( 'save_post_page', 'teznevise_save_page_meta_extra' );
