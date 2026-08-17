<?php
/**
 * Mobile bottom navigation.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="bottom-nav" aria-label="<?php esc_attr_e( 'منوی پایین موبایل', 'teznevise' ); ?>">
	<a class="bottom-nav-item<?php echo is_front_page() ? ' active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<i class="fa-solid fa-house" aria-hidden="true"></i><span><?php esc_html_e( 'خانه', 'teznevise' ); ?></span>
	</a>
	<a class="bottom-nav-item<?php echo is_page( 'tools' ) ? ' active' : ''; ?>" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">
		<i class="fa-solid fa-calculator" aria-hidden="true"></i><span><?php esc_html_e( 'ابزارها', 'teznevise' ); ?></span>
	</a>
	<a class="bottom-nav-item<?php echo ( is_home() || is_singular( 'post' ) ) ? ' active' : ''; ?>" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">
		<i class="fa-solid fa-book-open" aria-hidden="true"></i><span><?php esc_html_e( 'بلاگ', 'teznevise' ); ?></span>
	</a>
	<a class="bottom-nav-item<?php echo is_page( 'contact' ) ? ' active' : ''; ?>" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
		<i class="fa-solid fa-phone" aria-hidden="true"></i><span><?php esc_html_e( 'تماس', 'teznevise' ); ?></span>
	</a>
</nav>
