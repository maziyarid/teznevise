<?php
/**
 * TezNevise AI Core - Main AI functionality
 */

if (!defined('ABSPATH')) exit;

class TezNevise_AI_Core {
    
    private static $tools = [];
    private static $initialized = false;
    
    public static function init() {
        if (self::$initialized) return;
        
        // Load all tool configurations
        self::load_tools();
        
        // Initialize database
        TezNevise_AI_Database::init();
        
        // Initialize API
        TezNevise_AI_API::init();
        
        // Initialize chat
        TezNevise_AI_Chat::init();
        
        // Initialize settings
        TezNevise_AI_Settings::init();
        
        self::$initialized = true;
    }
    
    private static function load_tools() {
        $tools_dir = get_template_directory() . '/inc/ai/tools/';
        
        if (is_dir($tools_dir)) {
            $files = glob($tools_dir . '*.php');
            foreach ($files as $file) {
                $tool_name = basename($file, '.php');
                self::$tools[$tool_name] = include $file;
            }
        }
        
        // Register shortcodes for all tools
        foreach (self::$tools as $tool_name => $tool_config) {
            add_shortcode("teznevise_ai_{$tool_name}", function($atts) use ($tool_name) {
                return self::render_tool_chat($tool_name, $atts);
            });
        }
    }
    
    public static function get_tool($tool_name) {
        return self::$tools[$tool_name] ?? null;
    }
    
    public static function get_all_tools() {
        return self::$tools;
    }
    
    public static function render_tool_chat($tool_name, $atts) {
        $tool = self::get_tool($tool_name);
        if (!$tool) return '';
        
        $atts = shortcode_atts([
            'agent_id' => $tool['default_agent'],
            'collaboration_mode' => $tool['collaboration_mode'] ?? 'single',
            'thinking_enabled' => $tool['thinking_enabled'] ?? true,
        ], $atts);
        
        return TezNevise_AI_Chat::render_chat(array_merge($atts, [
            'tool_id' => $tool_name,
            'tool_config' => $tool,
        ]));
    }
    
    public static function register_tool($tool_name, $config) {
        self::$tools[$tool_name] = $config;
    }
}