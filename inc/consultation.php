<?php
/**
 * Consultation request endpoint for service landing pages.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle consultation requests from the service-page lead card.
 */
function teznevise_handle_consultation_request() {
	check_ajax_referer( 'teznevise_consultation', 'nonce' );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$degree  = isset( $_POST['degree'] ) ? sanitize_text_field( wp_unslash( $_POST['degree'] ) ) : '';
	$field   = isset( $_POST['field'] ) ? sanitize_text_field( wp_unslash( $_POST['field'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || '' === $phone || '' === $field || '' === $message ) {
		wp_send_json_error(
			array( 'message' => __( 'لطفاً همه فیلدهای ضروری را کامل کنید.', 'teznevise' ) ),
			400
		);
	}

	if ( ! preg_match( '/^[0-9۰-۹+()\-\s]{7,20}$/u', $phone ) ) {
		wp_send_json_error(
			array( 'message' => __( 'لطفاً شماره تماس معتبر وارد کنید.', 'teznevise' ) ),
			400
		);
	}

	$subject = sprintf(
		/* translators: %s: requester name */
		__( 'درخواست مشاوره جدید از %s', 'teznevise' ),
		$name
	);
	$body = implode(
		"\n",
		array(
			'نام: ' . $name,
			'شماره تماس: ' . $phone,
			'مقطع: ' . ( $degree ? $degree : '—' ),
			'رشته / گرایش: ' . $field,
			'توضیحات: ' . $message,
		)
	);

	$sent = wp_mail( get_option( 'admin_email' ), $subject, $body );
	if ( ! $sent ) {
		wp_send_json_error(
			array( 'message' => __( 'ارسال درخواست انجام نشد. لطفاً بعداً دوباره تلاش کنید.', 'teznevise' ) ),
			500
		);
	}

	wp_send_json_success(
		array( 'message' => __( 'درخواست شما با موفقیت ارسال شد. به‌زودی با شما تماس می‌گیریم.', 'teznevise' ) )
	);
}
add_action( 'wp_ajax_teznevise_consultation', 'teznevise_handle_consultation_request' );
add_action( 'wp_ajax_nopriv_teznevise_consultation', 'teznevise_handle_consultation_request' );
