<?php
/**
 * Desktop primary nav — markup matches teznevise_work/index.html
 * (خانه، خدمات، ابزارها، بلاگ، درباره ما + dropdowns).
 *
 * Live WP Appearance → Menus had 8 long labels that wrapped inside the
 * 72px pill. Chrome is designed; it is not a dump of the WP tree.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$u = static function ( $path ) {
	return esc_url( home_url( $path ) );
};
?>
<ul class="nav-links">
	<li>
		<a class="<?php echo esc_attr( trim( teznevise_nav_current_class( 'home' ) ) ); ?>" href="<?php echo $u( '/' ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'خانه', 'teznevise' ); ?></a>
	</li>
	<li class="menu-item-has-children">
		<a class="<?php echo esc_attr( trim( teznevise_nav_current_class( 'services' ) ) ); ?>" href="<?php echo $u( '/service-thesis/' ); ?>"><?php esc_html_e( 'خدمات', 'teznevise' ); ?> <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i></a>
		<ul class="nav-dropdown">
			<li>
				<a href="<?php echo $u( '/service-thesis/' ); ?>"><?php esc_html_e( 'پایان‌نامه', 'teznevise' ); ?> <i class="fa-solid fa-chevron-left nav-chevron" aria-hidden="true"></i></a>
				<ul class="nav-dropdown-l3">
					<li><a href="<?php echo $u( '/service-thesis/' ); ?>"><?php esc_html_e( 'ارشد و دکتری', 'teznevise' ); ?></a></li>
					<li><a href="<?php echo $u( '/service-thesis/' ); ?>"><?php esc_html_e( 'نگارش فصل‌ها', 'teznevise' ); ?></a></li>
					<li><a href="<?php echo $u( '/service-thesis/' ); ?>"><?php esc_html_e( 'آمادگی دفاع', 'teznevise' ); ?></a></li>
				</ul>
			</li>
			<li>
				<a href="<?php echo $u( '/service-proposal/' ); ?>"><?php esc_html_e( 'پروپوزال', 'teznevise' ); ?> <i class="fa-solid fa-chevron-left nav-chevron" aria-hidden="true"></i></a>
				<ul class="nav-dropdown-l3">
					<li><a href="<?php echo $u( '/service-proposal/' ); ?>"><?php esc_html_e( 'بیان مسئله', 'teznevise' ); ?></a></li>
					<li><a href="<?php echo $u( '/service-proposal/' ); ?>"><?php esc_html_e( 'روش‌شناسی', 'teznevise' ); ?></a></li>
					<li><a href="<?php echo $u( '/service-proposal/' ); ?>"><?php esc_html_e( 'مرور ادبیات', 'teznevise' ); ?></a></li>
				</ul>
			</li>
			<li>
				<a href="<?php echo $u( '/service-statistics/' ); ?>"><?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?> <i class="fa-solid fa-chevron-left nav-chevron" aria-hidden="true"></i></a>
				<ul class="nav-dropdown-l3">
					<li><a href="<?php echo $u( '/service-statistics/' ); ?>">SPSS / R / Python</a></li>
					<li><a href="<?php echo $u( '/service-statistics/' ); ?>"><?php esc_html_e( 'مدل‌سازی معادلات', 'teznevise' ); ?></a></li>
					<li><a href="<?php echo $u( '/tools/' ); ?>"><?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a></li>
				</ul>
			</li>
		</ul>
	</li>
	<li class="menu-item-has-children">
		<a class="<?php echo esc_attr( trim( teznevise_nav_current_class( 'tools' ) ) ); ?>" href="<?php echo $u( '/tools/' ); ?>"><?php esc_html_e( 'ابزارها', 'teznevise' ); ?> <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i></a>
		<ul class="nav-dropdown">
			<li><a href="<?php echo $u( '/tools/' ); ?>"><?php esc_html_e( 'آمار توصیفی', 'teznevise' ); ?></a></li>
			<li><a href="<?php echo $u( '/tools/' ); ?>"><?php esc_html_e( 'همبستگی و رگرسیون', 'teznevise' ); ?></a></li>
			<li><a href="<?php echo $u( '/tools/' ); ?>"><?php esc_html_e( 'آزمون‌های فرض', 'teznevise' ); ?></a></li>
		</ul>
	</li>
	<li class="menu-item-has-children">
		<a class="<?php echo esc_attr( trim( teznevise_nav_current_class( 'blog' ) ) ); ?>" href="<?php echo $u( '/blog/' ); ?>"><?php esc_html_e( 'بلاگ', 'teznevise' ); ?> <i class="fa-solid fa-chevron-down nav-chevron" aria-hidden="true"></i></a>
		<ul class="nav-dropdown">
			<li><a href="<?php echo $u( '/blog/' ); ?>"><?php esc_html_e( 'راهنمای پایان‌نامه', 'teznevise' ); ?></a></li>
			<li><a href="<?php echo $u( '/blog/' ); ?>"><?php esc_html_e( 'روش تحقیق', 'teznevise' ); ?></a></li>
			<li><a href="<?php echo $u( '/blog/' ); ?>"><?php esc_html_e( 'آمار و نرم‌افزار', 'teznevise' ); ?></a></li>
		</ul>
	</li>
	<li>
		<a class="<?php echo esc_attr( trim( teznevise_nav_current_class( 'about' ) ) ); ?>" href="<?php echo $u( '/about/' ); ?>"><?php esc_html_e( 'درباره ما', 'teznevise' ); ?></a>
	</li>
</ul>
