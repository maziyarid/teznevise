<?php
/**
 * Theme header.
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
<div class="announcement"><div class="announcement-inner container announcement-wrap"><div class="announcement-items"><span class="announce-pill"><i class="fa-solid fa-phone" aria-hidden="true"></i><strong><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></strong></span><span class="announce-pill announce-desktop"><i class="fa-regular fa-clock" aria-hidden="true"></i><?php echo esc_html( teznevise_get_contact( 'hours' ) ); ?></span><span class="announce-pill announce-desktop"><i class="fa-solid fa-lock" aria-hidden="true"></i><?php esc_html_e( 'مشاوره محرمانه و تخصصی', 'teznevise' ); ?></span></div><div class="utility-links announce-desktop" aria-label="<?php esc_attr_e( 'لینک‌های راهنما', 'teznevise' ); ?>"><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'teznevise' ); ?></a><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'بازخورد مشتریان', 'teznevise' ); ?></a><a href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></a></div></div></div>
<nav class="main-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'teznevise' ); ?>">
<button type="button" class="menu-btn" aria-label="<?php esc_attr_e( 'باز کردن منو', 'teznevise' ); ?>" aria-expanded="false" aria-controls="mobile-navigation" data-menu-toggle><i class="fa-solid fa-bars" data-menu-icon aria-hidden="true"></i></button>
<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php
$logo_url = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : '';
if ( ! $logo_url && has_custom_logo() ) {
	$logo_url = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
}
if ( $logo_url ) {
	printf( '<img src="%s" alt="%s" width="106" height="48" decoding="async" fetchpriority="high">', esc_url( $logo_url ), esc_attr( get_bloginfo( 'name' ) ) );
} else {
	echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
}
?></a>
<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-links', 'fallback_cb' => 'teznevise_fallback_menu', 'depth' => 3 ) ); ?>
<div class="nav-actions">
	<button type="button" class="nav-search-btn" data-search-open aria-label="<?php esc_attr_e( 'جستجو', 'teznevise' ); ?>"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
	<?php
	$logged  = is_user_logged_in();
	$balance = ( $logged && function_exists( 'teznevise_tezcoin_balance' ) ) ? teznevise_tezcoin_balance() : null;
	$tip     = $logged
		? sprintf( __( 'موجودی: %s تزکوین', 'teznevise' ), number_format_i18n( (int) $balance ) )
		: __( 'با ثبت‌نام و تکمیل پروفایل ۱۰۰۰ تزکوین هدیه بگیرید', 'teznevise' );
	$account = $logged ? admin_url( 'profile.php' ) : wp_login_url( home_url( '/' ) );
	?>
	<a class="nav-credits" href="<?php echo esc_url( $account ); ?>" title="<?php echo esc_attr( $tip ); ?>" aria-label="<?php echo esc_attr( $tip ); ?>">
		<i class="fa-solid fa-coins" aria-hidden="true"></i>
		<span><?php echo $logged ? esc_html( number_format_i18n( (int) $balance ) ) : '—'; ?></span>
	</a>
	<a class="nav-account" href="<?php echo esc_url( $account ); ?>" aria-label="<?php echo $logged ? esc_attr__( 'پروفایل', 'teznevise' ) : esc_attr__( 'ورود', 'teznevise' ); ?>" title="<?php echo $logged ? esc_attr__( 'پروفایل', 'teznevise' ) : esc_attr__( 'ورود', 'teznevise' ); ?>">
		<i class="<?php echo $logged ? 'fa-solid fa-user' : 'fa-regular fa-user'; ?>" aria-hidden="true"></i>
	</a>
	<div class="nav-quick-actions desktop-cta-group">
		<a class="nav-cta nav-cta-outline" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><i class="fa-regular fa-circle-question" aria-hidden="true"></i><span><?php esc_html_e( 'درباره ما', 'teznevise' ); ?></span></a>
		<a class="nav-cta nav-cta-solid" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></span></a>
	</div>
</div>
</nav>
</header>
<?php get_template_part( 'template-parts/mobile-nav' ); ?>
<?php get_template_part( 'template-parts/search-overlay' ); ?>
<main id="main-content">
