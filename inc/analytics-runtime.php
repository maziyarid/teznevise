<?php
/**
 * Analytics runtime compatibility.
 *
 * Google Analytics / Google Tag Manager must remain executable. Site Kit adds
 * attributes to its gtag loader and the generic third-party delay filter can
 * otherwise leave that loader as an inert text/plain script. Keep only truly
 * non-essential trackers on the delayed path.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Replace the broad tracker-delay filter registered by inc/perf.php.
remove_filter( 'script_loader_tag', 'teznevise_delay_tracker_scripts', 20 );

/**
 * Delay non-essential trackers without delaying Google Analytics or GTM.
 *
 * @param string $tag    Script tag.
 * @param string $handle Handle.
 * @param string $src    Source URL.
 * @return string
 */
function teznevise_delay_nonessential_tracker_scripts( $tag, $handle, $src ) {
	if ( ! is_string( $src ) || '' === $src ) {
		return $tag;
	}

	if ( ! preg_match( '/clarity\.ms|ywxi\.net/i', $src ) ) {
		return $tag;
	}

	$src = esc_url( $src );
	return '<script type="text/plain" data-tz-delay="1" data-src="' . $src . '"></script>';
}
add_filter( 'script_loader_tag', 'teznevise_delay_nonessential_tracker_scripts', 20, 3 );
