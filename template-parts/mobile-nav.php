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
		<div class="mobile-nav-links">
			<a class="mobile-nav-link<?php echo is_front_page() ? ' active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="fa-solid fa-house" aria-hidden="true"></i> <?php esc_html_e( 'خانه', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> <?php esc_html_e( 'انجام پایان‌نامه', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><i class="fa-solid fa-file-lines" aria-hidden="true"></i> <?php esc_html_e( 'انجام پروپوزال', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><i class="fa-solid fa-chart-line" aria-hidden="true"></i> <?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><i class="fa-solid fa-calculator" aria-hidden="true"></i> <?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><i class="fa-solid fa-book-open" aria-hidden="true"></i> <?php esc_html_e( 'بلاگ', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/team/' ) ); ?>"><i class="fa-solid fa-user-group" aria-hidden="true"></i> <?php esc_html_e( 'تیم پژوهشگران', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><i class="fa-regular fa-circle-question" aria-hidden="true"></i> <?php esc_html_e( 'درباره ما', 'teznevise' ); ?></a>
			<a class="mobile-nav-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i> <?php esc_html_e( 'تماس با ما', 'teznevise' ); ?></a>
		</div>
		<div class="mobile-nav-cta">
			<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> <?php esc_html_e( 'ثبت درخواست مشاوره', 'teznevise' ); ?></a>
			<a class="btn-tz btn-outline-tz" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?php esc_html_e( 'گفتگو در واتساپ', 'teznevise' ); ?></a>
		</div>
	</div>
</div>
