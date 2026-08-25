<?php
/**
 * Instant first paint: one stylesheet, inlined critical CSS, delayed third-parties.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ordered public CSS that must keep last-wins cascade.
 *
 * @return string[] Relative to theme root.
 */
function teznevise_runtime_css_files() {
	$files = array(
		'assets/css/tokens.css',
		'assets/css/components.css',
		'assets/css/pages.css',
		'assets/css/chrome.css',
		'assets/css/modernization.css',
		'assets/css/hotfix-196.css',
		'assets/css/hotfix-197.css',
		'assets/css/hotfix-198.css',
		'assets/css/hotfix-199.css',
		'assets/css/hotfix-200.css',
		'assets/css/hotfix-201.css',
		'assets/css/hotfix-202.css',
		'assets/css/hotfix-203.css',
		'assets/css/hotfix-204.css',
		'assets/css/hotfix-205.css',
		'assets/css/hotfix-206.css',
		'assets/css/hotfix-207.css',
		'assets/css/hotfix-208.css',
		'assets/css/hotfix-209.css',
		'assets/css/hotfix-210.css',
	);
	return array_values(
		array_filter(
			$files,
			static function ( $rel ) {
				return is_readable( TEZNEVISE_DIR . '/' . $rel );
			}
		)
	);
}

/**
 * Combined runtime stylesheet URL (cached in uploads).
 *
 * @return array{url:string,ver:string}|null
 */
function teznevise_runtime_stylesheet() {
	$files = teznevise_runtime_css_files();
	if ( ! $files ) {
		return null;
	}
	$stamp = TEZNEVISE_VERSION;
	foreach ( $files as $rel ) {
		$stamp .= '|' . $rel . ':' . (int) filemtime( TEZNEVISE_DIR . '/' . $rel );
	}
	$hash   = substr( md5( $stamp ), 0, 12 );
	$upload = wp_upload_dir();
	if ( ! empty( $upload['error'] ) ) {
		return null;
	}
	$dir  = trailingslashit( $upload['basedir'] ) . 'teznevise-cache';
	$file = $dir . '/runtime-' . $hash . '.css';
	$url  = trailingslashit( $upload['baseurl'] ) . 'teznevise-cache/runtime-' . $hash . '.css';
	if ( ! is_readable( $file ) ) {
		if ( ! wp_mkdir_p( $dir ) ) {
			return null;
		}
		$css = "/* teznevise runtime {$hash} */\n";
		foreach ( $files as $rel ) {
			$chunk = (string) file_get_contents( TEZNEVISE_DIR . '/' . $rel );
			$chunk = preg_replace( '#url\((["\']?)\.\./fonts/#', 'url($1' . TEZNEVISE_URI . '/assets/fonts/', $chunk );
			$css  .= "\n/* {$rel} */\n" . teznevise_minify_css( $chunk );
		}
		if ( false === file_put_contents( $file, $css, LOCK_EX ) ) {
			return null;
		}
		$index = $dir . '/index.php';
		if ( ! is_readable( $index ) ) {
			file_put_contents( $index, "<?php\n// Silence.\n" );
		}
	}
	return array(
		'url' => $url,
		'ver' => $hash,
	);
}

/**
 * Conservative CSS minify (comments + whitespace). Keeps calc() spaces.
 *
 * @param string $css Raw CSS.
 * @return string
 */
function teznevise_minify_css( $css ) {
	$css = (string) $css;
	$css = preg_replace( '#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#', '', $css );
	$css = preg_replace( '/\s+/', ' ', $css );
	return trim( (string) $css );
}

/** Inline critical CSS before any render-blocking styles. */
function teznevise_print_critical_css() {
	$path = TEZNEVISE_DIR . '/assets/css/critical.css';
	if ( ! is_readable( $path ) ) {
		return;
	}
	$css = (string) file_get_contents( $path );
	$css = preg_replace( '/\s+/', ' ', $css );
	$font = TEZNEVISE_URI . '/assets/fonts/Vazirmatn-Regular.woff2';
	$face = '@font-face{font-family:"Vazirmatn";src:url("' . esc_url( $font ) . '") format("woff2");font-weight:400;font-style:normal;font-display:swap}';
	echo '<style id="teznevise-critical">' . $face . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'teznevise_print_critical_css', 1 );

/**
 * Replace the many render-blocking theme CSS files with one async bundle.
 */
function teznevise_enqueue_runtime_css() {
	if ( is_admin() ) {
		return;
	}
	$handles = array(
		'teznevise-style',
		'teznevise-tokens',
		'teznevise-components',
		'teznevise-pages',
		'teznevise-chrome',
		'teznevise-modernization',
		'teznevise-hotfix-196',
		'teznevise-hotfix-197',
	);
	foreach ( $handles as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	$runtime = teznevise_runtime_stylesheet();
	if ( $runtime ) {
		wp_enqueue_style( 'teznevise-runtime', $runtime['url'], array(), $runtime['ver'] );
	} else {
		foreach ( teznevise_runtime_css_files() as $rel ) {
			wp_enqueue_style(
				'teznevise-' . sanitize_key( basename( $rel, '.css' ) ),
				TEZNEVISE_URI . '/' . $rel,
				array(),
				TEZNEVISE_VERSION
			);
		}
	}

	$fa_rel = '/assets/vendor/fontawesome/css/all.min.css';
	if ( is_readable( TEZNEVISE_DIR . $fa_rel ) ) {
		wp_enqueue_style( 'teznevise-fontawesome', TEZNEVISE_URI . $fa_rel, array(), TEZNEVISE_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_runtime_css', 120 );

/**
 * Make theme CSS non-blocking. Critical CSS already painted the header/hero.
 *
 * @param string $tag    Link tag.
 * @param string $handle Style handle.
 * @return string
 */
function teznevise_async_styles( $tag, $handle ) {
	$async = array(
		'teznevise-runtime',
		'teznevise-fontawesome',
		'teznevise-legacy-wpcode',
		'teznevise-tokens',
		'teznevise-components',
		'teznevise-pages',
		'teznevise-chrome',
		'teznevise-modernization',
		'teznevise-hotfix-196',
		'teznevise-hotfix-197',
		'teznevise-hotfix-198',
		'teznevise-ai',
		'teznevise-ai-chat',
	);
	if ( 0 === strpos( $handle, 'teznevise-service-' ) ) {
		$async[] = $handle;
	}
	if ( ! in_array( $handle, $async, true ) ) {
		return $tag;
	}
	// Never concatenate a second link tag. HTML minifiers unwrap noscript
	// fallbacks and the leftover copy is render-blocking (live PSI: CSS loaded twice).
	$tag = preg_replace( "/\smedia=(['\"])[^'\"]*\\1/", '', $tag );
	if ( false !== strpos( $tag, "rel='stylesheet'" ) ) {
		$tag = str_replace( "rel='stylesheet'", "rel='stylesheet' media='print' onload=\"this.onload=null;this.media='all'\"", $tag );
	} elseif ( false !== strpos( $tag, 'rel="stylesheet"' ) ) {
		$tag = str_replace( 'rel="stylesheet"', 'rel="stylesheet" media="print" onload="this.onload=null;this.media=\'all\'"', $tag );
	}
	return $tag;
}
add_filter( 'style_loader_tag', 'teznevise_async_styles', 20, 2 );

/** Drop core ballast that never paints the first screen. */
function teznevise_trim_front_payload() {
	if ( is_admin() ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
	wp_dequeue_script( 'wp-embed' );
	wp_deregister_script( 'wp-embed' );
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'teznevise_trim_front_payload', 100 );

/**
 * Delay GTM / Clarity / GA until idle or first input.
 *
 * @param string $tag    Script tag.
 * @param string $handle Handle.
 * @param string $src    Source URL.
 * @return string
 */
function teznevise_delay_tracker_scripts( $tag, $handle, $src ) {
	if ( ! is_string( $src ) || '' === $src ) {
		return $tag;
	}
	if ( ! preg_match( '/googletagmanager|google-analytics|clarity\.ms|gtag\/js|ywxi\.net/i', $src ) ) {
		return $tag;
	}
	$src = esc_url( $src );
	return '<script type="text/plain" data-tz-delay="1" data-src="' . $src . '"></script>';
}
add_filter( 'script_loader_tag', 'teznevise_delay_tracker_scripts', 20, 3 );

/** Boot delayed trackers after first interaction or 4s idle. */
function teznevise_print_delay_loader() {
	if ( is_admin() ) {
		return;
	}
	echo '<script id="teznevise-delay-3p">(function(){function go(){if(window.__tz3p)return;window.__tz3p=1;document.querySelectorAll("script[data-tz-delay-inline]").forEach(function(s){try{Function(s.textContent)();}catch(e){}});document.querySelectorAll("script[data-tz-delay]").forEach(function(s){var src=s.getAttribute("data-src");if(!src)return;var n=document.createElement("script");n.src=src;n.async=true;s.parentNode.replaceChild(n,s);});}["pointerdown","keydown","scroll","touchstart"].forEach(function(e){addEventListener(e,go,{once:true,passive:true});});if("requestIdleCallback"in window)requestIdleCallback(go,{timeout:4000});else setTimeout(go,4000);})();</script>' . "\n";
}
add_action( 'wp_footer', 'teznevise_print_delay_loader', 1 );

/**
 * Do not dns-prefetch GTM before it is needed.
 *
 * @param array  $urls URLs.
 * @param string $rel  Hint type.
 * @return array
 */
function teznevise_filter_resource_hints( $urls, $rel ) {
	if ( 'dns-prefetch' !== $rel && 'preconnect' !== $rel ) {
		return $urls;
	}
	return array_values(
		array_filter(
			(array) $urls,
			static function ( $url ) {
				return false === strpos( (string) $url, 'googletagmanager' ) && false === strpos( (string) $url, 'google-analytics' ) && false === strpos( (string) $url, 'clarity.ms' );
			}
		)
	);
}
add_filter( 'wp_resource_hints', 'teznevise_filter_resource_hints', 20, 2 );

/**
 * Preload the logo (home LCP) plus the runtime CSS and the Regular font.
 *
 * @param array $resources Preload list.
 * @return array
 */
function teznevise_perf_preloads( $resources ) {
	if ( function_exists( 'teznevise_logo_url' ) && teznevise_logo_url() ) {
		$resources[] = array(
			'href'          => teznevise_logo_url(),
			'as'            => 'image',
			'fetchpriority' => 'high',
		);
	}
	$runtime = teznevise_runtime_stylesheet();
	if ( $runtime ) {
		$resources[] = array(
			'href' => $runtime['url'] . '?ver=' . $runtime['ver'],
			'as'   => 'style',
		);
	}
	if ( is_singular( 'post' ) && has_post_thumbnail() ) {
		$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'teznevise-hero' );
		if ( $img ) {
			$resources[] = array(
				'href'          => $img[0],
				'as'            => 'image',
				'fetchpriority' => 'high',
			);
		}
	}
	return $resources;
}
add_filter( 'wp_preload_resources', 'teznevise_perf_preloads', 20 );

/**
 * Keep LCP images eager; lazy-load the rest. Stops Edge "lazy placeholder" interventions
 * when the first image is in the viewport.
 *
 * @param array  $attr    Attributes.
 * @param string $tag     Tag name.
 * @param string $context Context.
 * @return array
 */
function teznevise_loading_attrs( $attr, $tag, $context ) {
	if ( 'img' !== $tag ) {
		return $attr;
	}
	if ( isset( $attr['fetchpriority'] ) && 'high' === $attr['fetchpriority'] ) {
		$attr['loading']  = 'eager';
		$attr['decoding'] = 'async';
		return $attr;
	}
	$attr['loading']  = 'lazy';
	$attr['decoding'] = 'async';
	return $attr;
}
add_filter( 'wp_get_loading_optimization_attributes', 'teznevise_loading_attrs', 10, 3 );

/** Calculators only on tool pages (not every URL). */
function teznevise_page_needs_calculators() {
	if ( is_front_page() || is_home() ) {
		return false;
	}
	if ( is_page_template( 'page-tool.php' ) || is_page_template( 'page-tools.php' ) ) {
		return true;
	}
	if ( ! is_singular() ) {
		return false;
	}
	$post = get_post();
	if ( ! $post instanceof WP_Post ) {
		return false;
	}
	$hay = $post->post_content . ' ' . (string) get_post_meta( $post->ID, '_teznevise_builder_sections', true ) . ' ' . $post->post_name;
	return (bool) preg_match( '/\[tz(ss|pc|t|c|ca|hub|_price|_calculation|_sample|_cronbach|_pearson|_cvr|_power|_spearman|_ttest|_descriptive|_kr20|_cohens|_anova|_mann|_wilcoxon|_kruskal|_regression|_chi|_goodness|_icc)/i', $hay );
}
