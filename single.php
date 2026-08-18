<?php
/**
 * Single blog post template.
 *
 * @package Teznevise
 */
get_header();
while ( have_posts() ) : the_post();
	$post_id = get_the_ID();
	$kicker = teznevise_blog_field( 'kicker', $post_id );
	$subtitle = teznevise_blog_field( 'subtitle', $post_id );
	$featured_label = teznevise_blog_field( 'featured_label', $post_id );
	$author_label = teznevise_blog_field( 'author_label', $post_id, get_the_author() );
	$hide_toc = '1' === teznevise_blog_field( 'hide_toc', $post_id );
	$related_heading = teznevise_blog_field( 'related_heading', $post_id, __( 'Related articles', 'teznevise' ) );
	$content = teznevise_prepare_post_content( apply_filters( 'the_content', get_the_content() ) );
	$toc = ! $hide_toc ? teznevise_render_toc( $content ) : '';
	$previous = get_previous_post();
	$next = get_next_post();
	?>
	<section class="site-main blog-post section">
		<div class="container">
			<article <?php post_class( 'single-post' ); ?>>
				<header class="blog-post__header article-header" data-reveal>
					<?php if ( $kicker ) : ?><p class="blog-post__kicker"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
					<?php if ( $featured_label ) : ?><p class="blog-post__featured"><?php echo esc_html( $featured_label ); ?></p><?php endif; ?>
					<h1 class="blog-post__title"><?php the_title(); ?></h1>
					<?php if ( $subtitle ) : ?><p class="blog-post__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
					<div class="blog-post__meta article-meta">
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<?php if ( get_the_modified_time( 'U' ) !== get_the_time( 'U' ) ) : ?><time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>"><?php echo esc_html( sprintf( __( 'Updated %s', 'teznevise' ), get_the_modified_date() ) ); ?></time><?php endif; ?>
						<span><?php echo esc_html( $author_label ); ?></span><span><?php echo esc_html( teznevise_read_time( $post_id ) ); ?></span>
					</div>
				</header>
				<?php if ( has_post_thumbnail() ) : ?><figure class="blog-post__featured-image article-cover" data-reveal><?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?></figure><?php endif; ?>
				<?php if ( $toc ) : ?><details class="blog-post__toc-mobile" data-reveal><summary><span><?php esc_html_e( 'فهرست مطالب', 'teznevise' ); ?></span><span aria-hidden="true">⌄</span></summary><?php echo wp_kses_post( $toc ); ?></details><?php endif; ?>
				<div class="blog-post__layout">
					<?php if ( $toc ) : ?><aside class="blog-post__toc entry-toc" aria-label="<?php esc_attr_e( 'Table of contents', 'teznevise' ); ?>"><p class="entry-toc__label"><?php esc_html_e( 'On this page', 'teznevise' ); ?></p><?php echo wp_kses_post( $toc ); ?></aside><?php endif; ?>
					<div class="blog-post__content entry-content article-content longcopy" data-reveal><?php echo $content; ?></div>
				</div>
				<footer class="article-footer" data-reveal><?php the_tags( '<div class="post-tags">', ' ', '</div>' ); ?></footer>
			</article>
			<?php if ( $previous || $next ) : ?><nav class="blog-post__prev-next" aria-label="<?php esc_attr_e( 'Post navigation', 'teznevise' ); ?>"><div><?php if ( $previous ) : ?><a href="<?php echo esc_url( get_permalink( $previous ) ); ?>"><small><?php esc_html_e( 'Previous article', 'teznevise' ); ?></small><span><?php echo esc_html( get_the_title( $previous ) ); ?></span></a><?php endif; ?></div><div><?php if ( $next ) : ?><a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><small><?php esc_html_e( 'Next article', 'teznevise' ); ?></small><span><?php echo esc_html( get_the_title( $next ) ); ?></span></a><?php endif; ?></div></nav><?php endif; ?>
			<?php if ( comments_open() || get_comments_number() ) : ?><div data-reveal><?php comments_template(); ?></div><?php endif; ?>
			<?php $related = teznevise_related_posts( $post_id ); ?>
			<?php if ( $related && $related->have_posts() ) : ?><section class="blog-post__related" aria-labelledby="related-posts-title"><h2 id="related-posts-title"><?php echo esc_html( $related_heading ); ?></h2><div class="post-grid post-grid--related"><?php while ( $related->have_posts() ) : $related->the_post(); get_template_part( 'template-parts/post-card', null, array( 'heading_level' => 3 ) ); endwhile; ?></div></section><?php endif; wp_reset_postdata(); ?>
		</div>
	</section>
<?php teznevise_builder_render_sections(); ?>
<?php endwhile;
get_footer();
