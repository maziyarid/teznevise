<?php
get_header();
?>
<main id="primary" class="site-main blog-archive blog-archive--home">
	<header class="blog-archive__header">
		<p class="blog-archive__eyebrow"><?php esc_html_e( 'Teznevise journal', 'teznevise' ); ?></p>
		<h1><?php single_post_title( __( 'Blog', 'teznevise' ) ); ?></h1>
		<p class="blog-archive__intro"><?php esc_html_e( 'Practical insights, research guidance, and useful tools for better academic and professional work.', 'teznevise' ); ?></p>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
		<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => __( 'Previous', 'teznevise' ), 'next_text' => __( 'Next', 'teznevise' ) ) ); ?>
	<?php else : ?>
		<p class="blog-archive__empty"><?php esc_html_e( 'No articles have been published yet.', 'teznevise' ); ?></p>
	<?php endif; ?>
</main>
<?php get_footer();
