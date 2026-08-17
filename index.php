<?php
/**
 * Main template file (fallback).
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="section-head" data-reveal>
				<span class="eyebrow"><?php esc_html_e( 'مطالب', 'teznevise' ); ?></span>
				<h1><?php
					if ( is_home() && ! is_front_page() ) {
						single_post_title();
					} elseif ( is_archive() ) {
						the_archive_title();
					} else {
						esc_html_e( 'بلاگ', 'teznevise' );
					}
				?></h1>
			</div>

			<div class="article-grid" data-reveal-stagger>
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</div>

			<?php the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => '‹',
				'next_text' => '›',
			) ); ?>

		<?php else : ?>
			<p><?php esc_html_e( 'مطلبی یافت نشد.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
