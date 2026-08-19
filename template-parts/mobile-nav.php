<?php
/**
 * Mobile off-canvas navigation.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="mobile-navigation" class="mobile-nav" data-mobile-menu role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی موبایل', 'teznevise' ); ?>" hidden>
	<div class="mobile-nav-panel">
		<div class="mobile-nav-header">
			<?php
			$logo_url = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : '';
			if ( $logo_url ) {
				echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
			}
			?>
			<button type="button" class="mobile-nav-close" data-menu-close aria-label="<?php esc_attr_e( 'بستن منو', 'teznevise' ); ?>">
				<i class="fa-solid fa-xmark" aria-hidden="true"></i>
			</button>
		</div>
		<?php
		wp_nav_menu( array(
			'theme_location' => 'mobile',
			'container'      => false,
			'menu_class'     => 'mobile-nav-links',
			'fallback_cb'    => 'teznevise_fallback_menu',
			'depth'          => 3,
		) );
		?>
		<div class="mobile-nav-cta">
			<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>">
				<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> <?php esc_html_e( 'ثبت درخواست مشاوره', 'teznevise' ); ?>
			</a>
			<a class="btn-tz btn-outline-tz" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>">
				<i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?php esc_html_e( 'گفتگو در واتسا٢', 'teznevise' ); ?>
			</a>
		</div>
	</div>
</div>
