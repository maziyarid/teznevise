<?php
/**
 * Dedicated failure log for research / LLM cascade (admin review only).
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Logger {
	const OPTION = 'teznevise_core_ai_log';
	const MAX    = 80;

	public static function log( $code, $message, $context = array() ) {
		$row  = array(
			'time'    => time(),
			'code'    => sanitize_key( $code ),
			'message' => sanitize_text_field( (string) $message ),
			'context' => is_array( $context ) ? array_map( 'sanitize_text_field', $context ) : array(),
		);
		$log  = get_option( self::OPTION, array() );
		$log  = is_array( $log ) ? $log : array();
		$log[] = $row;
		if ( count( $log ) > self::MAX ) {
			$log = array_slice( $log, -self::MAX );
		}
		update_option( self::OPTION, $log, false );
	}

	public static function all() {
		$log = get_option( self::OPTION, array() );
		return is_array( $log ) ? $log : array();
	}
}
