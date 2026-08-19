<?php
/**
 * Theme header — chrome matches teznevise_work/index.html.
 *
 * Appearance → Menus is not the source of this markup. Edit this file
 * (or template-parts/mobile-nav.php / bottom-nav.php) to change labels.
 *
 * @package Teznevise
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'teznevise' ); ?></a>
<header class="site-header-new">
<div class="announcement">
	<div class="announcement-inner">
		<div class="announcement-items">
			<span class="announce-pill"><i class="fa-solid fa-phone" aria-hidden="true"></i> <strong><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></strong></span>
			<span class="announce-pill announce-desktop"><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( teznevise_get_contact( 'hours' ) ); ?></span>
			<span class="announce-pill announce-desktop"><i class="fa-solid fa-lock" aria-hidden="true"></i> <?php esc_html_e( 'مشاوره محرمانه و تخصصی', 'teznevise' ); ?></span>
		</div>
		<div class="utility-links announce-desktop" aria-label="<?php esc_attr_e( 'لینک‌های راهنما', 'teznevise' ); ?>">
			<a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'teznevise' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/team/' ) ); ?>"><?php esc_html_e( 'نظرات مشتریان', 'teznevise' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'بازگشت هزینه', 'teznevise' ); ?></a>
		</div>
	</div>
</div>
<nav class="main-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'teznevise' ); ?>">
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		$logo_url = teznevise_logo_url();
		if ( $logo_url ) {
			printf(
				'<img src="%s" alt="%s" width="106" height="48" decoding="async" fetchpriority="high">',
				esc_url( $logo_url ),
				esc_attr( get_bloginfo( 'name' ) )
			);
		} else {
			echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
		}
		?>
	</a>
	<ul class="nav-links">
		<li><a class="<?php echo teznevise_is_current( 'home' ) ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'teznevise' ); ?></a></li>
		<li>
			<a class="<?php echo teznevise_is_current( array( 'service-thesis', 'service-proposal', 'service-statistics', 'service-simulation', 'thesis', 'proposal' ) ) ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'خدمات', 'teznevise' ); ?> <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i></a>
			<ul class="nav-dropdown">
				<li>
					<a href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'پایان‌نامه', 'teznevise' ); ?> <i class="fa-solid fa-chevron-left nav-chevron" aria-hidden="true"></i></a>
					<ul class="nav-dropdown-l3">
						<li><a href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'ارشد و دکتری', 'teznevise' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'نگارش فصل‌ها', 'teznevise' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'آمادگی دفاع', 'teznevise' ); ?></a></li>
					</ul>
				</li>
				<li>
					<a href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><?php esc_html_e( 'پروپوزال', 'teznevise' ); ?> <i class="fa-solid fa-chevron-left nav-chevron" aria-hidden="true"></i></a>
					<ul class="nav-dropdown-l3">
						<li><a href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><?php esc_html_e( 'بیان مسئله', 'teznevise' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><?php esc_html_e( 'روش‌شناسی', 'teznevise' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><?php esc_html_e( 'مرور ادبیات', 'teznevise' ); ?></a></li>
					</ul>
				</li>
				<li>
					<a href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?> <i class="fa-solid fa-chevron-left nav-chevron" aria-hidden="true"></i></a>
					<ul class="nav-dropdown-l3">
						<li><a href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><?php esc_html_e( 'SPSS / R / Python', 'teznevise' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><?php esc_html_e( 'مدل‌سازی معادلات', 'teznevise' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			<a class="<?php echo teznevise_is_current( array( 'tools', 'tool-descriptive-statistics' ) ) ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'ابزارها', 'teznevise' ); ?> <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i></a>
			<ul class="nav-dropdown">
				<li><a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'آمار توصیفی', 'teznevise' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'همبستگی و رگرسیون', 'teznevise' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'آزمون‌های فرض', 'teznevise' ); ?></a></li>
			</ul>
		</li>
		<li>
			<a class="<?php echo teznevise_is_current( 'blog' ) ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'بلاگ', 'teznevise' ); ?> <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i></a>
			<ul class="nav-dropdown">
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'راهنمای پایان‌نامه', 'teznevise' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'روش تحقیق', 'teznevise' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'آمار و نرم‌افزار', 'teznevise' ); ?></a></li>
			</ul>
		</li>
		<li><a class="<?php echo teznevise_is_current( 'about' ) ? 'active' : ''; ?>" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'درباره ما', 'teznevise' ); ?></a></li>
	</ul>
	<div class="nav-actions">
		<button type="button" class="nav-search-btn" aria-label="<?php esc_attr_e( 'جستجو', 'teznevise' ); ?>" data-search-open>
			<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
		</button>
		<div class="nav-quick-actions desktop-cta-group">
			<a class="nav-cta nav-cta-outline" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><i class="fa-regular fa-circle-question" aria-hidden="true"></i><span><?php esc_html_e( 'درباره ما', 'teznevise' ); ?></span></a>
			<a class="nav-cta nav-cta-solid" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></span></a>
		</div>
	</div>
	<button type="button" class="menu-btn" aria-label="<?php esc_attr_e( 'باز کردن منو', 'teznevise' ); ?>" aria-expanded="false" aria-controls="mobile-navigation" data-menu-toggle>
		<i class="fa-solid fa-bars" data-menu-icon aria-hidden="true"></i>
	</button>
</nav>
</header>
<?php get_template_part( 'template-parts/mobile-nav' ); ?>
<?php get_template_part( 'template-parts/search-overlay' ); ?>
<main id="main-content">
