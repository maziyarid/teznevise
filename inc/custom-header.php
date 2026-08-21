<?php
/**
 * Custom header functionality for TezNevise theme
 * 
 * This file handles custom header features and is required by functions.php
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Custom header setup
function teznevise_custom_header_setup() {
    // Add custom header support
    add_theme_support('custom-header', apply_filters('teznevise_custom_header_args', array(
        'default-image'          => '',
        'default-text-color'     => '000000',
        'width'                 => 1200,
        'height'                => 90,
        'flex-height'           => true,
        'flex-width'            => true,
        'wp-head-callback'      => 'teznevise_header_style',
    )));
}
add_action('after_setup_theme', 'teznevise_custom_header_setup');

// Custom header style
function teznevise_header_style() {
    $header_image = get_header_image();
    if (!empty($header_image)) {
        echo '<style type="text/css">.site-header { background: url(' . esc_url($header_image) . ') no-repeat center top; background-size: cover; }</style>';
    }
}

// Custom header image markup
function teznevise_custom_header_markup() {
    if (get_header_image()) {
        echo '<div class="custom-header-image">';
        the_header_image_tag();
        echo '</div>';
    }
}
