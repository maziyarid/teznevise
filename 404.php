<?php
/**
 * 404 template — search, quick links, mini visual sitemap.
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section error-404-section">
	<div class="container" style="max-width:900px;margin-inline:auto;">
		<div class="section-head center" data-reveal>
			<span class="eyebrow">۴۰۴</span>
			<h1><?php esc_html_e( 'صفحه پیدا نشد', 'teznevise' ); ?></h1>
			<p><?php esc_html_e( 'آدرس ممکن است اشتباه باشد یا صفحه جابه‌جا شده باشد. از جستجو یا لینک‌های زیر استفاده کنید.', 'teznevise' ); ?></p>
		</div>

		<div class="search-404" data-reveal style="max-width:480px;margin:0 auto 32px;">
			<?php get_search_form(); ?>
		</div>

		<div class="hero-actions" style="justify-content:center;flex-wrap:wrap;gap:12px;margin-bottom:40px;" data-reveal>
			<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<i class="fa-solid fa-house" aria-hidden="true"></i> <?php esc_html_e( 'خانه', 'teznevise' ); ?>
			</a>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>">
				<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> <?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?>
			</a>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>">
				<i class="fa-solid fa-sitemap" aria-hidden="true"></i> <?php esc_html_e( 'نقشه سایت', 'teznevise' ); ?>
			</a>
		</div>

		<div class="services-grid" data-reveal-stagger style="margin-bottom:24px;">
			<?php
			$quick = array(
				array( 'url' => '/service-thesis/', 'icon' => 'fa-solid fa-graduation-cap', 'color' => 'icon-indigo', 'title' => __( 'انجام پایان‌نامه', 'teznevise' ) ),
				array( 'url' => '/service-proposal/', 'icon' => 'fa-solid fa-file-circle-check', 'color' => 'icon-teal', 'title' => __( 'انجام پروپوزال', 'teznevise' ) ),
				array( 'url' => '/service-statistics/', 'icon' => 'fa-solid fa-chart-line', 'color' => 'icon-cyan', 'title' => __( 'تحلیل آماری', 'teznevise' ) ),
				array( 'url' => '/tools/', 'icon' => 'fa-solid fa-calculator', 'color' => 'icon-amber', 'title' => __( 'ابزارهای آنلاین', 'teznevise' ) ),
				array( 'url' => '/blog/', 'icon' => 'fa-regular fa-lightbulb', 'color' => 'icon-danger-soft', 'title' => __( 'مرکز دانش', 'teznevise' ) ),
				array( 'url' => '/contact/', 'icon' => 'fa-solid fa-phone', 'color' => 'icon-purple-soft', 'title' => __( 'تماس با ما', 'teznevise' ) ),
			);
			foreach ( $quick as $item ) :
				?>
				<a class="service-card" href="<?php echo esc_url( home_url( $item['url'] ) ); ?>" style="text-decoration:none;color:inherit;">
					<div class="icon-box <?php echo esc_attr( $item['color'] ); ?>"><i class="<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i></div>
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_footer();
