<?php
/**
 * Theme header — chrome matches teznevise_work/*.html
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
		$logo_url = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : '';
		if ( ! $logo_url && has_custom_logo() ) {
			$logo_url = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
		}
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
	<?php get_template_part( 'template-parts/primary-nav' ); ?>
	<div class="nav-actions">
		<button type="button" class="nav-search-btn" aria-label="<?php esc_attr_e( 'جستجو', 'teznevise' ); ?>" data-search-open>
			<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
		</button>
		<div class="nav-quick-actions desktop-cta-group">
			<a class="nav-cta nav-cta-outline" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">
				<i class="fa-regular fa-circle-question" aria-hidden="true"></i>
				<span><?php esc_html_e( 'درباره ما', 'teznevise' ); ?></span>
			</a>
			<a class="nav-cta nav-cta-solid" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>">
				<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
				<span><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></span>
			</a>
		</div>
	</div>
	<button type="button" class="menu-btn" aria-label="<?php esc_attr_e( 'باز کردن منو', 'teznevise' ); ?>" aria-expanded="false" aria-controls="mobile-navigation" data-menu-toggle>
		<i class="fa-solid fa-bars" data-menu-icon aria-hidden="true"></i>
	</button>
</nav>
</header>
<?php get_template_part( 'template-parts/mobile-nav' ); ?>
<?php get_template_part( 'template-parts/search-overlay' ); ?>
<!-- Open main content landmark for skip link target -->
<main id="main-content">
