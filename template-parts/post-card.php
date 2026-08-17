<?php
/**
 * Post card for archives.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'article-card' ); ?>>
	<a href="<?php the_permalink(); ?>" class="article-cover"<?php
	if ( has_post_thumbnail() ) {
		$thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
		echo ' style="background-image:url(' . esc_url( $thumb ) . ')"';
	}
	?>></a>
	<div class="article-body">
		<div class="article-meta">
			<span><?php echo esc_html( get_the_date() ); ?></span>
			<?php
			$cats = get_the_category();
			if ( $cats ) {
				echo '<span>' . esc_html( $cats[0]->name ) . '</span>';
			}
			?>
		</div>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
		<a class="link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'مطالعه مقاله', 'teznevise' ); ?></a>
	</div>
</article>
