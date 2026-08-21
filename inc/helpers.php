<?php
/**
 * Shared theme helpers.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function teznevise_page_url_from_candidates( $candidates, $fallback_path = '/' ) {
	foreach ( (array) $candidates as $candidate ) {
		$page = get_page_by_path( sanitize_title( (string) $candidate ), OBJECT, 'page' );
		if ( $page instanceof WP_Post ) {
			$url = get_permalink( $page );
			if ( $url ) {
				return $url;
			}
		}
	}
	return home_url( $fallback_path );
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
		'services' => teznevise_page_url_from_candidates( array( 'thesis', 'service-thesis', 'proposal', 'service-proposal' ), '/service-thesis/' ),
		'about'    => teznevise_page_url_from_candidates( array( 'about-us', 'about', 'our-story' ), '/about/' ),
		'contact'  => teznevise_page_url_from_candidates( array( 'contact-us', 'contact', 'inquiry' ), '/contact/' ),
		'tools'    => teznevise_page_url_from_candidates( array( 'online-calculation-tools', 'tools' ), '/tools/' ),
		'team'     => teznevise_page_url_from_candidates( array( 'our-team', 'team' ), '/team/' ),
		'privacy'  => teznevise_page_url_from_candidates( array( 'privacy-policy', 'privacy' ), '/privacy/' ),
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
 * Site logo URL for header and footer.
 *
 * @return string
 */
function teznevise_logo_url() {
	if ( has_custom_logo() ) {
		$url = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
		if ( $url ) {
			return $url;
		}
	}
	return '';
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
	if ( ! is_object( $args ) || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $title;
	}
	$map = array(
		'انجام پایان نامه'              => 'پایان‌نامه',
		'انجام پایان‌نامه'              => 'پایان‌نامه',
		'انجام پروپوزال'                 => 'پروپوزال',
		'ابزارهای آنلاین'                => 'ابزار',
		'تماس با ما'                     => 'تماس',
		'انجام فصل به فصل'               => 'فصل به فصل',
		'نگارش فصل اول پایان نامه'     => 'نگارش فصل اول',
		'نگارش فصل دوم پایان نامه'     => 'نگارش فصل دوم',
		'نگارش فصل سوم پایان نامه'     => 'نگارش فصل سوم',
		'نگارش فصل چهارم پایان نامه'   => 'نگارش فصل چهارم',
		'نگارش فصل پنجم پایان نامه'    => 'نگارش فصل پنجم',
		'انجام پایان نامه علوم انسانی' => 'علوم انسانی',
		'انجام پایان نامه مهندسی'      => 'فنی و مهندسی',
		'انجام پایان نامه علوم پایه'   => 'علوم پایه',
		'انجام پایان نامه علوم پزشکی'  => 'علوم پزشکی',
		'انجام پایان نامه هنر/معماری'  => 'هنر و معماری',
		'علوم بین‌رشته‌ای و کاربردی'     => 'علوم بین‌رشته‌ای',
		'انجام رساله دکتری'              => 'رساله دکتری',
		'نگارش پایان نامه بین المللی'  => 'پایان‌نامه بین‌المللی',
		'انجام پروپوزال دکتری'           => 'پروپوزال دکتری',
		'انجام پروپوزال کلاسی'           => 'پروپوزال کلاسی',
		'انجام پروپوزال انگلیسی'         => 'پروپوزال انگلیسی',
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
