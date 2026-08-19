<?php
/**
 * Admin notice + cleanup for nav-menus screen.
 *
 * The theme's chrome (header/footer/mobile/bottom) is now fully hardcoded
 * in template parts; wp_nav_menu() is no longer called for the classic
 * locations. This file:
 *
 * - Shows a dismissible notice on Appearance → Menus explaining the change.
 * - Unregisters the four legacy nav locations so the screen doesn't show
 *   editable locations that silently do nothing.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_head-nav-menus.php', 'teznevise_menus_page_notice' );
/**
 * Print an inline admin notice on the Menus screen.
 */
function teznevise_menus_page_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'nav-menus' !== $screen->base ) {
		return;
	}
	add_action( 'admin_notices', 'teznevise_render_menus_notice' );
}

/**
 * Render the notice HTML.
 */
function teznevise_render_menus_notice() {
	?>
	<div class="notice notice-info is-dismissible">
		<p>
			<strong><?php esc_html_e( 'تزنویسه — منوهای پوسته', 'teznevise' ); ?></strong>
			<?php
			esc_html_e(
				'هدر، فوتر، منوی موبایل و نوار پایین در این پوسته به‌صورت طراحی‌شده و ثابت پیاده‌سازی شده‌اند و دیگر از «نمایش → منوها" کنترل نمی‌شوند. این تغییر برای جلوگیری از به‌هم‌ریختگی برچسب‌ها و ساختار در نوار ۷۲ پیکسلی انجام شده است.',
				'teznevise'
			);
			?>
			<a href="<?php echo esc_url( 'https://github.com/maziyarid/teznevise/pull/412' ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'توضیحات فنی در PR #412', 'teznevise' ); ?>
			</a>
		</p>
	</div>
	<?php
}

/**
 * Unregister the four legacy nav locations that are no longer used.
 */
add_action( 'after_setup_theme', 'teznevise_unregister_legacy_nav_locations', 11 );
function teznevise_unregister_legacy_nav_locations() {
	unregister_nav_menu( 'primary' );
	unregister_nav_menu( 'mobile' );
	unregister_nav_menu( 'bottom' );
	unregister_nav_menu( 'footer' );
}
