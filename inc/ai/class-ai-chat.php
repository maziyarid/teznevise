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
		if ( is_admin() ) {
			return;
		}
		self::enqueue_assets();
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
				$row = (array) $agent;
				if ( class_exists( 'Teznevise_Agent_Registry' ) ) {
					$row = Teznevise_Agent_Registry::hydrate( $row );
				}
				$agents[] = array(
					'id'               => $row['agent_id'] ?? '',
					'name'             => $row['alias'] ?? $row['name'] ?? '',
					'description'      => $row['description'] ?? '',
					'color'            => $row['color'] ?? '#145d4a',
					'icon'             => $row['icon'] ?? 'brain',
					'role'             => $row['role'] ?? 'general',
					'thinking_enabled' => ! empty( $row['thinking_enabled'] ),
					'avatar'           => $row['avatar'] ?? '',
					'display'          => $row['displayed_model_name'] ?? '',
				);
			}
		}
		$skills_map = array();
		if ( function_exists( 'teznevise_core_agent_skills' ) ) {
			foreach ( teznevise_core_agent_skills() as $aid => $list ) {
				$skills_map[ $aid ] = array();
				foreach ( (array) $list as $skill ) {
					$skills_map[ $aid ][] = array(
						'id'          => $skill['skill_id'],
						'name'        => $skill['name'],
						'description' => $skill['description'],
					);
				}
			}
		}
		if ( class_exists( 'TezNevise_AI_Database' ) ) {
			foreach ( $agents as $row ) {
				$aid = $row['id'] ?? '';
				if ( ! $aid || isset( $skills_map[ $aid ] ) ) {
					continue;
				}
				$db_skills = TezNevise_AI_Database::get_skills( $aid );
				foreach ( (array) $db_skills as $skill ) {
					$skill = (array) $skill;
					$skills_map[ $aid ][] = array(
						'id'          => $skill['skill_id'] ?? '',
						'name'        => $skill['name'] ?? '',
						'description' => $skill['description'] ?? '',
					);
				}
			}
		}
		wp_localize_script(
			'teznevise-ai-chat',
			'tezneviseAiConfig',
			array(
				'rest_url'   => rest_url( 'teznevise-ai/v1/' ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'isLoggedIn' => is_user_logged_in(),
				'loginUrl'   => home_url( '/account/' ),
				'page'       => array(
					'url'   => ( is_singular() ? get_permalink() : home_url( '/' ) ),
					'title' => wp_get_document_title(),
				),
				'settings'   => array(
					'persian_initial_message' => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
					'free_tier_limit'         => (int) get_option( 'teznevise_ai_free_tier_limit', 9999 ),
					'signed_in_limit'         => (int) get_option( 'teznevise_ai_signed_in_limit', 9999 ),
					'cost_per_message'        => 0,
				),
				'agents'     => $agents,
				'skills'     => $skills_map,
				'wpjson'     => rest_url(),
			)
		);
	}

	public static function render_chat( $atts ) {
		$atts = shortcode_atts(
			array(
				'tool_id'            => '',
				'agent_id'           => 'teznevise',
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
		if ( class_exists( 'Teznevise_Agent_Registry' ) ) {
			$agents = array_map( array( 'Teznevise_Agent_Registry', 'hydrate' ), $agents );
		}
		ob_start();
		?>
		<section class="tz-ai-chat tz-gpt" id="<?php echo esc_attr( $instance_id ); ?>" data-tool-id="<?php echo esc_attr( $tool_id ); ?>" data-agent-id="<?php echo esc_attr( $atts['agent_id'] ); ?>" data-collaboration-mode="<?php echo esc_attr( $collab ); ?>" data-thinking="0">
			<header class="tz-gpt__top">
				<?php self::render_agent_dropdown( $atts['agent_id'], $agents ); ?>
				<div class="tz-gpt__top-actions">
					<button type="button" class="tz-gpt__iconbtn" data-ai-new aria-label="<?php esc_attr_e( 'گفتگوی تازه', 'teznevise' ); ?>" title="<?php esc_attr_e( 'گفتگوی تازه', 'teznevise' ); ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
					<button type="button" class="tz-gpt__iconbtn" data-ai-full aria-pressed="false" aria-label="<?php esc_attr_e( 'تمام‌صفحه', 'teznevise' ); ?>" title="<?php esc_attr_e( 'تمام‌صفحه', 'teznevise' ); ?>"><i class="fa-solid fa-expand" aria-hidden="true"></i></button>
				</div>
			</header>
			<?php /* named-agent picker lives in render_agent_dropdown (data-agent-pick). */ ?>
			<div class="tz-gpt-skills" data-ai-skills hidden></div>
			<div class="tz-ai-chat__log tz-gpt__log" data-ai-log role="log" aria-live="polite">
				<article class="tz-ai-msg is-assistant">
					<span class="tz-ai-msg__avatar" aria-hidden="true"><?php echo esc_html( mb_substr( $agent_name, 0, 1 ) ); ?></span>
					<div class="tz-ai-msg__stack">
						<header class="tz-ai-msg__meta"><strong><?php echo esc_html( $agent_name ); ?></strong></header>
						<div class="tz-ai-msg__bubble"><?php echo esc_html( $initial ); ?></div>
					</div>
				</article>
			</div>
			<form class="tz-gpt-composer" data-ai-form>
				<div class="tz-gpt-box">
					<label class="screen-reader-text" for="<?php echo esc_attr( $instance_id ); ?>-q"><?php esc_html_e( 'پیام', 'teznevise' ); ?></label>
					<textarea id="<?php echo esc_attr( $instance_id ); ?>-q" data-ai-input rows="1" required minlength="4" placeholder="<?php esc_attr_e( 'پیام به تزنویسه…', 'teznevise' ); ?>"></textarea>
					<div class="tz-gpt-bar">
						<div class="tz-gpt-toggles" role="toolbar" aria-label="<?php esc_attr_e( 'ابزار گفتگو', 'teznevise' ); ?>">
							<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-thinking-btn aria-pressed="false" aria-label="<?php esc_attr_e( 'نمایش استدلال', 'teznevise' ); ?>" title="<?php esc_attr_e( 'استدلال', 'teznevise' ); ?>"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></button>
							<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-collab-btn aria-pressed="<?php echo 'collaborative' === $collab ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( 'هم‌فکری عامل‌ها', 'teznevise' ); ?>" title="<?php esc_attr_e( 'هم‌فکری', 'teznevise' ); ?>"><i class="fa-solid fa-users" aria-hidden="true"></i></button>
							<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-research-btn aria-pressed="<?php echo 'research' === $collab ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( 'پژوهش وب', 'teznevise' ); ?>" title="<?php esc_attr_e( 'پژوهش', 'teznevise' ); ?>"><i class="fa-solid fa-globe" aria-hidden="true"></i></button>
						</div>
						<input type="checkbox" data-ai-thinking hidden>
						<input type="checkbox" data-ai-collab-toggle <?php checked( $collab, 'collaborative' ); ?> hidden>
						<input type="checkbox" data-ai-research <?php checked( $collab, 'research' ); ?> hidden>
						<button type="button" class="tz-gpt-stop" data-ai-stop hidden aria-label="<?php esc_attr_e( 'توقف', 'teznevise' ); ?>" title="<?php esc_attr_e( 'توقف', 'teznevise' ); ?>">
							<i class="fa-solid fa-stop" aria-hidden="true"></i>
						</button>
						<button class="tz-gpt-send" type="submit" aria-label="<?php esc_attr_e( 'ارسال', 'teznevise' ); ?>" title="<?php esc_attr_e( 'ارسال', 'teznevise' ); ?>">
							<i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
						</button>
					</div>
				</div>
				<p class="tz-ai-chat__status" data-ai-status hidden></p>
				<p class="tz-gpt-hint"><?php esc_html_e( 'Enter برای ارسال · Shift+Enter خط جدید · هویت عامل قفل است.', 'teznevise' ); ?></p>
			</form>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	public static function render_generic_chat( $atts ) {
		$atts = shortcode_atts(
			array(
				'agent_id'           => 'teznevise',
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
					'default_agent_name'  => 'تزنویسه',
					'initial_message'     => 'اگه نتونستی با ابزار کار کنی میتونی از من کمک بگیری',
					'free_tier_limit'     => 9999,
					'signed_in_limit'     => 9999,
					'cost_per_message'    => 0,
					'recommended_agents'  => array( 'general' ),
				),
			)
		);
	}

	/**
	 * ChatGPT-style agent dropdown. Options keep data-agent-pick for CI.
	 *
	 * @param string           $current_id Current agent id.
	 * @param array<int,array> $agents     Agent rows.
	 */
	public static function render_agent_dropdown( $current_id, $agents ) {
		$named_ids = function_exists( 'teznevise_core_named_ids' ) ? teznevise_core_named_ids() : array();
		$picker    = array();
		foreach ( (array) $agents as $ag ) {
			$ag = (array) $ag;
			$id = $ag['agent_id'] ?? '';
			if ( $id && ( ! $named_ids || in_array( $id, $named_ids, true ) ) ) {
				$picker[] = $ag;
			}
		}
		if ( ! $picker ) {
			foreach ( (array) $agents as $ag ) {
				$picker[] = (array) $ag;
			}
		}
		$current = null;
		foreach ( $picker as $ag ) {
			if ( ( $ag['agent_id'] ?? '' ) === $current_id ) {
				$current = $ag;
				break;
			}
		}
		if ( ! $current && $picker ) {
			$current = $picker[0];
		}
		$label = $current['fa_name'] ?? $current['alias'] ?? $current['name'] ?? __( 'انتخاب عامل', 'teznevise' );
		?>
		<div class="tz-gpt-model" data-agent-menu>
			<button type="button" class="tz-gpt-model__btn" data-agent-menu-toggle aria-haspopup="listbox" aria-expanded="false" aria-label="<?php esc_attr_e( 'انتخاب عامل', 'teznevise' ); ?>">
				<?php if ( ! empty( $current['avatar'] ) ) : ?>
					<img src="<?php echo esc_url( $current['avatar'] ); ?>" width="22" height="22" alt="" />
				<?php else : ?>
					<i class="fa-solid fa-brain" aria-hidden="true"></i>
				<?php endif; ?>
				<span data-agent-label><?php echo esc_html( $label ); ?></span>
				<i class="fa-solid fa-chevron-down tz-gpt-model__caret" aria-hidden="true"></i>
			</button>
			<div class="tz-gpt-model__list" role="listbox" hidden aria-label="<?php esc_attr_e( 'عامل‌های پژوهشی', 'teznevise' ); ?>">
				<div class="tz-gpt-model__list-head">
					<strong><?php esc_html_e( 'انتخاب عامل', 'teznevise' ); ?></strong>
					<button type="button" class="tz-gpt__iconbtn tz-gpt-model__done" data-agent-menu-done aria-label="<?php esc_attr_e( 'بازگشت به گفتگو', 'teznevise' ); ?>" title="<?php esc_attr_e( 'بازگشت به گفتگو', 'teznevise' ); ?>">
						<i class="fa-solid fa-xmark" aria-hidden="true"></i>
					</button>
				</div>
				<?php foreach ( $picker as $ag ) : ?>
					<?php
					$id   = $ag['agent_id'] ?? '';
					$on   = ( $id === $current_id ) || ( 'general' === $current_id && 'teznevise' === $id );
					$name = $ag['fa_name'] ?? $ag['alias'] ?? $ag['name'] ?? $id;
					$desc = $ag['description'] ?? ( $ag['displayed_model_name'] ?? '' );
					?>
					<button type="button" class="tz-gpt-agent<?php echo $on ? ' is-on' : ''; ?>" role="option" data-agent-pick="<?php echo esc_attr( $id ); ?>" aria-selected="<?php echo $on ? 'true' : 'false'; ?>" title="<?php echo esc_attr( $ag['displayed_model_name'] ?? $name ); ?>">
						<?php if ( ! empty( $ag['avatar'] ) ) : ?>
							<img src="<?php echo esc_url( $ag['avatar'] ); ?>" width="28" height="28" alt="<?php echo esc_attr( $ag['alt'] ?? $name ); ?>" />
						<?php else : ?>
							<span class="tz-gpt-agent__dot" style="background:<?php echo esc_attr( $ag['color'] ?? '#145d4a' ); ?>"></span>
						<?php endif; ?>
						<span class="tz-gpt-agent__copy">
							<strong><?php echo esc_html( $name ); ?></strong>
							<?php if ( $desc ) : ?><small><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $desc ), 10 ) ); ?></small><?php endif; ?>
						</span>
					</button>
				<?php endforeach; ?>
			</div>
			<select data-ai-agent class="tz-gpt-model__native" aria-hidden="true" tabindex="-1">
				<?php foreach ( $picker as $ag ) : ?>
					<option value="<?php echo esc_attr( $ag['agent_id'] ?? '' ); ?>" <?php selected( $current_id, $ag['agent_id'] ?? '' ); ?>><?php echo esc_html( $ag['alias'] ?? $ag['name'] ?? '' ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}
}
