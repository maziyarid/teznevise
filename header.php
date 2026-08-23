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
<body <?php body_class( 'tz-react-shell' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'رفتن به محتوای اصلی', 'teznevise' ); ?></a>
<div class="tz-react-shell-inner">
<header class="site-header site-header-new">
<div class="announcement announce"><div class="announcement-inner announce-inner container announcement-wrap"><div class="announcement-items"><span class="announce-pill pill"><i class="fa-solid fa-phone" aria-hidden="true"></i><strong><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></strong></span><span class="announce-pill pill announce-desktop"><i class="fa-regular fa-clock" aria-hidden="true"></i><?php echo esc_html( teznevise_get_contact( 'hours' ) ); ?></span><span class="announce-pill pill announce-desktop"><i class="fa-solid fa-lock" aria-hidden="true"></i><?php esc_html_e( 'مشاوره محرمانه و تخصصی', 'teznevise' ); ?></span></div><div class="utility-links announce-utils announce-desktop" aria-label="<?php esc_attr_e( 'لینک‌های راهنما', 'teznevise' ); ?>"><a href="<?php echo esc_url( teznevise_page_url( 'privacy', '/privacy/' ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'teznevise' ); ?></a><a href="<?php echo esc_url( teznevise_page_url( 'testimonials', '/testimonials/' ) ); ?>"><?php esc_html_e( 'بازخورد مشتریان', 'teznevise' ); ?></a><a href="<?php echo esc_url( teznevise_page_url( 'inquiry', '/inquiry/' ) ); ?>"><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></a></div></div></div>
<nav class="main-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'teznevise' ); ?>">
<button type="button" class="menu-btn" aria-label="<?php esc_attr_e( 'باز کردن منو', 'teznevise' ); ?>" aria-expanded="false" aria-controls="mobile-navigation" data-menu-toggle><i class="fa-solid fa-bars" data-menu-icon aria-hidden="true"></i></button>
<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php
$logo_url = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : '';
if ( ! $logo_url && has_custom_logo() ) {
	$logo_url = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
}
if ( $logo_url ) {
	$logo_2x = function_exists( 'teznevise_logo_url_2x' ) ? teznevise_logo_url_2x() : $logo_url;
	printf(
		'<img src="%s" srcset="%s 198w, %s 397w" sizes="106px" alt="%s" width="106" height="48" decoding="async" fetchpriority="high">',
		esc_url( $logo_url ),
		esc_url( $logo_url ),
		esc_url( $logo_2x ),
		esc_attr( get_bloginfo( 'name' ) )
	);
} else {
	echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
}
?></a>
<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'nav-links', 'fallback_cb' => 'teznevise_fallback_menu', 'depth' => 3, 'walker' => class_exists( 'Teznevise_Nav_Walker' ) ? new Teznevise_Nav_Walker() : '' ) ); ?>
<div class="nav-actions">
	<button type="button" class="nav-search-btn" data-search-open aria-label="<?php esc_attr_e( 'جستجو', 'teznevise' ); ?>" aria-expanded="false" aria-controls="teznevise-search-overlay"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>
	<?php
	$logged  = is_user_logged_in();
	$balance = ( $logged && function_exists( 'teznevise_tezcoin_balance' ) ) ? teznevise_tezcoin_balance() : null;
	$tip     = $logged
		? sprintf( __( 'موجودی: %s تزکوین', 'teznevise' ), number_format_i18n( (int) $balance ) )
		: __( 'با ثبت‌نام ۳۰ تزکوین هدیه می‌گیرید', 'teznevise' );
	$account = home_url( '/account/' );
	if ( $logged ) :
		?>
	<a class="nav-credits" href="<?php echo esc_url( $account ); ?>" title="<?php echo esc_attr( $tip ); ?>" aria-label="<?php echo esc_attr( $tip ); ?>">
		<i class="fa-solid fa-coins" aria-hidden="true"></i>
		<span><?php echo esc_html( number_format_i18n( (int) $balance ) ); ?></span>
	</a>
		<?php
	endif;
	?>
	<a class="nav-account" href="<?php echo esc_url( $account ); ?>" aria-label="<?php echo $logged ? esc_attr__( 'پروفایل', 'teznevise' ) : esc_attr__( 'ورود', 'teznevise' ); ?>" title="<?php echo $logged ? esc_attr__( 'پروفایل', 'teznevise' ) : esc_attr__( 'ورود', 'teznevise' ); ?>">
		<i class="<?php echo $logged ? 'fa-solid fa-user' : 'fa-regular fa-user'; ?>" aria-hidden="true"></i>
	</a>
	<div class="nav-quick-actions desktop-cta-group">
		<a class="nav-cta nav-cta-outline" href="<?php echo esc_url( teznevise_page_url_from_candidates( array( 'about-us', 'about', 'our-story' ), '/about/' ) ); ?>"><i class="fa-regular fa-circle-question" aria-hidden="true"></i><span><?php esc_html_e( 'درباره ما', 'teznevise' ); ?></span></a>
		<a class="nav-cta nav-cta-solid" href="<?php echo esc_url( teznevise_page_url_from_candidates( array( 'inquiry', 'contact-us', 'contact' ), '/inquiry/' ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></span></a>
	</div>
</div>
</nav>
</header>
<?php
if ( function_exists( 'do_action' ) ) {
	do_action( 'teznevise_after_header' );
}
?>
<?php get_template_part( 'template-parts/mobile-nav' ); ?>
<?php get_template_part( 'template-parts/search-overlay' ); ?>
<main id="main-content" class="flex-1">
