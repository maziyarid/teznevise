<?php
/**
 * Template Name: صفحه خدمت (Service)
 * Description: Shared service landing — Flexible Page Builder first, page-meta fallback.
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
			if ( function_exists( 'teznevise_the_page_interactive_content' ) ) {
				teznevise_the_page_interactive_content();
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
		$eyebrow     = teznevise_page_field( 'eyebrow', 0, __( 'خدمات پژوهشی', 'teznevise' ) );
		$subtitle   = teznevise_page_field( 'subtitle' );
		$cta_text   = teznevise_page_field( 'cta_text', 0, __( 'شروع مشاوره رایگان', 'teznevise' ) );
		$cta_url    = teznevise_page_field( 'cta_url', 0, '/inquiry/' );
		$hero_note  = teznevise_page_field( 'hero_note' );
		$icon       = function_exists( 'teznevise_get_page_icon' ) ? teznevise_get_page_icon() : teznevise_page_field( 'service_icon', 0, 'fa-solid fa-graduation-cap' );
		if ( ! $icon ) {
			$icon = 'fa-solid fa-graduation-cap';
		}
		$icon_color = teznevise_page_field( 'service_color', 0, 'icon-teal' );
		$features   = teznevise_page_field( 'features' );
		$price_note = teznevise_page_field( 'price_note' );
		$sec_text   = teznevise_page_field( 'secondary_cta_text', 0, __( 'گفتگو در واتساپ', 'teznevise' ) );
		$sec_url    = teznevise_page_field( 'secondary_cta_url', 0, teznevise_get_contact( 'whatsapp' ) );
		?>

<section class="service-hero service-hero-aligned section tz-hero-split">
	<div class="container tz-hero-split__grid">
		<div class="section-head tz-hero-split__copy" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<div class="icon-box <?php echo esc_attr( $icon_color ); ?>" style="margin-bottom:14px;">
				<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
			</div>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="service-lead"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<?php if ( $hero_note ) : ?>
				<p class="hero-note"><?php echo esc_html( $hero_note ); ?></p>
			<?php endif; ?>
			<?php if ( $price_note ) : ?>
				<p class="price-note"><strong><?php echo esc_html( $price_note ); ?></strong></p>
			<?php endif; ?>
			<div class="hero-actions" style="margin-top:20px;display:flex;flex-wrap:wrap;gap:12px;">
				<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>">
					<i class="fa-solid fa-rocket" aria-hidden="true"></i> <?php echo esc_html( $cta_text ); ?>
				</a>
				<?php if ( $sec_text ) : ?>
					<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $sec_url ) ); ?>" target="_blank" rel="noopener">
						<i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?php echo esc_html( $sec_text ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="tz-hero-split__form">
		<?php
		if ( function_exists( 'teznevise_render_hero_inquiry' ) ) {
			echo teznevise_render_hero_inquiry( 'service' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		</div>
	</div>
</section>

		<?php if ( $features ) : ?>
<section class="section bg-soft">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'چه می‌گیرید؟', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'ویژگی‌ها و خروجی‌ها', 'teznevise' ); ?></h2>
		</div>
		<div class="reason-list" data-reveal-stagger style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
			<?php
			$lines = function_exists( 'teznevise_lines' ) ? teznevise_lines( $features ) : array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', is_string( $features ) ? $features : '' ) ) );
			foreach ( $lines as $line ) :
				?>
				<div class="reason-item">
					<div class="icon-box icon-teal"><i class="fa-solid fa-check" aria-hidden="true"></i></div>
					<p><?php echo esc_html( $line ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
		<?php endif; ?>

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
				<h2><?php esc_html_e( 'آماده شروع این خدمت هستید؟', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'موضوع و وضعیت فعلی پروژه را بفرستید؛ مسیر و برآورد اولیه را با شما بررسی می‌کنیم.', 'teznevise' ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
		</div>
	</div>
</section>
		<?php
	}

endwhile;

get_template_part( 'template-parts/universities' );
get_footer();
