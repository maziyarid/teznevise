<?php
/**
 * Archive template (categories, tags, dates).
 *
 * @package Teznevise
 */
get_header();
$archive_title       = get_the_archive_title();
$archive_description = get_the_archive_description();
?>

<section class="site-main blog-archive blog-archive--taxonomy section">
	<div class="container">
		<header class="blog-archive__header" data-reveal>
			<p class="blog-archive__eyebrow"><?php esc_html_e( 'بایگانی', 'teznevise' ); ?></p>
			<h1><?php echo esc_html( wp_strip_all_tags( $archive_title ) ); ?></h1>
			<?php if ( $archive_description ) : ?><div class="blog-archive__intro"><?php echo wp_kses_post( $archive_description ); ?></div><?php endif; ?>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid" data-reveal-stagger>
				<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __( 'جدیدتر', 'teznevise' ), 'next_text' => __( 'قدیمی‌تر', 'teznevise' ) ) ); ?>
		<?php else : ?>
			<p class="blog-archive__empty" data-reveal><?php esc_html_e( 'مطلبی در این بایگانی نیست.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
