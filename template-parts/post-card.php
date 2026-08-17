<?php
/**
 * Post card for archives and related posts.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$categories = get_the_category();
$category   = $categories ? $categories[0] : null;
?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read: %s', 'teznevise' ), get_the_title() ) ); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?>
		<?php endif; ?>
	</a>
	<div class="post-card__body">
		<div class="post-card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<?php if ( $category ) : ?>
				<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php endif; ?>
		</div>
		<h2><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h2>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<a class="link-arrow" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'مطالعه مقاله', 'teznevise' ); ?></a>
	</div>
</article>
