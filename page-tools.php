<?php
/**
 * Template Name: ابزارهای آنلاین (Tools Hub)
 * Description: Online tools grid via Flexible Page Builder, with page-meta fallback.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$use_builder = function_exists( 'teznevise_builder_has_sections' ) && teznevise_builder_has_sections();

	if ( $use_builder ) {
		teznevise_builder_render_sections();
		if ( function_exists( 'teznevise_page_should_print_content' ) ? teznevise_page_should_print_content() : get_the_content() ) :
			?>
<section class="section">
	<div class="container">
		<div class="longcopy article-content" data-reveal>
			<?php
			if ( function_exists( 'teznevise_the_page_leftover_content' ) ) {
				teznevise_the_page_leftover_content();
			} else {
				the_content();
			}
			?>
		</div>
	</div>
</section>
			<?php
		endif;
	} else {
		$eyebrow   = teznevise_page_field( 'eyebrow', 0, __( 'ابزار رایگان', 'teznevise' ) );
		$subtitle = teznevise_page_field( 'subtitle', 0, __( 'ماشین‌حساب‌های آماری برای پژوهشگران', 'teznevise' ) );
		$cta_text = teznevise_page_field( 'cta_text', 0, __( 'درخواست تحلیل تخصصی', 'teznevise' ) );
		$cta_url  = teznevise_page_field( 'cta_url', 0, '/service-statistics/' );

		$tools = function_exists( 'teznevise_parse_pipe_list' ) ? teznevise_parse_pipe_list( teznevise_page_field( 'tools_list' ), 4 ) : array();
		if ( ! $tools ) {
			$tools = array(
				array( 'آمار توصیفی', '/tool-descriptive-statistics/', 'میانگین، میانه، واریانس و شاخص‌های توصیفی.', 'fa-solid fa-chart-simple' ),
				array( 'همبستگی پیرسون', '/tools/', 'ضریب همبستگی خطی پیرسون.', 'fa-solid fa-chart-line' ),
				array( 'همبستگی اسپیرمن', '/tools/', 'همبستگی رتبه‌ای اسپیرمن.', 'fa-solid fa-chart-area' ),
				array( 'آزمون T-test', '/tools/', 'مقایسه میانگین‌ها.', 'fa-solid fa-not-equal' ),
				array( 'تحلیل واریانس (ANOVA)', '/tools/', 'مقایسه چند گروه.', 'fa-solid fa-table' ),
				array( 'آلفای کرونباخ', '/tools/', 'پایایی مقیاس.', 'fa-solid fa-list-check' ),
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
		<div class="services-grid" data-reveal-stagger style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
			<?php foreach ( $tools as $tool ) : ?>
				<a class="service-card" href="<?php echo esc_url( teznevise_url( $tool[1] ? $tool[1] : '/tools/' ) ); ?>" style="text-decoration:none;color:inherit;">
					<div class="icon-box icon-amber"><i class="<?php echo esc_attr( $tool[3] ? $tool[3] : 'fa-solid fa-calculator' ); ?>" aria-hidden="true"></i></div>
					<h3><?php echo esc_html( $tool[0] ); ?></h3>
					<?php if ( ! empty( $tool[2] ) ) : ?>
						<p><?php echo esc_html( $tool[2] ); ?></p>
					<?php endif; ?>
					<span class="link-arrow"><?php esc_html_e( 'باز کردن ابزار', 'teznevise' ); ?> <span>←</span></span>
				</a>
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

<section class="section">
	<div class="container">
		<div class="cta-band" data-reveal>
			<div>
				<h2><?php esc_html_e( 'نیاز به تحلیل تخصصی دارید؟', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'اگر خروجی ابزارها کافی نیست، مسیر تحلیل آماری یا شبیه‌سازی را با تیم ما شروع کنید.', 'teznevise' ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
		</div>
	</div>
</section>
		<?php
	}

endwhile;

get_footer();
