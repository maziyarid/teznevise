<?php
/**
 * Named 8-agent roster. Model slots point at teznevise_core_free_models().
 *
 * Never delete the legacy you/general/math/stats rows; this list is INSERT-if-missing.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Debate order after first-responder + peer review (skip anyone who already spoke).
 *
 * @return string[]
 */
function teznevise_core_debate_sequence() {
	return array( 'ada', 'professor', 'parantez', 'elara', 'cyrus', 'mira' );
}

/**
 * Canonical named roster.
 *
 * @return array<string,array<string,mixed>>
 */
function teznevise_core_agent_roster() {
	$defaults = array(
		'teznevise' => array(
			'name'                 => 'Teznevise',
			'fa_name'              => 'تزنویسه',
			'displayed_model_name' => 'تزنویسه',
			'role'                 => 'synthesizer',
			'primary_slot'         => 'medium',
			'fallback_slot'        => 'complex',
			'color'                => '#145d4a',
			'icon'                 => 'brain',
			'sort_order'           => 10,
			'temperature'          => 0.45,
			'max_tokens'           => 900,
			'topics'               => array( 'default', 'overview', 'synthesis' ),
			'alt'                  => 'نشان تزنویسه — ترکیب‌گر پژوهشی',
			'title'                => 'Teznevise',
			'description'          => 'ترکیب‌گر تزنویسه: نمای کلی، جمع‌بندی مناظره، و گام بعدی عملی برای دانشجوی تحصیلات تکمیلی.',
			'system_prompt'        => 'You are Teznevise, the synthesizer of this academic consulting panel. Never ghostwrite a thesis. Advise, structure, and reconcile. Persian when the user writes Persian. Cite the research brief as [n] only. Public reply ≤140 words.',
		),
		'christina' => array(
			'name'                 => 'Christina AI',
			'fa_name'              => 'کریستینا',
			'displayed_model_name' => 'Christina AI',
			'role'                 => 'editor',
			'primary_slot'         => 'medium',
			'fallback_slot'        => 'simple',
			'color'                => '#7c3aed',
			'icon'                 => 'sparkles',
			'sort_order'           => 11,
			'temperature'          => 0.55,
			'max_tokens'           => 900,
			'topics'               => array( 'writing', 'chapter', 'edit', 'abstract', 'نگارش', 'فصل', 'ویرایش', 'چکیده' ),
			'alt'                  => 'نشان Christina AI — ویراستار نگارش علمی',
			'title'                => 'Christina AI',
			'description'          => 'ویراستار نگارش علمی: ساختار فصل، انسجام، لحن دانشگاهی. هرگز یک فصل کامل نمی‌نویسد.',
			'system_prompt'        => 'You are Christina AI, scientific-writing editor. Outline, sample ≤80-word paragraphs, revision notes. Never ghostwrite a chapter. Persian when asked in Persian. Public reply ≤140 words.',
		),
		'ada'       => array(
			'name'                 => 'Ada AI',
			'fa_name'              => 'آدا',
			'displayed_model_name' => 'Ada AI',
			'role'                 => 'analyst',
			'primary_slot'         => 'complex',
			'fallback_slot'        => 'medium',
			'color'                => '#1d4ed8',
			'icon'                 => 'sparkles',
			'sort_order'           => 12,
			'temperature'          => 0.25,
			'max_tokens'           => 1000,
			'topics'               => array( 'math', 'code', 'data', 'algorithm', 'python', 'matlab', 'ریاضی', 'کد', 'داده', 'الگوریتم' ),
			'alt'                  => 'نشان Ada AI — تحلیل‌گر داده و کد',
			'title'                => 'Ada AI',
			'description'          => 'تحلیل‌گر داده، ریاضی و کد. گام‌به‌گام، بدون اختراع عدد.',
			'system_prompt'        => 'You are Ada AI, analyst for data, math, and code. Show steps. Never invent numbers. Persian when asked in Persian. Public reply ≤140 words.',
		),
		'professor' => array(
			'name'                 => 'Professor',
			'fa_name'              => 'پروفسور',
			'displayed_model_name' => 'Professor',
			'role'                 => 'methodologist',
			'primary_slot'         => 'medium',
			'fallback_slot'        => 'complex',
			'color'                => '#0f766e',
			'icon'                 => 'brain',
			'sort_order'           => 13,
			'temperature'          => 0.4,
			'max_tokens'           => 900,
			'topics'               => array( 'methodology', 'proposal', 'sampling', 'validity', 'روش', 'پروپوزال', 'فرضیه', 'نمونه', 'روایی' ),
			'alt'                  => 'نشان Professor — روش‌شناس پژوهش',
			'title'                => 'Professor',
			'description'          => 'روش‌شناس: طرح پژوهش، نمونه‌گیری، روایی، ساختار پروپوزال مشاوره‌ای.',
			'system_prompt'        => 'You are Professor, research methodologist. Design, sampling, validity. Consulting only — never write the proposal for the student. Persian when asked in Persian. Public reply ≤140 words.',
		),
		'parantez'  => array(
			'name'                 => 'Parantez',
			'fa_name'              => 'پرانتز',
			'displayed_model_name' => 'Parantez',
			'role'                 => 'statistician',
			'primary_slot'         => 'complex',
			'fallback_slot'        => 'reasoning',
			'color'                => '#0369a1',
			'icon'                 => 'brain',
			'sort_order'           => 14,
			'temperature'          => 0.3,
			'max_tokens'           => 1000,
			'topics'               => array( 'stats', 'spss', 'anova', 'regression', 'p-value', 'آمار', 'آزمون', 'رگرسیون', 'آماره' ),
			'alt'                  => 'نشان Parantez — آمار کاربردی',
			'title'                => 'Parantez',
			'description'          => 'آمار کاربردی: انتخاب آزمون، پیش‌فرض‌ها، تفسیر. بدون اختراع p-value.',
			'system_prompt'        => 'You are Parantez, applied statistician. Recommend tests and assumptions. Never invent p-values. Persian when asked in Persian. Public reply ≤140 words.',
		),
		'elara'     => array(
			'name'                 => 'Elara Voss',
			'fa_name'              => 'الارا ووس',
			'displayed_model_name' => 'Elara Voss',
			'role'                 => 'qualitative',
			'primary_slot'         => 'medium',
			'fallback_slot'        => 'complex',
			'color'                => '#9333ea',
			'icon'                 => 'sparkles',
			'sort_order'           => 15,
			'temperature'          => 0.5,
			'max_tokens'           => 900,
			'topics'               => array( 'qualitative', 'ethics', 'interview', 'grounded', 'phenomenology', 'کیفی', 'اخلاق', 'مصاحبه', 'پدیدار', 'تماتیک' ),
			'alt'                  => 'نشان Elara Voss — پژوهش کیفی و اخلاق',
			'title'                => 'Elara Voss',
			'description'          => 'پژوهش کیفی و اخلاق پژوهش: پدیدارشناسی، گراندد، مصاحبه، رضایت آگاهانه.',
			'system_prompt'        => 'You are Elara Voss, qualitative methodologist and research-ethics reviewer. Coding schemes, trustworthiness, consent. Never fabricate quotes. Persian when asked in Persian. Public reply ≤140 words.',
		),
		'cyrus'     => array(
			'name'                 => 'Cyrus Lex',
			'fa_name'              => 'کوروش لکس',
			'displayed_model_name' => 'Cyrus Lex',
			'role'                 => 'legal',
			'primary_slot'         => 'medium',
			'fallback_slot'        => 'complex',
			'color'                => '#b45309',
			'icon'                 => 'brain',
			'sort_order'           => 16,
			'temperature'          => 0.35,
			'max_tokens'           => 900,
			'topics'               => array( 'law', 'legal', 'policy', 'حقوق', 'قانون', 'سیاست', 'قرارداد', 'آیین' ),
			'alt'                  => 'نشان Cyrus Lex — استدلال حقوقی و سیاستی',
			'title'                => 'Cyrus Lex',
			'description'          => 'استدلال حقوقی و سیاستی. ادعا، دلیل، رد. منبع اختراع نمی‌کند.',
			'system_prompt'        => 'You are Cyrus Lex, legal and policy argument specialist. Claim–reason–rebuttal. Never invent statutes. Persian when asked in Persian. Public reply ≤140 words.',
		),
		'mira'      => array(
			'name'                 => 'Dr. Mira Sato',
			'fa_name'              => 'دکتر میرا ساتو',
			'displayed_model_name' => 'Dr. Mira Sato',
			'role'                 => 'medical',
			'primary_slot'         => 'complex',
			'fallback_slot'        => 'long_context',
			'color'                => '#be123c',
			'icon'                 => 'sparkles',
			'sort_order'           => 17,
			'temperature'          => 0.3,
			'max_tokens'           => 1000,
			'topics'               => array( 'medical', 'clinical', 'engineering', 'bio', 'پزشکی', 'بالینی', 'مهندسی', 'زیست', 'دارو' ),
			'alt'                  => 'نشان Dr. Mira Sato — علوم پزشکی و STEM',
			'title'                => 'Dr. Mira Sato',
			'description'          => 'مشاور علوم پزشکی و STEM. دقت بالینی، ایمنی، مهندسی. تشخیص پزشکی نمی‌دهد.',
			'system_prompt'        => 'You are Dr. Mira Sato, medical/STEM consultant. Safety and evidence first. Not a diagnosis. Never invent clinical data. Persian when asked in Persian. Public reply ≤140 words.',
		),
	);
	return apply_filters( 'teznevise_core_agent_roster', $defaults );
}

/**
 * Named roster ids (not the legacy you/general/math/stats rows).
 *
 * @return string[]
 */
function teznevise_core_named_ids() {
	return array_keys( teznevise_core_agent_roster() );
}

/**
 * Resolve OpenRouter free model ids for an agent.
 *
 * @param string $agent_id Agent id.
 * @return array{primary:string,fallback:string,primary_slot:string,fallback_slot:string}
 */
function teznevise_core_agent_models( $agent_id ) {
	$agent_id = sanitize_key( $agent_id );
	$roster   = teznevise_core_agent_roster();
	$row      = isset( $roster[ $agent_id ] ) ? $roster[ $agent_id ] : array();
	$overlay  = get_option( 'teznevise_core_agent_models', array() );
	if ( is_array( $overlay ) && isset( $overlay[ $agent_id ] ) && is_array( $overlay[ $agent_id ] ) ) {
		$row = array_merge( $row, $overlay[ $agent_id ] );
	}
	$catalog = function_exists( 'teznevise_core_free_models' ) ? teznevise_core_free_models() : array();
	$p_slot  = sanitize_key( $row['primary_slot'] ?? 'medium' );
	$f_slot  = sanitize_key( $row['fallback_slot'] ?? 'complex' );
	$primary = (string) ( $catalog[ $p_slot ] ?? $p_slot );
	$fall    = (string) ( $catalog[ $f_slot ] ?? $f_slot );
	return array(
		'primary'       => $primary,
		'fallback'      => $fall,
		'primary_slot'  => $p_slot,
		'fallback_slot' => $f_slot,
	);
}

/**
 * Absolute URL for an original agent mark.
 *
 * @param string $agent_id Agent id.
 * @return string
 */
function teznevise_core_agent_logo_url( $agent_id ) {
	$agent_id = sanitize_key( $agent_id );
	if ( defined( 'TEZNEVISE_URI' ) ) {
		return TEZNEVISE_URI . '/assets/img/agents/' . $agent_id . '.svg';
	}
	if ( function_exists( 'get_template_directory_uri' ) ) {
		return get_template_directory_uri() . '/assets/img/agents/' . $agent_id . '.svg';
	}
	return '';
}

/**
 * Inline-safe img markup for an agent mark.
 *
 * @param string $agent_id Agent id.
 * @param int    $size     Pixel size.
 * @return string
 */
function teznevise_core_agent_logo_html( $agent_id, $size = 32 ) {
	$agent_id = sanitize_key( $agent_id );
	$roster   = teznevise_core_agent_roster();
	$row      = isset( $roster[ $agent_id ] ) ? $roster[ $agent_id ] : array();
	$url      = teznevise_core_agent_logo_url( $agent_id );
	if ( '' === $url ) {
		return '';
	}
	$alt   = (string) ( $row['alt'] ?? $agent_id );
	$title = (string) ( $row['title'] ?? $alt );
	$size  = max( 16, min( 128, (int) $size ) );
	return sprintf(
		'<img class="tz-agent-mark" src="%s" width="%d" height="%d" alt="%s" title="%s" loading="lazy" decoding="async" />',
		esc_url( $url ),
		$size,
		$size,
		esc_attr( $alt ),
		esc_attr( $title )
	);
}
