<?php
/**
 * Search results template.
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'جستجو', 'teznevise' ); ?></span>
			<h1>
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'نتایج برای: %s', 'teznevise' ),
					'<span>' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
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
			<p data-reveal><?php esc_html_e( 'نتیجه‌ای یافت نشد. عبارت دیگری را امتحان کنید.', 'teznevise' ); ?></p>
			<div data-reveal style="margin-top:24px;">
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
