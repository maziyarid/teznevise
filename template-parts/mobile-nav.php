<?php
/**
 * Mobile navigation drawer.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div id="mobile-navigation" class="mobile-nav" data-mobile-menu role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی موبایل', 'teznevise' ); ?>" hidden>
	<div class="mobile-nav-panel">
		<div class="mobile-nav-header"><span class="mobile-nav-title"><?php esc_html_e( 'منو', 'teznevise' ); ?></span><button type="button" class="mobile-nav-close" aria-label="<?php esc_attr_e( 'بستن منو', 'teznevise' ); ?>" data-menu-close><i class="fa-solid fa-xmark" aria-hidden="true"></i></button></div>
		<nav class="mobile-nav-links" aria-label="<?php esc_attr_e( 'منوی موبایل', 'teznevise' ); ?>"><?php echo wp_nav_menu( array( 'theme_location' => 'mobile', 'container' => false, 'menu_class' => 'mobile-menu-list', 'fallback_cb' => 'teznevise_fallback_menu', 'echo' => false, 'depth' => 3, 'walker' => class_exists( 'Teznevise_Nav_Walker' ) ? new Teznevise_Nav_Walker() : '' ) ); ?></nav>
		<div class="mobile-nav-cta"><a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> <?php esc_html_e( 'ثبت درخواست مشاوره', 'teznevise' ); ?></a><a class="btn-tz btn-outline-tz" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?php esc_html_e( 'گفتگو در واتساپ', 'teznevise' ); ?></a></div>
	</div>
</div>
