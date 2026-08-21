<?php
/**
 * TezNevise functions and definitions
 * 
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * 
 * @package TezNevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'TEZNEVISE_VERSION', '1.9.2' );
define( 'TEZNEVISE_DIR', get_template_directory() );
define( 'TEZNEVISE_URI', get_template_directory_uri() );

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function teznevise_setup() {
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	
	// Add theme support for document Title tag and logo
	add_theme_support( 'title-tag' );
	
	// Add theme support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );
	
	// Add theme support for Custom Logo
	add_theme_support( 'custom-logo', array(
		'width'       => 200,
		'height'      => 200,
		'flex-width'  => true,
		'flex-height' => true,
	) );
	
	// Add theme support for Custom Background
	add_theme_support( 'custom-background', apply_filters( 'teznevise_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
	
	// Add theme support for Custom Header
	add_theme_support( 'custom-header', apply_filters( 'teznevise_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'    => '000000',
		'width'                 => 1200,
		'height'                => 200,
		'flex-height'           => true,
		'wp-head-callback'     => 'teznevise_header_style',
	) ) );
	
	// Add theme support for HTML5
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	
	// Add theme support for Post Formats
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'status',
		'audio',
		'chat',
	) );
	
	// Add theme support for WooCommerce
	add_theme_support( 'woocommerce' );
	
	// Add theme support for Gutenberg
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	
	// Register image sizes
	add_image_size( 'teznevise-thumbnail', 300, 200, true );
	add_image_size( 'teznevise-medium', 600, 400, true );
	add_image_size( 'teznevise-large', 1200, 600, true );
	
	// Register navigation menus
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'teznevise' ),
		'secondary' => esc_html__( 'Secondary Menu', 'teznevise' ),
		'footer' => esc_html__( 'Footer Menu', 'teznevise' ),
	) );
	
	// Load theme textdomain
	load_theme_textdomain( 'teznevise', get_template_directory() . '/languages' );
	
	// Add editor styles
	add_editor_style( 'style-editor.css' );
}
add_action( 'after_setup_theme', 'teznevise_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function teznevise_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'teznevise_content_width', 1200 );
}
add_action( 'after_setup_theme', 'teznevise_content_width', 0 );

/**
 * Register widget area.
 */
function teznevise_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'teznevise' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'teznevise' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'teznevise' ),
		'id'            => 'footer-widget-area',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'teznevise' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'teznevise_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function teznevise_scripts() {
	// Load CSS
	wp_enqueue_style( 'teznevise-style', get_stylesheet_uri(), array(), _S_VERSION );
	
	// Load JS
	wp_enqueue_script( 'teznevise-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'teznevise-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), _S_VERSION, true );
	
	// Load Font Awesome
	wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0' );
	
	// Load custom JS
	wp_enqueue_script( 'teznevise-custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), _S_VERSION, true );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'teznevise_scripts' );

/**
 * Enqueue admin scripts and styles.
 */
function teznevise_admin_scripts() {
	// Load admin CSS
	wp_enqueue_style( 'teznevise-admin-style', get_template_directory_uri() . '/css/admin.css', array(), _S_VERSION );
	
	// Load admin JS
	wp_enqueue_script( 'teznevise-admin-script', get_template_directory_uri() . '/js/admin.js', array('jquery'), _S_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'teznevise_admin_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Load Elementor compatibility file.
 */
if ( defined( 'ELEMENTOR_VERSION' ) ) {
	require get_template_directory() . '/inc/elementor.php';
}

/**
 * Load TezNevise Builder.
 */
require get_template_directory() . '/inc/class-teznevise-builder.php';

/**
 * Load theme helpers.
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * Load theme defaults.
 */
require get_template_directory() . '/inc/defaults.php';

/**
 * Load theme CPTs.
 */
require get_template_directory() . '/inc/cpts.php';

/**
 * Load theme SEO.
 */
require get_template_directory() . '/inc/seo.php';

/**
 * Load theme blog functions.
 */
require get_template_directory() . '/inc/blog.php';

/**
 * Load theme dashboard.
 */
require get_template_directory() . '/inc/dashboard.php';

/**
 * Load theme TezCoin.
 */
require get_template_directory() . '/inc/tezcoin.php';

/**
 * Load TezNevise AI Chat System.
 */
require get_template_directory() . '/inc/ai-agents.php';
