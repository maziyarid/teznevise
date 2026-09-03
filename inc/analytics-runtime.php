<?php
/**
 * Analytics runtime compatibility.
 *
 * Keep Google Analytics / Google Tag Manager executable through first-party
 * Google Tag Gateway paths on teznevise.ir. Site Kit remains connected for
 * reports, while its Analytics and Tag Manager snippet placement is disabled
 * in WordPress settings to avoid duplicate third-party loaders.
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
 * Emit GA4 through Teznevise's first-party Google Tag Gateway path.
 */
function teznevise_output_ga4_tag() {
	if ( is_admin() ) {
		return;
	}
	?>
<!-- Google tag (gtag.js) - Teznevise first-party gateway -->
<script async src="/_tzga4/"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-ZTB0ERWJYN');
</script>
	<?php
}
add_action( 'wp_head', 'teznevise_output_ga4_tag', 1 );

/**
 * Emit Google Tag Manager through Teznevise's first-party gateway path.
 * The standard noscript iframe is intentionally omitted because Google Tag
 * Gateway does not support first-party noscript transport.
 */
function teznevise_output_gtm_tag() {
	if ( is_admin() ) {
		return;
	}
	?>
<!-- Google Tag Manager - Teznevise first-party gateway -->
<script>
(function(w,d,s,l){
	w[l]=w[l]||[];
	w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
	var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),
		dl=l!='dataLayer'?'&l='+l:'';
	j.async=true;
	j.src='/_tzgtm/?id=GTM-T32XKX5Z'+dl;
	f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer');
</script>
<!-- End Google Tag Manager -->
	<?php
}
add_action( 'wp_head', 'teznevise_output_gtm_tag', 2 );
