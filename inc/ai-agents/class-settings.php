<?php
/**
 * Settings for TezNevise AI
 */

class TezNevise_AI_Settings {
    
    public static function init() {
        // Add admin menu
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        
        // Register settings
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }
    
    public static function add_admin_menu() {
        add_menu_page(
            'TezNevise AI',
            'TezNevise AI',
            'manage_options',
            'teznevise-ai-settings',
            [__CLASS__, 'render_settings_page'],
            'dashicons-admin-generic',
            80
        );
        
        add_submenu_page(
            'teznevise-ai-settings',
            __('Settings', 'teznevise'),
            __('Settings', 'teznevise'),
            'manage_options',
            'teznevise-ai-settings',
            [__CLASS__, 'render_settings_page']
        );
        
        add_submenu_page(
            'teznevise-ai-settings',
            __('Agents', 'teznevise'),
            __('Agents', 'teznevise'),
            'manage_options',
            'teznevise-ai-agents',
            [__CLASS__, 'render_agents_page']
        );
    }
    
    public static function register_settings() {
        register_setting('teznevise_ai_settings', 'teznevise_ai_free_tier_limit', [
            'type' => 'integer',
            'default' => 10,
            'sanitize_callback' => 'absint',
        ]);
        
        register_setting('teznevise_ai_settings', 'teznevise_ai_signed_in_limit', [
            'type' => 'integer',
            'default' => 100,
            'sanitize_callback' => 'absint',
        ]);
        
        register_setting('teznevise_ai_settings', 'teznevise_ai_cost_per_message', [
            'type' => 'number',
            'default' => 0.01,
            'sanitize_callback' => 'floatval',
        ]);
        
        register_setting('teznevise_ai_settings', 'teznevise_ai_default_agent', [
            'type' => 'string',
            'default' => 'general',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        
        register_setting('teznevise_ai_settings', 'teznevise_ai_collaboration_mode', [
            'type' => 'string',
            'default' => 'single',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        
        register_setting('teznevise_ai_settings', 'teznevise_ai_thinking_enabled', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => function($value) { return isset($value) && $value === '1'; },
        ]);
        
        register_setting('teznevise_ai_settings', 'teznevise_ai_persian_message', [
            'type' => 'string',
            'default' => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
            'sanitize_callback' => 'wp_kses_post',
        ]);
    }
    
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('TezNevise AI Settings', 'teznevise'); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('teznevise_ai_settings'); ?>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="teznevise_ai_free_tier_limit"><?php _e('Free Tier Limit', 'teznevise'); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="number" 
                                    id="teznevise_ai_free_tier_limit" 
                                    name="teznevise_ai_free_tier_limit" 
                                    value="<?php echo esc_attr(get_option('teznevise_ai_free_tier_limit', 10)); ?>"
                                    class="regular-text"
                                    min="0"
                                />
                                <p class="description"><?php _e('Number of free messages for guest users per day', 'teznevise'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="teznevise_ai_signed_in_limit"><?php _e('Signed-in Limit', 'teznevise'); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="number" 
                                    id="teznevise_ai_signed_in_limit" 
                                    name="teznevise_ai_signed_in_limit" 
                                    value="<?php echo esc_attr(get_option('teznevise_ai_signed_in_limit', 100)); ?>"
                                    class="regular-text"
                                    min="0"
                                />
                                <p class="description"><?php _e('Number of messages per day for logged-in users', 'teznevise'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="teznevise_ai_cost_per_message"><?php _e('Cost per Message', 'teznevise'); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    id="teznevise_ai_cost_per_message" 
                                    name="teznevise_ai_cost_per_message" 
                                    value="<?php echo esc_attr(get_option('teznevise_ai_cost_per_message', 0.01)); ?>"
                                    class="regular-text"
                                    min="0"
                                />
                                <p class="description"><?php _e('Cost in credits per message', 'teznevise'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="teznevise_ai_default_agent"><?php _e('Default Agent', 'teznevise'); ?></label>
                            </th>
                            <td>
                                <select id="teznevise_ai_default_agent" name="teznevise_ai_default_agent" class="regular-text">
                                    <?php
                                    $default_agent = get_option('teznevise_ai_default_agent', 'general');
                                    $all_agents = TezNevise_AI_Database::get_all_agents();
                                    foreach ($all_agents as $agent) :
                                    ?>
                                    <option value="<?php echo esc_attr($agent->agent_id); ?>" <?php selected($default_agent, $agent->agent_id); ?>>
                                        <?php echo esc_html($agent->name); ?> (<?php echo esc_html($agent->model); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php _e('Default AI agent for new chats', 'teznevise'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="teznevise_ai_collaboration_mode"><?php _e('Collaboration Mode', 'teznevise'); ?></label>
                            </th>
                            <td>
                                <select id="teznevise_ai_collaboration_mode" name="teznevise_ai_collaboration_mode" class="regular-text">
                                    <option value="single" <?php selected(get_option('teznevise_ai_collaboration_mode', 'single'), 'single'); ?>><?php _e('Single Agent', 'teznevise'); ?></option>
                                    <option value="collaborative" <?php selected(get_option('teznevise_ai_collaboration_mode', 'single'), 'collaborative'); ?>><?php _e('Collaborative', 'teznevise'); ?></option>
                                    <option value="separate" <?php selected(get_option('teznevise_ai_collaboration_mode', 'single'), 'separate'); ?>><?php _e('Separate with Reflections', 'teznevise'); ?></option>
                                </select>
                                <p class="description"><?php _e('How multiple agents should work together', 'teznevise'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="teznevise_ai_thinking_enabled"><?php _e('Thinking Process Enabled', 'teznevise'); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="checkbox" 
                                    id="teznevise_ai_thinking_enabled" 
                                    name="teznevise_ai_thinking_enabled" 
                                    value="1" 
                                    <?php checked(get_option('teznevise_ai_thinking_enabled', true), true); ?>
                                />
                                <label for="teznevise_ai_thinking_enabled"><?php _e('Show AI thinking process before responding', 'teznevise'); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="teznevise_ai_persian_message"><?php _e('Persian Initial Message', 'teznevise'); ?></label>
                            </th>
                            <td>
                                <textarea 
                                    id="teznevise_ai_persian_message" 
                                    name="teznevise_ai_persian_message" 
                                    class="large-text"
                                    rows="3"
                                    style="width: 100%; direction: rtl; text-align: right;"
                                ><?php echo esc_textarea(get_option('teznevise_ai_persian_message', 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری')); ?></textarea>
                                <p class="description"><?php _e('The first message shown to users in Persian', 'teznevise'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    public static function render_agents_page() {
        global $wpdb;
        
        $table = $wpdb->prefix . 'teznevise_ai_agents';
        
        // Handle form submission
        if (isset($_POST['teznevise_ai_save_agent']) && check_admin_referer('teznevise_ai_save_agent_action')) {
            $agent_id = sanitize_text_field($_POST['agent_id']);
            $name = sanitize_text_field($_POST['name']);
            $description = sanitize_textarea_field($_POST['description']);
            $api_endpoint = esc_url_raw($_POST['api_endpoint']);
            $api_key = sanitize_text_field($_POST['api_key']);
            $model = sanitize_text_field($_POST['model']);
            $color = sanitize_text_field($_POST['color']);
            $icon = sanitize_text_field($_POST['icon']);
            $thinking_enabled = isset($_POST['thinking_enabled']) ? 1 : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $sort_order = intval($_POST['sort_order']);
            
            $data = [
                'agent_id' => $agent_id,
                'name' => $name,
                'description' => $description,
                'api_endpoint' => $api_endpoint,
                'api_key' => $api_key,
                'model' => $model,
                'color' => $color,
                'icon' => $icon,
                'thinking_enabled' => $thinking_enabled,
                'is_active' => $is_active,
                'sort_order' => $sort_order,
            ];
            
            if (!empty($_POST['id'])) {
                $wpdb->update($table, $data, ['id' => intval($_POST['id'])]);
                echo '<div class="notice notice-success"><p>' . __('Agent updated!', 'teznevise') . '</p></div>';
            } else {
                $wpdb->insert($table, $data);
                echo '<div class="notice notice-success"><p>' . __('Agent added!', 'teznevise') . '</p></div>';
            }
        }
        
        // Handle delete
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && check_admin_referer('teznevise_ai_delete_agent')) {
            $wpdb->delete($table, ['id' => intval($_GET['id'])]);
            echo '<div class="notice notice-success"><p>' . __('Agent deleted!', 'teznevise') . '</p></div>';
        }
        
        // Handle edit
        $editing = null;
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $editing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", intval($_GET['id'])));
        }
        
        $agents = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC");
        
        ?>
        <div class="wrap">
            <h1><?php _e('TezNevise AI Agents', 'teznevise'); ?></h1>
            
            <h2><?php _e('Add/Edit Agent', 'teznevise'); ?></h2>
            <form method="post">
                <?php wp_nonce_field('teznevise_ai_save_agent_action'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th><label for="agent_id"><?php _e('Agent ID', 'teznevise'); ?></label></th>
                            <td><input type="text" id="agent_id" name="agent_id" class="regular-text" value="<?php echo $editing ? esc_attr($editing->agent_id) : ''; ?>" required /></td>
                        </tr>
                        <tr>
                            <th><label for="name"><?php _e('Name', 'teznevise'); ?></label></th>
                            <td><input type="text" id="name" name="name" class="regular-text" value="<?php echo $editing ? esc_attr($editing->name) : ''; ?>" required /></td>
                        </tr>
                        <tr>
                            <th><label for="description"><?php _e('Description', 'teznevise'); ?></label></th>
                            <td><textarea id="description" name="description" class="large-text" rows="3"><?php echo $editing ? esc_textarea($editing->description) : ''; ?></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="api_endpoint"><?php _e('API Endpoint', 'teznevise'); ?></label></th>
                            <td><input type="url" id="api_endpoint" name="api_endpoint" class="regular-text" value="<?php echo $editing ? esc_attr($editing->api_endpoint) : 'https://api.openai.com/v1/chat/completions'; ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="api_key"><?php _e('API Key', 'teznevise'); ?></label></th>
                            <td><input type="password" id="api_key" name="api_key" class="regular-text" value="<?php echo $editing ? esc_attr($editing->api_key) : ''; ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="model"><?php _e('Model', 'teznevise'); ?></label></th>
                            <td><input type="text" id="model" name="model" class="regular-text" value="<?php echo $editing ? esc_attr($editing->model) : 'gpt-4'; ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="color"><?php _e('Color', 'teznevise'); ?></label></th>
                            <td><input type="color" id="color" name="color" value="<?php echo $editing ? esc_attr($editing->color) : '#3b82f6'; ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="icon"><?php _e('Icon', 'teznevise'); ?></label></th>
                            <td><input type="text" id="icon" name="icon" class="regular-text" placeholder="brain" value="<?php echo $editing ? esc_attr($editing->icon) : ''; ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="thinking_enabled"><?php _e('Thinking Enabled', 'teznevise'); ?></label></th>
                            <td><input type="checkbox" id="thinking_enabled" name="thinking_enabled" value="1" <?php echo $editing ? checked($editing->thinking_enabled, 1, false) : 'checked'; ?> /></td>
                        </tr>
                        <tr>
                            <th><label for="is_active"><?php _e('Active', 'teznevise'); ?></label></th>
                            <td><input type="checkbox" id="is_active" name="is_active" value="1" <?php echo $editing ? checked($editing->is_active, 1, false) : 'checked'; ?> /></td>
                        </tr>
                        <tr>
                            <th><label for="sort_order"><?php _e('Sort Order', 'teznevise'); ?></label></th>
                            <td><input type="number" id="sort_order" name="sort_order" class="regular-text" value="<?php echo $editing ? esc_attr($editing->sort_order) : '0'; ?>" /></td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="id" value="<?php echo $editing ? esc_attr($editing->id) : ''; ?>" />
                <?php submit_button($editing ? __('Update Agent', 'teznevise') : __('Add Agent', 'teznevise'), 'primary', 'teznevise_ai_save_agent'); ?>
            </form>
            
            <h2><?php _e('Existing Agents', 'teznevise'); ?></h2>
            <?php if (empty($agents)) : ?>
                <p><?php _e('No agents found. Add your first agent above.', 'teznevise'); ?></p>
            <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('ID', 'teznevise'); ?></th>
                        <th><?php _e('Name', 'teznevise'); ?></th>
                        <th><?php _e('Agent ID', 'teznevise'); ?></th>
                        <th><?php _e('Model', 'teznevise'); ?></th>
                        <th><?php _e('Active', 'teznevise'); ?></th>
                        <th><?php _e('Actions', 'teznevise'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agents as $agent) : ?>
                    <tr>
                        <td><?php echo esc_html($agent->id); ?></td>
                        <td><?php echo esc_html($agent->name); ?></td>
                        <td><?php echo esc_html($agent->agent_id); ?></td>
                        <td><?php echo esc_html($agent->model); ?></td>
                        <td><?php echo $agent->is_active ? __('Yes', 'teznevise') : __('No', 'teznevise'); ?></td>
                        <td>
                            <a href="?page=teznevise-ai-agents&action=edit&id=<?php echo esc_attr($agent->id); ?>" class="button button-small"><?php _e('Edit', 'teznevise'); ?></a>
                            <a href="?page=teznevise-ai-agents&action=delete&id=<?php echo esc_attr($agent->id); ?>&_wpnonce=<?php echo wp_create_nonce('teznevise_ai_delete_agent'); ?>" class="button button-small button-danger" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete this agent?', 'teznevise')); ?>');"><?php _e('Delete', 'teznevise'); ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php
    }
}
