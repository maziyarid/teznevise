<?php
/**
 * Front-end account: wallet, tickets, projects, payments, legal seed.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_seed_v171_pages() {
	if ( get_option( 'teznevise_seeded_1_7_1' ) ) {
		return;
	}
	$legal = function_exists( 'teznevise_legal_bodies' ) ? teznevise_legal_bodies() : array();
	$map   = array(
		'account'        => array( 'حساب کاربری', 'page-account.php', '' ),
		'terms'          => array( 'قوانین استفاده', 'page-privacy.php', isset( $legal['terms'] ) ? $legal['terms'] : '' ),
		'cookies'        => array( 'سیاست کوکی', 'page-privacy.php', isset( $legal['cookies'] ) ? $legal['cookies'] : '' ),
		'refund'         => array( 'بازپرداخت', 'page-privacy.php', isset( $legal['refund'] ) ? $legal['refund'] : '' ),
		'research-rules' => array( 'ضوابط پژوهش', 'page-privacy.php', isset( $legal['rules'] ) ? $legal['rules'] : '' ),
	);
	foreach ( $map as $slug => $row ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_title'   => $row[0],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $row[2],
			),
			true
		);
		if ( ! is_wp_error( $id ) && $row[1] ) {
			update_post_meta( $id, '_wp_page_template', $row[1] );
		}
	}
	update_option( 'teznevise_seeded_1_7_1', 1, false );
}
add_action( 'init', 'teznevise_seed_v171_pages', 30 );

function teznevise_register_service_cpts() {
	if ( ! post_type_exists( 'tz_ticket' ) ) {
		register_post_type(
			'tz_ticket',
			array(
				'labels'              => array(
					'name'          => __( 'تیکت‌ها', 'teznevise' ),
					'singular_name' => __( 'تیکت', 'teznevise' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'supports'            => array( 'title', 'editor', 'author', 'comments' ),
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'menu_icon'           => 'dashicons-sos',
			)
		);
	}
	if ( ! post_type_exists( 'tz_project' ) ) {
		register_post_type(
			'tz_project',
			array(
				'labels'          => array(
					'name'          => __( 'پروژه‌ها', 'teznevise' ),
					'singular_name' => __( 'پروژه', 'teznevise' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'supports'        => array( 'title', 'editor', 'author' ),
				'menu_icon'       => 'dashicons-portfolio',
			)
		);
	}
}
add_action( 'init', 'teznevise_register_service_cpts' );

function teznevise_vault_dir() {
	$upload = wp_upload_dir();
	$dir    = trailingslashit( $upload['basedir'] ) . 'teznevise-vault';
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
		file_put_contents( $dir . '/index.php', "<?php\n// Silence.\n" );
		file_put_contents( $dir . '/.htaccess', "Deny from all\n" );
	}
	return $dir;
}

function teznevise_handle_account_post() {
	if ( empty( $_POST['teznevise_account_action'] ) || ! is_user_logged_in() ) {
		return;
	}
	if ( ! isset( $_POST['_tz_acc'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_acc'] ) ), 'teznevise_account' ) ) {
		return;
	}
	$user_id = get_current_user_id();
	$action  = sanitize_key( wp_unslash( $_POST['teznevise_account_action'] ) );

	if ( 'profile' === $action ) {
		if ( isset( $_POST['first_name'] ) ) {
			$fn = sanitize_text_field( wp_unslash( $_POST['first_name'] ) );
			$ln = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
			wp_update_user(
				array(
					'ID'           => $user_id,
					'first_name'   => $fn,
					'last_name'    => $ln,
					'display_name' => trim( $fn . ' ' . $ln ),
				)
			);
		}
		foreach ( array( 'teznevise_phone', 'teznevise_university', 'teznevise_field', 'teznevise_degree', 'teznevise_city', 'teznevise_orcid', 'teznevise_telegram' ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_user_meta( $user_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
		if ( function_exists( 'teznevise_maybe_profile_bonus' ) ) {
			teznevise_maybe_profile_bonus( $user_id );
		}
		wp_safe_redirect( add_query_arg( array( 'saved' => '1', 'tab' => 'profile' ), home_url( '/account/' ) ) );
		exit;
	}

	if ( 'ticket' === $action ) {
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$body    = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
		if ( strlen( $subject ) < 4 || strlen( $body ) < 4 ) {
			return;
		}
		$tid = wp_insert_post(
			array(
				'post_type'    => 'tz_ticket',
				'post_status'  => 'private',
				'post_title'   => $subject,
				'post_content' => $body,
				'post_author'  => $user_id,
			),
			true
		);
		if ( ! is_wp_error( $tid ) && ! empty( $_FILES['vault']['name'] ) ) {
			teznevise_vault_save( $tid, $user_id );
		}
		wp_safe_redirect( add_query_arg( 'ticket', (string) $tid, home_url( '/account/' ) ) );
		exit;
	}

	if ( 'project' === $action ) {
		$title = isset( $_POST['project_title'] ) ? sanitize_text_field( wp_unslash( $_POST['project_title'] ) ) : '';
		$svc   = isset( $_POST['project_service'] ) ? sanitize_text_field( wp_unslash( $_POST['project_service'] ) ) : '';
		if ( strlen( $title ) < 4 ) {
			return;
		}
		$pid = wp_insert_post(
			array(
				'post_type'    => 'tz_project',
				'post_status'  => 'private',
				'post_title'   => $title,
				'post_content' => $svc,
				'post_author'  => $user_id,
			),
			true
		);
		if ( ! is_wp_error( $pid ) ) {
			update_post_meta( $pid, 'tz_status', 'intake' );
			update_post_meta( $pid, 'tz_progress', 8 );
		}
		wp_safe_redirect( add_query_arg( 'tab', 'projects', home_url( '/account/' ) ) );
		exit;
	}

	if ( 'buy' === $action ) {
		$pack = isset( $_POST['pack'] ) ? absint( $_POST['pack'] ) : 1;
		if ( $pack < 1 || $pack > 3 ) {
			$pack = 1;
		}
		teznevise_start_pack_payment( $user_id, $pack );
	}

	if ( 'referral' === $action ) {
		$code = isset( $_POST['ref_code'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['ref_code'] ) ) ) : '';
		if ( $code && ! get_user_meta( $user_id, 'teznevise_referred_by', true ) ) {
			$owners = get_users( array( 'meta_key' => 'teznevise_ref_code', 'meta_value' => $code, 'number' => 1, 'fields' => 'ID' ) );
			if ( $owners && (int) $owners[0] !== $user_id ) {
				update_user_meta( $user_id, 'teznevise_referred_by', (int) $owners[0] );
			}
		}
		wp_safe_redirect( add_query_arg( 'tab', 'wallet', home_url( '/account/' ) ) );
		exit;
	}
}
add_action( 'template_redirect', 'teznevise_handle_account_post' );

function teznevise_vault_save( $ticket_id, $user_id ) {
	if ( empty( $_FILES['vault']['tmp_name'] ) || ! is_uploaded_file( $_FILES['vault']['tmp_name'] ) ) {
		return;
	}
	$size = (int) $_FILES['vault']['size'];
	if ( $size <= 0 || $size > 2 * 1024 * 1024 ) {
		return;
	}
	$orig = sanitize_file_name( (string) $_FILES['vault']['name'] );
	$ext  = strtolower( pathinfo( $orig, PATHINFO_EXTENSION ) );
	$ok   = array( 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'zip' );
	if ( ! in_array( $ext, $ok, true ) ) {
		return;
	}
	$dir  = teznevise_vault_dir();
	$name = $ticket_id . '-' . wp_generate_uuid4() . '.' . $ext;
	$path = $dir . '/' . $name;
	if ( ! move_uploaded_file( $_FILES['vault']['tmp_name'], $path ) ) {
		return;
	}
	add_post_meta(
		$ticket_id,
		'tz_vault',
		array(
			'file' => $name,
			'orig' => $orig,
			'mime' => sanitize_text_field( (string) $_FILES['vault']['type'] ),
			'user' => (int) $user_id,
		)
	);
}

function teznevise_vault_download() {
	if ( empty( $_GET['tz_vault'] ) ) {
		return;
	}
	if ( ! is_user_logged_in() ) {
		auth_redirect();
	}
	$ticket_id = absint( $_GET['tz_vault'] );
	$file      = isset( $_GET['f'] ) ? sanitize_file_name( wp_unslash( $_GET['f'] ) ) : '';
	$ticket    = get_post( $ticket_id );
	if ( ! $ticket || 'tz_ticket' !== $ticket->post_type ) {
		wp_die( esc_html__( 'یافت نشد', 'teznevise' ), '', array( 'response' => 404 ) );
	}
	$user_id = get_current_user_id();
	if ( (int) $ticket->post_author !== $user_id && ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( esc_html__( 'دسترسی ندارید', 'teznevise' ), '', array( 'response' => 403 ) );
	}
	$path = teznevise_vault_dir() . '/' . $file;
	if ( ! $file || false !== strpos( $file, '/' ) || false !== strpos( $file, '..' ) || ! is_file( $path ) ) {
		wp_die( esc_html__( 'فایل نیست', 'teznevise' ), '', array( 'response' => 404 ) );
	}
	$owned = false;
	foreach ( (array) get_post_meta( $ticket_id, 'tz_vault' ) as $meta ) {
		if ( is_array( $meta ) && ! empty( $meta['file'] ) && hash_equals( (string) $meta['file'], $file ) ) {
			$owned = true;
			break;
		}
	}
	if ( ! $owned ) {
		wp_die( esc_html__( 'دسترسی ندارید', 'teznevise' ), '', array( 'response' => 403 ) );
	}
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename="' . rawurlencode( $file ) . '"' );
	header( 'X-Content-Type-Options: nosniff' );
	readfile( $path );
	exit;
}
add_action( 'template_redirect', 'teznevise_vault_download', 1 );

function teznevise_user_ref_code( $user_id ) {
	$code = get_user_meta( $user_id, 'teznevise_ref_code', true );
	if ( $code ) {
		return $code;
	}
	$code = strtoupper( substr( md5( 'tz' . $user_id ), 0, 8 ) );
	update_user_meta( $user_id, 'teznevise_ref_code', $code );
	return $code;
}

function teznevise_start_pack_payment( $user_id, $pack ) {
	$coins    = (int) teznevise_tezcoin_get( 'pack_' . $pack . '_coins' );
	$irr      = (int) teznevise_tezcoin_get( 'pack_' . $pack . '_irr' );
	$merchant = (string) teznevise_tezcoin_get( 'zarinpal' );
	$pin      = (string) teznevise_tezcoin_get( 'aqaye_pin' );
	$callback = add_query_arg( 'tz_pay', '1', home_url( '/account/' ) );

	if ( $merchant ) {
		$res = wp_remote_post(
			'https://api.zarinpal.com/pg/v4/payment/request.json',
			array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'timeout' => 20,
				'body'    => wp_json_encode(
					array(
						'merchant_id'  => $merchant,
						'amount'       => $irr,
						'callback_url' => $callback,
						'description'  => 'Tezcoin pack ' . $pack,
						'metadata'     => array( 'order_id' => $user_id . '-' . $pack ),
					)
				),
			)
		);
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		$auth = isset( $body['data']['authority'] ) ? $body['data']['authority'] : '';
		if ( $auth ) {
			update_user_meta(
				$user_id,
				'teznevise_pending_pay',
				array(
					'pack'      => $pack,
					'coins'     => $coins,
					'irr'       => $irr,
					'authority' => $auth,
					'gateway'   => 'zarinpal',
				)
			);
			wp_redirect( 'https://www.zarinpal.com/pg/StartPay/' . rawurlencode( $auth ) );
			exit;
		}
	}

	if ( $pin ) {
		$res = wp_remote_post(
			'https://panel.aqayepardakht.ir/api/v2/create',
			array(
				'timeout' => 20,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode(
					array(
						'pin'        => $pin,
						'amount'     => (int) round( $irr / 10 ),
						'callback'   => $callback,
						'invoice_id' => $user_id . '-' . time(),
					)
				),
			)
		);
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		$tid  = isset( $body['transid'] ) ? $body['transid'] : '';
		if ( $tid ) {
			update_user_meta(
				$user_id,
				'teznevise_pending_pay',
				array(
					'pack'    => $pack,
					'coins'   => $coins,
					'irr'     => $irr,
					'transid' => $tid,
					'gateway' => 'aqaye',
				)
			);
			wp_redirect( 'https://panel.aqayepardakht.ir/startpay/' . rawurlencode( (string) $tid ) );
			exit;
		}
	}

	wp_safe_redirect( add_query_arg( 'pay', 'need-gateway', home_url( '/account/' ) ) );
	exit;
}

function teznevise_handle_payment_return() {
	if ( empty( $_GET['tz_pay'] ) || ! is_user_logged_in() ) {
		return;
	}
	$user_id = get_current_user_id();
	$pending = get_user_meta( $user_id, 'teznevise_pending_pay', true );
	if ( ! is_array( $pending ) ) {
		return;
	}
	$ok = false;
	if ( 'zarinpal' === ( $pending['gateway'] ?? '' ) ) {
		$status    = isset( $_GET['Status'] ) ? sanitize_text_field( wp_unslash( $_GET['Status'] ) ) : '';
		$authority = isset( $_GET['Authority'] ) ? sanitize_text_field( wp_unslash( $_GET['Authority'] ) ) : '';
		if ( 'OK' === $status && $authority && $authority === ( $pending['authority'] ?? '' ) ) {
			$res  = wp_remote_post(
				'https://api.zarinpal.com/pg/v4/payment/verify.json',
				array(
					'headers' => array( 'Content-Type' => 'application/json' ),
					'timeout' => 20,
					'body'    => wp_json_encode(
						array(
							'merchant_id' => teznevise_tezcoin_get( 'zarinpal' ),
							'amount'      => (int) $pending['irr'],
							'authority'   => $authority,
						)
					),
				)
			);
			$body = json_decode( wp_remote_retrieve_body( $res ), true );
			$code = isset( $body['data']['code'] ) ? (int) $body['data']['code'] : 0;
			$ok   = ( 100 === $code || 101 === $code );
		}
	}
	if ( 'aqaye' === ( $pending['gateway'] ?? '' ) ) {
		$tid = $pending['transid'] ?? '';
		$res = wp_remote_post(
			'https://panel.aqayepardakht.ir/api/v2/verify',
			array(
				'timeout' => 20,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode(
					array(
						'pin'     => teznevise_tezcoin_get( 'aqaye_pin' ),
						'amount'  => (int) round( ( (int) $pending['irr'] ) / 10 ),
						'transid' => $tid,
					)
				),
			)
		);
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		$ok   = ! empty( $body['code'] ) && (int) $body['code'] >= 1;
	}
	if ( $ok ) {
		teznevise_tezcoin_credit( $user_id, (int) $pending['coins'], 'خرید بسته تزکوین', (string) ( $pending['pack'] ?? '' ) );
		delete_user_meta( $user_id, 'teznevise_pending_pay' );
		wp_safe_redirect( add_query_arg( 'pay', 'ok', home_url( '/account/' ) ) );
		exit;
	}
	wp_safe_redirect( add_query_arg( 'pay', 'fail', home_url( '/account/' ) ) );
	exit;
}
add_action( 'template_redirect', 'teznevise_handle_payment_return', 2 );

function teznevise_capture_referral() {
	if ( empty( $_GET['ref'] ) ) {
		return;
	}
	$code = strtoupper( sanitize_text_field( wp_unslash( $_GET['ref'] ) ) );
	if ( $code ) {
		setcookie( 'teznevise_ref', $code, time() + WEEK_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	}
}
add_action( 'init', 'teznevise_capture_referral', 1 );

function teznevise_on_user_register( $user_id ) {
	teznevise_user_ref_code( $user_id );
	$code = isset( $_COOKIE['teznevise_ref'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_COOKIE['teznevise_ref'] ) ) ) : '';
	if ( ! $code ) {
		return;
	}
	$owners = get_users( array( 'meta_key' => 'teznevise_ref_code', 'meta_value' => $code, 'number' => 1, 'fields' => 'ID' ) );
	if ( $owners && (int) $owners[0] !== (int) $user_id ) {
		update_user_meta( $user_id, 'teznevise_referred_by', (int) $owners[0] );
	}
}
add_action( 'user_register', 'teznevise_on_user_register' );

function teznevise_project_status_label( $status ) {
	$map = array(
		'intake'     => 'ثبت اولیه',
		'brief'      => 'بررسی موضوع',
		'drafting'   => 'نگارش',
		'analysis'   => 'تحلیل',
		'revision'   => 'اصلاح',
		'defense'    => 'آمادگی دفاع',
		'delivered'  => 'تحویل',
	);
	return isset( $map[ $status ] ) ? $map[ $status ] : $status;
}

function teznevise_project_metaboxes() {
	add_meta_box( 'tz_project_track', __( 'پیگیری پروژه', 'teznevise' ), 'teznevise_project_metabox_html', 'tz_project', 'side' );
	add_meta_box( 'tz_ticket_track', __( 'وضعیت تیکت', 'teznevise' ), 'teznevise_ticket_metabox_html', 'tz_ticket', 'side' );
}
add_action( 'add_meta_boxes', 'teznevise_project_metaboxes' );

function teznevise_project_metabox_html( $post ) {
	wp_nonce_field( 'tz_project_track', 'tz_project_track_nonce' );
	$st = get_post_meta( $post->ID, 'tz_status', true );
	$pr = (int) get_post_meta( $post->ID, 'tz_progress', true );
	echo '<p><label>وضعیت<br><select name="tz_status">';
	foreach ( array( 'intake', 'brief', 'drafting', 'analysis', 'revision', 'defense', 'delivered' ) as $k ) {
		echo '<option value="' . esc_attr( $k ) . '" ' . selected( $st, $k, false ) . '>' . esc_html( teznevise_project_status_label( $k ) ) . '</option>';
	}
	echo '</select></label></p>';
	echo '<p><label>پیشرفت %<br><input type="number" min="0" max="100" name="tz_progress" value="' . esc_attr( $pr ) . '"></label></p>';
}

function teznevise_ticket_metabox_html( $post ) {
	wp_nonce_field( 'tz_ticket_track', 'tz_ticket_track_nonce' );
	$st = get_post_meta( $post->ID, 'tz_ticket_status', true );
	if ( ! $st ) {
		$st = 'open';
	}
	echo '<p><select name="tz_ticket_status">';
	foreach ( array( 'open' => 'باز', 'pending' => 'در انتظار پاسخ', 'closed' => 'بسته' ) as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '" ' . selected( $st, $k, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></p>';
}

function teznevise_save_project_track( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$type = get_post_type( $post_id );
	if ( 'tz_project' === $type ) {
		if ( ! isset( $_POST['tz_project_track_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tz_project_track_nonce'] ) ), 'tz_project_track' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['tz_status'] ) ) {
			update_post_meta( $post_id, 'tz_status', sanitize_key( wp_unslash( $_POST['tz_status'] ) ) );
		}
		if ( isset( $_POST['tz_progress'] ) ) {
			update_post_meta( $post_id, 'tz_progress', min( 100, max( 0, absint( $_POST['tz_progress'] ) ) ) );
		}
	}
	if ( 'tz_ticket' === $type ) {
		if ( ! isset( $_POST['tz_ticket_track_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tz_ticket_track_nonce'] ) ), 'tz_ticket_track' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['tz_ticket_status'] ) ) {
			update_post_meta( $post_id, 'tz_ticket_status', sanitize_key( wp_unslash( $_POST['tz_ticket_status'] ) ) );
		}
	}
}
add_action( 'save_post', 'teznevise_save_project_track' );
