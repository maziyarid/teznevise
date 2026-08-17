<?php
/** Single blog post template. @package Teznevise */
get_header();
while ( have_posts() ) : the_post();
	$subtitle = teznevise_post_field( 'subtitle' ); $kicker = teznevise_post_field( 'kicker' ); $read_time = teznevise_post_field( 'read_time' ); $featured_label = teznevise_post_field( 'featured_label' ); $hide_toc = '1' === teznevise_post_field( 'hide_toc' );
	$content = apply_filters( 'the_content', get_the_content() ); $content = teznevise_post_content_with_toc_ids( $content );
	?>
	<section class="section">
		<div class="container">
			<article <?php post_class( 'single-post' ); ?>><header class="article-header" data-reveal>
				<?php if ( $kicker ) : ?><p class="entry-kicker"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
				<?php if ( $featured_label ) : ?><span class="entry-featured-label"><?php echo esc_html( $featured_label ); ?></span><?php endif; ?>
				<div class="article-meta"><span><?php echo esc_html( get_the_date() ); ?></span><?php if ( get_the_category() ) : ?><span><?php echo esc_html( get_the_category()[0]->name ); ?></span><?php endif; ?><?php if ( $read_time ) : ?><span><?php echo esc_html( $read_time ); ?></span><?php endif; ?></div>
				<h1><?php the_title(); ?></h1>
				<?php if ( $subtitle ) : ?><p class="entry-subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
			</header>
			<?php if ( has_post_thumbnail() ) : ?><div class="article-cover" data-reveal><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
			<div class="entry-content">
				<?php if ( ! $hide_toc ) : ?><aside class="entry-toc" aria-label="<?php esc_attr_e( 'Table of contents', 'teznevise' ); ?>"><p class="entry-toc__label"><?php esc_html_e( 'On this page', 'teznevise' ); ?></p><?php echo wp_kses_post( teznevise_render_post_toc( $content ) ); ?></aside><?php endif; ?>
				<div class="article-content longcopy" data-reveal><?php echo $content; ?></div>
			</div>
			<footer class="article-footer" data-reveal><?php the_tags( '<div class="post-tags">', ' ', '</div>' ); ?></footer></article>
			<?php if ( comments_open() || get_comments_number() ) : ?><div data-reveal><?php comments_template(); ?></div><?php endif; ?>
		</div>
	</section>
<?php endwhile; get_footer();
