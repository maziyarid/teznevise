<?php
/**
 * Ask-AI panel on tools pages — Perplexity-style chat.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="section tools-ai">
	<div class="container">
		<?php
		if ( function_exists( 'teznevise_ai_shortcode' ) ) {
			echo teznevise_ai_shortcode( array( 'tool' => 'general' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( class_exists( 'TezNevise_AI_Chat' ) ) {
			echo TezNevise_AI_Chat::render_generic_chat( array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</div>
</section>
