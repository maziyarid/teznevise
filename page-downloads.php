<?php
/**
 * Template Name: دانلودها (Downloads)
 * Description: Downloadable resources. Each item: title|url|desc
 *
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: maziyarid/M-Z — A brand new repository with my complete brand identity, story, and website prototype.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$eyebrow   = teznevise_page_field( 'eyebrow', 0, __( 'منابع', 'teznevise' ) );
	$subtitle = teznevise_page_field( 'subtitle', 0, __( 'قالب‌ها، چک‌لیست‌ها و راهنماهای آماده', 'teznevise' ) );
	$items    = function_exists( 'teznevise_parse_pipe_list' ) ? teznevise_parse_pipe_list( teznevise_page_field( 'downloads_list' ), 3 ) : array();
	if ( ! $items ) {
		$items = array(
			array( 'قالب پروپوزال ارشد', '#', 'ساختار استاندارد پروپوزال برای مقطع ارشد.' ),
			array( 'قالب فصل اول پایان‌نامه', '#', 'چارچوب کلی فصل اول و بیان مسئله.' ),
			array( 'چک‌لیست دفاع پایان‌نامه', '#', 'موارد ضروری قبل از جلسه دفاع.' ),
			array( 'نمونه بیان مسئله', '#', 'الگوی نگارش بیان مسئله پژوهشی.' ),
			array( 'راهنمای انتخاب موضوع', '#', 'نکات عملی برای انتخاب موضوع مناسب.' ),
			array( 'جدول تعیین حجم نمونه', '#', 'مرجع سریع برای تعیین حجم نمونه.' ),
		);
	}
	?>

<section class="page-hero-new section">
	<div class="container">
		<div class="inner section-head" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'فایل‌های قابل دانلود', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'منابع کاربردی برای شروع و پیشبرد پژوهش', 'teznevise' ); ?></h2>
		</div>
		<div class="services-grid" data-reveal-stagger style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
			<?php foreach ( $items as $item ) : ?>
				<div class="service-card">
					<div class="icon-box icon-teal"><i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i></div>
					<h3><?php echo esc_html( $item[0] ); ?></h3>
					<?php if ( ! empty( $item[2] ) ) : ?>
						<p><?php echo esc_html( $item[2] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $item[1] ) && '#' !== $item[1] ) : ?>
						<a class="btn-tz btn-outline-tz" href="<?php echo esc_url( teznevise_url( $item[1] ) ); ?>" download>
							<i class="fa-solid fa-download" aria-hidden="true"></i> <?php esc_html_e( 'دانلود', 'teznevise' ); ?>
						</a>
					<?php else : ?>
						<span class="text-muted" style="font-size:13px;"><?php esc_html_e( 'به‌زودی — لینک را در فیلدهای صفحه تنظیم کنید', 'teznevise' ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="longcopy article-content" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>

<?php teznevise_builder_render_sections(); ?>

	<?php
endwhile;

get_footer();
