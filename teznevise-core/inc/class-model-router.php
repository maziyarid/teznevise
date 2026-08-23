<?php
/**
 * Deterministic free-first model selection. Never asks a model to rate itself.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Model_Router {

	public static function complexity( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$words = preg_split( '/\s+/u', trim( $text ) );
		$words = is_array( $words ) ? array_filter( $words ) : array();
		$count = count( $words );
		$debate = (bool) preg_match( '/analyze|debate|compare|تحلیل|بحث|مقایسه|نقد|واکاوی/iu', $text );
		if ( $count > 1200 || ( $debate && $count > 600 ) ) {
			return 'complex';
		}
		if ( $count > 400 || $debate ) {
			return 'medium';
		}
		return 'simple';
	}

	public static function pick( $text, $job = 'answer' ) {
		$catalog = function_exists( 'teznevise_core_free_models' ) ? teznevise_core_free_models() : array();
		$level   = self::complexity( $text );
		if ( 'debate' === $job ) {
			return $catalog['reasoning'] ?: ( $catalog['complex'] ?? '' );
		}
		if ( 'long' === $job || ( 'complex' === $level && strlen( $text ) > 8000 ) ) {
			return $catalog['long_context'] ?: ( $catalog['complex'] ?? '' );
		}
		return $catalog[ $level ] ?? ( $catalog['medium'] ?? '' );
	}

	/**
	 * Ordered attempts: agent's own model, then free cascade, then paid fallback.
	 *
	 * @param array  $agent Agent row.
	 * @param string $text  Prompt corpus.
	 * @param string $job   answer|debate|long|research.
	 * @return array<int,array{provider:string,model:string}>
	 */
	public static function chain( $agent, $text, $job = 'answer' ) {
		$catalog = function_exists( 'teznevise_core_free_models' ) ? teznevise_core_free_models() : array();
		$chain   = array();
		$seen    = array();
		$push    = static function ( $provider, $model ) use ( &$chain, &$seen ) {
			$model = trim( (string) $model );
			if ( '' === $model ) {
				return;
			}
			if ( function_exists( 'teznevise_core_retired_free_models' ) && in_array( $model, teznevise_core_retired_free_models(), true ) ) {
				return;
			}
			$key = $provider . '|' . $model;
			if ( isset( $seen[ $key ] ) ) {
				return;
			}
			$seen[ $key ] = true;
			$chain[]      = array(
				'provider' => $provider,
				'model'    => $model,
			);
		};

		$own_provider = sanitize_key( $agent['provider'] ?? 'openai' );
		$own_model    = (string) ( $agent['model'] ?? '' );
		$own_is_free  = ( false !== strpos( $own_model, ':free' ) );

		$agent_id = sanitize_key( $agent['agent_id'] ?? '' );
		if ( $agent_id && function_exists( 'teznevise_core_agent_models' ) && function_exists( 'teznevise_core_named_ids' ) && in_array( $agent_id, teznevise_core_named_ids(), true ) ) {
			$assigned = teznevise_core_agent_models( $agent_id );
			$push( 'openrouter', $assigned['primary'] ?? '' );
			$push( 'openrouter', $assigned['fallback'] ?? '' );
		}

		$free = self::pick( $text, $job );
		$push( 'openrouter', $free );
		foreach ( array( 'simple', 'medium', 'complex', 'long_context', 'reasoning' ) as $slot ) {
			$push( 'openrouter', $catalog[ $slot ] ?? '' );
		}
		if ( $own_is_free ) {
			$push( $own_provider, $own_model );
		}
		$push( $own_provider, $own_model );
		if ( ! empty( $catalog['paid_fallback'] ) ) {
			$push( 'openrouter', $catalog['paid_fallback'] );
		}
		return $chain;
	}

	public static function sleep_backoff( $attempt ) {
		$ms = (int) ( 200 * pow( 2, max( 0, (int) $attempt ) ) );
		usleep( min( 2500000, $ms * 1000 ) );
	}
}
