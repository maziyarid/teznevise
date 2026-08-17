<?php
/**
 * Archive template (categories, tags, dates).
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'بایگانی', 'teznevise' ); ?></span>
			<h1><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<p>', '</p>' ); ?>
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
			<p data-reveal><?php esc_html_e( 'مطلبی در این بایگانی نیست.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
