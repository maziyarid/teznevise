<?php
get_header();
while ( have_posts() ) : the_post();
	$post_id = get_the_ID();
	$kicker = teznevise_blog_field( 'kicker', $post_id );
	$subtitle = teznevise_blog_field( 'subtitle', $post_id );
	$featured_label = teznevise_blog_field( 'featured_label', $post_id );
	$author_label = teznevise_blog_field( 'author_label', $post_id, get_the_author() );
	$hide_toc = '1' === teznevise_blog_field( 'hide_toc', $post_id );
	$content = apply_filters( 'the_content', get_the_content() );
	$related_heading = teznevise_blog_field( 'related_heading', $post_id, __( 'Related articles', 'teznevise' ) );
	?>
	<main id="post-<?php the_ID(); ?>" <?php post_class( 'site-main single-post blog-post' ); ?>>
		<article>
			<header class="blog-post__header">
				<?php if ( $kicker ) : ?><p class="blog-post__kicker"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
				<?php if ( $featured_label ) : ?><p class="blog-post__featured"><?php echo esc_html( $featured_label ); ?></p><?php endif; ?>
				<h1 class="blog-post__title"><?php the_title(); ?></h1>
				<?php if ( $subtitle ) : ?><p class="blog-post__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
				<div class="blog-post__meta">
					<span><?php echo esc_html( get_the_date() ); ?></span>
					<span><?php echo esc_html( $author_label ); ?></span>
					<span><?php echo esc_html( teznevise_read_time( $post_id ) ); ?></span>
				</div>
			</header>
			<?php if ( has_post_thumbnail() ) : ?><figure class="blog-post__featured-image"><?php the_post_thumbnail( 'full' ); ?></figure><?php endif; ?>
			<div class="blog-post__layout">
				<?php if ( ! $hide_toc ) : ?><aside class="blog-post__toc" aria-label="<?php esc_attr_e( 'Table of contents', 'teznevise' ); ?>"><p><?php esc_html_e( 'On this page', 'teznevise' ); ?></p><?php echo wp_kses_post( teznevise_render_toc( $content ) ); ?></aside><?php endif; ?>
				<div class="blog-post__content entry-content"><?php echo $content; ?></div>
			</div>
		</article>
		<section class="blog-post__related" aria-labelledby="related-posts-title">
			<h2 id="related-posts-title"><?php echo esc_html( $related_heading ); ?></h2>
			<?php $related = teznevise_related_posts( $post_id ); if ( $related->have_posts() ) : ?>
				<div class="post-grid post-grid--related"><?php while ( $related->have_posts() ) : $related->the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?></div>
			<?php endif; wp_reset_postdata(); ?>
		</section>
	</main>
<?php endwhile; get_footer();
