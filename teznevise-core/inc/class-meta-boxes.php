<?php
/**
 * Per-post AI debate configuration + agent alias fields.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Meta_Boxes {

	public static function register() {
		foreach ( array( 'post', 'page' ) as $type ) {
			add_meta_box(
				'teznevise_core_ai_config',
				__( 'پیکربندی گفتگوی هوش مصنوعی', 'teznevise' ),
				array( __CLASS__, 'render' ),
				$type,
				'normal',
				'high'
			);
		}
	}

	public static function render( $post ) {
		wp_nonce_field( 'teznevise_core_ai', '_tz_core_ai' );
		$skill    = (string) get_post_meta( $post->ID, '_teznevise_skill_md', true );
		$pre      = (string) get_post_meta( $post->ID, '_pre_computed_thoughts', true );
		$refs     = get_post_meta( $post->ID, '_teznevise_references', true );
		$refs     = is_array( $refs ) ? $refs : array();
		$source   = (string) get_post_meta( $post->ID, '_teznevise_api_source', true );
		$chosen   = get_post_meta( $post->ID, '_teznevise_debate_agents', true );
		$chosen   = is_array( $chosen ) ? $chosen : array();
		$job      = (string) get_post_meta( $post->ID, '_teznevise_ai_job', true );
		$agents   = Teznevise_Agent_Registry::all( true );
		if ( count( $refs ) < 3 ) {
			$refs = array_pad( $refs, 3, array( 'title' => '', 'url' => '' ) );
		}
		echo '<p class="description">' . esc_html__( 'تولید گفتگو پس از ذخیره، غیرهمزمان انجام می‌شود تا صفحه قفل نشود.', 'teznevise' ) . '</p>';
		if ( $job ) {
			echo '<p><strong>' . esc_html( sprintf( __( 'وضعیت صف: %s', 'teznevise' ), $job ) ) . '</strong></p>';
		}
		echo '<p><label><input type="checkbox" name="teznevise_ai_generate" value="1" /> ' . esc_html__( 'با ذخیره مطلب، پژوهش و مناظره تولید شود', 'teznevise' ) . '</label></p>';
		echo '<p><label>' . esc_html__( 'SKILL.md همین مطلب', 'teznevise' ) . '</label><textarea class="widefat" rows="6" name="teznevise_skill_md">' . esc_textarea( $skill ) . '</textarea></p>';
		echo '<p><label>' . esc_html__( 'شناسه پیوست SKILL.md', 'teznevise' ) . ' <input type="number" min="0" class="small-text" name="teznevise_skill_md_id" value="" /></label></p>';
		echo '<p><label>' . esc_html__( 'پیش‌اندیشه‌ها (کاهش توکن)', 'teznevise' ) . '</label><textarea class="widefat" rows="4" name="pre_computed_thoughts">' . esc_textarea( $pre ) . '</textarea></p>';
		echo '<p>' . esc_html__( 'منابع اجباری', 'teznevise' ) . '</p>';
		foreach ( $refs as $i => $ref ) {
			$ref = is_array( $ref ) ? $ref : array();
			echo '<p><input class="regular-text" name="teznevise_ref_title[]" placeholder="' . esc_attr__( 'عنوان منبع', 'teznevise' ) . '" value="' . esc_attr( $ref['title'] ?? '' ) . '" /> ';
			echo '<input class="regular-text ltr" dir="ltr" name="teznevise_ref_url[]" placeholder="https://" value="' . esc_attr( $ref['url'] ?? '' ) . '" /></p>';
		}
		echo '<p><label>' . esc_html__( 'منبع API', 'teznevise' ) . '</label> ';
		echo '<select name="teznevise_api_source"><option value="global" ' . selected( $source, 'global', false ) . '>' . esc_html__( 'کلید سراسری', 'teznevise' ) . '</option>';
		echo '<option value="custom" ' . selected( $source, 'custom', false ) . '>' . esc_html__( 'کلید سفارشی این مطلب', 'teznevise' ) . '</option></select></p>';
		echo '<p><label>' . esc_html__( 'کلید OpenRouter سفارشی', 'teznevise' ) . ' <input type="password" autocomplete="new-password" name="teznevise_api_key_openrouter" class="regular-text" /></label></p>';
		echo '<p><label>' . esc_html__( 'کلید You.com سفارشی', 'teznevise' ) . ' <input type="password" autocomplete="new-password" name="teznevise_api_key_you" class="regular-text" /></label></p>';
		echo '<p>' . esc_html__( 'عامل‌های این مناظره', 'teznevise' ) . '</p>';
		foreach ( $agents as $ag ) {
			$id = $ag['agent_id'] ?? '';
			if ( ! $id || ( $ag['role'] ?? '' ) === 'researcher' ) {
				continue;
			}
			echo '<label style="display:block"><input type="checkbox" name="teznevise_debate_agents[]" value="' . esc_attr( $id ) . '" ' . checked( in_array( $id, $chosen, true ), true, false ) . ' /> ' . esc_html( $ag['alias'] ?? $ag['name'] ?? $id ) . '</label>';
		}
	}

	public static function save( $post_id, $post ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! isset( $_POST['_tz_core_ai'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_core_ai'] ) ), 'teznevise_core_ai' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_teznevise_ai_generate_flag', empty( $_POST['teznevise_ai_generate'] ) ? '0' : '1' );
		update_post_meta( $post_id, '_teznevise_skill_md', sanitize_textarea_field( wp_unslash( $_POST['teznevise_skill_md'] ?? '' ) ) );
		$skill_id = isset( $_POST['teznevise_skill_md_id'] ) ? absint( $_POST['teznevise_skill_md_id'] ) : 0;
		if ( $skill_id && current_user_can( 'upload_files' ) ) {
			$file = get_attached_file( $skill_id );
			if ( $file && is_readable( $file ) && preg_match( '/\.(md|txt)$/i', $file ) ) {
				$loaded = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				if ( is_string( $loaded ) && strlen( $loaded ) < 20000 ) {
					update_post_meta( $post_id, '_teznevise_skill_md', sanitize_textarea_field( $loaded ) );
					update_post_meta( $post_id, '_teznevise_skill_md_id', $skill_id );
				}
			}
		}
		update_post_meta( $post_id, '_pre_computed_thoughts', sanitize_textarea_field( wp_unslash( $_POST['pre_computed_thoughts'] ?? '' ) ) );
		update_post_meta( $post_id, '_teznevise_api_source', sanitize_key( wp_unslash( $_POST['teznevise_api_source'] ?? 'global' ) ) );
		$titles = isset( $_POST['teznevise_ref_title'] ) ? (array) wp_unslash( $_POST['teznevise_ref_title'] ) : array();
		$urls   = isset( $_POST['teznevise_ref_url'] ) ? (array) wp_unslash( $_POST['teznevise_ref_url'] ) : array();
		$refs   = array();
		foreach ( $titles as $i => $title ) {
			$title = sanitize_text_field( $title );
			$url   = esc_url_raw( $urls[ $i ] ?? '' );
			if ( '' === $title && '' === $url ) {
				continue;
			}
			$refs[] = array(
				'title' => $title,
				'url'   => $url,
			);
		}
		update_post_meta( $post_id, '_teznevise_references', $refs );
		$chosen = isset( $_POST['teznevise_debate_agents'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['teznevise_debate_agents'] ) ) : array();
		update_post_meta( $post_id, '_teznevise_debate_agents', $chosen );
		foreach ( array( 'openrouter', 'you' ) as $prov ) {
			$field = 'teznevise_api_key_' . $prov;
			if ( ! empty( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_teznevise_api_key_' . $prov, Teznevise_Key_Vault::encrypt( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) ) );
			}
		}
		$clicked = ! empty( $_POST['teznevise_ai_generate'] );
		$auto    = false;
		if ( function_exists( 'teznevise_ai_comment_settings' ) ) {
			$auto = ! empty( teznevise_ai_comment_settings()['auto_on_publish'] ) && 'publish' === $post->post_status;
		}
		if ( $clicked || $auto ) {
			Teznevise_Debate_Orchestrator::schedule( $post_id, (bool) $clicked );
		}
	}

	public static function render_agent_profiles() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_POST['teznevise_core_profile_save'] ) && isset( $_POST['_tz_core_prof'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_core_prof'] ) ), 'teznevise_core_prof' ) ) {
			$ids = isset( $_POST['profile_id'] ) ? (array) wp_unslash( $_POST['profile_id'] ) : array();
			foreach ( $ids as $i => $id ) {
				Teznevise_Agent_Registry::save_profile(
					$id,
					array(
						'alias'                => sanitize_text_field( wp_unslash( $_POST['profile_alias'][ $i ] ?? '' ) ),
						'displayed_model_name' => sanitize_text_field( wp_unslash( $_POST['profile_display'][ $i ] ?? '' ) ),
						'avatar'               => esc_url_raw( wp_unslash( $_POST['profile_avatar'][ $i ] ?? '' ) ),
					)
				);
			}
			$models = isset( $_POST['free_models'] ) && is_array( $_POST['free_models'] ) ? wp_unslash( $_POST['free_models'] ) : array();
			$clean  = array();
			foreach ( (array) $models as $k => $v ) {
				$clean[ sanitize_key( $k ) ] = sanitize_text_field( $v );
			}
			if ( $clean ) {
				update_option( 'teznevise_core_free_models', $clean, false );
			}
			echo '<div class="updated"><p>' . esc_html__( 'پروفایل عامل‌ها و مدل‌های رایگان ذخیره شد.', 'teznevise' ) . '</p></div>';
		}
		$agents = Teznevise_Agent_Registry::all( true );
		$models = teznevise_core_free_models();
		echo '<div class="wrap"><h1>' . esc_html__( 'نام مستعار و هویت عامل‌ها', 'teznevise' ) . '</h1>';
		echo '<form method="post">';
		wp_nonce_field( 'teznevise_core_prof', '_tz_core_prof' );
		echo '<input type="hidden" name="teznevise_core_profile_save" value="1" />';
		echo '<table class="widefat"><thead><tr><th>id</th><th>' . esc_html__( 'نام مستعار', 'teznevise' ) . '</th><th>' . esc_html__( 'نام مدل نمایشی', 'teznevise' ) . '</th><th>avatar URL</th></tr></thead><tbody>';
		foreach ( $agents as $ag ) {
			$id = $ag['agent_id'] ?? '';
			echo '<tr>';
			echo '<td><code>' . esc_html( $id ) . '</code><input type="hidden" name="profile_id[]" value="' . esc_attr( $id ) . '" /></td>';
			echo '<td><input name="profile_alias[]" value="' . esc_attr( $ag['alias'] ?? '' ) . '" /></td>';
			echo '<td><input name="profile_display[]" value="' . esc_attr( $ag['displayed_model_name'] ?? '' ) . '" /></td>';
			echo '<td><input class="regular-text ltr" dir="ltr" name="profile_avatar[]" value="' . esc_attr( $ag['avatar'] ?? '' ) . '" /></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
		echo '<h2>' . esc_html__( 'مدل‌های رایگان OpenRouter', 'teznevise' ) . '</h2>';
		foreach ( $models as $k => $v ) {
			echo '<p><label>' . esc_html( $k ) . ' <input class="regular-text ltr" dir="ltr" name="free_models[' . esc_attr( $k ) . ']" value="' . esc_attr( $v ) . '" /></label></p>';
		}
		submit_button();
		echo '</form>';
		$log = Teznevise_Logger::all();
		echo '<h2>' . esc_html__( 'گزارش شکست API', 'teznevise' ) . '</h2>';
		if ( ! $log ) {
			echo '<p>' . esc_html__( 'شکستی ثبت نشده.', 'teznevise' ) . '</p></div>';
			return;
		}
		echo '<ol>';
		foreach ( array_reverse( $log ) as $row ) {
			echo '<li><code>' . esc_html( $row['code'] ?? '' ) . '</code> ' . esc_html( $row['message'] ?? '' ) . ' <small>' . esc_html( date_i18n( 'Y/m/d H:i', (int) ( $row['time'] ?? 0 ) ) ) . '</small></li>';
		}
		echo '</ol></div>';
	}
}
