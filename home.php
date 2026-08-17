<?php
/**
 * Blog posts index (when a static front page is set).
 *
 * @package Teznevise
 */
get_header();
?>

<section class="site-main blog-archive blog-archive--home section">
	<div class="container">
		<header class="blog-archive__header" data-reveal>
			<p class="blog-archive__eyebrow"><?php esc_html_e( 'مرکز دانش', 'teznevise' ); ?></p>
			<h1><?php esc_html_e( 'بلاگ', 'teznevise' ); ?></h1>
			<p class="blog-archive__intro"><?php esc_html_e( 'راهنماهای کاربردی درباره روش تحقیق، نگارش دانشگاهی و تحلیل آماری.', 'teznevise' ); ?></p>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid" data-reveal-stagger>
				<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __( 'مقالات جدیدتر', 'teznevise' ), 'next_text' => __( 'مقالات قدیمی‌تر', 'teznevise' ) ) ); ?>
		<?php else : ?>
			<p class="blog-archive__empty" data-reveal><?php esc_html_e( 'هنوز مطلبی منتشر نشده است.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
