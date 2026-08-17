<?php
/**
 * Single blog post template.
 *
 * @package Teznevise
 */
get_header();

while ( have_posts() ) :
	the_post();
	$subtitle       = teznevise_post_field( 'subtitle' );
	$kicker         = teznevise_post_field( 'kicker' );
	$read_time      = teznevise_post_field( 'read_time' );
	$featured_label = teznevise_post_field( 'featured_label' );
	$hide_toc       = '1' === get_post_meta( get_the_ID(), '_teznevise_hide_toc', true );
	?>
	<main id="post-<?php the_ID(); ?>" <?php post_class( 'site-main single-post' ); ?>>
		<article>
			<header class="entry-header">
				<?php if ( $kicker ) : ?><p class="entry-kicker"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
				<?php if ( $featured_label ) : ?><span class="entry-featured-label"><?php echo esc_html( $featured_label ); ?></span><?php endif; ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php if ( $subtitle ) : ?><p class="entry-subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
				<div class="entry-meta">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<?php if ( $read_time ) : ?><span><?php echo esc_html( $read_time ); ?></span><?php endif; ?>
				</div>
			</header>
			<?php if ( has_post_thumbnail() ) : ?><figure class="post-thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure><?php endif; ?>
			<div class="entry-content">
				<?php if ( ! $hide_toc ) : ?>
					<div class="entry-toc">
						<p class="entry-toc__label"><?php esc_html_e( 'On this page', 'teznevise' ); ?></p>
						<?php the_content(); ?>
					</div>
				<?php else : the_content(); endif; ?>
			</div>
		</article>
	</main>
<?php endwhile;
get_footer();
