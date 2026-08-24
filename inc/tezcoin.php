<?php
/**
 * Tezcoin wallet, rewards, and admin cost settings.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_tezcoin_defaults() {
	return array(
		'irr'             => 1000,
		'profile_bonus'   => 1000,
		'comment_reward'  => 20,
		'share_reward'    => 50,
		'referral_bonus'  => 200,
		'zarinpal'        => '',
		'aqaye_pin'       => '',
		'pack_1_coins'    => 500,
		'pack_1_irr'      => 450000,
		'pack_2_coins'    => 1200,
		'pack_2_irr'      => 1000000,
		'pack_3_coins'    => 3000,
		'pack_3_irr'      => 2200000,
		'enamad_url'      => '',
		'openrouter_key'  => '',
		'youcom_key'      => '',
		'tavily_key'      => '',
		'ai_cost'         => 5,
		'ga_id'           => '',
		'clarity_id'      => '',
	);
}

function teznevise_tezcoin_get( $key ) {
	$defaults = teznevise_tezcoin_defaults();
	$opt      = get_option( 'teznevise_tezcoin', array() );
	if ( ! is_array( $opt ) ) {
		$opt = array();
	}
	return isset( $opt[ $key ] ) ? $opt[ $key ] : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

function teznevise_tezcoin_balance( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( ! $user_id ) {
		return 0;
	}
	return (int) get_user_meta( $user_id, 'teznevise_tezcoin_balance', true );
}

function teznevise_tezcoin_credit( $user_id, $amount, $reason, $ref = '' ) {
	$user_id = (int) $user_id;
	$amount  = (int) $amount;
	if ( $user_id <= 0 || 0 === $amount ) {
		return false;
	}
	$balance = teznevise_tezcoin_balance( $user_id ) + $amount;
	update_user_meta( $user_id, 'teznevise_tezcoin_balance', $balance );
	$log   = get_user_meta( $user_id, 'teznevise_tezcoin_ledger', true );
	$log   = is_array( $log ) ? $log : array();
	$log[] = array(
		'amount' => $amount,
		'reason' => sanitize_text_field( $reason ),
		'ref'    => sanitize_text_field( $ref ),
		'time'   => time(),
	);
	if ( count( $log ) > 80 ) {
		$log = array_slice( $log, -80 );
	}
	update_user_meta( $user_id, 'teznevise_tezcoin_ledger', $log );
	return $balance;
}

/**
 * Every subscriber gets 30 welcome coins once.
 *
 * @param int $user_id User ID.
 */
function teznevise_maybe_welcome_coins( $user_id ) {
	$user_id = (int) $user_id;
	if ( $user_id <= 0 ) {
		return;
	}
	if ( get_user_meta( $user_id, 'teznevise_welcome_coins', true ) ) {
		return;
	}
	teznevise_tezcoin_credit( $user_id, 30, 'هدیه خوش‌آمد عضویت', 'welcome' );
	update_user_meta( $user_id, 'teznevise_welcome_coins', 1 );
}
add_action( 'user_register', 'teznevise_maybe_welcome_coins' );
add_action(
	'wp_login',
	static function ( $login, $user ) {
		if ( $user instanceof WP_User ) {
			teznevise_maybe_welcome_coins( $user->ID );
		}
	},
	20,
	2
);

function teznevise_profile_is_complete( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return false;
	}
	$name       = trim( $user->first_name );
	if ( strlen( $name ) < 2 ) {
		$name = trim( $user->display_name );
	}
	$phone      = get_user_meta( $user_id, 'teznevise_phone', true );
	$university = get_user_meta( $user_id, 'teznevise_university', true );
	$field      = get_user_meta( $user_id, 'teznevise_field', true );
	$degree     = get_user_meta( $user_id, 'teznevise_degree', true );
	return ( strlen( $name ) >= 2 && $phone && $university && $field && $degree );
}

function teznevise_maybe_profile_bonus( $user_id ) {
	if ( get_user_meta( $user_id, 'teznevise_tezcoin_bonus_granted', true ) ) {
		return;
	}
	if ( ! teznevise_profile_is_complete( $user_id ) ) {
		return;
	}
	$bonus = (int) teznevise_tezcoin_get( 'profile_bonus' );
	if ( $bonus <= 0 ) {
		$bonus = 1000;
	}
	teznevise_tezcoin_credit( $user_id, $bonus, 'هدیه تکمیل پروفایل', 'profile' );
	update_user_meta( $user_id, 'teznevise_tezcoin_bonus_granted', 1 );
	$ref = (int) get_user_meta( $user_id, 'teznevise_referred_by', true );
	if ( $ref && ! get_user_meta( $user_id, 'teznevise_referral_paid', true ) && $ref !== (int) $user_id ) {
		$gift = (int) teznevise_tezcoin_get( 'referral_bonus' );
		if ( $gift > 0 ) {
			teznevise_tezcoin_credit( $ref, $gift, 'پاداش معرفی دوست', (string) $user_id );
			teznevise_tezcoin_credit( $user_id, $gift, 'هدیه کد معرف', (string) $ref );
			update_user_meta( $user_id, 'teznevise_referral_paid', 1 );
		}
	}
}

function teznevise_tezcoin_admin_menu() {
	add_theme_page(
		__( 'تزکوین', 'teznevise' ),
		__( 'تزکوین', 'teznevise' ),
		'manage_options',
		'teznevise-tezcoin',
		'teznevise_tezcoin_admin_page'
	);
}
add_action( 'admin_menu', 'teznevise_tezcoin_admin_menu' );

function teznevise_tezcoin_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['teznevise_tezcoin_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['teznevise_tezcoin_nonce'] ) ), 'teznevise_tezcoin_save' ) ) {
		$save = array();
		foreach ( array_keys( teznevise_tezcoin_defaults() ) as $key ) {
			$save[ $key ] = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		}
		update_option( 'teznevise_tezcoin', $save, false );
		echo '<div class="updated"><p>' . esc_html__( 'تنظیمات تزکوین ذخیره شد.', 'teznevise' ) . '</p></div>';
	}
	$d = wp_parse_args( get_option( 'teznevise_tezcoin', array() ), teznevise_tezcoin_defaults() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'تزکوین و درگاه‌ها', 'teznevise' ); ?></h1>
		<p><?php esc_html_e( 'قیمت هر تزکوین، پاداش‌ها و کلید زرین‌پال / آقای پرداخت. با تکمیل پروفایل ۱۰۰۰ تزکوین هدیه داده می‌شود.', 'teznevise' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'teznevise_tezcoin_save', 'teznevise_tezcoin_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tr><th><label for="irr"><?php esc_html_e( 'قیمت هر تزکوین (ریال)', 'teznevise' ); ?></label></th>
					<td><input name="irr" id="irr" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['irr'] ); ?>"></td></tr>
				<tr><th><label for="profile_bonus"><?php esc_html_e( 'هدیه تکمیل پروفایل', 'teznevise' ); ?></label></th>
					<td><input name="profile_bonus" id="profile_bonus" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['profile_bonus'] ); ?>"></td></tr>
				<tr><th><label for="comment_reward"><?php esc_html_e( 'پاداش نظر', 'teznevise' ); ?></label></th>
					<td><input name="comment_reward" id="comment_reward" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['comment_reward'] ); ?>"></td></tr>
				<tr><th><label for="share_reward"><?php esc_html_e( 'پاداش اشتراک', 'teznevise' ); ?></label></th>
					<td><input name="share_reward" id="share_reward" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['share_reward'] ); ?>"></td></tr>
				<tr><th><label for="referral_bonus"><?php esc_html_e( 'پاداش معرفی', 'teznevise' ); ?></label></th>
					<td><input name="referral_bonus" id="referral_bonus" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['referral_bonus'] ); ?>"></td></tr>
				<tr><th><label for="zarinpal"><?php esc_html_e( 'مرچنت زرین‌پال', 'teznevise' ); ?></label></th>
					<td><input name="zarinpal" id="zarinpal" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['zarinpal'] ); ?>"></td></tr>
				<tr><th><label for="aqaye_pin"><?php esc_html_e( 'پین آقای پرداخت', 'teznevise' ); ?></label></th>
					<td><input name="aqaye_pin" id="aqaye_pin" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['aqaye_pin'] ); ?>"></td></tr>
				<tr><th><?php esc_html_e( 'بسته ۱', 'teznevise' ); ?></th>
					<td>سکه <input name="pack_1_coins" dir="ltr" value="<?php echo esc_attr( $d['pack_1_coins'] ); ?>"> ریال <input name="pack_1_irr" dir="ltr" value="<?php echo esc_attr( $d['pack_1_irr'] ); ?>"></td></tr>
				<tr><th><?php esc_html_e( 'بسته ۲', 'teznevise' ); ?></th>
					<td>سکه <input name="pack_2_coins" dir="ltr" value="<?php echo esc_attr( $d['pack_2_coins'] ); ?>"> ریال <input name="pack_2_irr" dir="ltr" value="<?php echo esc_attr( $d['pack_2_irr'] ); ?>"></td></tr>
				<tr><th><?php esc_html_e( 'بسته ۳', 'teznevise' ); ?></th>
					<td>سکه <input name="pack_3_coins" dir="ltr" value="<?php echo esc_attr( $d['pack_3_coins'] ); ?>"> ریال <input name="pack_3_irr" dir="ltr" value="<?php echo esc_attr( $d['pack_3_irr'] ); ?>"></td></tr>
				<tr><th><label for="enamad_url"><?php esc_html_e( 'لینک اینماد', 'teznevise' ); ?></label></th>
					<td><input name="enamad_url" id="enamad_url" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['enamad_url'] ); ?>"></td></tr>
				<tr><th><label for="openrouter_key"><?php esc_html_e( 'کلید OpenRouter', 'teznevise' ); ?></label></th>
					<td><input name="openrouter_key" id="openrouter_key" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['openrouter_key'] ); ?>"></td></tr>
				<tr><th><label for="youcom_key"><?php esc_html_e( 'کلید you.com', 'teznevise' ); ?></label></th>
					<td><input name="youcom_key" id="youcom_key" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['youcom_key'] ); ?>"></td></tr>
				<tr><th><label for="tavily_key"><?php esc_html_e( 'کلید Tavily', 'teznevise' ); ?></label></th>
					<td><input name="tavily_key" id="tavily_key" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['tavily_key'] ); ?>"></td></tr>
				<tr><th><label for="ai_cost"><?php esc_html_e( 'هزینه هر پرسش هوش مصنوعی (تزکوین)', 'teznevise' ); ?></label></th>
					<td><input name="ai_cost" id="ai_cost" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['ai_cost'] ); ?>"></td></tr>
				<tr><th><label for="ga_id"><?php esc_html_e( 'شناسه Google Analytics (G-…)', 'teznevise' ); ?></label></th>
					<td><input name="ga_id" id="ga_id" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['ga_id'] ); ?>"></td></tr>
				<tr><th><label for="clarity_id"><?php esc_html_e( 'شناسه Microsoft Clarity', 'teznevise' ); ?></label></th>
					<td><input name="clarity_id" id="clarity_id" class="regular-text" dir="ltr" value="<?php echo esc_attr( $d['clarity_id'] ); ?>"></td></tr>
			</table>
			<?php submit_button( __( 'ذخیره تزکوین', 'teznevise' ) ); ?>
		</form>
	</div>
	<?php
}

function teznevise_extra_profile_fields( $user ) {
	$keys = array(
		'teznevise_phone'      => 'موبایل',
		'teznevise_university' => 'دانشگاه',
		'teznevise_field'      => 'رشته',
		'teznevise_degree'     => 'مقطع',
		'teznevise_city'       => 'شهر',
		'teznevise_orcid'      => 'ORCID',
		'teznevise_telegram'   => 'تلگرام',
	);
	?>
	<h2><?php esc_html_e( 'پروفایل تزنویسه', 'teznevise' ); ?></h2>
	<table class="form-table">
		<?php foreach ( $keys as $key => $label ) : ?>
		<tr><th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td><input type="text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>" class="regular-text"></td></tr>
		<?php endforeach; ?>
		<tr><th><?php esc_html_e( 'تزکوین', 'teznevise' ); ?></th>
			<td><strong><?php echo esc_html( number_format_i18n( teznevise_tezcoin_balance( $user->ID ) ) ); ?></strong></td></tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'teznevise_extra_profile_fields' );
add_action( 'edit_user_profile', 'teznevise_extra_profile_fields' );

function teznevise_save_extra_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	foreach ( array( 'teznevise_phone', 'teznevise_university', 'teznevise_field', 'teznevise_degree', 'teznevise_city', 'teznevise_orcid', 'teznevise_telegram' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_user_meta( $user_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
	teznevise_maybe_profile_bonus( $user_id );
}
add_action( 'personal_options_update', 'teznevise_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'teznevise_save_extra_profile_fields' );

function teznevise_award_comment_coins( $comment_id, $approved ) {
	if ( 1 !== (int) $approved && '1' !== (string) $approved ) {
		return;
	}
	$comment = get_comment( $comment_id );
	if ( ! $comment || ! $comment->user_id ) {
		return;
	}
	$key = 'teznevise_comment_paid_' . (int) $comment->comment_post_ID;
	if ( get_user_meta( $comment->user_id, $key, true ) ) {
		return;
	}
	$reward = (int) teznevise_tezcoin_get( 'comment_reward' );
	if ( $reward <= 0 ) {
		return;
	}
	teznevise_tezcoin_credit( (int) $comment->user_id, $reward, 'نظر در بلاگ', (string) $comment->comment_post_ID );
	update_user_meta( $comment->user_id, $key, 1 );
}
add_action( 'comment_post', 'teznevise_award_comment_coins', 10, 2 );

function teznevise_ajax_share_reward() {
	check_ajax_referer( 'teznevise_share', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
	$net  = isset( $_POST['network'] ) ? sanitize_key( wp_unslash( $_POST['network'] ) ) : '';
	if ( ! $slug || ! $net ) {
		wp_send_json_error();
	}
	$user_id = get_current_user_id();
	$key     = 'teznevise_share_' . $slug . '_' . $net;
	if ( get_user_meta( $user_id, $key, true ) ) {
		wp_send_json_success( array( 'already' => true, 'balance' => teznevise_tezcoin_balance( $user_id ) ) );
	}
	$reward = (int) teznevise_tezcoin_get( 'share_reward' );
	teznevise_tezcoin_credit( $user_id, $reward, 'اشتراک مطلب', $slug . ':' . $net );
	update_user_meta( $user_id, $key, 1 );
	wp_send_json_success( array( 'balance' => teznevise_tezcoin_balance( $user_id ) ) );
}
add_action( 'wp_ajax_teznevise_share_reward', 'teznevise_ajax_share_reward' );

function teznevise_auto_approve_member_comments( $approved, $commentdata ) {
	if ( ! empty( $commentdata['user_ID'] ) || ! empty( $commentdata['user_id'] ) ) {
		return 1;
	}
	return $approved;
}
add_filter( 'pre_comment_approved', 'teznevise_auto_approve_member_comments', 10, 2 );

function teznevise_default_image_alt( $attachment_id ) {
	if ( ! wp_attachment_is_image( $attachment_id ) ) {
		return;
	}
	if ( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) {
		return;
	}
	$post = get_post( $attachment_id );
	if ( $post && $post->post_title ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $post->post_title );
	}
}
add_action( 'add_attachment', 'teznevise_default_image_alt' );

function teznevise_tracking_head() {
	// Third-party tracking is opt-in. This avoids console/DNS failures on
	// networks that block Google or Clarity and prevents accidental tracking
	// before consent. Enable deliberately in wp-config.php and set IDs in admin.
	$enabled = defined( 'TEZNEVISE_ENABLE_THIRD_PARTY_ANALYTICS' )
		? (bool) TEZNEVISE_ENABLE_THIRD_PARTY_ANALYTICS
		: false;
	if ( ! apply_filters( 'teznevise_enable_third_party_analytics', $enabled ) ) {
		return;
	}
	$ga      = (string) teznevise_tezcoin_get( 'ga_id' );
	$clarity = (string) teznevise_tezcoin_get( 'clarity_id' );
	if ( $ga && preg_match( '/^G-[A-Z0-9]+$/i', $ga ) ) {
		echo '<script type="text/plain" data-tz-delay="1" data-src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga ) . '"></script>';
		echo '<script type="text/plain" data-tz-delay-inline="gtag">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . esc_js( $ga ) . '");</script>' . "\n";
	}
	if ( $clarity && preg_match( '/^[A-Za-z0-9]+$/', $clarity ) ) {
		echo '<script type="text/plain" data-tz-delay-inline="clarity">(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","' . esc_js( $clarity ) . '");</script>' . "\n";
	}
}
add_action( 'wp_head', 'teznevise_tracking_head', 20 );

function teznevise_tezcoin_accounting_menu() {
	add_theme_page(
		__( 'حسابداری تزکوین', 'teznevise' ),
		__( 'حسابداری تزکوین', 'teznevise' ),
		'manage_options',
		'teznevise-ledger',
		'teznevise_tezcoin_accounting_page'
	);
}
add_action( 'admin_menu', 'teznevise_tezcoin_accounting_menu' );

function teznevise_tezcoin_accounting_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$users = get_users(
		array(
			'meta_key'     => 'teznevise_tezcoin_balance',
			'meta_compare' => '>',
			'meta_value'   => 0,
			'number'       => 50,
			'orderby'      => 'meta_value_num',
			'order'        => 'DESC',
		)
	);
	echo '<div class="wrap"><h1>' . esc_html__( 'دفتر حساب تزکوین', 'teznevise' ) . '</h1>';
	echo '<table class="widefat striped"><thead><tr><th>کاربر</th><th>مانده</th><th>آخرین تراکنش</th></tr></thead><tbody>';
	foreach ( $users as $u ) {
		$ledger = get_user_meta( $u->ID, 'teznevise_tezcoin_ledger', true );
		$last   = is_array( $ledger ) && $ledger ? end( $ledger ) : array();
		echo '<tr><td>' . esc_html( $u->display_name ) . '</td><td>' . esc_html( number_format_i18n( teznevise_tezcoin_balance( $u->ID ) ) ) . '</td><td>' . esc_html( isset( $last['reason'] ) ? $last['reason'] : '—' ) . '</td></tr>';
	}
	echo '</tbody></table></div>';
}

function teznevise_log_search_query() {
	if ( ! is_search() ) {
		return;
	}
	$q = trim( (string) get_search_query() );
	if ( strlen( $q ) < 2 ) {
		return;
	}
	$log = get_option( 'teznevise_search_log', array() );
	if ( ! is_array( $log ) ) {
		$log = array();
	}
	$log[ $q ] = isset( $log[ $q ] ) ? (int) $log[ $q ] + 1 : 1;
	if ( count( $log ) > 80 ) {
		arsort( $log );
		$log = array_slice( $log, 0, 80, true );
	}
	update_option( 'teznevise_search_log', $log, false );
}
add_action( 'wp', 'teznevise_log_search_query' );

function teznevise_popular_searches() {
	$log = get_option( 'teznevise_search_log', array() );
	if ( is_array( $log ) && $log ) {
		arsort( $log );
		$terms = array();
		foreach ( array_keys( $log ) as $term ) {
			$term = str_replace( array( 'پرپوزال', 'پزوپوزال' ), 'پروپوزال', (string) $term );
			if ( '' === trim( $term ) ) {
				continue;
			}
			$terms[] = $term;
			if ( count( $terms ) >= 6 ) {
				break;
			}
		}
		if ( $terms ) {
			return $terms;
		}
	}
	return array( 'پایان‌نامه', 'پروپوزال', 'SPSS', 'حجم نمونه', 'آلفای کرونباخ', 'فصل چهارم' );
}

function teznevise_localize_front_script() {
	wp_localize_script(
		'teznevise-chrome',
		'tezneviseProduct',
		array(
			'ajax'      => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'teznevise_share' ),
			'restUrl'   => esc_url_raw( rest_url() ),
			'restNonce' => wp_create_nonce( 'wp_rest' ),
			'logged'    => is_user_logged_in() ? 1 : 0,
		)
	);
}

function teznevise_strip_dead_shortcodes( $content ) {
	if ( is_admin() || ! is_string( $content ) ) {
		return $content;
	}
	$keep = function_exists( 'teznevise_interactive_shortcode_names' ) ? teznevise_interactive_shortcode_names() : array();
	return preg_replace_callback(
		'/\[[\/]?([a-zA-Z0-9_-]+)[^\]]*\]/',
		function ( $m ) use ( $keep ) {
			$name = strtolower( $m[1] );
			return in_array( $name, $keep, true ) ? $m[0] : '';
		},
		$content
	);
}
add_filter( 'the_content', 'teznevise_strip_dead_shortcodes', 8 );

function teznevise_ajax_ask_ai() {
	check_ajax_referer( 'teznevise_share', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'login' ) );
	}
	$q = isset( $_POST['q'] ) ? sanitize_textarea_field( wp_unslash( $_POST['q'] ) ) : '';
	if ( strlen( $q ) < 4 ) {
		wp_send_json_error( array( 'message' => 'short' ) );
	}
	$key = (string) teznevise_tezcoin_get( 'openrouter_key' );
	if ( ! $key ) {
		wp_send_json_error( array( 'message' => 'no-key' ) );
	}
	$res = wp_remote_post(
		'https://openrouter.ai/api/v1/chat/completions',
		array(
			'timeout' => 40,
			'headers' => array(
				'Authorization' => 'Bearer ' . $key,
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode(
				array(
					'model'      => 'openai/gpt-4o-mini',
					'max_tokens' => 700,
					'messages'   => array(
						array( 'role' => 'system', 'content' => 'You are a Persian research-methods tutor for Teznevise. Answer in Persian. Be concise.' ),
						array( 'role' => 'user', 'content' => $q ),
					),
				)
			),
		)
	);
	if ( is_wp_error( $res ) ) {
		wp_send_json_error( array( 'message' => $res->get_error_message() ) );
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	$text = isset( $body['choices'][0]['message']['content'] ) ? $body['choices'][0]['message']['content'] : '';
	if ( ! $text ) {
		wp_send_json_error( array( 'message' => 'empty' ) );
	}
	wp_send_json_success( array( 'text' => $text ) );
}
// Replaced by teznevise_ajax_ask_ai_v2 in inc/ai-agents.php (priority 1).
