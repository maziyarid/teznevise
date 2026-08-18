<?php
/**
 * Flexible page builder — section registry, storage, sanitization and rendering.
 *
 * Sections are stored as a single JSON array in the `_teznevise_builder_sections`
 * post meta. Each section is `{ type, <section fields>, items: [ { <item fields> } ] }`
 * so editors can add, duplicate, reorder and remove an unlimited number of items
 * without touching template code.
 *
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEZNEVISE_BUILDER_META', '_teznevise_builder_sections' );

/**
 * Post types the builder is available on.
 *
 * @return string[]
 */
function teznevise_builder_post_types() {
	return (array) apply_filters( 'teznevise_builder_post_types', array( 'page', 'post' ) );
}

/**
 * Section-level field definitions shared by every section type.
 *
 * @return array<string,array>
 */
function teznevise_builder_section_fields() {
	return array(
		'eyebrow' => array(
			'label' => __( 'ابرو (Eyebrow)', 'teznevise' ),
			'type'  => 'text',
		),
		'title'   => array(
			'label' => __( 'عنوان بخش', 'teznevise' ),
			'type'  => 'text',
		),
		'text'    => array(
			'label' => __( 'توضیح بخش', 'teznevise' ),
			'type'  => 'textarea',
		),
		'cta_text' => array(
			'label' => __( 'متن دکمه', 'teznevise' ),
			'type'  => 'text',
		),
		'cta_url'  => array(
			'label' => __( 'لینک دکمه', 'teznevise' ),
			'type'  => 'url',
		),
		'columns'  => array(
			'label'   => __( 'تعداد ستون', 'teznevise' ),
			'type'    => 'select',
			'choices' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
			),
			'default' => '3',
		),
		'background' => array(
			'label'   => __( 'پس‌زمینه', 'teznevise' ),
			'type'    => 'select',
			'choices' => array(
				'default' => __( 'پیش‌فرض', 'teznevise' ),
				'soft'    => __( 'ملایم', 'teznevise' ),
			),
			'default' => 'default',
		),
	);
}

/**
 * Item field definitions used by the card-style section types.
 *
 * @return array<string,array>
 */
function teznevise_builder_card_item_fields() {
	return array(
		'title'    => array(
			'label' => __( 'عنوان', 'teznevise' ),
			'type'  => 'text',
		),
		'text'     => array(
			'label' => __( 'توضیح', 'teznevise' ),
			'type'  => 'textarea',
		),
		'icon'     => array(
			'label' => __( 'آیکون Font Awesome', 'teznevise' ),
			'type'  => 'icon',
		),
		'icon_svg' => array(
			'label'       => __( 'آیکون تصویری / SVG', 'teznevise' ),
			'type'        => 'image',
			'description' => __( 'در صورت انتخاب، جای آیکون Font Awesome نمایش داده می‌شود.', 'teznevise' ),
		),
		'color'    => array(
			'label'   => __( 'رنگ باکس آیکون', 'teznevise' ),
			'type'    => 'color',
			'default' => 'icon-teal',
		),
		'url'      => array(
			'label' => __( 'لینک', 'teznevise' ),
			'type'  => 'url',
		),
		'badge'    => array(
			'label' => __( 'برچسب کوچک', 'teznevise' ),
			'type'  => 'text',
		),
	);
}

/**
 * Registered section types.
 *
 * @return array<string,array>
 */
function teznevise_builder_section_types() {
	$card_fields = teznevise_builder_card_item_fields();

	$types = array(
		'hero'             => array(
			'label'       => __( 'هیرو صفحه', 'teznevise' ),
			'description' => __( 'ابرو، عنوان، توضیح، دکمه و نکات اعتماد', 'teznevise' ),
			'supports'    => array( 'eyebrow', 'title', 'text', 'cta_text', 'cta_url', 'background' ),
			'item_label'  => __( 'نکته اعتماد', 'teznevise' ),
			'item_fields' => array(
				'title' => $card_fields['title'],
				'icon'  => $card_fields['icon'],
			),
			'render'      => 'teznevise_builder_render_hero',
		),
		'software_catalog' => array(
			'label'       => __( 'کاتالوگ نرم‌افزارها', 'teznevise' ),
			'description' => __( 'شبکه‌ای از نرم‌افزارها و ابزارها با آیکون', 'teznevise' ),
			'supports'    => array( 'eyebrow', 'title', 'text', 'columns', 'background' ),
			'item_label'  => __( 'نرم‌افزار', 'teznevise' ),
			'item_fields' => $card_fields,
			'render'      => 'teznevise_builder_render_card_grid',
		),
		'challenges'       => array(
			'label'       => __( 'چالش‌ها', 'teznevise' ),
			'description' => __( 'کارت‌های چالش با آیکون و توضیح', 'teznevise' ),
			'supports'    => array( 'eyebrow', 'title', 'text', 'columns', 'background' ),
			'item_label'  => __( 'چالش', 'teznevise' ),
			'item_fields' => $card_fields,
			'render'      => 'teznevise_builder_render_card_grid',
		),
		'service_cards'    => array(
			'label'       => __( 'کارت‌های خدمت', 'teznevise' ),
			'description' => __( 'شبکه خدمات با آیکون، توضیح و لینک', 'teznevise' ),
			'supports'    => array( 'eyebrow', 'title', 'text', 'columns', 'background' ),
			'item_label'  => __( 'خدمت', 'teznevise' ),
			'item_fields' => $card_fields,
			'render'      => 'teznevise_builder_render_card_grid',
		),
		'feature_list'     => array(
			'label'       => __( 'فهرست ویژگی‌ها', 'teznevise' ),
			'description' => __( 'لیست بولت‌دار با آیکون تیک', 'teznevise' ),
			'supports'    => array( 'eyebrow', 'title', 'text', 'columns', 'background' ),
			'item_label'  => __( 'ویژگی', 'teznevise' ),
			'item_fields' => array(
				'title' => $card_fields['title'],
				'text'  => $card_fields['text'],
				'icon'  => $card_fields['icon'],
				'color' => $card_fields['color'],
			),
			'render'      => 'teznevise_builder_render_feature_list',
		),
		'process_steps'    => array(
			'label'       => __( 'مراحل فرایند', 'teznevise' ),
			'description' => __( 'مراحل شماره‌دار همراه با پنل تصویری', 'teznevise' ),
			'supports'    => array( 'eyebrow', 'title', 'text', 'background' ),
			'item_label'  => __( 'مرحله', 'teznevise' ),
			'item_fields' => array(
				'title' => $card_fields['title'],
				'text'  => $card_fields['text'],
				'icon'  => $card_fields['icon'],
				'color' => $card_fields['color'],
				'image' => array(
					'label'       => __( 'تصویر پنل', 'teznevise' ),
					'type'        => 'image',
					'description' => __( 'تصویر توضیحی این مرحله', 'teznevise' ),
				),
			),
			'render'      => 'teznevise_builder_render_process_steps',
		),
		'cta_band'         => array(
			'label'       => __( 'نوار اقدام (CTA)', 'teznevise' ),
			'description' => __( 'نوار پایانی با عنوان، توضیح و دکمه', 'teznevise' ),
			'supports'    => array( 'title', 'text', 'cta_text', 'cta_url', 'background' ),
			'item_label'  => '',
			'item_fields' => array(),
			'render'      => 'teznevise_builder_render_cta_band',
		),
	);

	return (array) apply_filters( 'teznevise_builder_section_types', $types );
}

/**
 * Sanitize a Font Awesome icon class list.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function teznevise_builder_sanitize_icon( $value ) {
	if ( ! is_string( $value ) ) {
		return '';
	}
	$value = strtolower( trim( preg_replace( '/[^a-zA-Z0-9\- ]/', '', $value ) ) );
	$value = preg_replace( '/\s+/', ' ', $value );
	return substr( $value, 0, 100 );
}

/**
 * Sanitize an icon color class.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function teznevise_builder_sanitize_color( $value ) {
	$choices = function_exists( 'teznevise_icon_color_choices' ) ? teznevise_icon_color_choices() : array( 'icon-teal' => 'teal' );
	$value   = is_string( $value ) ? $value : '';
	return isset( $choices[ $value ] ) ? $value : (string) array_key_first( $choices );
}

/**
 * Sanitize one field value according to its definition.
 *
 * @param mixed $value Raw value.
 * @param array $field Field definition.
 * @return mixed
 */
function teznevise_builder_sanitize_field( $value, $field ) {
	$type = isset( $field['type'] ) ? $field['type'] : 'text';

	switch ( $type ) {
		case 'textarea':
			return sanitize_textarea_field( is_string( $value ) ? $value : '' );
		case 'url':
		case 'image':
			$value = is_string( $value ) ? trim( $value ) : '';
			if ( '' === $value ) {
				return '';
			}
			// Relative internal paths stay untouched; templates resolve them.
			if ( 'url' === $type && ! preg_match( '#^(https?:)?//#i', $value ) ) {
				return sanitize_text_field( $value );
			}
			return esc_url_raw( $value );
		case 'icon':
			return teznevise_builder_sanitize_icon( $value );
		case 'color':
			return teznevise_builder_sanitize_color( $value );
		case 'select':
			$choices = isset( $field['choices'] ) ? $field['choices'] : array();
			$value   = is_string( $value ) ? $value : '';
			if ( isset( $choices[ $value ] ) ) {
				return $value;
			}
			return isset( $field['default'] ) ? $field['default'] : (string) array_key_first( $choices );
		default:
			return sanitize_text_field( is_string( $value ) ? $value : '' );
	}
}

/**
 * Sanitize a full sections payload.
 *
 * Unknown section types, unknown fields and non-array input are dropped.
 *
 * @param mixed $sections Raw sections.
 * @return array
 */
function teznevise_builder_sanitize_sections( $sections ) {
	if ( ! is_array( $sections ) ) {
		return array();
	}

	$types          = teznevise_builder_section_types();
	$section_fields = teznevise_builder_section_fields();
	$clean          = array();

	foreach ( $sections as $section ) {
		if ( ! is_array( $section ) ) {
			continue;
		}
		$type = isset( $section['type'] ) ? sanitize_key( $section['type'] ) : '';
		if ( ! isset( $types[ $type ] ) ) {
			continue;
		}

		$definition   = $types[ $type ];
		$clean_section = array(
			'type'    => $type,
			'enabled' => empty( $section['enabled'] ) ? false : true,
			'items'   => array(),
		);

		foreach ( (array) $definition['supports'] as $key ) {
			if ( ! isset( $section_fields[ $key ] ) ) {
				continue;
			}
			$raw                   = isset( $section[ $key ] ) ? $section[ $key ] : '';
			$clean_section[ $key ] = teznevise_builder_sanitize_field( $raw, $section_fields[ $key ] );
		}

		$item_fields = (array) $definition['item_fields'];
		$raw_items   = isset( $section['items'] ) && is_array( $section['items'] ) ? $section['items'] : array();
		foreach ( $raw_items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$clean_item = array();
			foreach ( $item_fields as $key => $field ) {
				$clean_item[ $key ] = teznevise_builder_sanitize_field( isset( $item[ $key ] ) ? $item[ $key ] : '', $field );
				if ( 'image' === $field['type'] ) {
					$clean_item[ $key . '_id' ] = isset( $item[ $key . '_id' ] ) ? absint( $item[ $key . '_id' ] ) : 0;
				}
			}
			if ( '' === implode( '', array_map( 'strval', $clean_item ) ) ) {
				continue;
			}
			$clean_section['items'][] = $clean_item;
		}

		$clean[] = $clean_section;
	}

	return $clean;
}

/**
 * Read the stored sections for a post.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function teznevise_builder_get_sections( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( ! $post_id ) {
		return array();
	}

	$raw = get_post_meta( $post_id, TEZNEVISE_BUILDER_META, true );
	if ( is_string( $raw ) && '' !== $raw ) {
		$raw = json_decode( $raw, true );
	}
	if ( ! is_array( $raw ) ) {
		return array();
	}

	return teznevise_builder_sanitize_sections( $raw );
}

/**
 * Persist sections for a post.
 *
 * @param int   $post_id  Post ID.
 * @param array $sections Sections (sanitized internally).
 * @return bool
 */
function teznevise_builder_save_sections( $post_id, $sections ) {
	$post_id  = (int) $post_id;
	$sections = teznevise_builder_sanitize_sections( $sections );

	if ( ! $sections ) {
		delete_post_meta( $post_id, TEZNEVISE_BUILDER_META );
		return true;
	}

	$json = wp_json_encode( $sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( false === $json ) {
		return false;
	}

	return (bool) update_post_meta( $post_id, TEZNEVISE_BUILDER_META, wp_slash( $json ) );
}

/**
 * Register the builder meta so it is exposed to the REST API.
 */
function teznevise_builder_register_meta() {
	foreach ( teznevise_builder_post_types() as $post_type ) {
		register_post_meta(
			$post_type,
			TEZNEVISE_BUILDER_META,
			array(
				'type'              => 'string',
				'description'       => __( 'بخش‌های صفحه‌ساز تزنویسه (JSON)', 'teznevise' ),
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => function ( $value ) {
					$decoded = is_string( $value ) ? json_decode( $value, true ) : $value;
					$clean   = teznevise_builder_sanitize_sections( $decoded );
					if ( ! $clean ) {
						return '';
					}
					return (string) wp_json_encode( $clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
				},
			)
		);
	}
}
add_action( 'init', 'teznevise_builder_register_meta', 15 );

/**
 * Column count → CSS min track width.
 *
 * @param string $columns Column count.
 * @return string
 */
function teznevise_builder_grid_class( $columns ) {
	$columns = in_array( (string) $columns, array( '2', '3', '4' ), true ) ? (string) $columns : '3';
	return 'tez-builder-grid tez-builder-cols-' . $columns;
}

/**
 * Icon (or uploaded SVG/image) markup for a builder item.
 *
 * @param array  $item          Item data.
 * @param string $fallback_icon Fallback Font Awesome class.
 * @return string
 */
function teznevise_builder_icon_markup( $item, $fallback_icon = 'fa-solid fa-circle-check' ) {
	$color = teznevise_builder_sanitize_color( isset( $item['color'] ) ? $item['color'] : '' );
	$svg   = isset( $item['icon_svg'] ) ? $item['icon_svg'] : '';
	$icon  = isset( $item['icon'] ) && $item['icon'] ? $item['icon'] : $fallback_icon;

	$inner = $svg
		? '<img src="' . esc_url( $svg ) . '" alt="" width="28" height="28" loading="lazy" decoding="async" />'
		: '<i class="' . esc_attr( $icon ) . '" aria-hidden="true"></i>';

	return '<div class="icon-box ' . esc_attr( $color ) . '">' . $inner . '</div>';
}

/**
 * Shared section head markup.
 *
 * @param array $section Section data.
 */
function teznevise_builder_section_head( $section ) {
	$eyebrow = isset( $section['eyebrow'] ) ? $section['eyebrow'] : '';
	$title   = isset( $section['title'] ) ? $section['title'] : '';
	$text    = isset( $section['text'] ) ? $section['text'] : '';

	if ( '' === $eyebrow && '' === $title && '' === $text ) {
		return;
	}
	?>
	<div class="section-head" data-reveal>
		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>
		<?php if ( $title ) : ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $text ) : ?>
			<p><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Open a builder section wrapper.
 *
 * @param array  $section Section data.
 * @param string $extra   Extra CSS classes.
 */
function teznevise_builder_open_section( $section, $extra = '' ) {
	$classes = array( 'section', 'tez-builder-section', 'tez-builder-section-' . $section['type'] );
	if ( isset( $section['background'] ) && 'soft' === $section['background'] ) {
		$classes[] = 'bg-soft';
	}
	if ( $extra ) {
		$classes[] = $extra;
	}
	echo '<section class="' . esc_attr( implode( ' ', $classes ) ) . '"><div class="container">';
}

/**
 * Close a builder section wrapper.
 */
function teznevise_builder_close_section() {
	echo '</div></section>';
}

/**
 * Section CTA button markup.
 *
 * @param array  $section Section data.
 * @param string $class   Button class.
 */
function teznevise_builder_cta( $section, $class = 'btn-tz btn-primary-tz btn-lg-tz' ) {
	$text = isset( $section['cta_text'] ) ? $section['cta_text'] : '';
	if ( ! $text ) {
		return;
	}
	$url = isset( $section['cta_url'] ) ? $section['cta_url'] : '';
	printf(
		'<a class="%1$s" href="%2$s">%3$s</a>',
		esc_attr( $class ),
		esc_url( teznevise_url( $url ) ),
		esc_html( $text )
	);
}

/**
 * Hero section renderer.
 *
 * @param array $section Section data.
 */
function teznevise_builder_render_hero( $section ) {
	teznevise_builder_open_section( $section, 'page-hero-new' );
	teznevise_builder_section_head( $section );

	if ( ! empty( $section['cta_text'] ) ) {
		echo '<div class="hero-actions tez-builder-actions">';
		teznevise_builder_cta( $section );
		echo '</div>';
	}

	if ( ! empty( $section['items'] ) ) {
		echo '<ul class="tez-builder-points" data-reveal-stagger>';
		foreach ( $section['items'] as $item ) {
			if ( empty( $item['title'] ) ) {
				continue;
			}
			$icon = ! empty( $item['icon'] ) ? $item['icon'] : 'fa-solid fa-check';
			printf(
				'<li><i class="%1$s" aria-hidden="true"></i><span>%2$s</span></li>',
				esc_attr( $icon ),
				esc_html( $item['title'] )
			);
		}
		echo '</ul>';
	}

	teznevise_builder_close_section();
}

/**
 * Card grid renderer (software catalog, challenges, service cards).
 *
 * @param array $section Section data.
 */
function teznevise_builder_render_card_grid( $section ) {
	if ( empty( $section['items'] ) ) {
		return;
	}

	teznevise_builder_open_section( $section );
	teznevise_builder_section_head( $section );

	$columns = isset( $section['columns'] ) ? $section['columns'] : '3';
	echo '<div class="' . esc_attr( teznevise_builder_grid_class( $columns ) ) . '" data-reveal-stagger>';

	foreach ( $section['items'] as $item ) {
		$url     = isset( $item['url'] ) ? $item['url'] : '';
		$tag     = $url ? 'a' : 'div';
		$attrs   = $url ? ' href="' . esc_url( teznevise_url( $url ) ) . '"' : '';
		$has_svg = ! empty( $item['icon_svg'] );

		echo '<' . $tag . ' class="service-card tez-builder-card' . ( $has_svg ? ' has-svg-icon' : '' ) . '"' . $attrs . '>';
		echo teznevise_builder_icon_markup( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.

		if ( ! empty( $item['badge'] ) ) {
			echo '<span class="tez-builder-badge">' . esc_html( $item['badge'] ) . '</span>';
		}
		if ( ! empty( $item['title'] ) ) {
			echo '<h3>' . esc_html( $item['title'] ) . '</h3>';
		}
		if ( ! empty( $item['text'] ) ) {
			echo '<p>' . esc_html( $item['text'] ) . '</p>';
		}
		if ( $url ) {
			echo '<span class="link-arrow">' . esc_html__( 'مشاهده', 'teznevise' ) . ' <span>←</span></span>';
		}
		echo '</' . $tag . '>';
	}

	echo '</div>';
	teznevise_builder_close_section();
}

/**
 * Feature list renderer.
 *
 * @param array $section Section data.
 */
function teznevise_builder_render_feature_list( $section ) {
	if ( empty( $section['items'] ) ) {
		return;
	}

	teznevise_builder_open_section( $section );
	teznevise_builder_section_head( $section );

	$columns = isset( $section['columns'] ) ? $section['columns'] : '3';
	echo '<ul class="reason-list ' . esc_attr( teznevise_builder_grid_class( $columns ) ) . '" data-reveal-stagger>';

	foreach ( $section['items'] as $item ) {
		if ( empty( $item['title'] ) && empty( $item['text'] ) ) {
			continue;
		}
		echo '<li class="reason-item tez-builder-feature">';
		echo teznevise_builder_icon_markup( $item, 'fa-solid fa-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
		echo '<div>';
		if ( ! empty( $item['title'] ) ) {
			echo '<h3>' . esc_html( $item['title'] ) . '</h3>';
		}
		if ( ! empty( $item['text'] ) ) {
			echo '<p>' . esc_html( $item['text'] ) . '</p>';
		}
		echo '</div></li>';
	}

	echo '</ul>';
	teznevise_builder_close_section();
}

/**
 * Process steps renderer.
 *
 * @param array $section Section data.
 */
function teznevise_builder_render_process_steps( $section ) {
	if ( empty( $section['items'] ) ) {
		return;
	}

	teznevise_builder_open_section( $section );
	teznevise_builder_section_head( $section );

	echo '<ol class="tez-builder-steps" data-reveal-stagger>';
	$index = 0;
	foreach ( $section['items'] as $item ) {
		$index++;
		echo '<li class="tez-builder-step">';
		echo '<div class="tez-builder-step-body">';
		echo '<span class="tez-builder-step-number" aria-hidden="true">' . esc_html( number_format_i18n( $index ) ) . '</span>';
		echo teznevise_builder_icon_markup( $item, 'fa-solid fa-arrow-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
		if ( ! empty( $item['title'] ) ) {
			echo '<h3>' . esc_html( $item['title'] ) . '</h3>';
		}
		if ( ! empty( $item['text'] ) ) {
			echo '<p>' . esc_html( $item['text'] ) . '</p>';
		}
		echo '</div>';

		if ( ! empty( $item['image'] ) ) {
			$alt = ! empty( $item['title'] ) ? $item['title'] : '';
			printf(
				'<figure class="tez-builder-step-visual"><img src="%1$s" alt="%2$s" loading="lazy" decoding="async" /></figure>',
				esc_url( $item['image'] ),
				esc_attr( $alt )
			);
		}
		echo '</li>';
	}
	echo '</ol>';

	teznevise_builder_close_section();
}

/**
 * CTA band renderer.
 *
 * @param array $section Section data.
 */
function teznevise_builder_render_cta_band( $section ) {
	$title = isset( $section['title'] ) ? $section['title'] : '';
	$text  = isset( $section['text'] ) ? $section['text'] : '';
	if ( '' === $title && '' === $text ) {
		return;
	}

	teznevise_builder_open_section( $section );
	echo '<div class="cta-band" data-reveal><div>';
	if ( $title ) {
		echo '<h2>' . esc_html( $title ) . '</h2>';
	}
	if ( $text ) {
		echo '<p>' . esc_html( $text ) . '</p>';
	}
	echo '</div>';
	teznevise_builder_cta( $section, 'btn-tz btn-light-tz btn-lg-tz' );
	echo '</div>';
	teznevise_builder_close_section();
}

/**
 * Render every enabled builder section for a post.
 *
 * Templates call this once; editors control the number, order and content of
 * the sections from the post editor.
 *
 * @param int $post_id Post ID.
 */
function teznevise_builder_render_sections( $post_id = 0 ) {
	$sections = teznevise_builder_get_sections( $post_id );
	if ( ! $sections ) {
		return;
	}

	$types = teznevise_builder_section_types();
	foreach ( $sections as $section ) {
		if ( empty( $section['enabled'] ) ) {
			continue;
		}
		$definition = isset( $types[ $section['type'] ] ) ? $types[ $section['type'] ] : null;
		if ( ! $definition || empty( $definition['render'] ) || ! is_callable( $definition['render'] ) ) {
			continue;
		}
		call_user_func( $definition['render'], $section );
	}
}

/**
 * Whether the current singular view renders builder sections.
 *
 * @return bool
 */
function teznevise_builder_has_sections() {
	if ( ! is_singular( teznevise_builder_post_types() ) ) {
		return false;
	}
	foreach ( teznevise_builder_get_sections( get_queried_object_id() ) as $section ) {
		if ( ! empty( $section['enabled'] ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Frontend styles, loaded only where sections exist.
 */
function teznevise_builder_enqueue_frontend() {
	if ( ! teznevise_builder_has_sections() ) {
		return;
	}
	wp_enqueue_style(
		'teznevise-builder-frontend',
		TEZNEVISE_URI . '/assets/css/builder-frontend.css',
		array( 'teznevise-redesign' ),
		TEZNEVISE_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'teznevise_builder_enqueue_frontend', 20 );
