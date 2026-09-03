<?php
/**
 * Analytics runtime compatibility.
 *
 * Keep Google Analytics / Google Tag Manager executable and use the direct
 * GA4 measurement ID for collection. Site Kit remains connected for reports,
 * while its Analytics snippet placement is disabled in WordPress settings to
 * avoid duplicate hits.
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

/**
 * Emit the canonical GA4 tag directly instead of relying on a GT-prefixed
 * Google-tag destination indirection. This is intentionally minimal so it
 * matches the snippet validated by Google Analytics/Tag Assistant.
 */
function teznevise_output_ga4_tag() {
	if ( is_admin() ) {
		return;
	}
	?>
<!-- Google tag (gtag.js) - Teznevise direct GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZTB0ERWJYN"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-ZTB0ERWJYN');
</script>
	<?php
}
add_action( 'wp_head', 'teznevise_output_ga4_tag', 1 );
