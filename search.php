<?php
/**
 * Search results template.
 *
 * @package Teznevise
 */

get_header();
$query = get_search_query();
?>

<section class="section search-results-section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'جستجو', 'teznevise' ); ?></span>
			<h1>
				<?php
				if ( $query ) {
					printf(
						esc_html__( 'نتایج برای: %s', 'teznevise' ),
						esc_html( $query )
					);
				} else {
					esc_html_e( 'جستجو در سایت', 'teznevise' );
				}
				?>
			</h1>
		</div>

		<div class="search-again" data-reveal style="max-width:480px;margin-bottom:28px;">
			<?php get_search_form(); ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="article-grid" data-reveal-stagger>
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p data-reveal><?php esc_html_e( 'نتیجه‌ای یافت نشد. عبارت دیگری را امتحان کنید یا از نقشه سایت استفاده کنید.', 'teznevise' ); ?></p>
			<div class="hero-actions" data-reveal style="margin-top:20px;gap:12px;display:flex;flex-wrap:wrap;">
				<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'teznevise' ); ?></a>
				<a class="btn-tz btn-light-tz" href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>"><?php esc_html_e( 'نقشه سایت', 'teznevise' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
