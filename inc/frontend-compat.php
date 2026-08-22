<?php
/**
 * Frontend compatibility after the HTML → WordPress migration.
 *
 * - Stops Gutenberg/global-styles from restyling the classic theme.
 * - Restores leftover WPCode / Gravity Forms shortcodes as designed UI.
 * - Normalizes curly quotes so shortcodes WordPress texturized still parse.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$teznevise_legacy = TEZNEVISE_DIR . '/inc/legacy-wpcode.php';
if ( is_readable( $teznevise_legacy ) ) {
	require_once $teznevise_legacy;
}

/**
 * Disable theme.json layout CSS (contentSize was shrinking classic pages).
 */
function teznevise_disable_layout_styles() {
	add_theme_support( 'disable-layout-styles' );
}
add_action( 'after_setup_theme', 'teznevise_disable_layout_styles', 20 );

/**
 * Dequeue block-library / global styles that fight the HTML design system.
 */
function teznevise_dequeue_block_styles() {
	if ( is_admin() ) {
		return;
	}
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'core-block-supports' );
	wp_dequeue_style( 'wp-block-library-inline' );
}
add_action( 'wp_enqueue_scripts', 'teznevise_dequeue_block_styles', 100 );

/**
 * Extra styles that stay outside the four public bundles.
 * 1.8.5: header-form / page-extras / wp-compat are folded into components/pages.
 * legacy-wpcode and service sheets load only when the page actually needs them.
 */
function teznevise_enqueue_compat_assets() {
	$need_calc = is_page_template( 'page-tool.php' ) || is_page_template( 'page-tools.php' );
	$haystack = '';
	if ( is_singular() ) {
		$post = get_post();
		if ( $post instanceof WP_Post ) {
			$haystack  = (string) $post->post_content;
			$haystack .= ' ' . (string) get_post_meta( $post->ID, '_teznevise_builder_sections', true );
			$haystack .= ' ' . (string) $post->post_name;
		}
	}

	if ( $haystack && preg_match( '/tzss-|tzpc-|tzt-|tzc-|tzca-|tzhub-|tz-careers|tz_price|tz_calculation|tz_sample|tz_cronbach|tz_pearson|tz_cvr|tz_power|tz_spearman|tz_ttest|tz_descriptive|tz_kr20|tz_cohens|tz_anova|tz_mann|tz_wilcoxon|tz_kruskal|tz_regression|tz_chi|tz_goodness|tz_icc|calculator|gravityform/i', $haystack ) ) {
		$need_calc = true;
	}

	if ( $need_calc ) {
		wp_enqueue_style(
			'teznevise-legacy-wpcode',
			TEZNEVISE_URI . '/assets/css/legacy-wpcode.css',
			array(),
			TEZNEVISE_VERSION
		);
		if ( ! wp_script_is( 'teznevise-calculators', 'enqueued' ) ) {
			wp_enqueue_script(
				'teznevise-calculators',
				TEZNEVISE_URI . '/assets/js/calculators.js',
				array(),
				TEZNEVISE_VERSION,
				true
			);
			wp_script_add_data( 'teznevise-calculators', 'strategy', 'defer' );
		}
	}

	if ( is_singular() && ! is_front_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		$map  = array(
			'service-thesis'     => 'service-thesis.css',
			'thesis'             => 'service-thesis.css',
			'service-proposal'   => 'service-thesis.css',
			'service-statistics' => 'service-statistics.css',
			'statistics'         => 'service-statistics.css',
			'service-simulation' => 'service-simulation.css',
			'simulation'         => 'service-simulation.css',
		);
		if ( isset( $map[ $slug ] ) ) {
				wp_enqueue_style(
					'teznevise-service-' . sanitize_key( $slug ),
					TEZNEVISE_URI . '/assets/css/' . $map[ $slug ],
					array( 'teznevise-modernization' ),
				TEZNEVISE_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'teznevise_enqueue_compat_assets', 30 );

/**
 * Map leftover slug pages onto the matching designed templates.
 *
 * @param string $template Current template path.
 * @return string
 */
function teznevise_legacy_template_include( $template ) {
	if ( ! is_page() ) {
		return $template;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$map  = array(
		'contact-us' => 'page-contact.php',
		'inquiry'    => 'page-contact.php',
		'order'      => 'page-contact.php',
	);
	if ( isset( $map[ $slug ] ) ) {
		$located = locate_template( $map[ $slug ] );
		if ( $located ) {
			return $located;
		}
	}
	return $template;
}
add_filter( 'template_include', 'teznevise_legacy_template_include', 40 );

/**
 * Whether post content is only a leftover shortcode.
 *
 * @param string $content Raw post content.
 * @return bool
 */
function teznevise_is_legacy_shortcode_content( $content ) {
	$plain = trim( wp_strip_all_tags( (string) $content ) );
	$plain = html_entity_decode( $plain, ENT_QUOTES, 'UTF-8' );
	$plain = preg_replace( '/\s+/u', ' ', $plain );
	$plain = teznevise_normalize_shortcode_quotes( $plain );
	if ( ! is_string( $plain ) || $plain === '' ) {
		return false;
	}
	return (bool) preg_match( '/^\[[a-zA-Z][a-zA-Z0-9_]*(?:\s[^\]]*)?\]$/u', $plain );
}

/**
 * Convert curly / smart quotes inside shortcode tags so they still parse.
 *
 * @param string $content Content.
 * @return string
 */
function teznevise_normalize_shortcode_quotes( $content ) {
	if ( ! is_string( $content ) || $content === '' ) {
		return $content;
	}
	return preg_replace_callback(
		'/\[[^\[\]]{1,200}\]/u',
		static function ( $m ) {
			$s = html_entity_decode( $m[0], ENT_QUOTES, 'UTF-8' );
			$s = strtr(
				$s,
				array(
					'“' => '"',
					'”' => '"',
					'„' => '"',
					'‟' => '"',
					'″' => '"',
					'«' => '"',
					'»' => '"',
					'‘' => "'",
					'’' => "'",
					'‚' => "'",
					'′' => "'",
				)
			);
			return $s;
		},
		$content
	);
}
add_filter( 'the_content', 'teznevise_normalize_shortcode_quotes', 8 );
add_filter( 'the_content', 'teznevise_normalize_shortcode_quotes', 10 );
add_filter( 'widget_text', 'teznevise_normalize_shortcode_quotes', 8 );
add_filter( 'widget_text', 'do_shortcode', 11 );

/**
 * Do not texturize leftover plugin shortcodes.
 *
 * @param array $tags Shortcode tags.
 * @return array
 */
function teznevise_no_texturize_shortcodes( $tags ) {
	$extra = array( 'gravityform', 'teznevise_blog', 'teznevise_downloads', 'tz_careers_terms', 'tz_join_form', 'tz_home' );
	return array_values( array_unique( array_merge( (array) $tags, $extra ) ) );
}
add_filter( 'no_texturize_shortcodes', 'teznevise_no_texturize_shortcodes' );

/**
 * Gravity Forms fallback so [gravityform] never prints as raw text.
 *
 * @param array|string $atts Shortcode attributes.
 * @return string
 */
function teznevise_gravityform_fallback( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'id'    => '',
			'title' => 'true',
		),
		$atts,
		'gravityform'
	);
	return teznevise_render_native_lead_form( 'gravity-' . sanitize_key( (string) $atts['id'] ) );
}

/**
 * Register fallbacks only when the original plugin shortcode is missing.
 */
function teznevise_register_missing_shortcodes() {
	if ( ! shortcode_exists( 'gravityform' ) ) {
		add_shortcode( 'gravityform', 'teznevise_gravityform_fallback' );
	}
	if ( ! shortcode_exists( 'teznevise_blog' ) ) {
		add_shortcode( 'teznevise_blog', 'teznevise_blog_shortcode_fallback' );
	}
	if ( ! shortcode_exists( 'tz_home' ) ) {
		add_shortcode( 'tz_home', '__return_empty_string' );
	}
	if ( ! shortcode_exists( 'tz_join_form' ) ) {
		add_shortcode(
			'tz_join_form',
			static function () {
				return teznevise_render_native_lead_form( 'join' );
			}
		);
	}

	$hubs = array(
		'tz_thesis_hub',
		'tz_thesis_phd',
		'tz_thesis_ch1',
		'tz_thesis_ch2',
		'tz_thesis_ch3',
		'tz_thesis_ch4',
		'tz_thesis_ch5',
		'tz_thesis_engineering',
		'tz_thesis_humanities',
		'tz_thesis_medhealth',
		'tz_thesis_purescience',
		'tz_thesis_interdisciplinary',
		'tz_thesis_agriculture',
		'tz_thesis_animal_vet',
		'tz_thesis_art_arch_media',
		'tz_thesis_intl',
		'tz_thesis_psychology',
		'tz_thesis_history',
		'tz_thesis_philosophy',
		'tz_thesis_social_sciences',
		'tz_thesis_law',
		'tz_thesis_management',
		'tz_proposal_hub',
		'tz_proposal_phd',
		'tz_proposal_master',
		'tz_proposal_project',
		'tz_proposal_qual',
		'tz_proposal_quan',
		'tz_proposal_applied',
		'tz_proposal_medical',
		'tz_proposal_english',
		'tz_statistics_hub',
		'tz_simulation_hub',
	);
	foreach ( $hubs as $tag ) {
		if ( ! shortcode_exists( $tag ) ) {
			add_shortcode( $tag, 'teznevise_hub_shortcode_fallback' );
		}
	}
}
add_action( 'init', 'teznevise_register_missing_shortcodes', 20 );

/**
 * Blog shortcode fallback: recent posts grid.
 *
 * @return string
 */
function teznevise_blog_shortcode_fallback() {
	if ( is_home() ) {
		return '';
	}
	ob_start();
	$q = new WP_Query(
		array(
			'posts_per_page'      => 3,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	echo '<div class="article-grid tz-shortcode-fallback">';
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			get_template_part( 'template-parts/post-card' );
		}
		wp_reset_postdata();
	} else {
		echo '<p class="blog-archive__empty"><a class="btn-tz btn-primary-tz" href="' . esc_url( function_exists( 'teznevise_posts_url' ) ? teznevise_posts_url() : home_url( '/' ) ) . '">' . esc_html__( 'مشاهده مطالب مرکز دانش', 'teznevise' ) . '</a></p>';
	}
	echo '</div>';
	return ob_get_clean();
}

/**
 * Designed landing for leftover WPCode hub shortcodes.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Enclosed content.
 * @param string $tag     Shortcode tag.
 * @return string
 */
function teznevise_hub_shortcode_fallback( $atts = array(), $content = '', $tag = '' ) {
	return teznevise_render_service_fallback( get_the_title(), $tag );
}

/**
 * Native lead form markup (contact / inquiry / gravity fallback).
 *
 * @param string $context Form context slug.
 * @return string
 */
function teznevise_render_native_lead_form( $context = 'contact' ) {
	$title = get_the_title();
	$uid   = sanitize_html_class( $context );
	$ok      = isset( $_GET['lead'] ) && in_array( sanitize_key( wp_unslash( $_GET['lead'] ) ), array( 'ok', 'queued' ), true ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$error   = isset( $_GET['lead'] ) ? sanitize_key( wp_unslash( $_GET['lead'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$queued  = 'queued' === $error;
	ob_start();
	if ( $ok ) :
		?>
	<div class="lead-card tz-lead-thanks" role="status">
		<p><strong><?php echo esc_html( $queued ? __( 'درخواست شما ذخیره شد.', 'teznevise' ) : __( 'درخواست شما ثبت شد.', 'teznevise' ) ); ?></strong></p>
		<p><?php echo esc_html( $queued ? __( 'ارسال ایمیل با تأخیر روبه‌رو شد؛ درخواست در پنل ذخیره شده و کارشناس تزنویسه در ساعات کاری با شما تماس می‌گیرد.', 'teznevise' ) : __( 'کارشناس تزنویسه در ساعات کاری (شنبه تا پنجشنبه، ۹ تا ۲۱) با شما تماس می‌گیرد. اطلاعات فقط برای بررسی همین درخواست استفاده می‌شود.', 'teznevise' ) ); ?></p>
	</div>
		<?php
		return ob_get_clean();
	endif;
	if ( in_array( $error, array( 'err', 'rate' ), true ) ) :
		?>
	<div class="account-flash is-warn" role="alert">
		<?php echo esc_html( 'rate' === $error ? __( 'تعداد درخواست‌ها زیاد است؛ لطفاً یک دقیقه دیگر دوباره تلاش کنید.', 'teznevise' ) : __( 'نام و شماره موبایل معتبر را بررسی و دوباره ارسال کنید.', 'teznevise' ) ); ?>
	</div>
		<?php
	endif;
	?>
	<form class="tz-form lead-card" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-test="<?php echo esc_attr( $uid ); ?>-form" data-tz-lead>
		<input type="hidden" name="action" value="teznevise_lead" />
		<?php wp_nonce_field( 'teznevise_lead', 'teznevise_lead_nonce' ); ?>
		<input type="hidden" name="context" value="<?php echo esc_attr( $context ); ?>" />
		<input type="hidden" name="_tz_redirect" value="<?php echo esc_url( get_permalink() ? get_permalink() : home_url( '/inquiry/' ) ); ?>" />
		<label class="tz-honeypot" aria-hidden="true" tabindex="-1">Website<input name="website" type="text" tabindex="-1" autocomplete="off" /></label>
		<div class="form-grid">
			<div class="field">
				<label for="tz-name-<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'نام و نام خانوادگی', 'teznevise' ); ?></label>
				<input id="tz-name-<?php echo esc_attr( $uid ); ?>" name="name" type="text" required autocomplete="name" maxlength="80" />
			</div>
			<div class="field">
				<label for="tz-phone-<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'شماره موبایل', 'teznevise' ); ?></label>
				<input id="tz-phone-<?php echo esc_attr( $uid ); ?>" name="phone" type="tel" required autocomplete="tel" inputmode="tel" dir="ltr" placeholder="0912xxxxxxx" pattern="^(?:\+98|0)?9\d{9}$" />
			</div>
			<div class="field full">
				<label for="tz-service-<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'نوع خدمت', 'teznevise' ); ?></label>
				<select id="tz-service-<?php echo esc_attr( $uid ); ?>" name="service">
					<option value="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></option>
					<option value="پایان‌نامه"><?php esc_html_e( 'انجام پایان‌نامه', 'teznevise' ); ?></option>
					<option value="پروپوزال"><?php esc_html_e( 'انجام پروپوزال', 'teznevise' ); ?></option>
					<option value="تحلیل آماری"><?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?></option>
					<option value="شبیه‌سازی"><?php esc_html_e( 'شبیه‌سازی', 'teznevise' ); ?></option>
				</select>
			</div>
			<div class="field full">
				<label for="tz-msg-<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'شرح کوتاه پروژه', 'teznevise' ); ?></label>
				<textarea id="tz-msg-<?php echo esc_attr( $uid ); ?>" name="message" rows="5" maxlength="2000" placeholder="<?php esc_attr_e( 'رشته، مقطع، موضوع و مرحله فعلی را بنویسید.', 'teznevise' ); ?>"></textarea>
			</div>
		</div>
		<button class="btn-tz btn-primary-tz btn-lg-tz" type="submit">
			<?php esc_html_e( 'ارسال درخواست', 'teznevise' ); ?>
		</button>
		<p class="privacy-note"><?php esc_html_e( 'اطلاعات فقط برای بررسی درخواست در تزنویسه ذخیره می‌شود و در نشانی صفحه نمایش داده نمی‌شود. مشاوره اولیه رایگان است.', 'teznevise' ); ?></p>
	</form>
	<script>
	(function(){
		var form = document.querySelector('[data-test="<?php echo esc_js( $uid ); ?>-form"]');
		if (!form) return;
		var phone = form.querySelector('[name="phone"]');
		if (!phone) return;
		phone.addEventListener('input', function () {
			var map = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9','٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
			phone.value = phone.value.replace(/[۰-۹٠-٩]/g, function (d) { return map[d] || d; });
		});
	})();
	</script>
	<?php
	return ob_get_clean();
}

/**
 * Compact inquiry form for page heroes. Posts to the same first-party lead handler.
 *
 * @param string $context Form context slug.
 * @return string
 */
function teznevise_render_hero_inquiry( $context = 'hero' ) {
	if ( is_page( array( 'account', 'contact-us', 'contact', 'inquiry' ) ) || is_page_template( 'page-account.php' ) || is_page_template( 'page-contact.php' ) ) {
		return '';
	}
	$title = get_the_title();
	$uid   = sanitize_html_class( $context . '-' . (int) get_the_ID() );
	$ok    = isset( $_GET['lead'] ) && in_array( sanitize_key( wp_unslash( $_GET['lead'] ) ), array( 'ok', 'queued' ), true ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$error = isset( $_GET['lead'] ) ? sanitize_key( wp_unslash( $_GET['lead'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	ob_start();
	echo '<div class="tz-hero-inquiry">';
	if ( $ok ) {
		echo '<p class="tz-hero-inquiry__ok" role="status"><strong>' . esc_html__( 'درخواست شما ثبت شد. کارشناس تزنویسه تماس می‌گیرد.', 'teznevise' ) . '</strong></p>';
		echo '</div>';
		return ob_get_clean();
	}
	if ( in_array( $error, array( 'err', 'rate' ), true ) ) {
		echo '<p class="account-flash is-warn" role="alert">' . esc_html( 'rate' === $error ? __( 'تعداد درخواست‌ها زیاد است؛ لطفاً کمی بعد دوباره تلاش کنید.', 'teznevise' ) : __( 'نام و شماره موبایل را بررسی کنید.', 'teznevise' ) ) . '</p>';
	}
	?>
	<p class="tz-hero-inquiry__label"><?php esc_html_e( 'مشاوره رایگان — همین حالا درخواست بدهید', 'teznevise' ); ?></p>
	<form class="tz-form tz-hero-inquiry__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-tz-lead data-test="hero-<?php echo esc_attr( $uid ); ?>">
		<input type="hidden" name="action" value="teznevise_lead" />
		<?php wp_nonce_field( 'teznevise_lead', 'teznevise_lead_nonce' ); ?>
		<input type="hidden" name="context" value="<?php echo esc_attr( 'hero-' . sanitize_key( $context ) ); ?>" />
		<input type="hidden" name="service" value="<?php echo esc_attr( $title ); ?>" />
		<input type="hidden" name="_tz_redirect" value="<?php echo esc_url( get_permalink() ? get_permalink() : home_url( '/inquiry/' ) ); ?>" />
		<label class="tz-honeypot" aria-hidden="true" tabindex="-1">Website<input name="website" type="text" tabindex="-1" autocomplete="off" /></label>
		<div class="form-grid">
			<div class="field">
				<label for="tz-h-name-<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'نام', 'teznevise' ); ?></label>
				<input id="tz-h-name-<?php echo esc_attr( $uid ); ?>" name="name" type="text" required autocomplete="name" maxlength="80" />
			</div>
			<div class="field">
				<label for="tz-h-phone-<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'موبایل', 'teznevise' ); ?></label>
				<input id="tz-h-phone-<?php echo esc_attr( $uid ); ?>" name="phone" type="tel" required autocomplete="tel" inputmode="tel" dir="ltr" placeholder="0912xxxxxxx" pattern="^(?:\+98|0)?9\d{9}$" />
			</div>
			<div class="field full">
				<label for="tz-h-msg-<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'موضوع کوتاه', 'teznevise' ); ?></label>
				<input id="tz-h-msg-<?php echo esc_attr( $uid ); ?>" name="message" type="text" maxlength="240" placeholder="<?php esc_attr_e( 'رشته، مقطع و موضوع', 'teznevise' ); ?>" />
			</div>
			<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'ارسال درخواست', 'teznevise' ); ?></button>
		</div>
	</form>
	<?php
	echo '</div>';
	return ob_get_clean();
}

/**
 * First-party lead intake (no WhatsApp GET, no PII in the URL).
 */
function teznevise_handle_lead() {
	$nonce = isset( $_POST['teznevise_lead_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['teznevise_lead_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'teznevise_lead' ) ) {
		wp_die( esc_html__( 'درخواست نامعتبر است. لطفاً دوباره تلاش کنید.', 'teznevise' ), '', array( 'response' => 400 ) );
	}
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? ( function_exists( 'teznevise_iran_mobile' ) ? teznevise_iran_mobile( wp_unslash( $_POST['phone'] ) ) : sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$redirect = isset( $_POST['_tz_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['_tz_redirect'] ) ) : home_url( '/inquiry/' );
	$home_host = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	if ( $home_host !== (string) wp_parse_url( $redirect, PHP_URL_HOST ) ) {
		$redirect = home_url( '/inquiry/' );
	}
	$honeypot = isset( $_POST['website'] ) ? trim( (string) wp_unslash( $_POST['website'] ) ) : '';
	if ( '' !== $honeypot ) {
		wp_safe_redirect( add_query_arg( 'lead', 'ok', $redirect ) );
		exit;
	}
	$client_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$rate_hash = substr( hash_hmac( 'sha256', $client_ip, wp_salt( 'nonce' ) ), 0, 32 );
	if ( '' === $name || '' === $phone ) {
		$invalid_key   = 'tez_lead_bad_' . $rate_hash;
		$invalid_count = (int) get_transient( $invalid_key );
		if ( $invalid_count >= 20 ) {
			wp_safe_redirect( add_query_arg( 'lead', 'rate', $redirect ) );
			exit;
		}
		set_transient( $invalid_key, $invalid_count + 1, MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'lead', 'err', $redirect ) );
		exit;
	}
	$rate_key   = 'tez_lead_' . $rate_hash;
	$rate_count = (int) get_transient( $rate_key );
	if ( $rate_count >= 3 ) {
		wp_safe_redirect( add_query_arg( 'lead', 'rate', $redirect ) );
		exit;
	}
	set_transient( $rate_key, $rate_count + 1, MINUTE_IN_SECONDS );
	$to        = function_exists( 'teznevise_get_contact' ) ? teznevise_get_contact( 'email' ) : get_option( 'admin_email' );
	$subject   = 'درخواست جدید تزنویسه — ' . $service;
	$body      = "نام: {$name}\nتلفن: {$phone}\nخدمت: {$service}\n\n{$message}\n";
	$mail_sent = wp_mail( $to ? $to : get_option( 'admin_email' ), $subject, $body );
	$stored    = get_option( 'teznevise_leads', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}
	$stored = array_values(
		array_filter(
			$stored,
			static function ( $lead ) {
				return is_array( $lead ) && isset( $lead['time'] ) && (int) $lead['time'] >= time() - ( 90 * DAY_IN_SECONDS );
			}
		)
	);
	$stored[] = array(
		'time'    => time(),
		'name'    => $name,
		'phone'   => $phone,
		'service' => $service,
		'message' => $message,
		'mail'    => (bool) $mail_sent,
	);
	if ( count( $stored ) > 200 ) {
		$stored = array_slice( $stored, -200 );
	}
	update_option( 'teznevise_leads', $stored, false );
	if ( $mail_sent ) {
		delete_option( 'teznevise_lead_mail_failures' );
		wp_safe_redirect( add_query_arg( 'lead', 'ok', $redirect ) );
		exit;
	}
	update_option( 'teznevise_lead_mail_failures', (int) get_option( 'teznevise_lead_mail_failures', 0 ) + 1, false );
	update_option( 'teznevise_lead_last_mail_fail', time(), false );
	wp_safe_redirect( add_query_arg( 'lead', 'queued', $redirect ) );
	exit;
}
add_action( 'admin_post_teznevise_lead', 'teznevise_handle_lead' );
add_action( 'admin_post_nopriv_teznevise_lead', 'teznevise_handle_lead' );

/**
 * Surface undelivered leads to administrators so stored-but-unsent rows are not silent.
 */
function teznevise_lead_mail_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$failures = (int) get_option( 'teznevise_lead_mail_failures', 0 );
	if ( $failures <= 0 ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html(
		sprintf(
			/* translators: %d: number of failed mail attempts */
			_n(
				'%d lead was stored but email delivery failed. Review Appearance → Teznevise leads / the teznevise_leads option and retry sending.',
				'%d leads were stored but email delivery failed. Review Appearance → Teznevise leads / the teznevise_leads option and retry sending.',
				$failures,
				'teznevise'
			),
			$failures
		)
	);
	echo '</p></div>';
}
add_action( 'admin_notices', 'teznevise_lead_mail_admin_notice' );

/**
 * Hub copy based on leftover shortcode tag.
 *
 * @param string $tag Shortcode tag.
 * @return array{eyebrow:string,lead:string,bullets:array<int,string>,inquiry:string}
 */
function teznevise_hub_copy( $tag ) {
	$tag = strtolower( (string) $tag );
	$inquiry = home_url( '/inquiry/' );
	$base    = array(
		'eyebrow' => __( 'مشاوره تخصصی', 'teznevise' ),
		'lead'    => __( 'مسیر، زمان و برآورد اولیه را با کارشناسان تزنویسه بررسی کنید. مشاوره اولیه رایگان و محرمانه است.', 'teznevise' ),
		'bullets' => array(
			__( 'محرمانگی کامل اطلاعات و فایل‌ها', 'teznevise' ),
			__( 'پژوهشگر متناسب با رشته و مقطع', 'teznevise' ),
			__( 'زمان‌بندی شفاف و قابل پیگیری', 'teznevise' ),
			__( 'امکان اعمال اصلاحات استاد', 'teznevise' ),
		),
		'inquiry' => $inquiry,
	);
	if ( str_contains( $tag, 'proposal' ) ) {
		$base['eyebrow'] = __( 'خدمات تخصصی پروپوزال', 'teznevise' );
		$base['lead']    = __( 'از بیان مسئله و مرور ادبیات تا اهداف، سوال‌ها و روش‌شناسی؛ پروپوزال را با ساختار روشن و قابل دفاع آماده کنید.', 'teznevise' );
		$base['inquiry'] = home_url( '/service-proposal/' ) ?: $inquiry;
	} elseif ( str_contains( $tag, 'thesis' ) ) {
		$base['eyebrow'] = __( 'خدمات تخصصی پایان‌نامه', 'teznevise' );
		$base['lead']    = __( 'از انتخاب موضوع و پروپوزال تا نگارش فصل‌ها، تحلیل آماری و آمادگی دفاع؛ مسیر پایان‌نامه را مرحله‌به‌مرحله پیش ببرید.', 'teznevise' );
		$base['inquiry'] = home_url( '/service-thesis/' ) ?: $inquiry;
	} elseif ( str_contains( $tag, 'stat' ) ) {
		$base['eyebrow'] = __( 'خدمات تحلیل آماری', 'teznevise' );
		$base['lead']    = __( 'از آماده‌سازی داده و انتخاب آزمون تا اجرای تحلیل و تفسیر علمی نتایج.', 'teznevise' );
		$base['inquiry'] = home_url( '/service-statistics/' ) ?: $inquiry;
	} elseif ( str_contains( $tag, 'sim' ) ) {
		$base['eyebrow'] = __( 'خدمات شبیه‌سازی', 'teznevise' );
		$base['lead']    = __( 'از تعریف مسئله و ساخت مدل تا اجرای شبیه‌سازی و تفسیر خروجی‌ها.', 'teznevise' );
		$base['inquiry'] = home_url( '/service-simulation/' ) ?: $inquiry;
	}
	return $base;
}

/**
 * Service-hub fallback used when WPCode snippets no longer run.
 *
 * @param string $title Page title.
 * @param string $tag   Shortcode tag (optional).
 * @return string
 */
function teznevise_render_service_fallback( $title, $tag = '' ) {
	$copy    = teznevise_hub_copy( $tag );
	$phone   = function_exists( 'teznevise_get_contact' ) ? teznevise_get_contact( 'phone_display' ) : '۰۹۳۰۲۸۲۲۰۹۱';
	$tel     = function_exists( 'teznevise_get_contact' ) ? teznevise_get_contact( 'phone_intl' ) : '+989302822091';
	$heading = teznevise_is_legacy_shortcode_content( (string) get_post_field( 'post_content', get_the_ID() ) ) ? 'h1' : 'h2';
	ob_start();
	?>
	<div class="tz-shortcode-fallback service-hero-align" data-fallback-tag="<?php echo esc_attr( $tag ); ?>">
		<section class="service-hero service-hero-aligned">
			<div class="service-hero-grid service-hero-grid-align">
				<div>
					<span class="eyebrow"><?php echo esc_html( $copy['eyebrow'] ); ?></span>
					<?php if ( 'h1' === $heading ) : ?>
						<h1><?php echo esc_html( $title ); ?></h1>
					<?php else : ?>
						<h2><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					<p class="service-lead"><?php echo esc_html( $copy['lead'] ); ?></p>
					<div class="hero-actions">
						<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> <?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></a>
						<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_attr( function_exists( 'teznevise_tel_href' ) ? teznevise_tel_href( $tel ? $tel : $phone ) : 'tel:+989302822091' ); ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i> <?php echo esc_html( $phone ); ?></a>
					</div>
					<div class="service-bullets">
						<?php foreach ( $copy['bullets'] as $bullet ) : ?>
							<div><i class="fa-regular fa-circle-check" aria-hidden="true"></i> <?php echo esc_html( $bullet ); ?></div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="lead-card" id="consult">
					<div class="lead-card-head">
						<h3><?php esc_html_e( 'درخواست مشاوره رایگان', 'teznevise' ); ?></h3>
						<p><?php esc_html_e( 'اطلاعات اولیه را وارد کنید تا موضوع و نیاز شما بررسی شود.', 'teznevise' ); ?></p>
					</div>
					<?php echo teznevise_render_native_lead_form( 'hub-' . sanitize_html_class( (string) $tag ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</section>
		<div class="reason-list" style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-top:28px;">
			<div class="reason-item"><div class="icon-box icon-teal"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></div><b><?php esc_html_e( 'محرمانگی کامل', 'teznevise' ); ?></b><p><?php esc_html_e( 'فایل‌ها و اطلاعات پژوهش فقط با شما و تیم متخصص در میان گذاشته می‌شود.', 'teznevise' ); ?></p></div>
			<div class="reason-item"><div class="icon-box icon-indigo"><i class="fa-solid fa-compass-drafting" aria-hidden="true"></i></div><b><?php esc_html_e( 'روش‌مندی علمی', 'teznevise' ); ?></b><p><?php esc_html_e( 'هر مرحله با استاندارد دانشگاه و منطق پژوهش پیش می‌رود.', 'teznevise' ); ?></p></div>
			<div class="reason-item"><div class="icon-box icon-cyan"><i class="fa-solid fa-bolt" aria-hidden="true"></i></div><b><?php esc_html_e( 'پاسخ‌گویی سریع', 'teznevise' ); ?></b><p><?php esc_html_e( 'شنبه تا پنجشنبه، ۹ تا ۲۱ — مشاوره اولیه رایگان.', 'teznevise' ); ?></p></div>
			<div class="reason-item"><div class="icon-box icon-amber"><i class="fa-solid fa-pen-ruler" aria-hidden="true"></i></div><b><?php esc_html_e( 'خروجی قابل ویرایش', 'teznevise' ); ?></b><p><?php esc_html_e( 'فایل‌ها، گزارش مرحله‌ای و پشتیبانی تا تحویل.', 'teznevise' ); ?></p></div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Expand leftover unknown tz_* / gravityform tags still sitting in the HTML.
 *
 * @param string $content Post content after do_shortcode.
 * @return string
 */
function teznevise_replace_unknown_shortcodes( $content ) {
	if ( ! is_string( $content ) || $content === '' ) {
		return $content;
	}
	return preg_replace_callback(
		'/\[(\/?)(tz_[a-z0-9_]+|teznevise_[a-z0-9_]+|gravityform)(\s[^\]]*)?\]/i',
		static function ( $m ) {
			if ( $m[1] === '/' ) {
				return '';
			}
			$tag = strtolower( $m[2] );
			if ( $tag === 'gravityform' || str_contains( $tag, 'form' ) || str_contains( $tag, 'join' ) ) {
				return teznevise_render_native_lead_form( $tag );
			}
			if ( $tag === 'teznevise_blog' || $tag === 'tz_home' ) {
				return $tag === 'teznevise_blog' ? teznevise_blog_shortcode_fallback() : '';
			}
			if ( str_contains( $tag, 'download' ) ) {
				return '<p class="tz-shortcode-fallback"><a class="btn-tz btn-primary-tz" href="' . esc_url( home_url( '/downloads/' ) ) . '">' . esc_html__( 'مشاهده فایل‌های قابل دانلود', 'teznevise' ) . '</a></p>';
			}
			return teznevise_render_service_fallback( get_the_title(), $tag );
		},
		$content
	);
}
add_filter( 'the_content', 'teznevise_replace_unknown_shortcodes', 12 );

/**
 * If post content is only an unexpanded shortcode, replace it with designed UI.
 *
 * @param string $content Post content.
 * @return string
 */
function teznevise_catch_leftover_shortcodes( $content ) {
	if ( is_admin() ) {
		return $content;
	}
	if ( ! teznevise_is_legacy_shortcode_content( wp_strip_all_tags( (string) $content ) ) ) {
		return $content;
	}
	$plain = trim( wp_strip_all_tags( (string) $content ) );
	$plain = teznevise_normalize_shortcode_quotes( html_entity_decode( $plain, ENT_QUOTES, 'UTF-8' ) );
	if ( stripos( $plain, 'gravityform' ) !== false || stripos( $plain, 'join_form' ) !== false ) {
		return teznevise_render_native_lead_form( 'gravity' );
	}
	if ( stripos( $plain, 'teznevise_blog' ) !== false ) {
		return teznevise_blog_shortcode_fallback();
	}
	return teznevise_render_service_fallback( get_the_title(), $plain );
}
add_filter( 'the_content', 'teznevise_catch_leftover_shortcodes', 99 );

/**
 * Persian-friendly archive dates.
 */
add_filter(
	'pre_option_date_format',
	static function ( $value ) {
		return is_admin() ? $value : 'j F Y';
	}
);
