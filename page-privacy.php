<?php
/**
 * Template Name: حریم خصوصی (Privacy Policy)
 * Description: Privacy / legal policy layout.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();
	$eyebrow   = teznevise_page_field( 'eyebrow', 0, __( 'حقوق و سیاست‌ها', 'teznevise' ) );
	$subtitle = teznevise_page_field( 'subtitle', 0, __( 'نحوه جمع‌آوری، استفاده و محافظت از اطلاعات شما', 'teznevise' ) );
	$updated  = teznevise_page_field( 'hero_note', 0, '' );
	?>

<section class="section privacy-policy-section">
	<div class="container" style="max-width:820px;margin-inline:auto;">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<?php if ( $updated ) : ?>
				<p class="privacy-updated" style="opacity:.8;font-size:.95rem;">
					<i class="fa-regular fa-calendar" aria-hidden="true"></i>
					<?php echo esc_html( $updated ); ?>
				</p>
			<?php else : ?>
				<p class="privacy-updated" style="opacity:.8;font-size:.95rem;">
					<?php
					printf(
						esc_html__( 'آخرین به‌روزرسانی: %s', 'teznevise' ),
						esc_html( get_the_modified_date() )
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<div class="longcopy article-content privacy-body" data-reveal>
			<?php the_content(); ?>
		</div>

		<div class="cta-band" data-reveal style="margin-top:40px;">
			<div>
				<h2><?php esc_html_e( 'سوالی درباره حریم خصوصی دارید؟', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'از طریق ایمیل یا فرم تماس با ما در ارتباط باشید.', 'teznevise' ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<?php esc_html_e( 'تماس با ما', 'teznevise' ); ?>
			</a>
		</div>
	</div>
</section>

<?php teznevise_builder_render_sections(); ?>

	<?php
endwhile;

get_footer();
