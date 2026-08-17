<?php
get_header();
$archive_title = get_the_archive_title();
$archive_description = get_the_archive_description();
?>
<main id="primary" class="site-main blog-archive blog-archive--taxonomy">
	<header class="blog-archive__header">
		<p class="blog-archive__eyebrow"><?php esc_html_e( 'Teznevise journal', 'teznevise' ); ?></p>
		<h1><?php echo esc_html( wp_strip_all_tags( $archive_title ) ); ?></h1>
		<?php if ( $archive_description ) : ?><div class="blog-archive__intro"><?php echo wp_kses_post( $archive_description ); ?></div><?php endif; ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
		<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => __( 'Previous', 'teznevise' ), 'next_text' => __( 'Next', 'teznevise' ) ) ); ?>
	<?php else : ?>
		<p class="blog-archive__empty"><?php esc_html_e( 'No articles found.', 'teznevise' ); ?></p>
	<?php endif; ?>
</main>
<?php get_footer();
