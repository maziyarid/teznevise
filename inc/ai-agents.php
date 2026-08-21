<?php
/**
 * TezNevise AI Agents - Complete AI Chat System
 * 
 * This file replaces the incomplete AI agents implementation with a full-featured
 * AI chat system integrated directly into the theme.
 * 
 * Version: 2.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define constants
define('TEZNEVISE_AI_VERSION', '2.0.0');
define('TEZNEVISE_AI_INC_DIR', get_template_directory() . '/inc/ai-agents/');

// Load AI Agents classes
require_once TEZNEVISE_AI_INC_DIR . 'class-database.php';
require_once TEZNEVISE_AI_INC_DIR . 'class-api.php';
require_once TEZNEVISE_AI_INC_DIR . 'class-chat.php';
require_once TEZNEVISE_AI_INC_DIR . 'class-settings.php';
require_once TEZNEVISE_AI_INC_DIR . 'class-assets.php';

// Initialize AI Agents
function teznevise_ai_init() {
    // Initialize database
    TezNevise_AI_Database::init();
    
    // Initialize REST API
    TezNevise_AI_API::init();
    
    // Initialize chat
    TezNevise_AI_Chat::init();
    
    // Initialize settings
    TezNevise_AI_Settings::init();
    
    // Initialize assets
    TezNevise_AI_Assets::init();
}
add_action('after_setup_theme', 'teznevise_ai_init', 20);

// Activation and deactivation hooks
// These are called when the theme is activated/deactivated
function teznevise_ai_activate() {
    TezNevise_AI_Database::activate();
}
add_action('after_switch_theme', 'teznevise_ai_activate');

function teznevise_ai_deactivate() {
    // Flush rewrite rules when theme is deactivated
    flush_rewrite_rules();
}
add_action('switch_theme', 'teznevise_ai_deactivate');

// Helper function to check if AI is enabled
function teznevise_ai_is_enabled() {
    return defined('TEZNEVISE_AI_VERSION');
}

// Helper function to render AI chat
function teznevise_ai_chat_shortcode($atts) {
    return TezNevise_AI_Chat::render_chat($atts);
}
add_shortcode('teznevise_ai_chat', 'teznevise_ai_chat_shortcode');
