<?php
/**
 * TezNevise AI Agents - Main entry point
 */

if (!defined('ABSPATH')) exit;

if (!defined('TEZNEVISE_AI_DIR')) {
    define('TEZNEVISE_AI_DIR', get_template_directory() . '/inc/ai/');
}

if (!defined('TEZNEVISE_AI_URL')) {
    define('TEZNEVISE_AI_URL', get_template_directory_uri() . '/inc/ai/');
}

require_once TEZNEVISE_AI_DIR . 'class-ai-core.php';
require_once TEZNEVISE_AI_DIR . 'class-ai-database.php';
require_once TEZNEVISE_AI_DIR . 'class-ai-knowledge.php';
require_once TEZNEVISE_AI_DIR . 'class-ai-api.php';
require_once TEZNEVISE_AI_DIR . 'class-ai-chat.php';
require_once TEZNEVISE_AI_DIR . 'class-ai-settings.php';

add_action('after_setup_theme', 'teznevise_ai_init', 20);

function teznevise_ai_init() {
    TezNevise_AI_Core::init();
    TezNevise_AI_Database::init();
    if ( class_exists( 'TezNevise_AI_Knowledge' ) ) {
        TezNevise_AI_Knowledge::init();
    }
    TezNevise_AI_API::init();
    TezNevise_AI_Chat::init();
    TezNevise_AI_Settings::init();
    
    $tools_dir = TEZNEVISE_AI_DIR . 'tools/';
    if (is_dir($tools_dir)) {
        $files = glob($tools_dir . '*.php');
        foreach ($files as $file) {
            $tool_name = basename($file, '.php');
            $config = include $file;
            TezNevise_AI_Core::register_tool($tool_name, $config);
        }
    }
    
    $skills_dir = TEZNEVISE_AI_DIR . 'skills/';
    if (is_dir($skills_dir)) {
        $files = glob($skills_dir . '*.php');
        foreach ($files as $file) {
            include $file;
        }
    }
}

register_activation_hook(__FILE__, ['TezNevise_AI_Database', 'activate']);
register_deactivation_hook(__FILE__, ['TezNevise_AI_Database', 'deactivate']);

function teznevise_ai_shortcode($atts) {
    $atts = shortcode_atts([
        'tool' => '',
        'agent_id' => '',
        'collaboration_mode' => '',
        'thinking_enabled' => '',
    ], $atts);
    
    $tool_id = $atts['tool'] ? $atts['tool'] : 'general';
    $tool = class_exists('TezNevise_AI_Core') ? TezNevise_AI_Core::get_tool($tool_id) : null;
    if (!$tool) {
        $tool = class_exists('TezNevise_AI_Core') ? TezNevise_AI_Core::get_tool('general') : null;
    }
    if (!$tool) return '';
    
    return TezNevise_AI_Chat::render_chat([
        'tool_id' => $tool_id,
        'agent_id' => $atts['agent_id'] ?: ($tool['default_agent'] ?? 'general'),
        'collaboration_mode' => $atts['collaboration_mode'] ?: ($tool['collaboration_mode'] ?? 'single'),
        'thinking_enabled' => $atts['thinking_enabled'] !== 'false' && ($tool['thinking_enabled'] ?? true),
        'tool_config' => $tool,
    ]);
}
add_shortcode('teznevise_ai', 'teznevise_ai_shortcode');
function teznevise_list_ai_agents() {
	if ( ! class_exists( 'TezNevise_AI_Database' ) ) {
		return array();
	}
	$rows = array();
	foreach ( (array) TezNevise_AI_Database::get_all_agents() as $agent ) {
		$agent = (array) $agent;
		$rows[] = (object) array(
			'ID'         => $agent['agent_id'] ?? '',
			'post_title' => $agent['name'] ?? '',
		);
	}
	return $rows;
}

function teznevise_ai_mann_whitney_shortcode($atts) {
    return teznevise_ai_shortcode(['tool' => 'mann-whitney'] + $atts);
}
add_shortcode('teznevise_ai_mann_whitney', 'teznevise_ai_mann_whitney_shortcode');

function teznevise_ai_t_test_shortcode($atts) {
    return teznevise_ai_shortcode(['tool' => 't-test'] + $atts);
}
add_shortcode('teznevise_ai_t_test', 'teznevise_ai_t_test_shortcode');

function teznevise_ai_correlation_shortcode($atts) {
    return teznevise_ai_shortcode(['tool' => 'correlation'] + $atts);
}
add_shortcode('teznevise_ai_correlation', 'teznevise_ai_correlation_shortcode');

function teznevise_ai_regression_shortcode($atts) {
    return teznevise_ai_shortcode(['tool' => 'regression'] + $atts);
}
add_shortcode('teznevise_ai_regression', 'teznevise_ai_regression_shortcode');
