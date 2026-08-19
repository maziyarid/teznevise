<?php
/**
 * Appearance → Menus notice.
 *
 * Header, mobile drawer, bottom bar, and footer chrome are designed HTML
 * matching teznevise_work/. Locations stay registered so existing menu
 * assignments are not deleted, but they no longer render in those surfaces.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Explain designed chrome on the Menus screen.
 */
function teznevise_admin_menus_notice() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->id !== 'nav-menus' ) {
		return;
	}
	echo '<div class="notice notice-info"><p>';
	echo esc_html__( 'تزنویسه: هدر، منوی موبایل، نوار پایین و فوتر از طراحی HTML ثابت (teznevise_work) پیروی می‌کنند و Appearance → Menus آن‌ها را تغییر نمی‌دهد. برای ویرایش برچسب‌ها فایل‌های header.php، footer.php، template-parts/mobile-nav.php و template-parts/bottom-nav.php را به‌روز کنید. مکان‌های منو برای سازگاری ثبت می‌مانند.', 'teznevise' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'teznevise_admin_menus_notice' );
