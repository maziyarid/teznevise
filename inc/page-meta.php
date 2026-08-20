<?php
/**
 * Page custom fields — registered post meta + admin meta boxes.
 *
 * Icons are selectable from a curated Font Awesome list or entered manually.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Curated Font Awesome icon choices for landing pages.
 *
 * @return array<string,string> class => label
 */
function teznevise_icon_choices() {
	return array(
		''                              => __( '— بدون آیکون / سفارشی —', 'teznevise' ),
		'fa-solid fa-graduation-cap'    => 'graduation-cap — پایان‌نامه',
		'fa-solid fa-file-circle-check' => 'file-circle-check — پروپوزال',
		'fa-solid fa-chart-line'        => 'chart-line — آمار',
		'fa-solid fa-chart-simple'      => 'chart-simple',
		'fa-solid fa-calculator'        => 'calculator — ابزار',
		'fa-solid fa-flask'             => 'flask — شبیه‌سازی',
		'fa-solid fa-microscope'        => 'microscope',
		'fa-solid fa-book-open'         => 'book-open',
		'fa-regular fa-lightbulb'       => 'lightbulb — دانش',
		'fa-solid fa-user-group'        => 'user-group — تیم',
		'fa-solid fa-users'             => 'users',
		'fa-solid fa-phone'             => 'phone',
		'fa-solid fa-envelope'          => 'envelope',
		'fa-brands fa-whatsapp'         => 'whatsapp',
		'fa-brands fa-telegram'         => 'telegram',
		'fa-solid fa-shield-halved'     => 'shield — امنیت',
		'fa-solid fa-lock'              => 'lock',
		'fa-solid fa-scale-balanced'    => 'scale — حقوقی',
		'fa-solid fa-sitemap'           => 'sitemap',
		'fa-solid fa-house'             => 'house',
		'fa-solid fa-rocket'            => 'rocket',
		'fa-solid fa-pen-to-square'     => 'pen — سفارش',
		'fa-solid fa-comments'          => 'comments',
		'fa-solid fa-circle-question'   => 'question',
		'fa-solid fa-compass-drafting'  => 'compass',
		'fa-solid fa-bolt'              => 'bolt',
		'fa-solid fa-pen-ruler'         => 'pen-ruler',
		'fa-solid fa-laptop-code'       => 'laptop-code',
		'fa-solid fa-database'          => 'database',
		'fa-solid fa-file-lines'        => 'file-lines',
		'fa-solid fa-clipboard-list'    => 'clipboard-list',
	);
}

/**
 * Icon color (CSS class) choices.
 *
 * @return array<string,string>
 */
function teznevise_icon_color_choices() {
	return array(
		'icon-teal'        => 'teal',
		'icon-indigo'      => 'indigo',
		'icon-cyan'        => 'cyan',
		'icon-amber'       => 'amber',
		'icon-danger-soft' => 'danger-soft',
		'icon-purple-soft' => 'purple-soft',
	);
}

/**
 * Schema of page meta fields (grouped).
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
			'group'       => 'header',
		),
		'subtitle' => array(
			'label'       => __( 'زیرعنوان', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'توضیح کوتاه زیر عنوان', 'teznevise' ),
			'group'       => 'header',
		),
		'hero_note' => array(
			'label'       => __( 'یادداشت هیرو / تاریخ به‌روزرسانی', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'متن کمکی کنار عنوان یا تاریخ سیاست حریم خصوصی', 'teznevise' ),
			'group'       => 'header',
		),
		'service_icon' => array(
			'label'       => __( 'آیکون (Font Awesome)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'از لیست انتخاب کنید یا کلاس سفارشی بنویسید', 'teznevise' ),
			'group'       => 'icon',
			'ui'          => 'icon',
		),
		'service_icon_custom' => array(
			'label'       => __( 'کلاس آیکون سفارشی', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'اگر در لیست نبود، اینجا وارد کنید. اولویت با این فیلد است.', 'teznevise' ),
			'group'       => 'icon',
		),
		'service_color' => array(
			'label'       => __( 'رنگ باکس آیکون', 'teznevise' ),
			'type'        => 'string',
			'default'     => 'icon-teal',
			'description' => __( 'کلاس CSS رنگ', 'teznevise' ),
			'group'       => 'icon',
			'ui'          => 'color',
		),
		'cta_text' => array(
			'label'   => __( 'متن دکمه CTA اصلی', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
			'group'   => 'cta',
		),
		'cta_url' => array(
			'label'   => __( 'لینک دکمه CTA اصلی', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
			'group'   => 'cta',
		),
		'secondary_cta_text' => array(
			'label'   => __( 'متن دکمه فرعی', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
			'group'   => 'cta',
		),
		'secondary_cta_url' => array(
			'label'   => __( 'لینک دکمه فرعی', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
			'group'   => 'cta',
		),
		'features' => array(
			'label'       => __( 'ویژگی‌ها (هر خط یک مورد)', 'teznevise' ),
			'type'        => 'string',
			'default'     => '',
			'description' => __( 'برای صفحات خدمت — هر خط یک بولت', 'teznevise' ),
			'ui'          => 'textarea',
			'group'       => 'content',
		),
		'price_note' => array(
			'label'   => __( 'یادداشت قیمت / زمان', 'teznevise' ),
			'type'    => 'string',
			'default' => '',
			'group'   => 'content',
		),
		'hide_title' => array(
			'label'       => __( 'مخفی کردن عنوان پیش‌فرض', 'teznevise' ),
			'type'        => 'boolean',
			'default'     => false,
			'description' => __( 'اگر فعال باشد عنوان H1 از قالب نشان داده نمی‌شود', 'teznevise' ),
			'ui'          => 'checkbox',
			'group'       => 'content',
		),
	);
}

/**
 * Meta field groups for admin UI.
 *
 * @return array<string,string>
 */
function teznevise_page_meta_groups() {
	return array(
		'header'  => __( 'سربرگ صفحه', 'teznevise' ),
		'icon'    => __( 'آیکون (قابل انتخاب دستی)', 'teznevise' ),
		'cta'     => __( 'دکمه‌های اقدام', 'teznevise' ),
		'content' => __( 'محتوا و تنظیمات', 'teznevise' ),
	);
}

/**
 * Resolve effective icon class (custom overrides preset).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function teznevise_get_page_icon( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$custom  = teznevise_page_field( 'service_icon_custom', $post_id, '' );
	if ( $custom ) {
		return $custom;
	}
	return teznevise_page_field( 'service_icon', $post_id, '' );
}

/**
 * Register post meta for pages.
 */
function teznevise_register_page_meta() {
	$schema = teznevise_page_meta_schema();
	foreach ( $schema as $key => $args ) {
		$type    = isset( $args['type'] ) ? $args['type'] : 'string';
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
				'auth_callback'     => function ( $allowed, $meta_key, $object_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
					return current_user_can( 'edit_page', (int) $object_id );
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
 * Register term meta for category/tag icons.
 */
function teznevise_register_term_meta() {
	foreach ( array( 'category', 'post_tag' ) as $taxonomy ) {
		register_term_meta(
			$taxonomy,
			'_teznevise_term_icon',
			array(
				'type'              => 'string',
				'description'       => __( 'Font Awesome icon class for taxonomy hub', 'teznevise' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
	}
}
add_action( 'init', 'teznevise_register_term_meta' );

/**
 * Category/tag icon fields in admin.
 */
function teznevise_term_icon_field_add() {
	?>
	<div class="form-field">
		<label for="teznevise_term_icon"><?php esc_html_e( 'آیکون (Font Awesome)', 'teznevise' ); ?></label>
		<input type="text" name="teznevise_term_icon" id="teznevise_term_icon" value="" placeholder="fa-solid fa-folder">
		<p class="description"><?php esc_html_e( 'مثال: fa-solid fa-book-open', 'teznevise' ); ?></p>
	</div>
	<?php
}
function teznevise_term_icon_field_edit( $term ) {
	$icon = get_term_meta( $term->term_id, '_teznevise_term_icon', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="teznevise_term_icon"><?php esc_html_e( 'آیکون (Font Awesome)', 'teznevise' ); ?></label></th>
		<td>
			<input type="text" name="teznevise_term_icon" id="teznevise_term_icon" value="<?php echo esc_attr( $icon ); ?>" class="regular-text" placeholder="fa-solid fa-folder">
			<p class="description"><?php esc_html_e( 'کلاس Font Awesome برای صفحه دسته/برچسب', 'teznevise' ); ?></p>
		</td>
	</tr>
	<?php
}
function teznevise_save_term_icon( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['teznevise_term_icon'] ) ) {
		update_term_meta( $term_id, '_teznevise_term_icon', sanitize_text_field( wp_unslash( $_POST['teznevise_term_icon'] ) ) );
	}
}
add_action( 'category_add_form_fields', 'teznevise_term_icon_field_add' );
add_action( 'post_tag_add_form_fields', 'teznevise_term_icon_field_add' );
add_action( 'category_edit_form_fields', 'teznevise_term_icon_field_edit' );
add_action( 'post_tag_edit_form_fields', 'teznevise_term_icon_field_edit' );
add_action( 'created_category', 'teznevise_save_term_icon' );
add_action( 'edited_category', 'teznevise_save_term_icon' );
add_action( 'created_post_tag', 'teznevise_save_term_icon' );
add_action( 'edited_post_tag', 'teznevise_save_term_icon' );

/**
 * Meta box.
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
 * Admin assets for icon preview.
 *
 * @param string $hook Hook.
 */
function teznevise_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_style(
		'teznevise-fa-admin',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
		array(),
		'6.5.2'
	);
	wp_add_inline_style(
		'teznevise-fa-admin',
		'.tez-meta-group{margin:16px 0 8px;padding:8px 0;border-top:1px solid #dcdcde;font-weight:600;}.tez-icon-preview{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#145D4A;color:#fff;margin-inline-start:8px;}'
	);
}
add_action( 'admin_enqueue_scripts', 'teznevise_admin_assets' );

/**
 * Render meta box.
 *
 * @param WP_Post $post Post.
 */
function teznevise_render_page_meta_box( $post ) {
	wp_nonce_field( 'teznevise_save_page_meta', 'teznevise_page_meta_nonce' );
	$schema = teznevise_page_meta_schema();
	$groups = teznevise_page_meta_groups();
	echo '<p style="margin-top:0;color:#646970;">' . esc_html__( 'محتوای قابل‌ویرایش قالب. آیکون را از لیست انتخاب یا دستی وارد کنید.', 'teznevise' ) . '</p>';
	$current_group = '';
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $schema as $key => $args ) {
		$group = isset( $args['group'] ) ? $args['group'] : 'content';
		if ( $group !== $current_group ) {
			$current_group = $group;
			$label         = isset( $groups[ $group ] ) ? $groups[ $group ] : $group;
			echo '<tr><td colspan="2"><div class="tez-meta-group">' . esc_html( $label ) . '</div></td></tr>';
		}
		$meta_key = '_teznevise_' . $key;
		$value    = get_post_meta( $post->ID, $meta_key, true );
		$ui       = isset( $args['ui'] ) ? $args['ui'] : ( 'boolean' === $args['type'] ? 'checkbox' : 'text' );
		$id       = 'teznevise_field_' . $key;
		echo '<tr>';
		echo '<th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $args['label'] ) . '</label></th>';
		echo '<td>';
		if ( 'checkbox' === $ui ) {
			printf( '<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>', esc_attr( $id ), esc_attr( $meta_key ), checked( (bool) $value, true, false ), esc_html__( 'فعال', 'teznevise' ) );
		} elseif ( 'textarea' === $ui ) {
			printf( '<textarea class="large-text" rows="5" id="%1$s" name="%2$s">%3$s</textarea>', esc_attr( $id ), esc_attr( $meta_key ), esc_textarea( (string) $value ) );
		} elseif ( 'icon' === $ui ) {
			$choices = teznevise_icon_choices();
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '" class="regular-text">';
			foreach ( $choices as $class => $label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $class ), selected( (string) $value, (string) $class, false ), esc_html( $label ) );
			}
			echo '</select>';
			$preview = $value ? $value : 'fa-solid fa-graduation-cap';
			echo ' <span class="tez-icon-preview" aria-hidden="true"><i class="' . esc_attr( $preview ) . '"></i></span>';
		} elseif ( 'color' === $ui ) {
			$choices = teznevise_icon_color_choices();
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $meta_key ) . '">';
			foreach ( $choices as $class => $label ) {
				printf( '<option value="%s" %s>%s (%s)</option>', esc_attr( $class ), selected( (string) $value, (string) $class, false ), esc_html( $label ), esc_html( $class ) );
			}
			echo '</select>';
		} else {
			printf( '<input type="text" class="large-text" id="%1$s" name="%2$s" value="%3$s" />', esc_attr( $id ), esc_attr( $meta_key ), esc_attr( (string) $value ) );
		}
		if ( ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Save page meta.
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
	$schema  = teznevise_page_meta_schema();
	$changed = false;
	foreach ( $schema as $key => $args ) {
		$meta_key = '_teznevise_' . $key;
		$type     = $args['type'];
		if ( 'boolean' === $type ) {
			$val     = isset( $_POST[ $meta_key ] ) ? 1 : 0;
			$current = get_post_meta( $post_id, $meta_key, true );
			if ( (string) (int) $current !== (string) $val ) {
				update_post_meta( $post_id, $meta_key, $val );
				$changed = true;
			}
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
add_action( 'save_post_page', 'teznevise_save_page_meta' );
