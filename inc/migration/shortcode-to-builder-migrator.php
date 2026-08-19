<?php
/**
 * Shortcode → Flexible Page Builder migration.
 *
 * Idempotent, capability-gated, admin-triggerable. Writes
 * `_teznevise_builder_sections` for pages that still only have legacy
 * shortcode / Elementor-style content and no builder meta yet.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEZNEVISE_MIGRATION_OPTION', 'teznevise_shortcode_migration_v1' );
define( 'TEZNEVISE_MIGRATION_VERSION', '1.0.0' );

/**
 * Whether migration has already completed successfully.
 *
 * @return bool
 */
function teznevise_migration_is_complete() {
	$state = get_option( TEZNEVISE_MIGRATION_OPTION, array() );
	return ! empty( $state['completed'] ) && TEZNEVISE_MIGRATION_VERSION === ( $state['version'] ?? '' );
}

/**
 * Record migration completion.
 *
 * @param array $stats Stats from the last run.
 */
function teznevise_migration_mark_complete( array $stats ) {
	update_option(
		TEZNEVISE_MIGRATION_OPTION,
		array(
			'completed'   => true,
			'version'     => TEZNEVISE_MIGRATION_VERSION,
			'completed_at'=> current_time( 'mysql' ),
			'stats'       => $stats,
		),
		false
	);
}

/**
 * Parse legacy post_content into builder sections matching the real schema.
 *
 * Real schema (see inc/class-teznevise-builder.php):
 *   { type, enabled, eyebrow?, title?, text?, cta_text?, cta_url?, columns?, background?, items: [...] }
 *
 * @param string $content Post content.
 * @param string $slug    Page slug (for special cases).
 * @return array Sanitizable sections array.
 */
function teznevise_migration_parse_content( $content, $slug = '' ) {
	$sections = array();
	$content  = is_string( $content ) ? $content : '';

	// --- [tz_home] / home page ---
	if ( false !== strpos( $content, '[tz_home]' ) || 'home' === $slug ) {
		$sections = array_merge( $sections, teznevise_migration_default_home_sections() );
	}

	// --- [teznevise_download_category slug="..."] → software_catalog ---
	if ( preg_match_all( '/\[teznevise_download_category\s+([^\]]*)\]/', $content, $dl_matches ) ) {
		foreach ( $dl_matches[1] as $atts_raw ) {
			$slug_attr = '';
			if ( preg_match( '/slug=["\']([^"\']+)["\']/', $atts_raw, $m ) ) {
				$slug_attr = sanitize_title( $m[1] );
			}
			$title = __( 'دانلودها', 'teznevise' );
			if ( $slug_attr && taxonomy_exists( 'download_category' ) ) {
				$term = get_term_by( 'slug', $slug_attr, 'download_category' );
				if ( $term && ! is_wp_error( $term ) ) {
					$title = $term->name;
				}
			}
			$sections[] = array(
				'type'      => 'software_catalog',
				'enabled'   => true,
				'title'     => $title,
				'text'      => '',
				'columns'   => '3',
				'background'=> 'default',
				'items'     => array(), // populated live by catalog shortcode / CPT later
			);
		}
	}

	// --- Gravity Forms stay in post_content; add a CTA band pointing to contact ---
	if ( preg_match( '/\[gravityform\s+/', $content ) ) {
		$sections[] = array(
			'type'      => 'cta_band',
			'enabled'   => true,
			'title'     => __( 'فرم تماس', 'teznevise' ),
			'text'      => __( 'لطفاً فرم زیر را تکمیل کنید', 'teznevise' ),
			'cta_text'  => __( 'ارسال پیام', 'teznevise' ),
			'cta_url'   => '/contact/',
			'background'=> 'soft',
			'items'     => array(),
		);
	}

	// --- [title]...[/title] + [ux_text]...[/ux_text] → hero / feature blocks ---
	$titles = array();
	$texts  = array();
	if ( preg_match_all( '/\[title[^\]]*\](.*?)\[\/title\]/s', $content, $tm ) ) {
		$titles = array_map( 'wp_strip_all_tags', $tm[1] );
	}
	if ( preg_match_all( '/\[ux_text[^\]]*\](.*?)\[\/ux_text\]/s', $content, $tx ) ) {
		$texts = array_map( 'wp_strip_all_tags', $tx[1] );
	}

	foreach ( $titles as $i => $title ) {
		$title = trim( $title );
		$text  = isset( $texts[ $i ] ) ? trim( $texts[ $i ] ) : '';
		if ( '' === $title && '' === $text ) {
			continue;
		}
		// First pair becomes hero; subsequent become feature_list section heads.
		if ( 0 === $i && empty( $sections ) ) {
			$sections[] = array(
				'type'      => 'hero',
				'enabled'   => true,
				'eyebrow'   => '',
				'title'     => $title,
				'text'      => $text,
				'cta_text'  => '',
				'cta_url'   => '',
				'background'=> 'default',
				'items'     => array(),
			);
		} else {
			$sections[] = array(
				'type'      => 'feature_list',
				'enabled'   => true,
				'eyebrow'   => '',
				'title'     => $title,
				'text'      => $text,
				'columns'   => '3',
				'background'=> 'default',
				'items'     => array(),
			);
		}
	}

	// --- Plain HTML headings (Elementor remnants) when no shortcodes matched ---
	if ( empty( $sections ) && preg_match_all( '/<h1[^>]*>(.*?)<\/h1>/is', $content, $h1s ) ) {
		$first_title = trim( wp_strip_all_tags( $h1s[1][0] ) );
		$first_text  = '';
		if ( preg_match( '/<p[^>]*>(.*?)<\/p>/is', $content, $p ) ) {
			$first_text = trim( wp_strip_all_tags( $p[1] ) );
		}
		if ( $first_title ) {
			$sections[] = array(
				'type'      => 'hero',
				'enabled'   => true,
				'eyebrow'   => '',
				'title'     => $first_title,
				'text'      => mb_substr( $first_text, 0, 400 ),
				'cta_text'  => '',
				'cta_url'   => '',
				'background'=> 'default',
				'items'     => array(),
			);
		}
	}

	return $sections;
}

/**
 * Default builder sections for the home page ([tz_home]).
 *
 * @return array
 */
function teznevise_migration_default_home_sections() {
	return array(
		array(
			'type'       => 'hero',
			'enabled'    => true,
			'eyebrow'    => __( 'تزنویسه', 'teznevise' ),
			'title'      => __( 'به تزنویسه خوش آمدید', 'teznevise' ),
			'text'       => __( 'سیستم جامع تحلیل آماری و خدمات پژوهشی', 'teznevise' ),
			'cta_text'   => __( 'شروع مشاوره رایگان', 'teznevise' ),
			'cta_url'    => '/inquiry/',
			'background' => 'default',
			'items'      => array(
				array( 'title' => __( 'تحلیل دقیق و قابل دفاع', 'teznevise' ), 'icon' => 'fa-solid fa-check' ),
				array( 'title' => __( 'محرمانگی کامل', 'teznevise' ), 'icon' => 'fa-solid fa-shield-halved' ),
				array( 'title' => __( 'پشتیبانی تا دفاع', 'teznevise' ), 'icon' => 'fa-solid fa-headset' ),
			),
		),
		array(
			'type'       => 'service_cards',
			'enabled'    => true,
			'eyebrow'    => __( 'خدمات', 'teznevise' ),
			'title'      => __( 'خدمات پژوهشی ما', 'teznevise' ),
			'text'       => '',
			'columns'    => '4',
			'background' => 'default',
			'items'      => array(
				array(
					'title' => __( 'انجام پایان‌نامه', 'teznevise' ),
					'text'  => __( 'از انتخاب موضوع تا دفاع', 'teznevise' ),
					'icon'  => 'fa-solid fa-graduation-cap',
					'color' => 'icon-indigo',
					'url'   => '/service-thesis/',
				),
				array(
					'title' => __( 'انجام پروپوزال', 'teznevise' ),
					'text'  => __( 'بیان مسئله و روش‌شناسی', 'teznevise' ),
					'icon'  => 'fa-solid fa-file-circle-check',
					'color' => 'icon-teal',
					'url'   => '/service-proposal/',
				),
				array(
					'title' => __( 'تحلیل آماری', 'teznevise' ),
					'text'  => __( 'SPSS, R, Python, AMOS', 'teznevise' ),
					'icon'  => 'fa-solid fa-chart-line',
					'color' => 'icon-cyan',
					'url'   => '/service-statistics/',
				),
				array(
					'title' => __( 'شبیه‌سازی', 'teznevise' ),
					'text'  => __( 'مدل‌سازی و شبیه‌سازی', 'teznevise' ),
					'icon'  => 'fa-solid fa-flask',
					'color' => 'icon-amber',
					'url'   => '/service-simulation/',
				),
			),
		),
		array(
			'type'       => 'cta_band',
			'enabled'    => true,
			'title'      => __( 'آماده شروع هستید؟', 'teznevise' ),
			'text'       => __( 'موضوع را بفرستید؛ مسیر و برآورد اولیه را بررسی می‌کنیم.', 'teznevise' ),
			'cta_text'   => __( 'ثبت درخواست', 'teznevise' ),
			'cta_url'    => '/inquiry/',
			'background' => 'soft',
			'items'      => array(),
		),
	);
}

/**
 * Pages that should be considered for shortcode migration.
 *
 * @return WP_Post[]
 */
function teznevise_migration_get_candidate_pages() {
	global $wpdb;

	$like_patterns = array(
		'%[row%',
		'%[col%',
		'%[title%',
		'%[ux_text%',
		'%[ux_html%',
		'%[tz_home%',
		'%[teznevise_download_category%',
		'%[gravityform%',
		'%<h1%',
	);

	$conditions = array();
	$params     = array();
	foreach ( $like_patterns as $pattern ) {
		$conditions[] = 'post_content LIKE %s';
		$params[]     = $pattern;
	}

	$sql = "SELECT ID, post_title, post_name, post_content FROM {$wpdb->posts}
		WHERE post_type = 'page' AND post_status = 'publish'
		AND (" . implode( ' OR ', $conditions ) . ")
		ORDER BY post_title ASC";

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- placeholders built above.
	return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
}

/**
 * Run the migration.
 *
 * @param bool $dry_run If true, do not write meta.
 * @param int  $limit   Max pages to process (0 = all).
 * @return array{processed:int,migrated:int,skipped:int,errors:string[],dry_run:bool}
 */
function teznevise_migration_run( $dry_run = true, $limit = 0 ) {
	$stats = array(
		'processed' => 0,
		'migrated'  => 0,
		'skipped'   => 0,
		'errors'    => array(),
		'dry_run'   => (bool) $dry_run,
	);

	if ( ! function_exists( 'teznevise_builder_save_sections' ) ) {
		$stats['errors'][] = 'Builder module not loaded.';
		return $stats;
	}

	$pages = teznevise_migration_get_candidate_pages();
	if ( $limit > 0 ) {
		$pages = array_slice( $pages, 0, $limit );
	}

	foreach ( $pages as $page ) {
		$stats['processed']++;

		// Skip if builder sections already exist.
		$existing = get_post_meta( $page->ID, TEZNEVISE_BUILDER_META, true );
		if ( is_string( $existing ) && '' !== trim( $existing ) && '[]' !== trim( $existing ) ) {
			$stats['skipped']++;
			continue;
		}

		$sections = teznevise_migration_parse_content( $page->post_content, $page->post_name );
		if ( empty( $sections ) ) {
			$stats['skipped']++;
			continue;
		}

		if ( $dry_run ) {
			$stats['migrated']++;
			continue;
		}

		$ok = teznevise_builder_save_sections( $page->ID, $sections );
		if ( $ok ) {
			$stats['migrated']++;
		} else {
			$stats['errors'][] = sprintf( 'Failed to save sections for page %d (%s)', $page->ID, $page->post_title );
		}
	}

	if ( ! $dry_run && empty( $stats['errors'] ) && $stats['migrated'] > 0 ) {
		teznevise_migration_mark_complete( $stats );
	}

	return $stats;
}

/**
 * Admin notice + Setup page action for the migration.
 */
function teznevise_migration_admin_ui() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Handle form submit from Setup page.
	if ( isset( $_POST['teznevise_run_migration'] ) && check_admin_referer( 'teznevise_run_migration' ) ) {
		$dry  = ! empty( $_POST['teznevise_migration_dry_run'] );
		$stats = teznevise_migration_run( $dry, 0 );
		set_transient( 'teznevise_migration_last_result', $stats, 60 );
	}
}
add_action( 'admin_init', 'teznevise_migration_admin_ui' );

/**
 * Append migration controls to the existing Setup admin page.
 */
function teznevise_migration_render_setup_section() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$result = get_transient( 'teznevise_migration_last_result' );
	if ( $result ) {
		delete_transient( 'teznevise_migration_last_result' );
	}

	$done = teznevise_migration_is_complete();
	?>
	<hr style="margin:28px 0;">
	<h2><?php esc_html_e( 'مهاجرت شورت‌کد → صفحه‌ساز', 'teznevise' ); ?></h2>
	<p><?php esc_html_e( 'صفحات قدیمی که فقط با شورت‌کد/HTML ساخته شده‌اند را به متای `_teznevise_builder_sections` منتقل می‌کند. فقط صفحاتی که هنوز بخش سازنده ندارند تغییر می‌کنند (idempotent).', 'teznevise' ); ?></p>
	<?php if ( $done ) : ?>
		<div class="notice notice-success inline"><p><?php esc_html_e( 'مهاجرت v1 قبلاً با موفقیت انجام شده است.', 'teznevise' ); ?></p></div>
	<?php endif; ?>
	<?php if ( is_array( $result ) ) : ?>
		<div class="notice notice-info inline"><p>
			<?php
			printf(
				esc_html__( 'نتیجه: پردازش %1$d — مهاجرت %2$d — رد شده %3$d — خطا %4$d %5$s',
					'teznevise' ),
				(int) $result['processed'],
				(int) $result['migrated'],
				(int) $result['skipped'],
				count( $result['errors'] ),
				! empty( $result['dry_run'] ) ? '(dry-run)' : ''
			);
			?>
		</p>
		<?php if ( ! empty( $result['errors'] ) ) : ?>
			<ul><?php foreach ( $result['errors'] as $err ) : ?>
				<li><?php echo esc_html( $err ); ?></li>
			<?php endforeach; ?></ul>
		<?php endif; ?>
		</div>
	<?php endif; ?>
	<form method="post" style="margin-top:12px;">
		<?php wp_nonce_field( 'teznevise_run_migration' ); ?>
		<label style="display:block;margin-bottom:8px;">
			<input type="checkbox" name="teznevise_migration_dry_run" value="1" checked />
			<?php esc_html_e( 'حالت آزمایشی (dry-run) — چیزی در دیتابیس نوشته نمی‌شود', 'teznevise' ); ?>
		</label>
		<button type="submit" name="teznevise_run_migration" class="button button-secondary">
			<?php esc_html_e( 'اجرای مهاجرت شورت‌کد → صفحه‌ساز', 'teznevise' ); ?>
		</button>
	</form>
	<p class="description" style="margin-top:10px;">
		<?php esc_html_e( 'شورت‌کدها در post_content باقی می‌مانند (سازگاری عقب‌رو). پس از تأیید نمایش، می‌توانید آن‌ها را دستی پاک کنید.', 'teznevise' ); ?>
	</p>
	<?php
}
add_action( 'teznevise_setup_after_seed', 'teznevise_migration_render_setup_section' );
