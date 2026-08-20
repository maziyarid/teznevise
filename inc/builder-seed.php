<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 *
 * Seed Flexible Page Builder sections from the HTML conversion map.
 *
 * Idempotent: existing `_teznevise_builder_sections` is left untouched unless
 * the caller explicitly requests a replace.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seed builder sections for one conversion key onto a post.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Conversion key.
 * @param bool   $replace Overwrite existing builder meta.
 * @return string created|updated|skipped|empty|invalid
 */
function teznevise_builder_seed_post( $post_id, $key, $replace = false ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return 'invalid';
	}

	$sections = teznevise_builder_default_sections( $key );
	if ( ! $sections ) {
		return 'empty';
	}

	$existing = get_post_meta( $post_id, TEZNEVISE_BUILDER_META, true );
	if ( $existing && ! $replace ) {
		return 'skipped';
	}

	$ok = teznevise_builder_save_sections( $post_id, $sections );
	if ( ! $ok ) {
		return 'invalid';
	}

	if ( function_exists( 'teznevise_set_builder_provenance' ) ) {
		teznevise_set_builder_provenance( $post_id, 'default-seed' );
	}

	$entry = teznevise_builder_defaults_entry( $key );
	if ( ! empty( $entry['excerpt'] ) && ! get_post_field( 'post_excerpt', $post_id ) ) {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_excerpt' => sanitize_textarea_field( $entry['excerpt'] ),
			)
		);
	}

	return $existing ? 'updated' : 'created';
}

/**
 * Ensure a sample blog post exists for post-sample.html.
 *
 * @param bool $replace Replace builder meta on the sample post.
 * @return array{id:int,status:string}
 */
function teznevise_builder_seed_sample_post( $replace = false ) {
	$entry = teznevise_builder_defaults_entry( 'post-sample' );
	$slug  = isset( $entry['slug'] ) ? $entry['slug'] : 'amoozesh-fasl-avval-payanname';
	$post  = get_page_by_path( $slug, OBJECT, 'post' );

	if ( ! $post ) {
		$post_id = wp_insert_post(
			array(
				'post_title'   => isset( $entry['title'] ) ? $entry['title'] : $slug,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'post',
				'post_excerpt' => isset( $entry['excerpt'] ) ? $entry['excerpt'] : '',
				'post_content' => teznevise_builder_sample_post_content(),
				'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			return array(
				'id'     => 0,
				'status' => 'invalid',
			);
		}
	} else {
		$post_id = (int) $post->ID;
	}

	return array(
		'id'     => (int) $post_id,
		'status' => teznevise_builder_seed_post( $post_id, 'post-sample', $replace ),
	);
}

/**
 * Fallback article body for the sample post (used only when the post is created).
 *
 * @return string
 */
function teznevise_builder_sample_post_content() {
	$paragraphs = array(
		'<h2>مقدمه: چرا فصل اول مهم است؟</h2>',
		'<p>فصل اول پایان‌نامه چارچوب مسئله، اهمیت پژوهش و مسیر کلی کار را مشخص می‌کند. اگر پروپوزال تصویب شده باشد، بخش زیادی از این فصل از همان متن قابل استخراج است — به شرطی که ساختار فصل با آیین‌نامه دانشگاه هم‌خوان شود.</p>',
		'<h2>از پروپوزال به فصل اول</h2>',
		'<p>بیان مسئله، پیشینه منتخب، اهداف، سوال‌ها یا فرضیه‌ها و تعریف مفاهیم را از پروپوزال منتقل کنید، سپس افعال و ارجاع به «پیشنهاد پژوهش» را به زبان گزارش نهایی تغییر دهید.</p>',
		'<h2>چک‌لیست کوتاه</h2>',
		'<ul><li>عنوان و کلیدواژه‌ها با پروپوزال مصوب هم‌خوان باشند.</li><li>شکاف علمی در یک بند روشن نوشته شود.</li><li>اهداف قابل اندازه‌گیری باشند.</li><li>محدوده و محدودیت‌ها جداگانه ذکر شوند.</li></ul>',
	);
	return implode( "\n\n", $paragraphs );
}

/**
 * Seed every mapped page plus the sample post.
 *
 * @param bool $replace Overwrite existing builder meta.
 * @return array{created:string[],updated:string[],skipped:string[],missing:string[]}
 */
function teznevise_builder_seed_all( $replace = false ) {
	$result = array(
		'created' => array(),
		'updated' => array(),
		'skipped' => array(),
		'missing' => array(),
	);

	$doc = teznevise_builder_defaults_document();
	foreach ( $doc['pages'] as $key => $entry ) {
		if ( isset( $entry['post_type'] ) && 'post' === $entry['post_type'] ) {
			$sample = teznevise_builder_seed_sample_post( $replace );
			if ( 'missing' === $sample['status'] || 'invalid' === $sample['status'] || 'empty' === $sample['status'] ) {
				$result['missing'][] = $key;
			} else {
				$result[ $sample['status'] ][] = $key;
			}
			continue;
		}

		if ( 'index' === $key ) {
			$front_id = (int) get_option( 'page_on_front' );
			if ( ! $front_id ) {
				$result['missing'][] = $key;
				continue;
			}
			$status = teznevise_builder_seed_post( $front_id, $key, $replace );
		} else {
			$slug = isset( $entry['slug'] ) ? $entry['slug'] : $key;
			$page = get_page_by_path( $slug );
			if ( ! $page ) {
				$result['missing'][] = $key;
				continue;
			}
			$status = teznevise_builder_seed_post( (int) $page->ID, $key, $replace );
		}

		if ( isset( $result[ $status ] ) ) {
			$result[ $status ][] = $key;
		} else {
			$result['missing'][] = $key;
		}
	}

	return $result;
}

/**
 * Persist the 1.8.4 homepage catalog (9 services / 6 steps) once.
 *
 * Render-time rewrite covers the public site immediately; this writes the
 * same items into builder meta so wp-admin matches what visitors see.
 */
function teznevise_builder_upgrade_homepage_184() {
	if ( get_option( 'teznevise_homepage_catalog_184' ) ) {
		return;
	}
	if ( ! defined( 'TEZNEVISE_BUILDER_META' ) || ! function_exists( 'teznevise_builder_get_sections' ) ) {
		return;
	}
	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id <= 0 ) {
		return;
	}
	$sections = teznevise_builder_get_sections( $front_id );
	if ( ! $sections ) {
		return;
	}
	if ( teznevise_builder_save_sections( $front_id, $sections ) ) {
		update_option( 'teznevise_homepage_catalog_184', '1.8.4', true );
	}
}
add_action( 'init', 'teznevise_builder_upgrade_homepage_184', 40 );

