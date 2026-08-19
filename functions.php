<?php
/**
 * Teznevise theme bootstrap.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once TEZNEVISE_DIR . '/inc/builder-defaults.php';
require_once TEZNEVISE_DIR . '/inc/builder-seed.php';
if ( is_admin() ) {
	require_once TEZNEVISE_DIR . '/inc/admin/builder-admin.php';
	require_once TEZNEVISE_DIR . '/inc/admin/builder-assets.php';
}
require_once TEZNEVISE_DIR . '/inc/cpts.php';
require_once TEZNEVISE_DIR . '/inc/blog.php';
require_once TEZNEVISE_DIR . '/inc/seo.php';
require_once TEZNEVISE_DIR . '/inc/setup-pages.php';
require_once TEZNEVISE_DIR . '/inc/promote-assets.php';
require_once TEZNEVISE_DIR . '/inc/screenshot-data.php';
require_once TEZNEVISE_DIR . '/inc/consultation.php';
require_once TEZNEVISE_DIR . '/inc/migration/shortcode-to-builder-migrator.php';
require_once TEZNEVISE_DIR . '/inc/migration/auto-run.php';

function teznevise_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'teznevise' ),
		'mobile'  => __( 'Mobile Menu', 'teznevise' ),
		'bottom'  => __( 'Bottom Mobile Menu', 'teznevise' ),
		'footer'  => __( 'Footer Menu', 'teznevise' ),
	) );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

function teznevise_enqueue_assets() {
	wp_enqueue_style( 'teznevise-style', get_stylesheet_uri(), array(), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-font-vazirmatn', 'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css', array(), '33.003' );
	wp_enqueue_style( 'teznevise-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );
	wp_enqueue_style( 'teznevise-redesign', TEZNEVISE_URI . '/assets/css/redesign.css', array( 'teznevise-style' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-layout-refinements', TEZNEVISE_URI . '/assets/css/layout-refinements.css', array( 'teznevise-redesign' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-motion', TEZNEVISE_URI . '/assets/css/motion.css', array( 'teznevise-layout-refinements' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-batch-fixes', TEZNEVISE_URI . '/assets/css/batch-fixes.css', array( 'teznevise-motion' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-ui-round2', TEZNEVISE_URI . '/assets/css/ui-round2.css', array( 'teznevise-batch-fixes' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-site-polish', TEZNEVISE_URI . '/assets/css/site-polish.css', array( 'teznevise-ui-round2' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-header-form', TEZNEVISE_URI . '/assets/css/header-form.css', array( 'teznevise-site-polish' ), TEZNEVISE_VERSION );
	wp_enqueue_style( 'teznevise-mobile-fixes', TEZNEVISE_URI . '/assets/css/mobile-fixes.css', array( 'teznevise-header-form' ), TEZNEVISE_VERSION );
	wp_enqueue_script( 'teznevise-redesign', TEZNEVISE_URI . '/assets/js/redesign.js', array(), TEZNEVISE_VERSION, true );
	wp_enqueue_script( 'teznevise-main', TEZNEVISE_URI . '/assets/js/main.js', array( 'teznevise-redesign' ), TEZNEVISE_VERSION, true );

	wp_localize_script(
		'teznevise-main',
		'tezneviseConsultation',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'teznevise_consultation' ),
		)
	);

	wp_add_inline_script(
		'teznevise-main',
		"document.addEventListener('DOMContentLoaded', function () {\n" .
		"  var config = window.tezneviseConsultation;\n" .
		"  if (!config) return;\n" .
		"  document.querySelectorAll('.lead-card form').forEach(function (form) {\n" .
		"    var fields = form.querySelectorAll('input, select, textarea');\n" .
		"    var names = ['name', 'phone', 'degree', 'field', 'message'];\n" .
		"    fields.forEach(function (field, index) { if (!field.name && names[index]) field.name = names[index]; });\n" .
		"    var button = form.querySelector('button[type=button], button[type=submit]');\n" .
		"    if (!button) return;\n" .
		"    var status = document.createElement('p');\n" .
		"    status.className = 'inq-msg';\n" .
		"    status.setAttribute('role', 'status');\n" .
		"    form.appendChild(status);\n" .
		"    form.addEventListener('submit', function (event) { event.preventDefault(); });\n" .
		"    button.addEventListener('click', function (event) {\n" .
		"      event.preventDefault();\n" .
		"      status.textContent = '';\n" .
		"      button.disabled = true;\n" .
		"      var data = new FormData(form);\n" .
		"      data.append('action', 'teznevise_consultation');\n" .
		"      data.append('nonce', config.nonce);\n" .
		"      fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })\n" .
		"        .then(function (response) { return response.json(); })\n" .
		"        .then(function (result) {\n" .
		"          status.textContent = result && result.data && result.data.message ? result.data.message : 'ارسال درخواست ناموفق بود.';\n" .
		"          if (result && result.success) form.reset();\n" .
		"        })\n" .
		"        .catch(function () { status.textContent = 'ارسال درخواست ناموفق بود. لطفاً دوباره تلاش کنید.'; })\n" .
		"        .finally(function () { button.disabled = false; });\n" .
		"    });\n" .
		"  });\n" .
		"});"
	);
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_assets' );