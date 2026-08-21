<?php
/**
 * Custom header setup for TezNevise theme
 * 
 * @package TezNevise
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('teznevise_custom_header_setup')) {
    function teznevise_custom_header_setup() {
        add_theme_support('custom-header', array(
            'default-image' => '',
            'default-text-color' => '000000',
            'width' => 1200,
            'height' => 90,
            'flex-height' => true,
            'flex-width' => true,
            'wp-head-callback' => 'teznevise_header_style',
            'admin-head-callback' => 'teznevise_admin_header_style',
            'admin-preview-callback' => 'teznevise_admin_header_image',
        ));
    }
    add_action('after_setup_theme', 'teznevise_custom_header_setup');

    function teznevise_header_style() {
        $header_text_color = get_header_textcolor();
        echo '<style type="text/css">';
        if (!display_header_text()) {
            echo '.site-title, .site-description { position: absolute; clip: rect(1px, 1px, 1px, 1px); padding: 0; margin: 0; overflow: hidden; }';
        } else {
            echo '.site-title a, .site-description { color: #' . esc_attr($header_text_color) . '; }';
        }
        echo '</style>';
    }

    function teznevise_admin_header_style() {
        echo '<style type="text/css">.appearance_page_custom-header #headimg { border: none; }</style>';
    }

    function teznevise_admin_header_image() {
        $style = ' style="color:#' . get_header_textcolor() . ';"';
        echo '<div id="headimg">';
        if (get_header_image()) {
            echo '<img src="' . esc_url(header_image()) . '" alt="' . esc_attr__('Header Image', 'teznevise') . '" />';
        }
        echo '<h1 class="displaying-header' . $style . '">' . get_bloginfo('name') . '</h1>';
        echo '<p class="displaying-header' . $style . '">' . get_bloginfo('description') . '</p>';
        echo '</div>';
    }
}