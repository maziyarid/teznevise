<?php
/**
 * TezNevise AI Chat — ChatGPT-style composer.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TezNevise_AI_Chat {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ), 40 );
		add_shortcode( 'teznevise_ai_chat', array( __CLASS__, 'render_generic_chat' ) );
	}

	public static function maybe_enqueue() {
		if ( is_page_template( 'page-tool.php' ) || is_page_template( 'page-tools.php' ) ) {
			self::enqueue_assets();
		}
	}

	public static function enqueue_assets() {
		if ( wp_script_is( 'teznevise-ai-chat', 'enqueued' ) ) {
			return;
		}
		$js  = get_template_directory() . '/js/ai/chat.js';
		$css = get_template_directory() . '/css/teznevise-ai.css';
		if ( is_readable( $js ) ) {
			wp_enqueue_script(
				'teznevise-ai-chat',
				get_template_directory_uri() . '/js/ai/chat.js',
				array(),
				(string) filemtime( $js ),
				true
			);
			wp_script_add_data( 'teznevise-ai-chat', 'strategy', 'defer' );
		}
		if ( is_readable( $css ) ) {
			wp_enqueue_style(
				'teznevise-ai',
				get_template_directory_uri() . '/css/teznevise-ai.css',
				array(),
				(string) filemtime( $css )
			);
		}
		$agents = array();
		if ( class_exists( 'TezNevise_AI_Database' ) ) {
			foreach ( (array) TezNevise_AI_Database::get_all_agents() as $agent ) {
				$row      = (array) $agent;
				$agents[] = array(
					'id'               => $row['agent_id'] ?? '',
					'name'             => $row['name'] ?? '',
					'description'      => $row['description'] ?? '',
					'model'            => $row['model'] ?? '',
					'provider'         => $row['provider'] ?? '',
					'color'            => $row['color'] ?? '#145d4a',
					'icon'             => $row['icon'] ?? 'brain',
					'role'             => $row['role'] ?? 'general',
					'thinking_enabled' => ! empty( $row['thinking_enabled'] ),
				);
			}
		}
		wp_localize_script(
			'teznevise-ai-chat',
			'tezneviseAiConfig',
			array(
				'rest_url'   => rest_url( 'teznevise-ai/v1/' ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'isLoggedIn' => is_user_logged_in(),
				'loginUrl'   => wp_login_url( get_permalink() ),
				'settings'   => array(
					'persian_initial_message' => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
					'free_tier_limit'         => (int) get_option( 'teznevise_ai_free_tier_limit', 10 ),
					'signed_in_limit'         => (int) get_option( 'teznevise_ai_signed_in_limit', 100 ),
					'cost_per_message'        => (float) get_option( 'teznevise_ai_cost_per_message', 0 ),
				),
				'agents'     => $agents,
			)
		);
	}

	public static function render_chat( $atts ) {
		$atts = shortcode_atts(
			array(
				'tool_id'            => '',
				'agent_id'           => 'general',
				'collaboration_mode' => 'single',
				'thinking_enabled'   => true,
				'tool_config'        => array(),
			),
			$atts
		);

		$tool_id     = $atts['tool_id'];
		$tool_config = $atts['tool_config'];
		if ( empty( $tool_config ) ) {
			$tool_config = TezNevise_AI_Core::get_tool( $tool_id ) ?: array();
		}

		self::enqueue_assets();
		$instance_id = 'teznevise-ai-chat-' . sanitize_html_class( $tool_id ) . '-' . wp_generate_password( 4, false );
		$initial     = $tool_config['initial_message'] ?? 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری';
		$agent_name  = $tool_config['default_agent_name'] ?? 'دستیار پژوهشی';
		$collab      = $atts['collaboration_mode'];
		$thinking    = ! empty( $atts['thinking_enabled'] );
		$agents      = class_exists( 'TezNevise_AI_Database' ) ? (array) TezNevise_AI_Database::get_all_agents() : array();
		ob_start();
		?>
		<section class="tz-ai-chat tz-gpt" id="<?php echo esc_attr( $instance_id ); ?>" data-tool-id="<?php echo esc_attr( $tool_id ); ?>" data-agent-id="<?php echo esc_attr( $atts['agent_id'] ); ?>" data-collaboration-mode="<?php echo esc_attr( $collab ); ?>" data-thinking="<?php echo $thinking ? '1' : '0'; ?>">
			<header class="tz-gpt__top">
				<div class="tz-gpt__brand">
					<span class="tz-gpt__dot" aria-hidden="true"></span>
					<strong><?php echo esc_html( $tool_config['name'] ?? __( 'گفتگو', 'teznevise' ) ); ?></strong>
				</div>
				<div class="tz-gpt__top-actions">
					<button type="button" class="tz-gpt__iconbtn" data-ai-new><?php esc_html_e( 'گفتگوی تازه', 'teznevise' ); ?></button>
					<button type="button" class="tz-gpt__iconbtn" data-ai-full aria-pressed="false"><?php esc_html_e( 'تمام‌صفحه', 'teznevise' ); ?></button>
				</div>
			</header>
			<div class="tz-ai-chat__log tz-gpt__log" data-ai-log>
				<article class="tz-ai-msg is-assistant">
					<header class="tz-ai-msg__meta"><strong><?php echo esc_html( $agent_name ); ?></strong></header>
					<div class="tz-ai-msg__bubble"><?php echo esc_html( $initial ); ?></div>
				</article>
			</div>
			<form class="tz-gpt-composer" data-ai-form>
				<div class="tz-gpt-box">
					<label class="screen-reader-text" for="<?php echo esc_attr( $instance_id ); ?>-q"><?php esc_html_e( 'پیام', 'teznevise' ); ?></label>
					<textarea id="<?php echo esc_attr( $instance_id ); ?>-q" data-ai-input rows="1" required minlength="4" placeholder="<?php esc_attr_e( 'پیام به تزنویسه…', 'teznevise' ); ?>"></textarea>
					<div class="tz-gpt-bar">
						<details class="tz-gpt-tools">
							<summary><?php esc_html_e( 'ابزارها', 'teznevise' ); ?></summary>
							<div class="tz-gpt-tools__panel">
								<label><?php esc_html_e( 'عامل / مدل', 'teznevise' ); ?>
									<select data-ai-agent>
										<?php foreach ( $agents as $ag ) : $ag = (array) $ag; ?>
											<option value="<?php echo esc_attr( $ag['agent_id'] ?? '' ); ?>" <?php selected( $atts['agent_id'], $ag['agent_id'] ?? '' ); ?>><?php echo esc_html( ( $ag['name'] ?? '' ) . ' · ' . ( $ag['model'] ?? '' ) ); ?></option>
										<?php endforeach; ?>
									</select>
								</label>
								<label><?php esc_html_e( 'همکاری', 'teznevise' ); ?>
									<select data-ai-collab>
										<option value="single" <?php selected( $collab, 'single' ); ?>><?php esc_html_e( 'یک عامل', 'teznevise' ); ?></option>
										<option value="collaborative" <?php selected( $collab, 'collaborative' ); ?>><?php esc_html_e( 'همکاری زنجیره‌ای', 'teznevise' ); ?></option>
										<option value="separate" <?php selected( $collab, 'separate' ); ?>><?php esc_html_e( 'جدا + بازتاب', 'teznevise' ); ?></option>
										<option value="research" <?php selected( $collab, 'research' ); ?>><?php esc_html_e( 'You تحقیق می‌کند، بقیه تحلیل', 'teznevise' ); ?></option>
									</select>
								</label>
								<label class="tz-ai-chat__check"><input type="checkbox" data-ai-thinking <?php checked( $thinking ); ?>> <?php esc_html_e( 'فرآیند فکر', 'teznevise' ); ?></label>
							</div>
						</details>
						<button class="tz-gpt-send" type="submit" aria-label="<?php esc_attr_e( 'ارسال', 'teznevise' ); ?>">
							<span><?php esc_html_e( 'ارسال', 'teznevise' ); ?></span>
						</button>
					</div>
				</div>
				<p class="tz-ai-chat__status" data-ai-status hidden></p>
			</form>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	public static function render_generic_chat( $atts ) {
		$atts = shortcode_atts(
			array(
				'agent_id'           => 'general',
				'collaboration_mode' => 'single',
				'thinking_enabled'   => true,
			),
			$atts
		);
		return self::render_chat(
			array(
				'tool_id'            => 'generic',
				'agent_id'           => $atts['agent_id'],
				'collaboration_mode' => $atts['collaboration_mode'],
				'thinking_enabled'   => $atts['thinking_enabled'],
				'tool_config'        => array(
					'id'                  => 'generic',
					'name'                => 'تزنویسه',
					'default_agent'       => $atts['agent_id'],
					'default_agent_name'  => 'دستیار پژوهشی',
					'initial_message'     => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
					'free_tier_limit'     => 10,
					'signed_in_limit'     => 100,
					'cost_per_message'    => 0.01,
					'recommended_agents'  => array( 'general' ),
				),
			)
		);
	}
}
