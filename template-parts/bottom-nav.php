<?php
/**
 * Bottom mobile navigation — four designed items from teznevise_work/index.html
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="bottom-nav" aria-label="<?php esc_attr_e( 'منوی پایین موبایل', 'teznevise' ); ?>">
	<a class="bottom-nav-item<?php echo esc_attr( teznevise_nav_current_class( 'home' ) ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<i class="fa-solid fa-house" aria-hidden="true"></i>
		<span><?php esc_html_e( 'خانه', 'teznevise' ); ?></span>
	</a>
	<a class="bottom-nav-item<?php echo esc_attr( teznevise_nav_current_class( 'tools' ) ); ?>" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">
		<i class="fa-solid fa-calculator" aria-hidden="true"></i>
		<span><?php esc_html_e( 'ابزارها', 'teznevise' ); ?></span>
	</a>
	<a class="bottom-nav-item<?php echo esc_attr( teznevise_nav_current_class( 'blog' ) ); ?>" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
		<i class="fa-solid fa-book-open" aria-hidden="true"></i>
		<span><?php esc_html_e( 'بلاگ', 'teznevise' ); ?></span>
	</a>
	<a class="bottom-nav-item<?php echo esc_attr( teznevise_nav_current_class( 'contact' ) ); ?>" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
		<i class="fa-solid fa-phone" aria-hidden="true"></i>
		<span><?php esc_html_e( 'تماس', 'teznevise' ); ?></span>
	</a>
</nav>
