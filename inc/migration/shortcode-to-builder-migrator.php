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
 * Completion is recorded only when no remaining candidate pages exist
 * (see teznevise_migration_maybe_mark_complete). Partial batches must
 * not flip the completed flag or later admin loads will skip leftovers.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TEZNEVISE_MIGRATION_OPTION', 'teznevise_shortcode_migration_v1' );
define( 'TEZNEVISE_MIGRATION_VERSION', '1.2.0' );

/**
 * Marker written on pages we inspected but could not parse into sections.
 * Keeps the candidate query from retrying the same empty pages forever.
 */
define( 'TEZNEVISE_MIGRATION_SKIP_META', '_teznevise_migration_skip' );

/**
 * Whether migration has already completed successfully for this schema version.
 *
 * Sites that ran 1.0/1.1 may have been marked complete after a partial
 * batch. Requiring >= 1.2.0 resumes leftovers; existing builder meta is
 * never overwritten (idempotent skip).
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
 * Mark complete only when a live scan finds zero remaining candidates.
 *
 * @param array $stats Stats from the last run (must include has_more / errors / dry_run).
 */
function teznevise_migration_maybe_mark_complete( array $stats ) {
	if ( ! empty( $stats['dry_run'] ) ) {
		return;
	}
	if ( ! empty( $stats['errors'] ) ) {
		return;
	}
	if ( ! empty( $stats['has_more'] ) ) {
		return;
	}
	$remaining = teznevise_migration_get_candidate_pages( 1 );
	if ( ! empty( $remaining ) ) {
		return;
	}
	teznevise_migration_mark_complete( $stats );
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
		'tool-cohens-kappa'           => array(
			'title'     => 'کاپای کوهن',
			'shortcode' => '[tz_cohens_kappa]',
			'subtitle'  => 'توافق بین ارزیاب‌ها',
			'icon'      => 'fa-solid fa-handshake',
		),
		'tool-icc'                    => array(
			'title'     => 'ضریب همبستگی درون‌رده‌ای (ICC)',
			'shortcode' => '[tz_icc]',
			'subtitle'  => 'پایایی اندازه‌گیری‌های تکراری',
			'icon'      => 'fa-solid fa-rotate',
		),
		'tool-kr20'                   => array(
			'title'     => 'KR-20',
			'shortcode' => '[tz_kr20]',
			'subtitle'  => 'پایایی کودر–ریچاردسون',
			'icon'      => 'fa-solid fa-list-check',
		),
		'tool-goodness-of-fit'        => array(
			'title'     => 'نیکویی برازش',
			'shortcode' => '[tz_goodness_of_fit]',
			'subtitle'  => 'آزمون نیکویی برازش',
			'icon'      => 'fa-solid fa-chart-pie',
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
		'/\[tz_calculation_hub[^\]]*\]/',
		'/\[tz_careers_terms[^\]]*\]/',
		'/\[tz_price_box[^\]]*\]/',
		'/\[tz_price_cta[^\]]*\]/',
	);
	foreach ( $patterns as $pattern ) {
		$content = preg_replace( $pattern, '', $content );
	}
	$content = preg_replace( "/\n{3,}/", "\n\n", (string) $content );
	return trim( (string) $content );
}

/**
 * Shared empty section skeleton matching the builder schema.
 *
 * @param string $type Section type.
 * @return array
 */
function teznevise_migration_section_base( $type ) {
	return array(
		'type'       => $type,
		'enabled'    => true,
		'eyebrow'    => '',
		'title'      => '',
		'text'       => '',
		'cta_text'   => '',
		'cta_url'    => '',
		'columns'    => '3',
		'background' => 'default',
		'items'      => array(),
	);
}

/**
 * Map a tz_service post into a builder card item.
 *
 * @param int $id Service post ID.
 * @return array{title:string,text:string,url:string,icon:string,color:string}|null
 */
function teznevise_migration_price_box_item( $id, $title_override = '' ) {
	$id = absint( $id );
	if ( ! $id ) {
		return null;
	}
	$title = trim( (string) $title_override );
	if ( ! $title ) {
		$title = get_the_title( $id );
	}
	if ( ! $title ) {
		return null;
	}
	$desc  = (string) get_post_meta( $id, '_tz_desc', true );
	$min   = (string) get_post_meta( $id, '_tz_price_min', true );
	$max   = (string) get_post_meta( $id, '_tz_price_max', true );
	$unit  = (string) get_post_meta( $id, '_tz_unit', true );
	$icon  = (string) get_post_meta( $id, '_tz_icon', true );
	$range = $min;
	if ( $max && $max !== $min ) {
		$range .= ' – ' . $max;
	}
	if ( $unit && $range ) {
		$range .= ' ' . $unit;
	}
	$text = trim( $desc . ( $range ? ' (' . $range . ')' : '' ) );
	return array(
		'title' => $title,
		'text'  => $text,
		'url'   => get_permalink( $id ) ? get_permalink( $id ) : '',
		'icon'  => $icon ? $icon : 'fa-solid fa-tag',
		'color' => 'icon-amber',
	);
}

/**
 * Parse a shortcode attribute blob for id="N" / id='N' / id=N.
 *
 * @param string $atts_raw Attribute string.
 * @return int
 */
function teznevise_migration_shortcode_id( $atts_raw ) {
	if ( preg_match( '/\bid=["\']?(\d+)/', (string) $atts_raw, $m ) ) {
		return absint( $m[1] );
	}
	return 0;
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
			$parsed_atts = shortcode_parse_atts( $atts_raw );
			$slug_attr   = ! empty( $parsed_atts['slug'] ) ? sanitize_title( $parsed_atts['slug'] ) : '';
			$count       = isset( $parsed_atts['count'] ) ? absint( $parsed_atts['count'] ) : 12;
			$count       = $count > 0 ? $count : 12;
			$title = __( 'دانلودها', 'teznevise' );
			if ( $slug_attr && taxonomy_exists( 'download_category' ) ) {
				$term = get_term_by( 'slug', $slug_attr, 'download_category' );
				if ( $term && ! is_wp_error( $term ) ) {
					$title = $term->name;
				}
			}
			$items = array();
			if ( function_exists( 'teznevise_downloads_as_builder_items' ) ) {
				$items = teznevise_downloads_as_builder_items( $count, $slug_attr );
			}
			$section          = teznevise_migration_section_base( 'software_catalog' );
			$section['title']         = $title;
			$section['category_slug'] = $slug_attr;
			$section['items']         = $items;
			$sections[]       = $section;
		}
	}

	$price_items = array();
	if ( preg_match_all( '/\[tz_price_box\s+([^\]]*)\]/', $content, $price_matches ) ) {
		foreach ( $price_matches[1] as $atts_raw ) {
			$price_atts = shortcode_parse_atts( $atts_raw );
			$item       = teznevise_migration_price_box_item( teznevise_migration_shortcode_id( $atts_raw ), $price_atts['title'] ?? '' );
			if ( $item ) {
				$price_items[] = $item;
			}
		}
	}
	if ( $price_items ) {
		$section            = teznevise_migration_section_base( 'service_cards' );
		$section['eyebrow'] = __( 'تعرفه', 'teznevise' );
		$section['title']   = __( 'خدمات قیمتی', 'teznevise' );
		$section['items']   = $price_items;
		$sections[]         = $section;
	}

	if ( preg_match( '/\[tz_price_cta(?:\s[^\]]*)?\]/', $content ) ) {
		$cta_url   = '/inquiry/';
		$cta_text  = '';
		if ( preg_match( '/\[tz_price_cta(?:\s([^\]]*))?\]/', $content, $cta_m ) ) {
			$cta_atts = shortcode_parse_atts( $cta_m[1] ?? '' );
			$sid      = teznevise_migration_shortcode_id( $cta_m[1] ?? '' );
			$cta_text = ! empty( $cta_atts['text'] ) ? $cta_atts['text'] : '';
			if ( $sid && get_permalink( $sid ) ) {
				$cta_url = get_permalink( $sid );
			}
		}
		$section            = teznevise_migration_section_base( 'cta_band' );
		$section['title']   = __( 'درخواست برآورد هزینه', 'teznevise' );
		$section['text']    = __( 'موضوع را بفرستید؛ مسیر و برآورد اولیه را بررسی می‌کنیم.', 'teznevise' );
		$section['cta_text'] = $cta_text ? $cta_text : __( 'ثبت درخواست', 'teznevise' );
		$section['cta_url'] = $cta_url;
		$section['background'] = 'soft';
		$sections[]         = $section;
	}

	if ( false !== strpos( $content, '[tz_calculation_hub]' ) ) {
		$hub_items = array();
		foreach ( teznevise_migration_calculator_tools() as $tool_slug => $cfg ) {
			$hub_items[] = array(
				'title' => $cfg['title'],
				'text'  => $cfg['subtitle'],
				'url'   => '/' . $tool_slug . '/',
				'icon'  => $cfg['icon'],
				'color' => 'icon-amber',
			);
		}
		$section            = teznevise_migration_section_base( 'service_cards' );
		$section['eyebrow'] = __( 'ابزار آنلاین', 'teznevise' );
		$section['title']   = __( 'ماشین‌حساب‌های آماری', 'teznevise' );
		$section['columns'] = '4';
		$section['items']   = $hub_items;
		$sections[]         = $section;
	}

	if ( false !== strpos( $content, '[tz_careers_terms]' ) ) {
		$section            = teznevise_migration_section_base( 'feature_list' );
		$section['eyebrow'] = __( 'همکاری', 'teznevise' );
		$section['title']   = __( 'شرایط همکاری', 'teznevise' );
		$section['items']   = array(
			array(
				'title' => __( 'همکاری پژوهشی', 'teznevise' ),
				'text'  => __( 'شرایط همکاری با پژوهشگران و متخصصان آماری.', 'teznevise' ),
				'icon'  => 'fa-solid fa-briefcase',
				'color' => 'icon-teal',
			),
		);
		$sections[]         = $section;
	}

	if ( preg_match( '/\[gravityform\s+/', $content ) ) {
		$section               = teznevise_migration_section_base( 'cta_band' );
		$section['title']      = __( 'فرم تماس', 'teznevise' );
		$section['text']       = __( 'لطفاً فرم زیر را تکمیل کنید', 'teznevise' );
		$section['cta_text']   = __( 'ارسال پیام', 'teznevise' );
		$section['cta_url']    = '/contact/';
		$section['background'] = 'soft';
		$sections[]            = $section;
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
			$section            = teznevise_migration_section_base( 'hero' );
			$section['title']   = $title;
			$section['text']    = $text;
			$sections[]         = $section;
		} else {
			$section            = teznevise_migration_section_base( 'feature_list' );
			$section['title']   = $title;
			$section['text']    = $text;
			$sections[]         = $section;
		}
	}

	if ( empty( $sections ) && preg_match_all( '/<h1[^>]*>(.*?)<\/h1>/is', $content, $h1s ) ) {
		$first_title = trim( wp_strip_all_tags( $h1s[1][0] ) );
		$first_text  = '';
		if ( preg_match( '/<p[^>]*>(.*?)<\/p>/is', $content, $p ) ) {
			$first_text = trim( wp_strip_all_tags( $p[1] ) );
		}
		if ( $first_title ) {
			$section          = teznevise_migration_section_base( 'hero' );
			$section['title'] = $first_title;
			$section['text']  = function_exists( 'mb_substr' ) ? mb_substr( $first_text, 0, 400 ) : substr( $first_text, 0, 400 );
			$sections[]       = $section;
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
 * Only returns published pages that still need work: no builder meta yet
 * and not previously skipped as unparseable. Ordered by ID so batches
 * resume from the remaining set instead of re-scanning the same head.
 *
 * @param int $limit Max pages (0 = all remaining).
 * @return object[]
 */
function teznevise_migration_get_candidate_pages( $limit = 0 ) {
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
		'%[tz_price_box%',
		'%[tz_price_cta%',
		'%[tz_calculation_hub%',
		'%[tz_careers_terms%',
		'%<h1%',
	);

	$conditions = array();
	$params     = array();
	foreach ( $like_patterns as $pattern ) {
		$conditions[] = 'p.post_content LIKE %s';
		$params[]     = $pattern;
	}

	$builder_key = defined( 'TEZNEVISE_BUILDER_META' ) ? TEZNEVISE_BUILDER_META : '_teznevise_builder_sections';
	$skip_key    = TEZNEVISE_MIGRATION_SKIP_META;

	$sql = "SELECT p.ID, p.post_title, p.post_name, p.post_content
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} bm
			ON bm.post_id = p.ID AND bm.meta_key = %s AND bm.meta_value <> '' AND bm.meta_value <> '[]'
		LEFT JOIN {$wpdb->postmeta} sm
			ON sm.post_id = p.ID AND sm.meta_key = %s
		WHERE p.post_type = 'page' AND p.post_status = 'publish'
		AND bm.meta_id IS NULL
		AND ( sm.meta_id IS NULL OR sm.meta_value <> CONCAT(%s, ':', MD5(p.post_content)) )
		AND (" . implode( ' OR ', $conditions ) . ')'
		ORDER BY p.ID ASC';

	array_unshift( $params, $builder_key, $skip_key, TEZNEVISE_MIGRATION_VERSION );

	if ( $limit > 0 ) {
		$sql     .= ' LIMIT %d';
		$params[] = (int) $limit;
	}

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- placeholders built above; $wpdb->prepare used.
	return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
}

/**
 * Run migration.
 *
 * @param bool $dry_run        Dry run.
 * @param int  $limit          Limit pages (0 = all remaining candidates).
 * @param bool $strip_codes    Strip structural shortcodes after write.
 * @param bool $seed_tools     Seed calculator tool pages.
 * @return array
 */
function teznevise_migration_run( $dry_run = true, $limit = 0, $strip_codes = false, $seed_tools = false ) {
	$stats = array(
		'processed' => 0,
		'migrated'  => 0,
		'skipped'   => 0,
		'stripped'  => 0,
		'tools'     => array(),
		'errors'    => array(),
		'dry_run'   => (bool) $dry_run,
		'has_more'  => false,
	);

	if ( ! function_exists( 'teznevise_builder_save_sections' ) ) {
		$stats['errors'][] = 'Builder module not loaded.';
		return $stats;
	}

	if ( $seed_tools ) {
		$stats['tools'] = teznevise_migration_seed_calculator_tools( $dry_run );
	}

	$fetch = $limit > 0 ? (int) $limit + 1 : 0;
	$pages = teznevise_migration_get_candidate_pages( $fetch );
	if ( $limit > 0 && count( $pages ) > $limit ) {
		$stats['has_more'] = true;
		$pages             = array_slice( $pages, 0, $limit );
	}

	foreach ( $pages as $page ) {
		$stats['processed']++;

		$existing = get_post_meta( $page->ID, TEZNEVISE_BUILDER_META, true );
		if ( is_string( $existing ) && '' !== trim( $existing ) && '[]' !== trim( $existing ) ) {
			$stats['skipped']++;
			continue;
		}

		$sections = teznevise_migration_parse_content( $page->post_content, $page->post_name );
		if ( empty( $sections ) ) {
			if ( ! $dry_run ) {
				update_post_meta( $page->ID, TEZNEVISE_MIGRATION_SKIP_META, TEZNEVISE_MIGRATION_VERSION . ':' . md5( $page->post_content ) );
			}
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

	if ( ! $dry_run && empty( $stats['errors'] ) && ! $stats['has_more'] ) {
		teznevise_migration_maybe_mark_complete( $stats );
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
		$dry   = ! empty( $_POST['teznevise_migration_dry_run'] );
		$strip = ! empty( $_POST['teznevise_migration_strip'] );
		$tools = ! empty( $_POST['teznevise_migration_seed_tools'] );
		$stats = teznevise_migration_run( $dry, 0, $strip, $tools );
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
	<p><?php esc_html_e( 'صفحات قدیمی شورت‌کد/HTML را به `_teznevise_builder_sections` منتقل می‌کند. فقط صفحاتی بدون متای سازنده تغییر می‌کنند.', 'teznevise' ); ?></p>
	<p><a href="https://github.com/maziyarid/teznevise/blob/main/docs/SHORTCODE-TO-BUILDER-MIGRATION.md" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'مستندات مهاجرت', 'teznevise' ); ?></a>
	· <a href="https://github.com/maziyarid/teznevise/blob/main/docs/MIGRATION-DATA-SECURITY.md" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'هشدار امنیتی dumpها', 'teznevise' ); ?></a></p>
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
			if ( ! empty( $result['has_more'] ) ) {
				echo ' | ';
				esc_html_e( 'صفحات بیشتری باقی مانده است.', 'teznevise' );
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
