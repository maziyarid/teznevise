<?php
/**
 * Post card for archives and related posts.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$heading_level = isset( $args['heading_level'] ) ? absint( $args['heading_level'] ) : 2;
$heading_level = in_array( $heading_level, array( 2, 3, 4 ), true ) ? $heading_level : 2;
$heading_tag = 'h' . $heading_level;
$categories = get_the_category();
$category = $categories ? $categories[0] : null;
?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read: %s', 'teznevise' ), get_the_title() ) ); ?>">
		<?php if ( has_post_thumbnail() ) : ?><?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) ); ?><?php endif; ?>
	</a>
	<div class="post-card__body">
		<div class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time><?php if ( $category ) : ?><a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a><?php endif; ?></div>
		<<?php echo esc_attr( $heading_tag ); ?>><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></<?php echo esc_attr( $heading_tag ); ?>>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<a class="link-arrow" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'مطالعه مقاله', 'teznevise' ); ?></a>
	</div>
</article>
