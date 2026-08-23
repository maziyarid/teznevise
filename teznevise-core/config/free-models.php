<?php
/**
 * Editable free-first OpenRouter model map.
 *
 * Swap IDs here when the OpenRouter free tier changes. Logic in
 * Teznevise_Model_Router reads this array and never hardcodes model names.
 *
 * Verified against OpenRouter /api/v1/models on 2026-08-23. The previous
 * llama-3.3 / gpt-oss-20b / qwen-2.5 / deepseek-r1 `:free` IDs no longer exist
 * and 404, which produced the live `llm_fail` cascade.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Model IDs that OpenRouter no longer serves on the free tier.
 *
 * @return string[]
 */
function teznevise_core_retired_free_models() {
	return array(
		'openai/gpt-oss-20b:free',
		'meta-llama/llama-3.3-70b-instruct:free',
		'qwen/qwen-2.5-72b-instruct:free',
		'nvidia/llama-3.1-nemotron-70b-instruct:free',
		'deepseek/deepseek-r1:free',
		'deepseek/deepseek-r1-0528:free',
		'meta-llama/llama-3.1-8b-instruct:free',
		'google/gemma-2-9b-it:free',
		'google/gemma-3-27b-it:free',
		'google/gemma-3-12b-it:free',
		'google/gemma-3-4b-it:free',
		'google/gemma-2-27b-it:free',
	);
}

/**
 * Deterministic free-model catalog.
 *
 * @return array<string,string>
 */
function teznevise_core_free_models() {
	$defaults = array(
		'simple'          => 'google/gemma-4-31b-it:free',
		'medium'          => 'z-ai/glm-5.2:free',
		'complex'         => 'nvidia/nemotron-3-super-120b-a12b:free',
		'long_context'    => 'nvidia/nemotron-3.5-lightning:free',
		'reasoning'       => 'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning:free',
		'paid_fallback'   => '',
		'openrouter_base' => 'https://openrouter.ai/api/v1/chat/completions',
	);
	$stored  = get_option( 'teznevise_core_free_models', array() );
	$retired = teznevise_core_retired_free_models();
	if ( is_array( $stored ) ) {
		foreach ( $stored as $key => $value ) {
			if ( ! is_string( $value ) || '' === $value ) {
				continue;
			}
			if ( in_array( $value, $retired, true ) ) {
				continue;
			}
			if ( isset( $defaults[ $key ] ) && ! in_array( $key, array( 'paid_fallback', 'openrouter_base' ), true ) && false === strpos( $value, ':free' ) ) {
				continue;
			}
			$defaults[ $key ] = $value;
		}
	}
	return apply_filters( 'teznevise_core_free_models', $defaults );
}

/**
 * Drop retired IDs from the stored option so admin UI matches live models.
 */
function teznevise_core_migrate_free_models() {
	$stored = get_option( 'teznevise_core_free_models', array() );
	if ( ! is_array( $stored ) || ! $stored ) {
		return;
	}
	$retired = teznevise_core_retired_free_models();
	$clean   = $stored;
	foreach ( $clean as $key => $value ) {
		if ( is_string( $value ) && in_array( $value, $retired, true ) ) {
			unset( $clean[ $key ] );
		}
	}
	if ( $clean !== $stored ) {
		update_option( 'teznevise_core_free_models', $clean, false );
	}
}
add_action( 'init', 'teznevise_core_migrate_free_models', 4 );
