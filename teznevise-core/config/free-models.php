<?php
/**
 * Editable free-first OpenRouter model map.
 *
 * Swap IDs here when the OpenRouter free tier changes. Logic in
 * Teznevise_Model_Router reads this array and never hardcodes model names.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deterministic free-model catalog.
 *
 * @return array<string,string>
 */
function teznevise_core_free_models() {
	$stored = get_option( 'teznevise_core_free_models', array() );
	$defaults = array(
		'simple'         => 'openai/gpt-oss-20b:free',
		'medium'         => 'meta-llama/llama-3.3-70b-instruct:free',
		'complex'        => 'qwen/qwen-2.5-72b-instruct:free',
		'long_context'   => 'nvidia/llama-3.1-nemotron-70b-instruct:free',
		'reasoning'      => 'deepseek/deepseek-r1:free',
		'paid_fallback'  => '',
		'openrouter_base'=> 'https://openrouter.ai/api/v1/chat/completions',
	);
	if ( is_array( $stored ) ) {
		$defaults = array_merge( $defaults, array_filter( $stored, 'is_string' ) );
	}
	return apply_filters( 'teznevise_core_free_models', $defaults );
}
