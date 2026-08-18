<?php
/**
 * Flexible page builder — admin meta box and save handling.
 *
 * The meta box renders a JSON payload plus a mount point; the repeater UI itself
 * lives in assets/js/builder-admin.js. The hidden field is pre-filled with the
 * stored payload so saving without JavaScript never destroys existing sections.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the builder meta box.
 */
function teznevise_builder_add_meta_box() {
	foreach ( teznevise_builder_post_types() as $post_type ) {
		add_meta_box(
			'teznevise_builder',
			__( 'صفحه‌ساز تزنویسه — بخش‌های سفارشی', 'teznevise' ),
			'teznevise_builder_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'teznevise_builder_add_meta_box' );

/**
 * Configuration handed to the admin script.
 *
 * @return array
 */
function teznevise_builder_admin_config() {
	$types = array();
	foreach ( teznevise_builder_section_types() as $key => $definition ) {
		$types[ $key ] = array(
			'label'       => $definition['label'],
			'description' => isset( $definition['description'] ) ? $definition['description'] : '',
			'supports'    => array_values( (array) $definition['supports'] ),
			'itemLabel'   => isset( $definition['item_label'] ) ? $definition['item_label'] : '',
			'itemFields'  => (array) $definition['item_fields'],
		);
	}

	return array(
		'types'         => $types,
		'sectionFields' => teznevise_builder_section_fields(),
		'iconChoices'   => function_exists( 'teznevise_icon_choices' ) ? teznevise_icon_choices() : array(),
		'colorChoices'  => function_exists( 'teznevise_icon_color_choices' ) ? teznevise_icon_color_choices() : array(),
		'i18n'          => array(
			'addSection'     => __( 'افزودن بخش', 'teznevise' ),
			'addItem'        => __( 'افزودن مورد', 'teznevise' ),
			'duplicate'      => __( 'تکثیر', 'teznevise' ),
			'remove'         => __( 'حذف', 'teznevise' ),
			'moveUp'         => __( 'انتقال به بالا', 'teznevise' ),
			'moveDown'       => __( 'انتقال به پایین', 'teznevise' ),
			'enabled'        => __( 'نمایش این بخش', 'teznevise' ),
			'confirmRemove'  => __( 'این مورد حذف شود؟', 'teznevise' ),
			'confirmSection' => __( 'کل این بخش حذف شود؟', 'teznevise' ),
			'chooseImage'    => __( 'انتخاب تصویر', 'teznevise' ),
			'clearImage'     => __( 'حذف تصویر', 'teznevise' ),
			'emptyState'     => __( 'هنوز بخشی اضافه نشده است. یک نوع بخش را انتخاب و اضافه کنید.', 'teznevise' ),
			'noItems'        => __( 'موردی وجود ندارد.', 'teznevise' ),
			'untitled'       => __( 'بدون عنوان', 'teznevise' ),
			'dragHint'       => __( 'برای جابه‌جایی بکشید', 'teznevise' ),
		),
	);
}

/**
 * Render the builder meta box.
 *
 * @param WP_Post $post Post being edited.
 */
function teznevise_builder_render_meta_box( $post ) {
	wp_nonce_field( 'teznevise_builder_save', 'teznevise_builder_nonce' );

	$sections = teznevise_builder_get_sections( $post->ID );
	$json     = wp_json_encode( $sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( false === $json ) {
		$json = '[]';
	}
	?>
	<div class="tez-builder" data-tez-builder>
		<p class="tez-builder-intro">
			<?php esc_html_e( 'بخش‌ها را اضافه، تکثیر، جابه‌جا یا حذف کنید. تعداد موردها در هر بخش محدودیتی ندارد.', 'teznevise' ); ?>
		</p>
		<div class="tez-builder-toolbar">
			<select data-tez-builder-type aria-label="<?php esc_attr_e( 'نوع بخش', 'teznevise' ); ?>">
				<?php foreach ( teznevise_builder_section_types() as $key => $definition ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $definition['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="button" class="button button-primary" data-tez-builder-add>
				<?php esc_html_e( 'افزودن بخش', 'teznevise' ); ?>
			</button>
		</div>
		<div class="tez-builder-sections" data-tez-builder-sections></div>
		<textarea
			class="tez-builder-payload"
			name="teznevise_builder_payload"
			data-tez-builder-payload
			rows="4"
		><?php echo esc_textarea( $json ); ?></textarea>
		<p class="description tez-builder-fallback-note">
			<?php esc_html_e( 'اگر جاوااسکریپت غیرفعال باشد، همین JSON مستقیماً قابل ویرایش است.', 'teznevise' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Save the builder payload.
 *
 * @param int $post_id Post ID.
 */
function teznevise_builder_save_post( $post_id ) {
	if ( ! isset( $_POST['teznevise_builder_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_builder_nonce'] ) ), 'teznevise_builder_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! in_array( get_post_type( $post_id ), teznevise_builder_post_types(), true ) ) {
		return;
	}
	if ( ! isset( $_POST['teznevise_builder_payload'] ) ) {
		return;
	}

	$raw     = wp_unslash( $_POST['teznevise_builder_payload'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON decoded and sanitized field-by-field below.
	$decoded = is_string( $raw ) ? json_decode( $raw, true ) : null;
	if ( null === $decoded && JSON_ERROR_NONE !== json_last_error() ) {
		return;
	}

	teznevise_builder_save_sections( $post_id, $decoded );
}
add_action( 'save_post', 'teznevise_builder_save_post' );
