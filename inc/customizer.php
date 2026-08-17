<?php
/**
 * Theme Customizer — homepage sections + contact.
 *
 * Appearance → Customize → تزنویسه
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer panels, sections, settings, controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function teznevise_customize_register( $wp_customize ) {
	$defaults = teznevise_content_defaults();

	$wp_customize->add_panel(
		'teznevise_panel',
		array(
			'title'       => __( 'تزنویسه — محتوای سایت', 'teznevise' ),
			'description' => __( 'ویرایش بخش‌های صفحه اصلی، تماس و CTA از این پنل.', 'teznevise' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_section( 'teznevise_contact', array( 'title' => __( 'اطلاعات تماس', 'teznevise' ), 'panel' => 'teznevise_panel', 'priority' => 10 ) );

	$contact_fields = array(
		'phone' => __( 'شماره تلفن (لاتین)', 'teznevise' ),
		'phone_display' => __( 'نمایش تلفن (فارسی)', 'teznevise' ),
		'phone_intl' => __( 'تلفن بین‌المللی', 'teznevise' ),
		'whatsapp' => __( 'لینک واتساپ', 'teznevise' ),
		'telegram' => __( 'لینک تلگرام', 'teznevise' ),
		'bale' => __( 'لینک بله', 'teznevise' ),
		'email' => __( 'ایمیل', 'teznevise' ),
		'address' => __( 'آدرس', 'teznevise' ),
		'hours' => __( 'ساعات پاسخ‌گویی', 'teznevise' ),
	);
	foreach ( $contact_fields as $key => $label ) {
		teznevise_add_text_setting( $wp_customize, $key, $label, 'teznevise_contact', $defaults );
	}

	$wp_customize->add_section( 'teznevise_hero', array( 'title' => __( 'صفحه اصلی — هیرو', 'teznevise' ), 'panel' => 'teznevise_panel', 'priority' => 20 ) );
	$hero_fields = array(
		'hero_eyebrow' => array( 'label' => __( 'ابرو (Eyebrow)', 'teznevise' ), 'type' => 'text' ),
		'hero_title_1' => array( 'label' => __( 'عنوان — بخش اول', 'teznevise' ), 'type' => 'text' ),
		'hero_title_grad' => array( 'label' => __( 'عنوان — بخش رنگی', 'teznevise' ), 'type' => 'text' ),
		'hero_title_2' => array( 'label' => __( 'عنوان — بخش آخر', 'teznevise' ), 'type' => 'text' ),
		'hero_text' => array( 'label' => __( 'توضیح هیرو', 'teznevise' ), 'type' => 'textarea' ),
		'hero_btn_primary' => array( 'label' => __( 'دکمه اصلی — متن', 'teznevise' ), 'type' => 'text' ),
		'hero_btn_primary_url' => array( 'label' => __( 'دکمه اصلی — لینک', 'teznevise' ), 'type' => 'url' ),
		'hero_btn_secondary' => array( 'label' => __( 'دکمه فرعی — متن', 'teznevise' ), 'type' => 'text' ),
		'hero_point_1' => array( 'label' => __( 'نقطه قوت 1', 'teznevise' ), 'type' => 'text' ),
		'hero_point_2' => array( 'label' => __( 'نقطه قوت 2', 'teznevise' ), 'type' => 'text' ),
		'hero_point_3' => array( 'label' => __( 'نقطه قوت 3', 'teznevise' ), 'type' => 'text' ),
	);
	foreach ( $hero_fields as $key => $cfg ) {
		teznevise_add_text_setting( $wp_customize, $key, $cfg['label'], 'teznevise_hero', $defaults, $cfg['type'] );
	}

	$wp_customize->add_section( 'teznevise_services', array( 'title' => __( 'صفحه اصلی — خدمات', 'teznevise' ), 'panel' => 'teznevise_panel', 'priority' => 30 ) );
	foreach ( array( 'services_eyebrow' => __( 'ابرو بخش خدمات', 'teznevise' ), 'services_title' => __( 'عنوان بخش خدمات', 'teznevise' ), 'services_text' => __( 'توضیح بخش خدمات', 'teznevise' ) ) as $key => $label ) {
		$type = ( $key === 'services_text' ) ? 'textarea' : 'text';
		teznevise_add_text_setting( $wp_customize, $key, $label, 'teznevise_services', $defaults, $type );
	}
	for ( $i = 1; $i <= 6; $i++ ) {
		teznevise_add_text_setting( $wp_customize, "svc{$i}_title", sprintf( __( 'کارت %d — عنوان', 'teznevise' ), $i ), 'teznevise_services', $defaults );
		teznevise_add_text_setting( $wp_customize, "svc{$i}_text", sprintf( __( 'کارت %d — توضیح', 'teznevise' ), $i ), 'teznevise_services', $defaults, 'textarea' );
		teznevise_add_text_setting( $wp_customize, "svc{$i}_url", sprintf( __( 'کارت %d — لینک', 'teznevise' ), $i ), 'teznevise_services', $defaults, 'url' );
		teznevise_add_text_setting( $wp_customize, "svc{$i}_icon", sprintf( __( 'کارت %d — کلاس آیکون FA', 'teznevise' ), $i ), 'teznevise_services', $defaults );
		teznevise_add_text_setting( $wp_customize, "svc{$i}_color", sprintf( __( 'کارت %d — کلاس رنگ (icon-*)', 'teznevise' ), $i ), 'teznevise_services', $defaults );
	}

	$wp_customize->add_section( 'teznevise_about', array( 'title' => __( 'صفحه اصلی — درباره و مزایا', 'teznevise' ), 'panel' => 'teznevise_panel', 'priority' => 40 ) );
	foreach ( array(
		'about_eyebrow' => array( __( 'ابرو درباره', 'teznevise' ), 'text' ),
		'about_title' => array( __( 'عنوان درباره', 'teznevise' ), 'text' ),
		'about_text' => array( __( 'متن درباره', 'teznevise' ), 'textarea' ),
		'about_btn' => array( __( 'متن دکمه درباره', 'teznevise' ), 'text' ),
		'about_btn_url' => array( __( 'لینک دکمه درباره', 'teznevise' ), 'url' ),
		'reason1_title' => array( __( 'مزیت 1 — عنوان', 'teznevise' ), 'text' ),
		'reason1_text' => array( __( 'مزیت 1 — متن', 'teznevise' ), 'textarea' ),
		'reason2_title' => array( __( 'مزیت 2 — عنوان', 'teznevise' ), 'text' ),
		'reason2_text' => array( __( 'مزیت 2 — متن', 'teznevise' ), 'textarea' ),
		'reason3_title' => array( __( 'مزیت 3 — عنوان', 'teznevise' ), 'text' ),
		'reason3_text' => array( __( 'مزیت 3 — متن', 'teznevise' ), 'textarea' ),
		'reason4_title' => array( __( 'مزیت 4 — عنوان', 'teznevise' ), 'text' ),
		'reason4_text' => array( __( 'مزیت 4 — متن', 'teznevise' ), 'textarea' ),
	) as $key => $cfg ) {
		teznevise_add_text_setting( $wp_customize, $key, $cfg[0], 'teznevise_about', $defaults, $cfg[1] );
	}

	$wp_customize->add_section( 'teznevise_steps', array( 'title' => __( 'صفحه اصلی — چهار قدم', 'teznevise' ), 'panel' => 'teznevise_panel', 'priority' => 50 ) );
	foreach ( array(
		'steps_eyebrow' => array( __( 'ابرو قدم‌ها', 'teznevise' ), 'text' ),
		'steps_title' => array( __( 'عنوان قدم‌ها', 'teznevise' ), 'text' ),
		'steps_text' => array( __( 'توضیح قدم‌ها', 'teznevise' ), 'textarea' ),
		'step1_title' => array( __( 'قدم 1 — عنوان', 'teznevise' ), 'text' ),
		'step1_text' => array( __( 'قدم 1 — متن', 'teznevise' ), 'textarea' ),
		'step2_title' => array( __( 'قدم 2 — عنوان', 'teznevise' ), 'text' ),
		'step2_text' => array( __( 'قدم 2 — متن', 'teznevise' ), 'textarea' ),
		'step3_title' => array( __( 'قدم 3 — عنوان', 'teznevise' ), 'text' ),
		'step3_text' => array( __( 'قدم 3 — متن', 'teznevise' ), 'textarea' ),
		'step4_title' => array( __( 'قدم 4 — عنوان', 'teznevise' ), 'text' ),
		'step4_text' => array( __( 'قدم 4 — متن', 'teznevise' ), 'textarea' ),
	) as $key => $cfg ) {
		teznevise_add_text_setting( $wp_customize, $key, $cfg[0], 'teznevise_steps', $defaults, $cfg[1] );
	}

	$wp_customize->add_section( 'teznevise_articles', array( 'title' => __( 'صفحه اصلی — مقالات', 'teznevise' ), 'panel' => 'teznevise_panel', 'priority' => 60 ) );
	foreach ( array( 'articles_eyebrow' => array( __( 'ابرو مقالات', 'teznevise' ), 'text' ), 'articles_title' => array( __( 'عنوان مقالات', 'teznevise' ), 'text' ), 'articles_text' => array( __( 'توضیح مقالات', 'teznevise' ), 'textarea' ) ) as $key => $cfg ) {
		teznevise_add_text_setting( $wp_customize, $key, $cfg[0], 'teznevise_articles', $defaults, $cfg[1] );
	}

	$wp_customize->add_section( 'teznevise_cta', array( 'title' => __( 'صفحه اصلی — نوار CTA', 'teznevise' ), 'panel' => 'teznevise_panel', 'priority' => 70 ) );
	foreach ( array( 'cta_title' => array( __( 'عنوان CTA', 'teznevise' ), 'text' ), 'cta_text' => array( __( 'متن CTA', 'teznevise' ), 'textarea' ), 'cta_btn' => array( __( 'متن دکمه CTA', 'teznevise' ), 'text' ), 'cta_btn_url' => array( __( 'لینک دکمه CTA', 'teznevise' ), 'url' ) ) as $key => $cfg ) {
		teznevise_add_text_setting( $wp_customize, $key, $cfg[0], 'teznevise_cta', $defaults, $cfg[1] );
	}
}
add_action( 'customize_register', 'teznevise_customize_register' );

/**
 * Helper to register a simple text/textarea/url setting + control.
 */
function teznevise_add_text_setting( $wp_customize, $key, $label, $section, $defaults, $type = 'text' ) {
	$setting_id = 'teznevise_' . $key;
	$default    = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$sanitize = 'sanitize_text_field';
	if ( 'textarea' === $type ) {
		$sanitize = 'sanitize_textarea_field';
	} elseif ( 'url' === $type ) {
		$sanitize = 'esc_url_raw';
	} elseif ( 'email' === $type ) {
		$sanitize = 'sanitize_email';
	}
	$wp_customize->add_setting( $setting_id, array( 'default' => $default, 'sanitize_callback' => $sanitize, 'transport' => 'refresh', 'type' => 'theme_mod' ) );
	$control_type = ( 'textarea' === $type ) ? 'textarea' : 'text';
	if ( 'url' === $type || 'email' === $type ) {
		$control_type = $type;
	}
	$wp_customize->add_control( $setting_id, array( 'label' => $label, 'section' => $section, 'type' => $control_type ) );
}
