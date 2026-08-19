<?php
/**
 * Mobile navigation drawer — matches teznevise_work/index.html
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$logo_url = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : '';
$u        = static function ( $path ) {
	return esc_url( home_url( $path ) );
};
?>
<div id="mobile-navigation" class="mobile-nav" data-mobile-menu role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی موبایل', 'teznevise' ); ?>">
	<div class="mobile-nav-panel">
		<div class="mobile-nav-header">
			<?php if ( $logo_url ) : ?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="96" height="38">
			<?php else : ?>
				<span class="mobile-nav-title"><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
			<button type="button" class="mobile-nav-close" aria-label="<?php esc_attr_e( 'بستن منو', 'teznevise' ); ?>" data-menu-close>
				<i class="fa-solid fa-xmark" aria-hidden="true"></i>
			</button>
		</div>
		<nav class="mobile-nav-links" aria-label="<?php esc_attr_e( 'منوی موبایل', 'teznevise' ); ?>">
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'home' ) ); ?>" href="<?php echo $u( '/' ); ?>"><i class="fa-solid fa-house" aria-hidden="true"></i> <?php esc_html_e( 'خانه', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'thesis' ) ); ?>" href="<?php echo $u( '/service-thesis/' ); ?>"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> <?php esc_html_e( 'انجام پایان‌نامه', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'proposal' ) ); ?>" href="<?php echo $u( '/service-proposal/' ); ?>"><i class="fa-solid fa-file-lines" aria-hidden="true"></i> <?php esc_html_e( 'انجام پروپوزال', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'statistics' ) ); ?>" href="<?php echo $u( '/service-statistics/' ); ?>"><i class="fa-solid fa-chart-line" aria-hidden="true"></i> <?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'tools' ) ); ?>" href="<?php echo $u( '/tools/' ); ?>"><i class="fa-solid fa-calculator" aria-hidden="true"></i> <?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'blog' ) ); ?>" href="<?php echo $u( '/blog/' ); ?>"><i class="fa-solid fa-book-open" aria-hidden="true"></i> <?php esc_html_e( 'بلاگ', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'team' ) ); ?>" href="<?php echo $u( '/team/' ); ?>"><i class="fa-solid fa-user-group" aria-hidden="true"></i> <?php esc_html_e( 'تیم پژوهشگران', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'about' ) ); ?>" href="<?php echo $u( '/about/' ); ?>"><i class="fa-regular fa-circle-question" aria-hidden="true"></i> <?php esc_html_e( 'درباره ما', 'teznevise' ); ?></a>
			<a class="mobile-nav-link<?php echo esc_attr( teznevise_nav_current_class( 'contact' ) ); ?>" href="<?php echo $u( '/contact/' ); ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i> <?php esc_html_e( 'تماس با ما', 'teznevise' ); ?></a>
		</nav>
		<div class="mobile-nav-cta">
			<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>">
				<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
				<?php esc_html_e( 'ثبت درخواست مشاوره', 'teznevise' ); ?>
			</a>
			<a class="btn-tz btn-outline-tz" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>">
				<i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
				<?php esc_html_e( 'گفتگو در واتساپ', 'teznevise' ); ?>
			</a>
		</div>
	</div>
</div>
