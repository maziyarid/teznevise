<?php
/**
 * Shortcode → Flexible Page Builder migration.
 *
 * Idempotent, capability-gated, admin-triggerable. Writes
 * `_teznevise_builder_sections` for pages that still only have legacy
 * shortcode / Elementor-style content and no builder meta yet.
 *
 * Also seeds calculator tool pages and optionally strips structural
 * shortcodes from post_content after a successful migration.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEZNEVISE_MIGRATION_OPTION', 'teznevise_shortcode_migration_v1' );
define( 'TEZNEVISE_MIGRATION_VERSION', '1.2.0' );
if ( ! defined( 'TEZNEVISE_EXTRACTED_CURSOR_OPTION' ) ) {
	define( 'TEZNEVISE_EXTRACTED_CURSOR_OPTION', 'teznevise_extracted_cursor' );
}

/**
 * Whether migration has already completed successfully.
 *
 * @return bool
 */
function teznevise_migration_is_complete() {
	$state = get_option( TEZNEVISE_MIGRATION_OPTION, array() );
	return ! empty( $state['completed'] ) && version_compare( (string) ( $state['version'] ?? '0' ), '1.2.0', '>=' );
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
			'completed'    => true,
			'version'      => TEZNEVISE_MIGRATION_VERSION,
			'completed_at' => current_time( 'mysql' ),
			'stats'        => $stats,
		),
		false
	);
}

/**
 * Recommended calculator tool pages (slug => config).
 * post_content holds the calculator shortcode; template is page-tool.php.
 *
 * @return array<string,array{title:string,shortcode:string,subtitle:string,icon:string}>
 */
function teznevise_migration_calculator_tools() {
	return array(
		'tool-descriptive-statistics' => array(
			'title'     => 'آمار توصیفی',
			'shortcode' => '[tz_descriptive]',
			'subtitle'  => 'میانگین، میانه، واریانس و شاخص‌های توصیفی',
			'icon'      => 'fa-solid fa-chart-simple',
		),
		'tool-pearson-correlation'    => array(
			'title'     => 'همبستگی پیرسون',
			'shortcode' => '[pearson-correlation-calculator]',
			'subtitle'  => 'محاسبه ضریب همبستگی پیرسون',
			'icon'      => 'fa-solid fa-chart-line',
		),
		'tool-spearman'               => array(
			'title'     => 'همبستگی اسپیرمن',
			'shortcode' => '[tz_spearman]',
			'subtitle'  => 'ضریب همبستگی رتبه‌ای اسپیرمن',
			'icon'      => 'fa-solid fa-chart-line',
		),
		'tool-ttest'                  => array(
			'title'     => 'آزمون t',
			'shortcode' => '[tz_ttest]',
			'subtitle'  => 'آزمون t تک‌نمونه‌ای و مستقل',
			'icon'      => 'fa-solid fa-flask',
		),
		'tool-anova'                  => array(
			'title'     => 'آنالیز واریانس (ANOVA)',
			'shortcode' => '[tz_anova]',
			'subtitle'  => 'مقایسه میانگین چند گروه',
			'icon'      => 'fa-solid fa-layer-group',
		),
		'tool-chi-square'             => array(
			'title'     => 'آزمون کای‌اسکوئر',
			'shortcode' => '[tz_chi_square]',
			'subtitle'  => 'استقلال و نیکویی برازش',
			'icon'      => 'fa-solid fa-table',
		),
		'tool-regression'             => array(
			'title'     => 'رگرسیون',
			'shortcode' => '[tz_regression]',
			'subtitle'  => 'رگرسیون خطی ساده',
			'icon'      => 'fa-solid fa-chart-line',
		),
		'tool-cronbach-alpha'         => array(
			'title'     => 'آلفای کرونباخ',
			'shortcode' => '[cronbach-alpha-calculator]',
			'subtitle'  => 'پایایی پرسشنامه',
			'icon'      => 'fa-solid fa-clipboard-check',
		),
		'tool-sample-size'            => array(
			'title'     => 'حجم نمونه',
			'shortcode' => '[sample-size-calculator]',
			'subtitle'  => 'محاسبه حجم نمونه پژوهشی',
			'icon'      => 'fa-solid fa-users',
		),
		'tool-power-analysis'         => array(
			'title'     => 'تحلیل توان',
			'shortcode' => '[power-analysis-calculator]',
			'subtitle'  => 'قدرت آزمون آماری',
			'icon'      => 'fa-solid fa-bolt',
		),
		'tool-content-validity'       => array(
			'title'     => 'روایی محتوا',
			'shortcode' => '[content-validity-calculator]',
			'subtitle'  => 'شاخص روایی محتوا (CVI/CVR)',
			'icon'      => 'fa-solid fa-check-double',
		),
		'tool-mann-whitney'           => array(
			'title'     => 'مان–ویتنی',
			'shortcode' => '[tz_mann_whitney]',
			'subtitle'  => 'آزمون ناپارامتری دو گروه مستقل',
			'icon'      => 'fa-solid fa-scale-balanced',
		),
		'tool-wilcoxon'               => array(
			'title'     => 'ویلکاکسون',
			'shortcode' => '[tz_wilcoxon]',
			'subtitle'  => 'آزمون رتبه‌های علامت‌دار',
			'icon'      => 'fa-solid fa-scale-balanced',
		),
		'tool-kruskal-wallis'         => array(
			'title'     => 'کروسکال–والیس',
			'shortcode' => '[tz_kruskal_wallis]',
			'subtitle'  => 'مقایسه چند گروه ناپارامتری',
			'icon'      => 'fa-solid fa-layer-group',
		),
	);
}

/**
 * Seed calculator tool pages (idempotent).
 *
 * @param bool $dry_run Dry run.
 * @return array{created:int,updated:int,skipped:int}
 */
function teznevise_migration_seed_calculator_tools( $dry_run = true ) {
	$stats = array(
		'created' => 0,
		'updated' => 0,
		'skipped' => 0,
	);

	foreach ( teznevise_migration_calculator_tools() as $slug => $cfg ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			// Ensure template + shortcode content if empty.
			$needs = false;
			if ( 'page-tool.php' !== get_post_meta( $existing->ID, '_wp_page_template', true ) ) {
				$needs = true;
			}
			if ( '' === trim( (string) $existing->post_content ) ) {
				$needs = true;
			}
			if ( ! $needs ) {
				$stats['skipped']++;
				continue;
			}
			if ( $dry_run ) {
				$stats['updated']++;
				continue;
			}
			update_post_meta( $existing->ID, '_wp_page_template', 'page-tool.php' );
			if ( '' === trim( (string) $existing->post_content ) ) {
				wp_update_post(
					array(
						'ID'           => $existing->ID,
						'post_content' => $cfg['shortcode'],
					)
				);
			}
			update_post_meta( $existing->ID, '_teznevise_eyebrow', __( 'ابزار آنلاین', 'teznevise' ) );
			update_post_meta( $existing->ID, '_teznevise_subtitle', $cfg['subtitle'] );
			update_post_meta( $existing->ID, '_teznevise_service_icon', $cfg['icon'] );
			update_post_meta( $existing->ID, '_teznevise_service_color', 'icon-amber' );
			$stats['updated']++;
			continue;
		}

		if ( $dry_run ) {
			$stats['created']++;
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => $cfg['title'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $cfg['shortcode'],
				'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			$stats['skipped']++;
			continue;
		}
		update_post_meta( $page_id, '_wp_page_template', 'page-tool.php' );
		update_post_meta( $page_id, '_teznevise_eyebrow', __( 'ابزار آنلاین', 'teznevise' ) );
		update_post_meta( $page_id, '_teznevise_subtitle', $cfg['subtitle'] );
		update_post_meta( $page_id, '_teznevise_service_icon', $cfg['icon'] );
		update_post_meta( $page_id, '_teznevise_service_color', 'icon-amber' );
		update_post_meta( $page_id, '_teznevise_cta_text', __( 'تحلیل آماری تخصصی', 'teznevise' ) );
		update_post_meta( $page_id, '_teznevise_cta_url', '/service-statistics/' );
		$stats['created']++;
	}

	return $stats;
}

/**
 * Strip structural shortcodes from content after migration.
 * Always preserves [gravityform ...] and calculator shortcodes.
 *
 * @param string $content Content.
 * @return string
 */
function teznevise_migration_strip_structural_shortcodes( $content ) {
	$patterns = array(
		'/\[tz_home\]/',
		'/\[row[^\]]*\]/',
		'/\[\/row\]/',
		'/\[col[^\]]*\]/',
		'/\[\/col\]/',
		'/\[title[^\]]*\].*?\[\/title\]/s',
		'/\[ux_text[^\]]*\].*?\[\/ux_text\]/s',
		'/\[ux_html[^\]]*\].*?\[\/ux_html\]/s',
		'/\[teznevise_download_category[^\]]*\]/',
	);
	foreach ( $patterns as $pattern ) {
		$content = preg_replace( $pattern, '', $content );
	}
	// Collapse excess blank lines.
	$content = preg_replace( "/\n{3,}/", "\n\n", (string) $content );
	return trim( (string) $content );
}

/**
 * Parse legacy post_content into builder sections matching the real schema.
 *
 * @param string $content Post content.
 * @param string $slug    Page slug.
 * @return array
 */
function teznevise_migration_parse_content( $content, $slug = '' ) {
	$sections = array();
	$content  = is_string( $content ) ? $content : '';

	if ( false !== strpos( $content, '[tz_home]' ) || 'home' === $slug ) {
		$sections = array_merge( $sections, teznevise_migration_default_home_sections() );
	}

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
				'type'       => 'software_catalog',
				'enabled'    => true,
				'title'      => $title,
				'text'       => '',
				'columns'    => '3',
				'background' => 'default',
				'items'      => array(),
			);
		}
	}

	if ( preg_match( '/\[gravityform\s+/', $content ) ) {
		$sections[] = array(
			'type'       => 'cta_band',
			'enabled'    => true,
			'title'      => __( 'فرم تماس', 'teznevise' ),
			'text'       => __( 'لطفاً فرم زیر را تکمیل کنید', 'teznevise' ),
			'cta_text'   => __( 'ارسال پیام', 'teznevise' ),
			'cta_url'    => '/contact/',
			'background' => 'soft',
			'items'      => array(),
		);
	}

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
		if ( 0 === $i && empty( $sections ) ) {
			$sections[] = array(
				'type'       => 'hero',
				'enabled'    => true,
				'eyebrow'    => '',
				'title'      => $title,
				'text'       => $text,
				'cta_text'   => '',
				'cta_url'    => '',
				'background' => 'default',
				'items'      => array(),
			);
		} else {
			$sections[] = array(
				'type'       => 'feature_list',
				'enabled'    => true,
				'eyebrow'    => '',
				'title'      => $title,
				'text'       => $text,
				'columns'    => '3',
				'background' => 'default',
				'items'      => array(),
			);
		}
	}

	if ( empty( $sections ) && preg_match_all( '/<h1[^>]*>(.*?)<\/h1>/is', $content, $h1s ) ) {
		$first_title = trim( wp_strip_all_tags( $h1s[1][0] ) );
		$first_text  = '';
		if ( preg_match( '/<p[^>]*>(.*?)<\/p>/is', $content, $p ) ) {
			$first_text = trim( wp_strip_all_tags( $p[1] ) );
		}
		if ( $first_title ) {
			$sections[] = array(
				'type'       => 'hero',
				'enabled'    => true,
				'eyebrow'    => '',
				'title'      => $first_title,
				'text'       => mb_substr( $first_text, 0, 400 ),
				'cta_text'   => '',
				'cta_url'    => '',
				'background' => 'default',
				'items'      => array(),
			);
		}
	}

	return $sections;
}

/**
 * Default home sections.
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
 * Candidate pages for shortcode migration.
 *
 * @return object[]
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

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
}

/**
 * Run migration.
 *
 * @param bool $dry_run        Dry run.
 * @param int  $limit          Limit pages.
 * @param bool $strip_codes    Strip structural shortcodes after write.
 * @param bool $seed_tools     Seed calculator tool pages.
 * @param bool $force_replace  Administrator opt-in to overwrite existing builder JSON.
 * @return array
 */
function teznevise_migration_run( $dry_run = true, $limit = 0, $strip_codes = false, $seed_tools = false, $force_replace = false ) {
	$stats = array(
		'processed'   => 0,
		'migrated'    => 0,
		'skipped'     => 0,
		'stripped'    => 0,
		'tools'       => array(),
		'errors'      => array(),
		'dry_run'     => (bool) $dry_run,
	);

	if ( ! function_exists( 'teznevise_builder_save_sections' ) ) {
		$stats['errors'][] = 'Builder module not loaded.';
		return $stats;
	}

	// Extracted original page copy → custom fields. Pages only; never posts;
	// never changes slug/title/content. Auto path is fill/provenance-only —
	// administrator-owned builder JSON is not replaced unless $force_replace.
	if ( function_exists( 'teznevise_apply_extracted_to_pages' ) ) {
		$limit  = (int) $limit;
		$offset = 0;
		if ( $limit > 0 ) {
			$offset = (int) get_option( TEZNEVISE_EXTRACTED_CURSOR_OPTION, 0 );
		} else {
			delete_option( TEZNEVISE_EXTRACTED_CURSOR_OPTION );
		}
		$extracted = teznevise_apply_extracted_to_pages( (bool) $force_replace, (bool) $dry_run, $limit, $offset );
		$stats['processed'] += (int) ( $extracted['processed'] ?? 0 );
		$stats['migrated']  += (int) ( $extracted['created'] ?? 0 ) + (int) ( $extracted['updated'] ?? 0 );
		$stats['skipped']   += (int) ( $extracted['skipped'] ?? 0 ) + (int) ( $extracted['empty'] ?? 0 );
		if ( ! empty( $extracted['errors'] ) ) {
			$stats['errors'] = array_merge( $stats['errors'], $extracted['errors'] );
		}
		if ( ! $dry_run && $limit > 0 ) {
			$processed = (int) ( $extracted['processed'] ?? 0 );
			if ( $processed >= $limit ) {
				update_option( TEZNEVISE_EXTRACTED_CURSOR_OPTION, $offset + $processed, false );
			} else {
				delete_option( TEZNEVISE_EXTRACTED_CURSOR_OPTION );
			}
		}
	}

	if ( $seed_tools ) {
		$stats['tools'] = teznevise_migration_seed_calculator_tools( $dry_run );
	}

	$pages = teznevise_migration_get_candidate_pages();
	if ( $limit > 0 ) {
		$pages = array_slice( $pages, 0, $limit );
	}

	foreach ( $pages as $page ) {
		if ( 'page' !== get_post_type( $page->ID ) ) {
			continue;
		}
		$stats['processed']++;

		if ( function_exists( 'teznevise_extracted_entry_for_post' ) ) {
			$extracted_entry = teznevise_extracted_entry_for_post( (int) $page->ID );
			if ( $extracted_entry && ( ! isset( $extracted_entry['source'] ) || 'empty' !== $extracted_entry['source'] ) ) {
				$stats['skipped']++;
				continue;
			}
		}

		// Manual provenance wins even when builder JSON is empty [].
		// Force-replace remains the only override (mirrors #418 extracted path).
		if ( ! $force_replace && function_exists( 'teznevise_page_has_manual_builder_provenance' ) && teznevise_page_has_manual_builder_provenance( (int) $page->ID ) ) {
			$stats['skipped']++;
			continue;
		}

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
		if ( ! $ok ) {
			$stats['errors'][] = sprintf( 'Failed to save sections for page %d (%s)', $page->ID, $page->post_title );
			continue;
		}

		$stats['migrated']++;

		// Strip is opt-in and still never changes slug/status. Default off.
		if ( $strip_codes ) {
			$cleaned = teznevise_migration_strip_structural_shortcodes( $page->post_content );
			if ( $cleaned !== $page->post_content ) {
				wp_update_post(
					array(
						'ID'           => $page->ID,
						'post_content' => $cleaned,
					)
				);
				$stats['stripped']++;
			}
		}
	}

	if ( ! $dry_run && empty( $stats['errors'] ) && 0 === (int) $limit ) {
		teznevise_migration_mark_complete( $stats );
	}

	return $stats;
}

/**
 * Admin form handler.
 */
function teznevise_migration_admin_ui() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['teznevise_run_migration'] ) && check_admin_referer( 'teznevise_run_migration' ) ) {
		$dry    = ! empty( $_POST['teznevise_migration_dry_run'] );
		$strip  = ! empty( $_POST['teznevise_migration_strip'] );
		$tools  = ! empty( $_POST['teznevise_migration_seed_tools'] );
		$force  = ! empty( $_POST['teznevise_migration_replace'] );
		$stats  = teznevise_migration_run( $dry, 0, $strip, $tools, $force );
		set_transient( 'teznevise_migration_last_result', $stats, 120 );
	}
}
add_action( 'admin_init', 'teznevise_migration_admin_ui' );

/**
 * Setup page UI.
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
	<p><?php esc_html_e( 'محتوای اصلی برگه‌ها (نه نوشته‌ها) از شورت‌کد/HTML به فیلدهای سفارشی صفحه‌ساز `_teznevise_builder_sections` و `_teznevise_*` منتقل می‌شود. اسلاگ، عنوان، والد و post_content دست نمی‌خورند.', 'teznevise' ); ?></p>
	<p><a href="https://github.com/maziyarid/teznevise/blob/main/docs/SHORTCODE-TO-BUILDER-MIGRATION.md" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'مستندات مهاجرت', 'teznevise' ); ?></a>
	· <a href="https://github.com/maziyarid/teznevise/blob/SHortcode-based-content-migration/docs/MIGRATION-DATA-SECURITY.md" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'هشدار امنیتی dumpها', 'teznevise' ); ?></a></p>
	<?php if ( $done ) : ?>
		<div class="notice notice-success inline"><p><?php esc_html_e( 'مهاجرت قبلاً با موفقیت انجام شده است (نسخه ≥ 1.2).', 'teznevise' ); ?></p></div>
	<?php endif; ?>
	<?php if ( is_array( $result ) ) : ?>
		<div class="notice notice-info inline"><p>
			<?php
			printf(
				esc_html__( 'نتیجه: پردازش %1$d — مهاجرت %2$d — رد %3$d — پاک‌سازی شورت‌کد %4$d — خطا %5$d %6$s', 'teznevise' ),
				(int) $result['processed'],
				(int) $result['migrated'],
				(int) $result['skipped'],
				(int) ( $result['stripped'] ?? 0 ),
				count( $result['errors'] ),
				! empty( $result['dry_run'] ) ? '(dry-run)' : ''
			);
			if ( ! empty( $result['tools'] ) ) {
				echo ' | ';
				printf(
					esc_html__( 'ابزارها: ایجاد %1$d — به‌روز %2$d — رد %3$d', 'teznevise' ),
					(int) ( $result['tools']['created'] ?? 0 ),
					(int) ( $result['tools']['updated'] ?? 0 ),
					(int) ( $result['tools']['skipped'] ?? 0 )
				);
			}
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
			<?php esc_html_e( 'حالت آزمایشی (dry-run)', 'teznevise' ); ?>
		</label>
		<label style="display:block;margin-bottom:8px;">
			<input type="checkbox" name="teznevise_migration_seed_tools" value="1" />
			<?php esc_html_e( 'ایجاد / هم‌ترازسازی صفحات ابزارهای محاسباتی (ماشین‌حساب‌ها)', 'teznevise' ); ?>
		</label>
		<label style="display:block;margin-bottom:8px;">
			<input type="checkbox" name="teznevise_migration_replace" value="1" />
			<?php esc_html_e( 'بازنویسی اجباری بخش‌های صفحه‌ساز موجود (ویرایش‌های دستی پاک می‌شوند)', 'teznevise' ); ?>
		</label>
		<label style="display:block;margin-bottom:8px;">
			<input type="checkbox" name="teznevise_migration_strip" value="1" />
			<?php esc_html_e( 'پس از مهاجرت موفق: پاک کردن شورت‌کدهای ساختاری از post_content (فرم‌ها و ماشین‌حساب‌ها حفظ می‌شوند)', 'teznevise' ); ?>
		</label>
		<button type="submit" name="teznevise_run_migration" class="button button-secondary">
			<?php esc_html_e( 'اجرای مهاجرت', 'teznevise' ); ?>
		</button>
	</form>
	<?php
}
add_action( 'teznevise_setup_after_seed', 'teznevise_migration_render_setup_section' );
