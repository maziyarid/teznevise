<?php
/**
 * Default page template.
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section page-content-section">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="section-head" data-reveal>
				<span class="eyebrow"><?php esc_html_e( 'صفحه', 'teznevise' ); ?></span>
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="longcopy article-content" data-reveal>
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	</div>
</section>

<?php
get_footer();
