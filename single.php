<?php
/**
 * Single post template.
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'single-post' ); ?>>
				<header class="article-header" data-reveal>
					<div class="article-meta">
						<span><?php echo esc_html( get_the_date() ); ?></span>
						<?php
						$cats = get_the_category();
						if ( $cats ) {
							echo '<span>' . esc_html( $cats[0]->name ) . '</span>';
						}
						?>
					</div>
					<h1><?php the_title(); ?></h1>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="article-cover" data-reveal>
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>

				<div class="article-content longcopy" data-reveal>
					<?php the_content(); ?>
				</div>

				<footer class="article-footer" data-reveal>
					<?php the_tags( '<div class="post-tags">', ' ', '</div>' ); ?>
				</footer>
			</article>
			<?php if ( comments_open() || get_comments_number() ) : ?>
				<div data-reveal>
					<?php comments_template(); ?>
				</div>
			<?php endif; ?>
		<?php endwhile; ?>
	</div>
</section>

<?php
get_footer();
