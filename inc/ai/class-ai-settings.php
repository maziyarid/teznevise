<?php
/**
 * TezNevise AI Settings — provider keys, named agents, limits.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TezNevise_AI_Settings {
	const OPTION_PREFIX = 'teznevise_ai_';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_agent_post' ) );
	}

	public static function add_admin_menu() {
		add_submenu_page(
			'options-general.php',
			'TezNevise AI',
			'TezNevise AI',
			'manage_options',
			'teznevise-ai-settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	public static function register_settings() {
		$keys = array( 'openai_key', 'gemini_key', 'openrouter_key', 'groq_key', 'xai_key', 'anthropic_key', 'mistral_key', 'together_key', 'deepseek_key', 'you_key', 'tavily_key', 'genspark_key', 'genspark_endpoint', 'genspark_model', 'perplexity_key', 'perplexity_model', 'notify_emails', 'default_agent', 'free_tier_limit', 'signed_in_limit', 'cost_per_message' );
		foreach ( $keys as $key ) {
			register_setting( 'teznevise_ai_settings', self::OPTION_PREFIX . $key );
		}
	}

	public static function handle_agent_post() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( empty( $_POST['teznevise_ai_agent_action'] ) ) {
			return;
		}
		if ( ! isset( $_POST['_tz_ai_agent'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_ai_agent'] ) ), 'teznevise_ai_agent' ) ) {
			return;
		}
		$action = sanitize_key( wp_unslash( $_POST['teznevise_ai_agent_action'] ) );
		if ( 'save' === $action && class_exists( 'TezNevise_AI_Database' ) ) {
			TezNevise_AI_Database::save_agent(
				array(
					'agent_id'          => isset( $_POST['agent_id'] ) ? sanitize_key( wp_unslash( $_POST['agent_id'] ) ) : '',
					'name'              => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
					'description'       => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
					'provider'          => isset( $_POST['provider'] ) ? sanitize_key( wp_unslash( $_POST['provider'] ) ) : 'openai',
					'api_endpoint'      => isset( $_POST['api_endpoint'] ) ? esc_url_raw( wp_unslash( $_POST['api_endpoint'] ) ) : '',
					'api_key'           => ( isset( $_POST['api_key'] ) && '' !== trim( (string) wp_unslash( $_POST['api_key'] ) ) )
						? ( class_exists( 'Teznevise_Key_Vault' ) ? Teznevise_Key_Vault::encrypt( sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) ) : sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) )
						: '',
					'model'             => isset( $_POST['model'] ) ? sanitize_text_field( wp_unslash( $_POST['model'] ) ) : '',
					'color'             => isset( $_POST['color'] ) ? sanitize_hex_color( wp_unslash( $_POST['color'] ) ) : '#145d4a',
					'icon'              => isset( $_POST['icon'] ) ? sanitize_text_field( wp_unslash( $_POST['icon'] ) ) : 'brain',
					'thinking_enabled'  => ! empty( $_POST['thinking_enabled'] ),
					'is_active'         => ! empty( $_POST['is_active'] ),
					'sort_order'        => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
					'system_prompt'     => isset( $_POST['system_prompt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['system_prompt'] ) ) : '',
					'role'              => isset( $_POST['role'] ) ? sanitize_key( wp_unslash( $_POST['role'] ) ) : 'general',
					'language'          => isset( $_POST['language'] ) ? sanitize_text_field( wp_unslash( $_POST['language'] ) ) : 'fa',
					'temperature'       => isset( $_POST['temperature'] ) ? (float) $_POST['temperature'] : 0.7,
					'max_tokens'        => isset( $_POST['max_tokens'] ) ? (int) $_POST['max_tokens'] : 1500,
				)
			);
			if ( class_exists( 'Teznevise_Agent_Registry' ) && ! empty( $_POST['agent_id'] ) ) {
				Teznevise_Agent_Registry::save_profile(
					sanitize_key( wp_unslash( $_POST['agent_id'] ) ),
					array(
						'alias'                => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
						'displayed_model_name' => sanitize_text_field( wp_unslash( $_POST['displayed_model_name'] ?? '' ) ),
					)
				);
			}
		}
		if ( 'delete' === $action && ! empty( $_POST['agent_id'] ) && class_exists( 'TezNevise_AI_Database' ) ) {
			$del_id = sanitize_key( wp_unslash( $_POST['agent_id'] ) );
			$protected = function_exists( 'teznevise_core_named_ids' ) ? teznevise_core_named_ids() : array();
			$protected = array_merge( $protected, array( 'you', 'general', 'math', 'stats' ) );
			if ( ! in_array( $del_id, $protected, true ) ) {
				TezNevise_AI_Database::delete_agent( $del_id );
			}
		}
		$redirect = admin_url( 'admin.php?page=teznevise-ai-settings' );
		if ( isset( $_POST['_wp_http_referer'] ) && false !== strpos( (string) wp_unslash( $_POST['_wp_http_referer'] ), 'options-general.php' ) ) {
			$redirect = add_query_arg( array( 'page' => 'teznevise-ai-settings', 'updated' => '1' ), admin_url( 'options-general.php' ) );
		} else {
			$redirect = add_query_arg( array( 'page' => 'teznevise-ai-settings', 'updated' => '1' ), admin_url( 'admin.php' ) );
		}
		wp_safe_redirect( $redirect );
		exit;
	}

	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$providers = class_exists( 'TezNevise_AI_API' ) ? TezNevise_AI_API::providers() : array();
		$agents    = class_exists( 'TezNevise_AI_Database' ) ? TezNevise_AI_Database::get_all_agents_admin() : array();
		if ( class_exists( 'Teznevise_Agent_Registry' ) ) {
			$agents = array_map( array( 'Teznevise_Agent_Registry', 'hydrate' ), (array) $agents );
		}
		$log = class_exists( 'Teznevise_Logger' ) ? Teznevise_Logger::all() : array();
		$ok  = 0;
		foreach ( $providers as $row ) {
			if ( '' !== (string) get_option( $row['option'], '' ) ) {
				++$ok;
			}
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'تنظیمات هوش مصنوعی تزنویسه', 'teznevise' ); ?></h1>
			<style>
				.tz-ai-ops{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin:16px 0 24px}
				.tz-ai-ops article{background:#fff;border:1px solid #dfe9e5;border-radius:10px;padding:12px 14px}
				.tz-ai-ops .is-ok{border-color:#82d8b9}
				.tz-ai-ops .is-missing{opacity:.7}
				.tz-ai-ops span{display:block;font-size:12px;margin-top:4px}
				.tz-identity-lock th{color:#145d4a}
				.tz-identity-lock td{background:#f4faf7;border-radius:8px;padding:12px}
			</style>
			<p><?php echo esc_html( sprintf( __( '%1$d از %2$d ارائه‌دهنده کلید ذخیره دارند. اگر یکی شکست بخورد بقیه در آبشار استفاده می‌شوند. پژوهش: Perplexity سپس You.com سپس Tavily.', 'teznevise' ), $ok, count( $providers ) ) ); ?></p>
			<div class="tz-ai-ops">
				<?php foreach ( $providers as $id => $row ) : ?>
					<?php $has = '' !== (string) get_option( $row['option'], '' ); ?>
					<article class="<?php echo $has ? 'is-ok' : 'is-missing'; ?>">
						<strong><?php echo esc_html( $row['label'] ); ?></strong>
						<span><?php echo $has ? esc_html__( 'کلید ذخیره است', 'teznevise' ) : esc_html__( 'کلید نیست', 'teznevise' ); ?></span>
						<code><?php echo esc_html( $row['host'] ); ?></code>
					</article>
				<?php endforeach; ?>
			</div>
			<?php if ( $log ) : ?>
				<details><summary><?php esc_html_e( 'آخرین شکست‌های API', 'teznevise' ); ?></summary>
					<ol>
						<?php foreach ( array_reverse( array_slice( $log, -12 ) ) as $row ) : ?>
							<li><code><?php echo esc_html( $row['code'] ?? '' ); ?></code> <?php echo esc_html( $row['message'] ?? '' ); ?> <small><?php echo esc_html( date_i18n( 'Y/m/d H:i', (int) ( $row['time'] ?? 0 ) ) ); ?></small></li>
						<?php endforeach; ?>
					</ol>
				</details>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( 'teznevise_ai_settings' ); ?>
				<h2><?php esc_html_e( 'کلیدهای API', 'teznevise' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $providers as $id => $row ) : ?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( self::OPTION_PREFIX . $row['option'] ); ?>"><?php echo esc_html( $row['label'] ); ?></label></th>
							<td>
								<?php
								$stored = (string) get_option( $row['option'], '' );
								$has    = '' !== $stored;
								?>
								<input class="regular-text" type="password" autocomplete="new-password" id="<?php echo esc_attr( self::OPTION_PREFIX . $row['option'] ); ?>" name="<?php echo esc_attr( $row['option'] ); ?>" value="" placeholder="<?php echo $has ? esc_attr__( 'ذخیره شده — برای جایگزینی بنویسید', 'teznevise' ) : ''; ?>" />
								<p class="description"><code><?php echo esc_html( $row['host'] ); ?></code><?php echo $has ? ' — ' . esc_html__( 'کلید ذخیره است. برای پاک‌کردن یک خط تیره (-) بگذارید.', 'teznevise' ) : ''; ?></p>
							</td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'سقف مهمان / عضو', 'teznevise' ); ?></th>
						<td>
							<input type="number" name="teznevise_ai_free_tier_limit" value="<?php echo esc_attr( get_option( 'teznevise_ai_free_tier_limit', 10 ) ); ?>" min="0" /> /
							<input type="number" name="teznevise_ai_signed_in_limit" value="<?php echo esc_attr( get_option( 'teznevise_ai_signed_in_limit', 100 ) ); ?>" min="0" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'هزینه هر پیام (تزکوین)', 'teznevise' ); ?></th>
						<td><input type="number" step="0.01" name="teznevise_ai_cost_per_message" value="<?php echo esc_attr( get_option( 'teznevise_ai_cost_per_message', 0 ) ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="teznevise_ai_genspark_endpoint"><?php esc_html_e( 'آدرس Genspark (پشتیبان)', 'teznevise' ); ?></label></th>
						<td>
							<input class="regular-text" type="url" id="teznevise_ai_genspark_endpoint" name="teznevise_ai_genspark_endpoint" value="<?php echo esc_attr( get_option( 'teznevise_ai_genspark_endpoint', 'https://api.genspark.ai/v1/chat/completions' ) ); ?>" placeholder="https://api.genspark.ai/v1/chat/completions" />
							<p class="description"><?php esc_html_e( 'اگر کلید Genspark را ذخیره کنید، پس از شکست بقیه ارائه‌دهنده‌ها از این آدرس OpenAI-سازگار استفاده می‌شود.', 'teznevise' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="teznevise_ai_perplexity_model"><?php esc_html_e( 'مدل Perplexity (پژوهش / پشتیبان)', 'teznevise' ); ?></label></th>
						<td>
							<input class="regular-text" type="text" id="teznevise_ai_perplexity_model" name="teznevise_ai_perplexity_model" value="<?php echo esc_attr( get_option( 'teznevise_ai_perplexity_model', 'sonar' ) ); ?>" placeholder="sonar" />
							<p class="description"><?php esc_html_e( 'مدل Sonar برای پژوهش زنده (sonar، sonar-pro، sonar-reasoning-pro). کلید را در ردیف Perplexity بالا بگذارید.', 'teznevise' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="teznevise_ai_notify_emails"><?php esc_html_e( 'ایمیل تاریخچه گفتگو / رزرو تماس', 'teznevise' ); ?></label></th>
						<td>
							<input class="regular-text" type="text" id="teznevise_ai_notify_emails" name="teznevise_ai_notify_emails" value="<?php echo esc_attr( get_option( 'teznevise_ai_notify_emails', '' ) ); ?>" placeholder="teznevisan@gmail.com" />
							<p class="description"><?php esc_html_e( 'چند آدرس را با ویرگول جدا کنید. تاریخچه گفتگوی زنده به این صندوق‌ها ارسال می‌شود.', 'teznevise' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'ذخیره کلیدها و سقف‌ها', 'teznevise' ) ); ?>
			</form>

			<hr />
			<h2><?php esc_html_e( 'عامل‌ها (نام، مدل، ارائه‌دهنده)', 'teznevise' ); ?></h2>
			<table class="wp-list-table widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'شناسه', 'teznevise' ); ?></th>
						<th><?php esc_html_e( 'نام نمایشی', 'teznevise' ); ?></th>
						<th><?php esc_html_e( 'نام مدل قفل‌شده', 'teznevise' ); ?></th>
						<th><?php esc_html_e( 'ارائه‌دهنده', 'teznevise' ); ?></th>
						<th><?php esc_html_e( 'مدل', 'teznevise' ); ?></th>
						<th><?php esc_html_e( 'فعال', 'teznevise' ); ?></th>
						<th><?php esc_html_e( 'فکر', 'teznevise' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( (array) $agents as $ag ) : $ag = (array) $ag;
						$edit = array(
							'agent_id'           => $ag['agent_id'] ?? '',
							'name'               => $ag['name'] ?? '',
							'displayed_model_name' => $ag['displayed_model_name'] ?? '',
							'description'        => $ag['description'] ?? '',
							'system_prompt'      => $ag['system_prompt'] ?? '',
							'role'               => $ag['role'] ?? 'general',
							'language'           => $ag['language'] ?? 'fa',
							'temperature'        => $ag['temperature'] ?? 0.7,
							'max_tokens'         => $ag['max_tokens'] ?? 1500,
							'provider'           => $ag['provider'] ?? 'openai',
							'model'              => $ag['model'] ?? '',
							'api_endpoint'       => $ag['api_endpoint'] ?? '',
							'color'              => $ag['color'] ?? '#145d4a',
							'sort_order'         => $ag['sort_order'] ?? 0,
							'is_active'          => ! empty( $ag['is_active'] ),
							'thinking_enabled'   => ! empty( $ag['thinking_enabled'] ),
						);
						?>
						<tr>
							<td><code><?php echo esc_html( $ag['agent_id'] ?? '' ); ?></code></td>
							<td><?php echo esc_html( $ag['alias'] ?? $ag['name'] ?? '' ); ?></td>
							<td><?php echo esc_html( $ag['displayed_model_name'] ?? '' ); ?></td>
							<td><?php echo esc_html( $ag['provider'] ?? 'openai' ); ?></td>
							<td><?php echo esc_html( $ag['model'] ?? '' ); ?></td>
							<td><?php echo empty( $ag['is_active'] ) ? '—' : '✓'; ?></td>
							<td><?php echo empty( $ag['thinking_enabled'] ) ? '—' : '✓'; ?></td>
							<td>
								<button type="button" class="button tz-edit-agent" data-agent="<?php echo esc_attr( wp_json_encode( $edit, JSON_UNESCAPED_UNICODE ) ); ?>"><?php esc_html_e( 'ویرایش', 'teznevise' ); ?></button>
								<form method="post" style="display:inline">
									<?php wp_nonce_field( 'teznevise_ai_agent', '_tz_ai_agent' ); ?>
									<input type="hidden" name="teznevise_ai_agent_action" value="delete" />
									<input type="hidden" name="agent_id" value="<?php echo esc_attr( $ag['agent_id'] ?? '' ); ?>" />
									<button class="button-link-delete" type="submit"><?php esc_html_e( 'حذف', 'teznevise' ); ?></button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<h3><?php esc_html_e( 'افزودن یا به‌روزرسانی عامل', 'teznevise' ); ?></h3>
			<form method="post" id="tz-agent-form">
				<?php wp_nonce_field( 'teznevise_ai_agent', '_tz_ai_agent' ); ?>
				<input type="hidden" name="teznevise_ai_agent_action" value="save" />
				<table class="form-table" role="presentation">
					<tr><th><?php esc_html_e( 'شناسه لاتین', 'teznevise' ); ?></th><td><input required name="agent_id" id="agent_id" class="regular-text" placeholder="research_editor" /></td></tr>
					<tr><th><?php esc_html_e( 'نام نمایشی', 'teznevise' ); ?></th><td><input required name="name" id="agent_name" class="regular-text" placeholder="ویراستار علمی" /></td></tr>
					<tr class="tz-identity-lock">
						<th><label for="displayed_model_name"><?php esc_html_e( 'نام مدل نمایشی (هویت قفل‌شده)', 'teznevise' ); ?></label></th>
						<td>
							<input name="displayed_model_name" id="displayed_model_name" class="regular-text" placeholder="<?php esc_attr_e( 'مثلاً پژوهشگر تزنویسه', 'teznevise' ); ?>" />
							<p class="description"><strong><?php esc_html_e( 'این نام را دستی بنویسید.', 'teznevise' ); ?></strong> <?php esc_html_e( 'اگر از عامل پرسیده شود «چه مدلی هستی؟» فقط همین متن را می‌گوید — نه نام OpenAI یا Claude یا Gemini، مگر خودتان همان را نوشته باشید.', 'teznevise' ); ?></p>
						</td>
					</tr>
					<tr><th><?php esc_html_e( 'توضیح / پرامپت نقش', 'teznevise' ); ?></th><td><textarea name="description" class="large-text" rows="3"></textarea></td></tr>
					<tr><th><?php esc_html_e( 'پرامپت سیستم (جزئی)', 'teznevise' ); ?></th><td><textarea name="system_prompt" class="large-text" rows="6" placeholder="<?php esc_attr_e( 'نقش، لحن، منابع مجاز، قالب خروجی، زبان…', 'teznevise' ); ?>"></textarea></td></tr>
					<tr>
						<th><?php esc_html_e( 'نقش عامل', 'teznevise' ); ?></th>
						<td>
							<select name="role">
								<option value="general"><?php esc_html_e( 'عمومی', 'teznevise' ); ?></option>
								<option value="researcher"><?php esc_html_e( 'پژوهشگر (You)', 'teznevise' ); ?></option>
								<option value="analyst"><?php esc_html_e( 'تحلیل‌گر', 'teznevise' ); ?></option>
								<option value="methodologist"><?php esc_html_e( 'روش‌شناس', 'teznevise' ); ?></option>
								<option value="statistician"><?php esc_html_e( 'آمار', 'teznevise' ); ?></option>
								<option value="editor"><?php esc_html_e( 'ویراستار علمی', 'teznevise' ); ?></option>
								<option value="synthesizer"><?php esc_html_e( 'ترکیب‌گر', 'teznevise' ); ?></option>
								<option value="qualitative"><?php esc_html_e( 'کیفی / اخلاق', 'teznevise' ); ?></option>
								<option value="legal"><?php esc_html_e( 'حقوق / سیاست', 'teznevise' ); ?></option>
								<option value="medical"><?php esc_html_e( 'پزشکی / STEM', 'teznevise' ); ?></option>
							</select>
							<input name="language" class="small-text" value="fa" />
							<label><?php esc_html_e( 'دما', 'teznevise' ); ?> <input name="temperature" type="number" step="0.1" min="0" max="2" value="0.7" class="small-text" /></label>
							<label><?php esc_html_e( 'حداکثر توکن', 'teznevise' ); ?> <input name="max_tokens" type="number" value="1800" class="small-text" /></label>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'ارائه‌دهنده', 'teznevise' ); ?></th>
						<td>
							<select name="provider">
								<?php foreach ( $providers as $id => $row ) : ?>
									<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $row['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr><th><?php esc_html_e( 'مدل', 'teznevise' ); ?></th><td><input name="model" class="regular-text" placeholder="gpt-4o-mini / gemini-2.0-flash / llama-3.1-8b-instant" /></td></tr>
					<tr><th><?php esc_html_e( 'آدرس API (اختیاری)', 'teznevise' ); ?></th><td><input name="api_endpoint" class="regular-text ltr" dir="ltr" placeholder="https://..." /></td></tr>
					<tr><th><?php esc_html_e( 'کلید اختصاصی عامل (اختیاری)', 'teznevise' ); ?></th><td><input name="api_key" class="regular-text" type="password" autocomplete="new-password" /></td></tr>
					<tr><th><?php esc_html_e( 'رنگ', 'teznevise' ); ?></th><td><input name="color" type="color" value="#145d4a" /></td></tr>
					<tr><th><?php esc_html_e( 'ترتیب', 'teznevise' ); ?></th><td><input name="sort_order" type="number" value="0" /></td></tr>
					<tr><th></th><td>
						<label><input type="checkbox" name="is_active" value="1" checked /> <?php esc_html_e( 'فعال', 'teznevise' ); ?></label>
						<label style="margin-inline-start:16px"><input type="checkbox" name="thinking_enabled" value="1" checked /> <?php esc_html_e( 'فرآیند فکر', 'teznevise' ); ?></label>
					</td></tr>
				</table>
				<?php submit_button( __( 'ذخیره عامل', 'teznevise' ) ); ?>
			</form>
			<script>
			(function(){
			  document.querySelectorAll('.tz-edit-agent').forEach(function(btn){
			    btn.addEventListener('click', function(){
			      var a = {};
			      try { a = JSON.parse(btn.getAttribute('data-agent') || '{}'); } catch (e) { return; }
			      var f = document.getElementById('tz-agent-form');
			      if (!f) return;
			      var set = function(name, val){ var el = f.querySelector('[name="'+name+'"]'); if (el) el.value = val == null ? '' : val; };
			      set('agent_id', a.agent_id); set('name', a.name); set('displayed_model_name', a.displayed_model_name);
			      set('description', a.description); set('system_prompt', a.system_prompt); set('role', a.role);
			      set('language', a.language); set('temperature', a.temperature); set('max_tokens', a.max_tokens);
			      set('provider', a.provider); set('model', a.model); set('api_endpoint', a.api_endpoint);
			      set('color', a.color || '#145d4a'); set('sort_order', a.sort_order || 0);
			      var act = f.querySelector('[name="is_active"]'); if (act) act.checked = !!a.is_active;
			      var th = f.querySelector('[name="thinking_enabled"]'); if (th) th.checked = !!a.thinking_enabled;
			      f.scrollIntoView({behavior:'smooth', block:'start'});
			    });
			  });
			})();
			</script>
		</div>
		<?php
	}
}
