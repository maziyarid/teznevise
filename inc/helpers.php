<?php
/**
 * Shared theme helpers.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Resolve a published page URL from slug candidates (first match wins).
 *
 * Candidates are tried in array order so preferred slug variants come first
 * (for example `thesis` before `service-thesis`). The `$fallback_path` is
 * used only when none of the candidates exist; callers may pass different
 * fallbacks because header CTAs and the compact bottom bar have different
 * conversion paths (`/inquiry/` vs `/contact/`).
 *
 * Results are memoized for the rest of the request.
 *
 * @param string[] $candidates    Preferred slug variants, most specific first.
 * @param string   $fallback_path Path used when no candidate exists.
 * @return string
 */
function teznevise_page_url_from_candidates( $candidates, $fallback_path = '/' ) {
	static $cache = array();
	$candidates   = array_values( array_filter( array_map( 'strval', (array) $candidates ) ) );
	$key          = implode( '|', $candidates ) . '>' . (string) $fallback_path;
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	foreach ( $candidates as $candidate ) {
		$page = get_page_by_path( sanitize_title( $candidate ), OBJECT, 'page' );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			$url = get_permalink( $page );
			if ( $url ) {
				$cache[ $key ] = $url;
				return $url;
			}
		}
	}
	$cache[ $key ] = home_url( $fallback_path );
	return $cache[ $key ];
}

function teznevise_fallback_menu( $args = array() ) {
	$defaults = array(
		'theme_location' => '',
		'container'      => false,
		'fallback_cb'    => false,
		'echo'           => false,
		'menu_class'     => 'nav-links',
	);
	$args = wp_parse_args( $args, $defaults );
	$locations = array(
		'primary' => array( 'home', 'blog', 'services', 'about', 'contact' ),
		'mobile'  => array( 'home', 'services', 'tools', 'blog', 'team', 'about', 'contact' ),
		'bottom'  => array( 'home', 'services', 'tools', 'blog', 'contact' ),
		'footer'  => array( 'blog', 'about', 'team', 'contact', 'privacy', 'sitemap' ),
	);
	$items = isset( $locations[ $args['theme_location'] ] ) ? $locations[ $args['theme_location'] ] : $locations['primary'];
	$labels = array(
		'home' => __( 'خانه', 'teznevise' ), 'blog' => __( 'بلاگ', 'teznevise' ), 'services' => __( 'خدمات', 'teznevise' ), 'about' => __( 'درباره ما', 'teznevise' ), 'contact' => __( 'تماس با ما', 'teznevise' ), 'tools' => __( 'ابزارهای آنلاین', 'teznevise' ), 'team' => __( 'تیم پژوهشگران', 'teznevise' ), 'privacy' => __( 'حریم خصوصی', 'teznevise' ), 'sitemap' => __( 'نقشه سایت', 'teznevise' ),
	);
	$urls = array(
		'home'     => home_url( '/' ),
		'blog'     => teznevise_posts_url(),
		'services' => teznevise_page_url_from_candidates( array( 'thesis', 'service-thesis', 'proposal', 'service-proposal' ), '/thesis/' ),
		'about'    => teznevise_page_url_from_candidates( array( 'about-us', 'about', 'our-story' ), '/about-us/' ),
		'contact'  => teznevise_page_url_from_candidates( array( 'contact-us', 'contact', 'inquiry' ), '/contact-us/' ),
		'tools'    => teznevise_page_url_from_candidates( array( 'online-calculation-tools', 'tools' ), '/online-calculation-tools/' ),
		'team'     => teznevise_page_url_from_candidates( array( 'our-team', 'team' ), '/our-team/' ),
		'privacy'  => teznevise_page_url_from_candidates( array( 'privacy-policy', 'privacy' ), '/privacy-policy/' ),
		'sitemap'  => teznevise_page_url_from_candidates( array( 'sitemap' ), '/sitemap/' ),
	);
	$html = '<ul class="' . esc_attr( $args['menu_class'] ) . '">';
	foreach ( $items as $key ) {
		$html .= '<li class="menu-item"><a href="' . esc_url( $urls[ $key ] ) . '">' . esc_html( $labels[ $key ] ) . '</a></li>';
	}
	return $html . '</ul>';
}

/**
 * Contact / site-info value (phone, WhatsApp, email, …).
 *
 * Used by header.php, footer.php, template-parts/fab.php,
 * template-parts/mobile-nav.php and several page templates.
 * Values come from the Customizer with Persian defaults as fallback.
 *
 * @param string $key Setting key (see teznevise_content_defaults()).
 * @return string
 */
function teznevise_get_contact( $key ) {
	return teznevise_mod( $key );
}

/**
 * Flatten mixed meta (string or array) to a single string for escaping.
 *
 * @param mixed  $value    Raw value.
 * @param string $fallback Fallback.
 * @return string
 */
function teznevise_plain( $value, $fallback = '' ) {
	if ( is_array( $value ) ) {
		$flat = array();
		array_walk_recursive(
			$value,
			static function ( $item ) use ( &$flat ) {
				if ( is_scalar( $item ) ) {
					$item = trim( (string) $item );
					if ( '' !== $item ) {
						$flat[] = $item;
					}
				}
			}
		);
		return $flat ? implode( "\n", $flat ) : (string) $fallback;
	}
	if ( null === $value || is_bool( $value ) ) {
		return (string) $fallback;
	}
	return (string) $value;
}

/**
 * Split a page field into non-empty lines. Accepts strings or arrays.
 *
 * @param mixed $value Raw meta.
 * @return string[]
 */
function teznevise_lines( $value ) {
	if ( is_array( $value ) ) {
		$out = array();
		foreach ( $value as $item ) {
			if ( is_array( $item ) ) {
				$out = array_merge( $out, teznevise_lines( $item ) );
			} else {
				$line = trim( (string) $item );
				if ( '' !== $line ) {
					$out[] = $line;
				}
			}
		}
		return $out;
	}
	$parts = preg_split( '/\r\n|\r|\n/', (string) $value );
	return array_values( array_filter( array_map( 'trim', is_array( $parts ) ? $parts : array() ) ) );
}

/**
 * Parse "a | b | c" rows (one per line) into arrays of columns.
 *
 * @param mixed $value Raw field.
 * @param int   $cols  Expected columns.
 * @return array<int,string[]>
 */
function teznevise_parse_pipe_list( $value, $cols = 2 ) {
	$rows = array();
	$cols = max( 1, (int) $cols );
	foreach ( teznevise_lines( $value ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( count( $parts ) < $cols ) {
			$parts = array_pad( $parts, $cols, '' );
		}
		$rows[] = array_slice( $parts, 0, $cols );
	}
	return $rows;
}

/**
 * Absolute tel: href that esc_url() will not turn into /tel:...
 *
 * @param string $raw Phone number or existing tel: value.
 * @return string
 */
function teznevise_tel_href( $raw ) {
	$raw = trim( (string) $raw );
	if ( 0 === strpos( $raw, 'tel:' ) ) {
		$raw = substr( $raw, 4 );
	}
	$digits = preg_replace( '/[^\d+]/', '', $raw );
	if ( 0 === strpos( $digits, '00' ) ) {
		$digits = '+' . substr( $digits, 2 );
	}
	if ( 0 === strpos( $digits, '09' ) ) {
		$digits = '+98' . substr( $digits, 1 );
	} elseif ( 0 === strpos( $digits, '9' ) && 10 === strlen( $digits ) ) {
		$digits = '+98' . $digits;
	} elseif ( 0 === strpos( $digits, '98' ) && '+' !== substr( $digits, 0, 1 ) ) {
		$digits = '+' . $digits;
	}
	if ( '' === $digits ) {
		return '';
	}
	return 'tel:' . $digits;
}

/**
 * Persian/Arabic digits → Latin digits.
 *
 * @param string $raw Input.
 * @return string
 */
function teznevise_latin_digits( $raw ) {
	$map = array(
		'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
		'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
		'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
		'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
	);
	return strtr( (string) $raw, $map );
}

/**
 * Iranian mobile number as +989xxxxxxxxx, or empty if invalid.
 *
 * @param string $raw Raw phone.
 * @return string
 */
function teznevise_iran_mobile( $raw ) {
	$digits = preg_replace( '/\D+/', '', teznevise_latin_digits( $raw ) );
	if ( 0 === strpos( $digits, '0098' ) ) {
		$digits = substr( $digits, 4 );
	} elseif ( 0 === strpos( $digits, '98' ) ) {
		$digits = substr( $digits, 2 );
	}
	if ( 0 === strpos( $digits, '0' ) ) {
		$digits = substr( $digits, 1 );
	}
	if ( ! preg_match( '/^9\d{9}$/', $digits ) ) {
		return '';
	}
	return '+98' . $digits;
}

/**
 * Public byline: never expose raw WP logins like akumumono.
 *
 * @param int $user_id Author ID.
 * @return string
 */
function teznevise_public_author_name( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : (int) get_the_author_meta( 'ID' );
	$first   = trim( (string) get_the_author_meta( 'first_name', $user_id ) );
	$last    = trim( (string) get_the_author_meta( 'last_name', $user_id ) );
	if ( $first || $last ) {
		return trim( $first . ' ' . $last );
	}
	$display = trim( (string) get_the_author_meta( 'display_name', $user_id ) );
	$login   = strtolower( (string) get_the_author_meta( 'user_login', $user_id ) );
	$blocked = array( 'akumumono', 'admin', 'administrator', 'maziyarid', 'root', 'user' );
	if ( '' === $display || strtolower( $display ) === $login || in_array( $login, $blocked, true ) ) {
		return get_bloginfo( 'name' );
	}
	return $display;
}

/**
 * Rewrite tel: hrefs and stale phone numbers in HTML.
 *
 * @param string $html HTML.
 * @return string
 */
function teznevise_rewrite_tel_html( $html ) {
	if ( ! is_string( $html ) || '' === $html ) {
		return $html;
	}
	$current = function_exists( 'teznevise_get_contact' ) ? teznevise_get_contact( 'phone_display' ) : '۰۹۳۰۲۸۲۲۰۹۱';
	$html    = str_replace( array( '09331663849', '۰۹۳۳۱۶۶۳۸۴۹', '9331663849' ), $current, $html );
	$html    = preg_replace_callback(
		'/href=(["\'])\s*(?:\/)?tel:([^"\']+)\1/i',
		static function ( $m ) {
			$href = function_exists( 'teznevise_tel_href' ) ? teznevise_tel_href( $m[2] ) : ( 'tel:' . $m[2] );
			return 'href=' . $m[1] . esc_attr( $href ) . $m[1];
		},
		$html
	);
	return is_string( $html ) ? $html : '';
}
add_filter( 'the_content', 'teznevise_rewrite_tel_html', 20 );

/**
 * Site logo URL for header and footer.
 *
 * @return string
 */
function teznevise_logo_url() {
	$optimized = '/assets/img/logo-header.webp';
	if ( defined( 'TEZNEVISE_DIR' ) && is_readable( TEZNEVISE_DIR . $optimized ) ) {
		return TEZNEVISE_URI . $optimized;
	}
	if ( has_custom_logo() ) {
		$url = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
		if ( $url ) {
			return $url;
		}
	}
	return '';
}

/**
 * 2x header logo for retina srcset.
 *
 * @return string
 */
function teznevise_logo_url_2x() {
	$optimized = '/assets/img/logo-header-2x.webp';
	if ( defined( 'TEZNEVISE_DIR' ) && is_readable( TEZNEVISE_DIR . $optimized ) ) {
		return TEZNEVISE_URI . $optimized;
	}
	return teznevise_logo_url();
}

/**
 * Icon class for a bottom-nav destination.
 *
 * @param string $url   Item URL.
 * @param string $label Item label.
 * @return string
 */
function teznevise_bottom_icon( $url, $label ) {
	$u = strtolower( (string) $url );
	$l = (string) $label;
	if ( 0 === strpos( $u, 'tel:' ) || false !== strpos( $l, 'تماس' ) ) {
		return 'fa-solid fa-phone';
	}
	if ( false !== strpos( $u, '/tools' ) || false !== strpos( $l, 'ابزار' ) ) {
		return 'fa-solid fa-calculator';
	}
	if ( false !== strpos( $u, '/blog' ) || false !== strpos( $l, 'بلاگ' ) ) {
		return 'fa-solid fa-book-open';
	}
	if ( false !== strpos( $u, '/account' ) || false !== strpos( $l, 'حساب' ) || false !== strpos( $l, 'ورود' ) ) {
		return 'fa-solid fa-user';
	}
	if ( false !== strpos( $u, '/wallet' ) || false !== strpos( $l, 'کیف' ) ) {
		return 'fa-solid fa-wallet';
	}
	if ( trailingslashit( $u ) === trailingslashit( home_url( '/' ) ) || 'خانه' === $l ) {
		return 'fa-solid fa-house';
	}
	return 'fa-solid fa-layer-group';
}

/**
 * Font Awesome class for a primary-nav (or mega-menu) item.
 *
 * More-specific path fragments are listed first so chapter / calculator
 * pages do not inherit the parent service icon.
 *
 * @param string $url   Item URL.
 * @param string $title Item label.
 * @return string
 */
function teznevise_nav_icon( $url, $title = '' ) {
	$path  = strtolower( (string) wp_parse_url( (string) $url, PHP_URL_PATH ) );
	$title = (string) $title;

	if ( '#' === (string) $url || '' === $path ) {
		if ( false !== strpos( $title, 'فصل' ) ) {
			return 'fa-solid fa-list-ol';
		}
		if ( false !== strpos( $title, 'رشته' ) ) {
			return 'fa-solid fa-tags';
		}
		if ( false !== strpos( $title, 'سایر' ) ) {
			return 'fa-solid fa-ellipsis';
		}
	}

	$map = array(
		'chapter-one'                    => 'fa-solid fa-file-lines',
		'chapter-two'                    => 'fa-solid fa-book',
		'chapter-three'                  => 'fa-solid fa-flask',
		'chapter-four'                   => 'fa-solid fa-chart-column',
		'chapter-five'                   => 'fa-solid fa-flag-checkered',
		'humanities'                     => 'fa-solid fa-landmark',
		'engineering'                    => 'fa-solid fa-gear',
		'pure-science'                   => 'fa-solid fa-atom',
		'medical-health'                 => 'fa-solid fa-heart-pulse',
		'art-architecture'               => 'fa-solid fa-palette',
		'agriculture'                    => 'fa-solid fa-leaf',
		'animal-science'                 => 'fa-solid fa-paw',
		'interdisciplinary'              => 'fa-solid fa-puzzle-piece',
		'thesis/phd'                     => 'fa-solid fa-user-graduate',
		'thesis/international'           => 'fa-solid fa-globe',
		'proposal/phd'                   => 'fa-solid fa-user-graduate',
		'proposal/project'               => 'fa-solid fa-folder-open',
		'proposal/english'               => 'fa-solid fa-language',
		'proposal/qualitative'           => 'fa-solid fa-comments',
		'proposal/quantitative'          => 'fa-solid fa-chart-pie',
		'proposal/applied'               => 'fa-solid fa-screwdriver-wrench',
		'proposal/medical'               => 'fa-solid fa-stethoscope',
		'wilcoxon'                       => 'fa-solid fa-not-equal',
		'descriptive-statistics'         => 'fa-solid fa-chart-simple',
		'sample-size'                    => 'fa-solid fa-users',
		'kr20'                           => 'fa-solid fa-list-ol',
		'kruskal'                        => 'fa-solid fa-bars',
		't-test'                         => 'fa-solid fa-square-root-variable',
		'chi-square'                     => 'fa-solid fa-table-cells',
		'mann-whitney'                   => 'fa-solid fa-scale-balanced',
		'goodness-of-fit'                => 'fa-solid fa-check-double',
		'cronbach'                       => 'fa-solid fa-percent',
		'regression'                     => 'fa-solid fa-chart-line',
		'anova'                          => 'fa-solid fa-layer-group',
		'power-analysis'                 => 'fa-solid fa-bolt',
		'content-validity'               => 'fa-solid fa-clipboard-check',
		'cohens-kappa'                   => 'fa-solid fa-handshake',
		'pearson'                        => 'fa-solid fa-link',
		'icc-calculator'                 => 'fa-solid fa-diagram-project',
		'spearman'                       => 'fa-solid fa-arrow-trend-up',
		'online-calculation-tools'       => 'fa-solid fa-calculator',
		'/tools'                         => 'fa-solid fa-calculator',
		'/proposal'                      => 'fa-solid fa-file-lines',
		'/thesis'                        => 'fa-solid fa-graduation-cap',
		'/blog'                          => 'fa-solid fa-book-open',
		'contact'                        => 'fa-solid fa-phone',
		'about'                          => 'fa-solid fa-circle-info',
		'inquiry'                        => 'fa-solid fa-pen-to-square',
		'privacy'                        => 'fa-solid fa-shield-halved',
		'testimonial'                    => 'fa-solid fa-quote-right',
		'careers'                        => 'fa-solid fa-briefcase',
		'join-us'                        => 'fa-solid fa-handshake',
		'our-story'                      => 'fa-solid fa-book-open-reader',
		'achievements'                   => 'fa-solid fa-trophy',
		'case-stud'                      => 'fa-solid fa-folder-open',
		'our-team'                       => 'fa-solid fa-users',
		'/team'                          => 'fa-solid fa-users',
	);

	foreach ( $map as $needle => $icon ) {
		if ( false !== strpos( $path, $needle ) ) {
			return $icon;
		}
	}

	$home = trailingslashit( (string) home_url( '/' ) );
	if ( trailingslashit( (string) $url ) === $home || 'خانه' === $title ) {
		return 'fa-solid fa-house';
	}
	if ( false !== strpos( $title, 'پایان' ) ) {
		return 'fa-solid fa-graduation-cap';
	}
	if ( false !== strpos( $title, 'پروپوزال' ) ) {
		return 'fa-solid fa-file-lines';
	}
	if ( false !== strpos( $title, 'ابزار' ) ) {
		return 'fa-solid fa-calculator';
	}
	if ( false !== strpos( $title, 'بلاگ' ) ) {
		return 'fa-solid fa-book-open';
	}
	if ( false !== strpos( $title, 'تماس' ) ) {
		return 'fa-solid fa-phone';
	}

	return 'fa-solid fa-circle-dot';
}

/**
 * True when a top-level primary item duplicates a header CTA (About / Inquiry).
 *
 * @param string $url Menu item URL.
 * @return bool
 */
function teznevise_nav_is_cta_duplicate( $url ) {
	$path = untrailingslashit( (string) wp_parse_url( (string) $url, PHP_URL_PATH ) );
	return in_array( $path, array( '/inquiry', '/about-us', '/about' ), true );
}

/**
 * Compact primary-nav labels to match the React SiteHeader.
 *
 * @param string   $title Item title.
 * @param WP_Post  $item  Menu item.
 * @param stdClass $args  Walker args.
 * @param int      $depth Depth.
 * @return string
 */
function teznevise_nav_item_title_short( $title, $item, $args = null, $depth = 0 ) {
	unset( $item, $depth );
	$loc = ( is_object( $args ) && ! empty( $args->theme_location ) ) ? (string) $args->theme_location : '';
	if ( ! in_array( $loc, array( 'primary', 'mobile' ), true ) ) {
		return $title;
	}
	$map = array(
		'انجام پایان نامه'                     => 'پایان‌نامه',
		'انجام پایان‌نامه'                     => 'پایان‌نامه',
		'مشاوره انجام پایان نامه'              => 'پایان‌نامه',
		'مشاوره انجام پایان‌نامه'              => 'پایان‌نامه',
		'انجام پروپوزال'                       => 'پروپوزال',
		'مشاوره انجام پروپوزال'                 => 'پروپوزال',
		'ابزارهای آنلاین'                => 'ابزار',
		'تماس با ما'                     => 'تماس',
		'انجام فصل به فصل'               => 'فصل به فصل',
		'نگارش فصل اول پایان نامه'     => 'نگارش فصل اول',
		'نگارش فصل دوم پایان نامه'     => 'نگارش فصل دوم',
		'نگارش فصل سوم پایان نامه'     => 'نگارش فصل سوم',
		'نگارش فصل چهارم پایان نامه'   => 'نگارش فصل چهارم',
		'نگارش فصل پنجم پایان نامه'    => 'نگارش فصل پنجم',
		'انجام پایان نامه علوم انسانی' => 'علوم انسانی',
		'مشاوره انجام پایان نامه علوم انسانی' => 'علوم انسانی',
		'انجام پایان نامه مهندسی'      => 'فنی و مهندسی',
		'مشاوره انجام پایان نامه مهندسی'      => 'فنی و مهندسی',
		'انجام پایان نامه علوم پایه'   => 'علوم پایه',
		'مشاوره انجام پایان نامه علوم پایه'   => 'علوم پایه',
		'انجام پایان نامه علوم پزشکی'  => 'علوم پزشکی',
		'مشاوره انجام پایان نامه علوم پزشکی'  => 'علوم پزشکی',
		'انجام پایان نامه هنر/معماری'  => 'هنر و معماری',
		'مشاوره انجام پایان نامه هنر/معماری'  => 'هنر و معماری',
		'علوم بین‌رشته‌ای و کاربردی'     => 'علوم بین‌رشته‌ای',
		'انجام رساله دکتری'              => 'رساله دکتری',
		'مشاوره انجام رساله دکتری'      => 'رساله دکتری',
		'نگارش پایان نامه بین المللی'  => 'پایان‌نامه بین‌المللی',
		'انجام پروپوزال دکتری'           => 'پروپوزال دکتری',
		'مشاوره انجام پروپوزال دکتری'           => 'پروپوزال دکتری',
		'انجام پروپوزال کلاسی'           => 'پروپوزال کلاسی',
		'مشاوره انجام پروپوزال کلاسی'           => 'پروپوزال کلاسی',
		'انجام پروپوزال انگلیسی'         => 'پروپوزال انگلیسی',
		'مشاوره انجام پروپوزال انگلیسی'         => 'پروپوزال انگلیسی',
		'نگارش پروپوزال کیفی'            => 'پژوهش کیفی',
		'نگارش پروپوزال کمی'             => 'پژوهش کمی',
		'نگارش پروپوزال کاربردی'         => 'تحقیق کاربردی',
		'نگارش پروپوزال پزشکی'           => 'پروپوزال پزشکی',
		'سایر موارد'                     => 'سایر',
	);
	return isset( $map[ $title ] ) ? $map[ $title ] : $title;
}
add_filter( 'nav_menu_item_title', 'teznevise_nav_item_title_short', 20, 4 );

/**
 * Posts-index URL: configured page_for_posts, then a `blog` page, then home.
 *
 * @return string
 */
function teznevise_posts_url() {
	$id = (int) get_option( 'page_for_posts' );
	if ( $id > 0 ) {
		$url = get_permalink( $id );
		if ( $url ) {
			return $url;
		}
	}
	$page = get_page_by_path( 'blog' );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}
	return home_url( '/' );
}

/**
 * Permalink for a seeded page slug, with a path fallback.
 *
 * @param string $slug          Page slug.
 * @param string $fallback_path Optional path if the page does not exist.
 * @return string
 */
function teznevise_page_url( $slug, $fallback_path = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post ) {
		$url = get_permalink( $page );
		if ( $url ) {
			return $url;
		}
	}
	$path = $fallback_path ? $fallback_path : '/' . trim( (string) $slug, '/' ) . '/';
	return home_url( $path );
}
