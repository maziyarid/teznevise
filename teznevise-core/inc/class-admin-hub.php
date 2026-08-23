<?php
/**
 * Top-level wp-admin hub: keys, named agents, pipeline, waitlist, free models.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Admin_Hub {

	public static function register() {
		add_menu_page(
			__( 'تزنویسه', 'teznevise' ),
			__( 'تزنویسه', 'teznevise' ),
			'manage_options',
			'teznevise-hub',
			array( __CLASS__, 'render' ),
			'dashicons-awards',
			3
		);
		add_submenu_page(
			'teznevise-hub',
			__( 'پیشخوان تزنویسه', 'teznevise' ),
			__( 'پیشخوان', 'teznevise' ),
			'manage_options',
			'teznevise-hub',
			array( __CLASS__, 'render' )
		);
	}

	public static function handle_post() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( empty( $_POST['teznevise_hub_save'] ) ) {
			return;
		}
		if ( ! isset( $_POST['_tz_hub'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_hub'] ) ), 'teznevise_hub' ) ) {
			return;
		}
		if ( ! empty( $_POST['seed_roster'] ) && class_exists( 'Teznevise_Agent_Registry' ) ) {
			Teznevise_Agent_Registry::seed_named_roster();
		}
		if ( ! empty( $_POST['backfill_all'] ) && class_exists( 'Teznevise_Debate_Orchestrator' ) ) {
			Teznevise_Debate_Orchestrator::enqueue_published( ! empty( $_POST['backfill_force'] ) );
		}
		$models = isset( $_POST['hub_agent_models'] ) && is_array( $_POST['hub_agent_models'] ) ? wp_unslash( $_POST['hub_agent_models'] ) : array();
		$clean  = array();
		foreach ( (array) $models as $id => $row ) {
			$id = sanitize_key( $id );
			if ( '' === $id || ! is_array( $row ) ) {
				continue;
			}
			$clean[ $id ] = array(
				'primary_slot'  => sanitize_key( $row['primary_slot'] ?? 'medium' ),
				'fallback_slot' => sanitize_key( $row['fallback_slot'] ?? 'complex' ),
			);
		}
		if ( $clean ) {
			update_option( 'teznevise_core_agent_models', $clean, false );
		}
		$free = isset( $_POST['free_models'] ) && is_array( $_POST['free_models'] ) ? wp_unslash( $_POST['free_models'] ) : array();
		$fm   = array();
		foreach ( (array) $free as $k => $v ) {
			$fm[ sanitize_key( $k ) ] = sanitize_text_field( $v );
		}
		if ( $fm ) {
			update_option( 'teznevise_core_free_models', $fm, false );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'teznevise-hub', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( class_exists( 'Teznevise_Agent_Registry' ) ) {
			Teznevise_Agent_Registry::seed_named_roster();
		}
		$providers = class_exists( 'TezNevise_AI_API' ) ? TezNevise_AI_API::providers() : array();
		$ok        = 0;
		foreach ( $providers as $row ) {
			if ( '' !== (string) get_option( $row['option'], '' ) ) {
				++$ok;
			}
		}
		$roster  = function_exists( 'teznevise_core_agent_roster' ) ? teznevise_core_agent_roster() : array();
		$catalog = function_exists( 'teznevise_core_free_models' ) ? teznevise_core_free_models() : array();
		$slots   = array( 'simple', 'medium', 'complex', 'long_context', 'reasoning' );
		$wait_n  = 0;
		if ( function_exists( 'teznevise_waitlist_table' ) ) {
			global $wpdb;
			$table = teznevise_waitlist_table();
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
			$wait_n = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		}
		$jobs = get_posts(
			array(
				'post_type'      => array( 'post', 'page' ),
				'post_status'    => 'any',
				'posts_per_page' => 8,
				'meta_key'       => '_teznevise_ai_job',
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'fields'         => 'ids',
			)
		);
		$queue   = get_option( 'teznevise_core_backfill_queue', array() );
		$queue_n = is_array( $queue ) ? count( $queue ) : 0;
		$log     = class_exists( 'Teznevise_Logger' ) ? Teznevise_Logger::all() : array();
		?>
		<div class="wrap tz-hub">
			<h1><?php esc_html_e( 'پیشخوان تزنویسه', 'teznevise' ); ?></h1>
			<?php if ( ! empty( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="updated"><p><?php esc_html_e( 'ذخیره شد.', 'teznevise' ); ?></p></div>
			<?php endif; ?>
			<style>
				.tz-hub-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin:16px 0 24px}
				.tz-hub-grid article{background:#fff;border:1px solid #dfe9e5;border-radius:12px;padding:14px 16px}
				.tz-hub-grid .is-ok{border-color:#82d8b9}
				.tz-hub-agents{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px}
				.tz-hub-agent{background:#fff;border:1px solid #dfe9e5;border-radius:12px;padding:14px;display:grid;grid-template-columns:48px 1fr;gap:12px;align-items:start}
				.tz-hub-agent img{width:48px;height:48px;border-radius:12px}
				.tz-hub table.widefat td,.tz-hub table.widefat th{vertical-align:middle}
			</style>
			<div class="tz-hub-grid">
				<article class="<?php echo $ok ? 'is-ok' : ''; ?>">
					<strong><?php esc_html_e( 'کلیدهای API', 'teznevise' ); ?></strong>
					<p><?php echo esc_html( sprintf( __( '%1$d از %2$d ارائه‌دهنده', 'teznevise' ), $ok, count( $providers ) ) ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=teznevise-ai-settings' ) ); ?>"><?php esc_html_e( 'ویرایش کلیدها', 'teznevise' ); ?></a>
				</article>
				<article>
					<strong><?php esc_html_e( 'عامل‌های نام‌دار', 'teznevise' ); ?></strong>
					<p><?php echo esc_html( (string) count( $roster ) ); ?> / 8</p>
				</article>
				<article>
					<strong><?php esc_html_e( 'لیست انتظار ابزار', 'teznevise' ); ?></strong>
					<p><?php echo esc_html( number_format_i18n( $wait_n ) ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'tools.php?page=teznevise-waitlist' ) ); ?>"><?php esc_html_e( 'مشاهده', 'teznevise' ); ?></a>
				</article>
				<article>
					<strong><?php esc_html_e( 'صف نمای کلی / مناظره', 'teznevise' ); ?></strong>
					<p><?php echo esc_html( number_format_i18n( $queue_n ) ); ?></p>
				</article>
				<article>
					<strong><?php esc_html_e( 'مهر اعتماد', 'teznevise' ); ?></strong>
					<p><?php esc_html_e( 'اینماد + TrustedSite', 'teznevise' ); ?></p>
				</article>
			</div>

			<h2><?php esc_html_e( 'هشت عامل پژوهشی', 'teznevise' ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( 'teznevise_hub', '_tz_hub' ); ?>
				<input type="hidden" name="teznevise_hub_save" value="1" />
				<div class="tz-hub-agents">
					<?php foreach ( $roster as $id => $row ) : ?>
						<?php
						$models = teznevise_core_agent_models( $id );
						$agent  = class_exists( 'Teznevise_Agent_Registry' ) ? Teznevise_Agent_Registry::get( $id ) : null;
						?>
						<article class="tz-hub-agent">
							<?php echo teznevise_core_agent_logo_html( $id, 48 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div>
								<strong><?php echo esc_html( $row['name'] ); ?></strong>
								<div class="description"><?php echo esc_html( $row['displayed_model_name'] ); ?> · <?php echo esc_html( $row['role'] ); ?></div>
								<p><?php echo esc_html( $row['description'] ); ?></p>
								<?php
								$skills = function_exists( 'teznevise_core_agent_skills' ) ? teznevise_core_agent_skills( $id ) : array();
								if ( $skills ) :
									echo '<p class="description">' . esc_html__( 'مهارت‌ها: ', 'teznevise' );
									$names = array();
									foreach ( $skills as $skill ) {
										$names[] = $skill['name'];
									}
									echo esc_html( implode( ' · ', $names ) ) . '</p>';
								endif;
								?>
								<p>
									<label><?php esc_html_e( 'اصلی', 'teznevise' ); ?>
										<select name="hub_agent_models[<?php echo esc_attr( $id ); ?>][primary_slot]">
											<?php foreach ( $slots as $slot ) : ?>
												<option value="<?php echo esc_attr( $slot ); ?>" <?php selected( $models['primary_slot'], $slot ); ?>><?php echo esc_html( $slot . ' — ' . ( $catalog[ $slot ] ?? '' ) ); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
								</p>
								<p>
									<label><?php esc_html_e( 'پشتیبان', 'teznevise' ); ?>
										<select name="hub_agent_models[<?php echo esc_attr( $id ); ?>][fallback_slot]">
											<?php foreach ( $slots as $slot ) : ?>
												<option value="<?php echo esc_attr( $slot ); ?>" <?php selected( $models['fallback_slot'], $slot ); ?>><?php echo esc_html( $slot . ' — ' . ( $catalog[ $slot ] ?? '' ) ); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
								</p>
								<p class="description"><?php echo esc_html( ( $agent['displayed_model_name'] ?? '' ) . ' · ' . ( $models['primary'] ?? '' ) ); ?></p>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<h2><?php esc_html_e( 'کاتالوگ مدل رایگان OpenRouter', 'teznevise' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $catalog as $k => $v ) : ?>
						<tr>
							<th><label for="fm-<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $k ); ?></label></th>
							<td><input class="regular-text ltr" dir="ltr" id="fm-<?php echo esc_attr( $k ); ?>" name="free_models[<?php echo esc_attr( $k ); ?>]" value="<?php echo esc_attr( $v ); ?>" /></td>
						</tr>
					<?php endforeach; ?>
				</table>
				<p>
					<label><input type="checkbox" name="seed_roster" value="1" /> <?php esc_html_e( 'اگر هشت عامل در پایگاه نیستند، درج کن (هرگز حذف نمی‌کند)', 'teznevise' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="backfill_all" value="1" /> <?php esc_html_e( 'نمای کلی و مناظره را برای مطالب بدون خروجی در صف بگذار', 'teznevise' ); ?></label>
				</p>
				<p>
					<label><input type="checkbox" name="backfill_force" value="1" /> <?php esc_html_e( 'بازنویسی همه (نمای کلی بازبینی‌شده انسانی حفظ می‌شود مگر همین را هم بخواهید از ویرایش مطلب)', 'teznevise' ); ?></label>
				</p>
				<?php submit_button( __( 'ذخیره مدل‌ها و عامل‌ها', 'teznevise' ) ); ?>
			</form>

			<h2><?php esc_html_e( 'آخرین کارهای مناظره', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'صف پس‌زمینه هر مطلب را جداگانه پژوهش و مناظره می‌کند تا سهمیه API نترکد. پس از همگام‌سازی یک‌بار پیشخوان را باز کنید.', 'teznevise' ); ?></p>
				<?php if ( ! $jobs ) : ?>
				<p><?php esc_html_e( 'هنوز کاری در صف نیست. از ویرایش مطلب «پژوهش و مناظره تولید شود» را بزنید.', 'teznevise' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead><tr><th><?php esc_html_e( 'مطلب', 'teznevise' ); ?></th><th><?php esc_html_e( 'وضعیت', 'teznevise' ); ?></th><th><?php esc_html_e( 'گام‌ها', 'teznevise' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( $jobs as $jid ) : ?>
						<?php
						$st   = (string) get_post_meta( $jid, '_teznevise_ai_job', true );
						$pipe = get_post_meta( $jid, '_teznevise_ai_pipeline', true );
						$steps = is_array( $pipe ) && ! empty( $pipe['steps'] ) ? implode( ' ← ', array_reverse( (array) $pipe['steps'] ) ) : '—';
						?>
						<tr>
							<td><a href="<?php echo esc_url( get_edit_post_link( $jid ) ); ?>"><?php echo esc_html( get_the_title( $jid ) ); ?></a></td>
							<td><code><?php echo esc_html( $st ); ?></code></td>
							<td><?php echo esc_html( $steps ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<p>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?page=teznevise-core-agents' ) ); ?>"><?php esc_html_e( 'هویت عامل‌ها', 'teznevise' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?page=teznevise-ai-comments' ) ); ?>"><?php esc_html_e( 'گفتگوی هوش مصنوعی', 'teznevise' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'tools.php?page=teznevise-waitlist' ) ); ?>"><?php esc_html_e( 'لیست انتظار', 'teznevise' ); ?></a>
			</p>

			<?php if ( $log ) : ?>
				<details><summary><?php esc_html_e( 'آخرین شکست‌های API', 'teznevise' ); ?></summary>
					<ol>
						<?php foreach ( array_reverse( array_slice( $log, -12 ) ) as $row ) : ?>
							<li><code><?php echo esc_html( $row['code'] ?? '' ); ?></code> <?php echo esc_html( $row['message'] ?? '' ); ?></li>
						<?php endforeach; ?>
					</ol>
				</details>
			<?php endif; ?>
		</div>
		<?php
	}
}
