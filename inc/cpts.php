<?php
/**
 * Custom post types, taxonomies, and meta formerly provided via WPCode.
 *
 * Registers only when the post type is not already registered (so existing
 * WPCode snippets can be disabled gradually without conflict).
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register download CPT + taxonomies.
 */
function teznevise_register_download_cpt() {
	if ( post_type_exists( 'download' ) ) {
		return;
	}

	register_post_type(
		'download',
		array(
			'labels'       => array(
				'name'          => __( 'دانلودها', 'teznevise' ),
				'singular_name' => __( 'دانلود', 'teznevise' ),
				'add_new'       => __( 'افزودن دانلود', 'teznevise' ),
				'add_new_item'  => __( 'افزودن دانلود جدید', 'teznevise' ),
				'edit_item'     => __( 'ویرایش دانلود', 'teznevise' ),
				'search_items'  => __( 'جستجوی دانلود', 'teznevise' ),
				'not_found'     => __( 'دانلودی یافت نشد', 'teznevise' ),
			),
			'public'       => true,
			'has_archive'  => 'download',
			'rewrite'      => array(
				'slug'       => 'download',
				'with_front' => false,
			),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments' ),
			'menu_position'=> 6,
			'menu_icon'    => 'dashicons-download',
			'show_in_rest' => true,
		)
	);

	if ( ! taxonomy_exists( 'download_category' ) ) {
		register_taxonomy(
			'download_category',
			'download',
			array(
				'labels'            => array(
					'name'          => __( 'دسته‌بندی دانلودها', 'teznevise' ),
					'singular_name' => __( 'دسته دانلود', 'teznevise' ),
				),
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'download-category' ),
			)
		);
	}

	if ( ! taxonomy_exists( 'download_tag' ) ) {
		register_taxonomy(
			'download_tag',
			'download',
			array(
				'labels'       => array(
					'name'          => __( 'برچسب دانلودها', 'teznevise' ),
					'singular_name' => __( 'برچسب دانلود', 'teznevise' ),
				),
				'hierarchical' => false,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'download-tag' ),
			)
		);
	}
}
add_action( 'init', 'teznevise_register_download_cpt', 5 );

/**
 * Register tz_service CPT (price calculator items).
 */
function teznevise_register_service_cpt() {
	if ( post_type_exists( 'tz_service' ) ) {
		return;
	}

	register_post_type(
		'tz_service',
		array(
			'labels'       => array(
				'name'          => __( 'خدمات قیمتی', 'teznevise' ),
				'singular_name' => __( 'خدمت قیمتی', 'teznevise' ),
				'add_new_item'  => __( 'افزودن خدمت', 'teznevise' ),
				'edit_item'     => __( 'ویرایش خدمت', 'teznevise' ),
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'menu_icon'    => 'dashicons-money-alt',
			'menu_position'=> 7,
			'rewrite'      => array( 'slug' => 'tz-service' ),
		)
	);
}
add_action( 'init', 'teznevise_register_service_cpt', 5 );

/**
 * Register case_study CPT + taxonomy.
 */
function teznevise_register_case_study_cpt() {
	if ( post_type_exists( 'case_study' ) ) {
		return;
	}

	register_post_type(
		'case_study',
		array(
			'labels'       => array(
				'name'          => __( 'مطالعات موردی', 'teznevise' ),
				'singular_name' => __( 'مطالعه موردی', 'teznevise' ),
				'add_new_item'  => __( 'افزودن مطالعه موردی', 'teznevise' ),
				'edit_item'     => __( 'ویرایش مطالعه موردی', 'teznevise' ),
			),
			'public'       => true,
			'has_archive'  => 'case-studies',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'menu_icon'    => 'dashicons-portfolio',
			'menu_position'=> 8,
			'rewrite'      => array( 'slug' => 'case-study' ),
		)
	);

	if ( ! taxonomy_exists( 'case_category' ) ) {
		register_taxonomy(
			'case_category',
			'case_study',
			array(
				'labels'       => array(
					'name'          => __( 'دسته‌بندی مطالعات موردی', 'teznevise' ),
					'singular_name' => __( 'دسته مطالعه', 'teznevise' ),
				),
				'hierarchical' => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'case-category' ),
			)
		);
	}
}
add_action( 'init', 'teznevise_register_case_study_cpt', 5 );

/**
 * Meta field definitions per post type.
 *
 * @return array<string,array<string,array{label:string,type:string}>>
 */
function teznevise_cpt_meta_fields() {
	return array(
		'download'    => array(
			'_teznevise_download_links' => array( 'label' => __( 'لینک‌های دانلود (هر خط: عنوان|URL|حجم)', 'teznevise' ), 'type' => 'textarea' ),
			'_teznevise_download_count' => array( 'label' => __( 'تعداد دانلود', 'teznevise' ), 'type' => 'number' ),
			'_teznevise_version'        => array( 'label' => __( 'نسخه', 'teznevise' ), 'type' => 'text' ),
			'_teznevise_license'        => array( 'label' => __( 'مجوز', 'teznevise' ), 'type' => 'text' ),
			'_teznevise_lang'           => array( 'label' => __( 'زبان', 'teznevise' ), 'type' => 'text' ),
			'_teznevise_source'         => array( 'label' => __( 'منبع', 'teznevise' ), 'type' => 'text' ),
		),
		'tz_service'  => array(
			'_tz_price_min' => array( 'label' => __( 'حداقل قیمت', 'teznevise' ), 'type' => 'number' ),
			'_tz_price_max' => array( 'label' => __( 'حداکثر قیمت', 'teznevise' ), 'type' => 'number' ),
			'_tz_unit'      => array( 'label' => __( 'واحد', 'teznevise' ), 'type' => 'text' ),
			'_tz_duration'  => array( 'label' => __( 'مدت زمان', 'teznevise' ), 'type' => 'text' ),
			'_tz_desc'      => array( 'label' => __( 'توضیحات', 'teznevise' ), 'type' => 'textarea' ),
			'_tz_note'      => array( 'label' => __( 'نکته', 'teznevise' ), 'type' => 'textarea' ),
			'_tz_icon'      => array( 'label' => __( 'آیکون (Font Awesome)', 'teznevise' ), 'type' => 'text' ),
			'_tz_factors'   => array( 'label' => __( 'عوامل قیمت (هر خط یک مورد)', 'teznevise' ), 'type' => 'textarea' ),
		),
		'case_study'  => array(
			'_tz_cs_client'    => array( 'label' => __( 'مشتری', 'teznevise' ), 'type' => 'text' ),
			'_tz_cs_field'     => array( 'label' => __( 'حوزه', 'teznevise' ), 'type' => 'text' ),
			'_tz_cs_region'    => array( 'label' => __( 'منطقه', 'teznevise' ), 'type' => 'text' ),
			'_tz_cs_duration'  => array( 'label' => __( 'مدت زمان', 'teznevise' ), 'type' => 'text' ),
			'_tz_cs_degree'    => array( 'label' => __( 'مقطع', 'teznevise' ), 'type' => 'text' ),
			'_tz_cs_service'   => array( 'label' => __( 'خدمت', 'teznevise' ), 'type' => 'text' ),
			'_tz_cs_challenge' => array( 'label' => __( 'چالش', 'teznevise' ), 'type' => 'textarea' ),
			'_tz_cs_solution'  => array( 'label' => __( 'راهکار', 'teznevise' ), 'type' => 'textarea' ),
			'_tz_cs_result'    => array( 'label' => __( 'نتیجه', 'teznevise' ), 'type' => 'textarea' ),
			'_tz_cs_quote'     => array( 'label' => __( 'نقل قول', 'teznevise' ), 'type' => 'textarea' ),
			'_tz_cs_metrics'   => array( 'label' => __( 'معیارها', 'teznevise' ), 'type' => 'textarea' ),
			'_tz_cs_tools'     => array( 'label' => __( 'ابزارها', 'teznevise' ), 'type' => 'text' ),
			'_tz_cs_icon'      => array( 'label' => __( 'آیکون', 'teznevise' ), 'type' => 'text' ),
		),
	);
}

/**
 * Register post meta for REST + sanitization.
 */
function teznevise_register_cpt_meta() {
	foreach ( teznevise_cpt_meta_fields() as $post_type => $fields ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}
		foreach ( $fields as $key => $def ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'auth_callback'     => function ( $allowed, $meta_key, $object_id ) {
						return current_user_can( 'edit_post', (int) $object_id );
					},
					'sanitize_callback' => function ( $value ) use ( $def ) {
						if ( 'textarea' === $def['type'] ) {
							return sanitize_textarea_field( is_string( $value ) ? $value : '' );
						}
						if ( 'number' === $def['type'] ) {
							return is_numeric( $value ) ? (string) $value : '';
						}
						return sanitize_text_field( is_string( $value ) ? $value : '' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'teznevise_register_cpt_meta', 20 );

/**
 * Meta boxes for CPT custom fields.
 */
function teznevise_cpt_add_meta_boxes() {
	$map = array(
		'download'   => __( 'فیلدهای دانلود', 'teznevise' ),
		'tz_service' => __( 'فیلدهای قیمت / خدمت', 'teznevise' ),
		'case_study' => __( 'فیلدهای مطالعه موردی', 'teznevise' ),
	);
	foreach ( $map as $post_type => $title ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}
		add_meta_box(
			'teznevise_cpt_meta_' . $post_type,
			$title,
			'teznevise_cpt_render_meta_box',
			$post_type,
			'normal',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'teznevise_cpt_add_meta_boxes' );

/**
 * Render CPT meta box.
 *
 * @param WP_Post $post Post.
 */
function teznevise_cpt_render_meta_box( $post ) {
	$all = teznevise_cpt_meta_fields();
	if ( empty( $all[ $post->post_type ] ) ) {
		return;
	}
	wp_nonce_field( 'teznevise_save_cpt_meta', 'teznevise_cpt_meta_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $all[ $post->post_type ] as $key => $def ) {
		$value = get_post_meta( $post->ID, $key, true );
		$id    = 'tez_' . sanitize_html_class( $key );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $def['label'] ) . '</label></th><td>';
		if ( 'textarea' === $def['type'] ) {
			printf(
				'<textarea class="large-text" rows="4" id="%1$s" name="%2$s">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $key ),
				esc_textarea( (string) $value )
			);
		} else {
			printf(
				'<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s" />',
				'number' === $def['type'] ? 'number' : 'text',
				esc_attr( $id ),
				esc_attr( $key ),
				esc_attr( (string) $value )
			);
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Save CPT meta.
 *
 * @param int $post_id Post ID.
 */
function teznevise_cpt_save_meta( $post_id ) {
	if ( ! isset( $_POST['teznevise_cpt_meta_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_cpt_meta_nonce'] ) ), 'teznevise_save_cpt_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$all       = teznevise_cpt_meta_fields();
	if ( empty( $all[ $post_type ] ) ) {
		return;
	}

	foreach ( $all[ $post_type ] as $key => $def ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );
		if ( 'textarea' === $def['type'] ) {
			$val = sanitize_textarea_field( $raw );
		} elseif ( 'number' === $def['type'] ) {
			$val = is_numeric( $raw ) ? (string) $raw : '';
		} else {
			$val = sanitize_text_field( $raw );
		}
		update_post_meta( $post_id, $key, $val );
	}
}
add_action( 'save_post', 'teznevise_cpt_save_meta' );

/**
 * Minimal shortcodes so pages keep working after WPCode is disabled.
 * Full UI markup can stay in WPCode or be moved later.
 */
function teznevise_register_fallback_shortcodes() {
	if ( ! shortcode_exists( 'teznevise_download_category' ) ) {
		add_shortcode(
			'teznevise_download_category',
			function ( $atts ) {
				$atts = shortcode_atts(
					array(
						'slug'  => '',
						'count' => 12,
					),
					$atts,
					'teznevise_download_category'
				);
				$q = array(
					'post_type'      => 'download',
					'posts_per_page' => absint( $atts['count'] ),
					'post_status'    => 'publish',
				);
				if ( $atts['slug'] && taxonomy_exists( 'download_category' ) ) {
					$q['tax_query'] = array(
						array(
							'taxonomy' => 'download_category',
							'field'    => 'slug',
							'terms'    => sanitize_title( $atts['slug'] ),
						),
					);
				}
				$posts = get_posts( $q );
				if ( ! $posts ) {
					return '<p class="tez-dl-empty">' . esc_html__( 'موردی یافت نشد.', 'teznevise' ) . '</p>';
				}
				ob_start();
				echo '<div class="tez-dl-grid services-grid" style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">';
				foreach ( $posts as $p ) {
					printf(
						'<a class="service-card" href="%1$s"><h3>%2$s</h3><p>%3$s</p></a>',
						esc_url( get_permalink( $p ) ),
						esc_html( get_the_title( $p ) ),
						esc_html( wp_trim_words( $p->post_excerpt ? $p->post_excerpt : $p->post_content, 18 ) )
					);
				}
				echo '</div>';
				return ob_get_clean();
			}
		);
	}

	if ( ! shortcode_exists( 'tz_price_box' ) ) {
		add_shortcode(
			'tz_price_box',
			function ( $atts ) {
				$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'tz_price_box' );
				$id   = absint( $atts['id'] );
				if ( ! $id ) {
					return '';
				}
				$min  = get_post_meta( $id, '_tz_price_min', true );
				$max  = get_post_meta( $id, '_tz_price_max', true );
				$unit = get_post_meta( $id, '_tz_unit', true );
				$desc = get_post_meta( $id, '_tz_desc', true );
				$title = get_the_title( $id );
				ob_start();
				echo '<div class="tez-price-box service-card">';
				echo '<h3>' . esc_html( $title ) . '</h3>';
				if ( $desc ) {
					echo '<p>' . esc_html( $desc ) . '</p>';
				}
				if ( $min || $max ) {
					$range = $min;
					if ( $max && $max !== $min ) {
						$range .= ' – ' . $max;
					}
					if ( $unit ) {
						$range .= ' ' . $unit;
					}
					echo '<p class="tez-price-range"><strong>' . esc_html( $range ) . '</strong></p>';
				}
				echo '</div>';
				return ob_get_clean();
			}
		);
	}
}
add_action( 'init', 'teznevise_register_fallback_shortcodes', 30 );
