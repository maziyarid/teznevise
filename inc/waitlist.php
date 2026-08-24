<?php
/**
 * Tools waitlist: name + mobile stored encrypted, never in a public option dump.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_waitlist_table() {
	global $wpdb;
	return $wpdb->prefix . 'teznevise_tool_waitlist';
}

function teznevise_waitlist_install() {
	global $wpdb;
	$table   = teznevise_waitlist_table();
	$charset = $wpdb->get_charset_collate();
	$sql     = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(120) NOT NULL DEFAULT '',
		phone_enc longtext NOT NULL,
		phone_hash char(64) NOT NULL DEFAULT '',
		ip_hash char(64) NOT NULL DEFAULT '',
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY phone_hash (phone_hash)
	) {$charset};";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	update_option( 'teznevise_waitlist_db', '1.0', false );
}
add_action(
	'admin_init',
	static function () {
		if ( '1.0' !== get_option( 'teznevise_waitlist_db' ) ) {
			teznevise_waitlist_install();
		}
	}
);

function teznevise_waitlist_render_bar() {
	// The tools are live. Keep the opt-in database/admin screen available, but
	// do not obscure every page and the chat composer with an obsolete banner.
	if ( is_admin() || ! apply_filters( 'teznevise_show_waitlist_bar', false ) ) {
		return;
	}
	$ok = isset( $_GET['waitlist'] ) ? sanitize_key( wp_unslash( $_GET['waitlist'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<aside class="tz-tools-notice" data-tz-notice>
		<div class="container tz-tools-notice__inner">
			<p class="tz-tools-notice__copy"><?php esc_html_e( 'ابزارهای تزنویسه بزودی راه‌اندازی می شوند، در صورت تمایل شماره همراه خود را وارد کنید تا به شما اطلاع رسانی شود.', 'teznevise' ); ?></p>
			<?php if ( 'ok' === $ok ) : ?>
				<p class="tz-tools-notice__ok" role="status"><?php esc_html_e( 'شماره شما ثبت شد. هنگام راه‌اندازی ابزارها خبر می‌دهیم.', 'teznevise' ); ?></p>
			<?php elseif ( 'dup' === $ok ) : ?>
				<p class="tz-tools-notice__ok" role="status"><?php esc_html_e( 'این شماره از قبل در فهرست اطلاع‌رسانی است.', 'teznevise' ); ?></p>
			<?php elseif ( 'err' === $ok ) : ?>
				<p class="tz-tools-notice__ok" role="status"><?php esc_html_e( 'نام و موبایل را بررسی کنید و دوباره بفرستید.', 'teznevise' ); ?></p>
			<?php else : ?>
				<form class="tz-tools-notice__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="teznevise_waitlist" />
					<?php wp_nonce_field( 'teznevise_waitlist', 'teznevise_waitlist_nonce' ); ?>
					<label class="screen-reader-text" for="tz-wl-name"><?php esc_html_e( 'نام', 'teznevise' ); ?></label>
					<input id="tz-wl-name" name="wl_name" type="text" required maxlength="80" autocomplete="name" placeholder="<?php esc_attr_e( 'نام', 'teznevise' ); ?>" />
					<label class="screen-reader-text" for="tz-wl-phone"><?php esc_html_e( 'موبایل', 'teznevise' ); ?></label>
					<input id="tz-wl-phone" name="wl_phone" type="tel" required dir="ltr" inputmode="tel" autocomplete="tel" placeholder="0912xxxxxxx" pattern="^(?:\+98|0)?9\d{9}$" />
					<label class="tz-honeypot" aria-hidden="true"><input name="website" tabindex="-1" autocomplete="off" /></label>
					<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'خبرم کنید', 'teznevise' ); ?></button>
				</form>
			<?php endif; ?>
			<button type="button" class="tz-tools-notice__close" data-tz-notice-close aria-label="<?php esc_attr_e( 'بستن', 'teznevise' ); ?>">&times;</button>
		</div>
	</aside>
	<script>
	(function () {
		var el = document.querySelector('[data-tz-notice]');
		if (!el) return;
		try { if (window.localStorage.getItem('tz-tools-notice') === '1') { el.hidden = true; return; } } catch (e) {}
		var btn = el.querySelector('[data-tz-notice-close]');
		if (btn) btn.addEventListener('click', function () {
			el.hidden = true;
			try { window.localStorage.setItem('tz-tools-notice', '1'); } catch (e) {}
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'teznevise_waitlist_render_bar', 5 );

function teznevise_waitlist_handle() {
	$nonce = isset( $_POST['teznevise_waitlist_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['teznevise_waitlist_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'teznevise_waitlist' ) ) {
		wp_die( esc_html__( 'درخواست نامعتبر است.', 'teznevise' ), '', array( 'response' => 400 ) );
	}
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'waitlist', 'ok', $redirect ) );
		exit;
	}
	$name  = isset( $_POST['wl_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wl_name'] ) ) : '';
	$phone = isset( $_POST['wl_phone'] ) ? ( function_exists( 'teznevise_iran_mobile' ) ? teznevise_iran_mobile( wp_unslash( $_POST['wl_phone'] ) ) : sanitize_text_field( wp_unslash( $_POST['wl_phone'] ) ) ) : '';
	if ( strlen( $name ) < 2 || '' === $phone ) {
		wp_safe_redirect( add_query_arg( 'waitlist', 'err', $redirect ) );
		exit;
	}
	$ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ip_hash = hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
	$rate    = 'tz_wl_' . substr( $ip_hash, 0, 24 );
	$count   = (int) get_transient( $rate );
	if ( $count >= 5 ) {
		wp_safe_redirect( add_query_arg( 'waitlist', 'rate', $redirect ) );
		exit;
	}
	set_transient( $rate, $count + 1, HOUR_IN_SECONDS );

	teznevise_waitlist_install();
	global $wpdb;
	$hash   = hash_hmac( 'sha256', $phone, wp_salt( 'auth' ) );
	$enc    = class_exists( 'Teznevise_Key_Vault' ) ? Teznevise_Key_Vault::encrypt( $phone ) : $phone;
	$exists = $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . teznevise_waitlist_table() . ' WHERE phone_hash = %s', $hash ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( $exists ) {
		wp_safe_redirect( add_query_arg( 'waitlist', 'dup', $redirect ) );
		exit;
	}
	$wpdb->insert(
		teznevise_waitlist_table(),
		array(
			'name'       => $name,
			'phone_enc'  => $enc,
			'phone_hash' => $hash,
			'ip_hash'    => $ip_hash,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s' )
	);
	wp_safe_redirect( add_query_arg( 'waitlist', 'ok', $redirect ) );
	exit;
}
add_action( 'admin_post_nopriv_teznevise_waitlist', 'teznevise_waitlist_handle' );
add_action( 'admin_post_teznevise_waitlist', 'teznevise_waitlist_handle' );

function teznevise_waitlist_admin_menu() {
	add_submenu_page(
		'tools.php',
		__( 'اطلاع‌رسانی ابزارها', 'teznevise' ),
		__( 'اطلاع‌رسانی ابزارها', 'teznevise' ),
		'manage_options',
		'teznevise-waitlist',
		'teznevise_waitlist_admin_page'
	);
}
add_action( 'admin_menu', 'teznevise_waitlist_admin_menu' );

function teznevise_waitlist_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	teznevise_waitlist_install();
	global $wpdb;
	$table = teznevise_waitlist_table();
	if ( isset( $_GET['export'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'teznevise_waitlist_csv' ) ) {
		$rows = $wpdb->get_results( 'SELECT * FROM ' . $table . ' ORDER BY id DESC', ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=teznevise-waitlist.csv' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'name', 'phone', 'created_at' ) );
		foreach ( (array) $rows as $row ) {
			$phone = class_exists( 'Teznevise_Key_Vault' ) ? Teznevise_Key_Vault::decrypt( $row['phone_enc'] ) : $row['phone_enc'];
			fputcsv( $out, array( $row['name'], $phone, $row['created_at'] ) );
		}
		fclose( $out );
		exit;
	}
	$rows  = $wpdb->get_results( 'SELECT * FROM ' . $table . ' ORDER BY id DESC LIMIT 500', ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$total = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$csv   = wp_nonce_url( add_query_arg( array( 'page' => 'teznevise-waitlist', 'export' => '1' ), admin_url( 'tools.php' ) ), 'teznevise_waitlist_csv' );
	echo '<div class="wrap"><h1>' . esc_html__( 'فهرست اطلاع‌رسانی ابزارها', 'teznevise' ) . '</h1>';
	echo '<p>' . esc_html( sprintf( __( '%s شماره ثبت شده. شماره‌ها رمزنگاری شده‌اند.', 'teznevise' ), number_format_i18n( $total ) ) ) . ' ';
	echo '<a class="button" href="' . esc_url( $csv ) . '">' . esc_html__( 'خروجی CSV', 'teznevise' ) . '</a></p>';
	echo '<table class="widefat striped"><thead><tr><th>نام</th><th>موبایل</th><th>زمان</th></tr></thead><tbody>';
	if ( ! $rows ) {
		echo '<tr><td colspan="3">' . esc_html__( 'هنوز کسی ثبت نشده.', 'teznevise' ) . '</td></tr>';
	}
	foreach ( (array) $rows as $row ) {
		$phone = class_exists( 'Teznevise_Key_Vault' ) ? Teznevise_Key_Vault::decrypt( $row['phone_enc'] ) : $row['phone_enc'];
		echo '<tr><td>' . esc_html( $row['name'] ) . '</td><td dir="ltr">' . esc_html( $phone ) . '</td><td>' . esc_html( $row['created_at'] ) . '</td></tr>';
	}
	echo '</tbody></table></div>';
}
