<?php
$post_id = get_the_ID();
$subtitle = teznevise_blog_field( 'subtitle', $post_id );
$read_time = teznevise_read_time( $post_id );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?><div class="post-card__media"><?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?></div><?php endif; ?>
		<div class="post-card__body">
			<div class="post-card__meta"><span><?php echo esc_html( get_the_date() ); ?></span><span><?php echo esc_html( $read_time ); ?></span></div>
			<h2 class="post-card__title"><?php the_title(); ?></h2>
			<?php if ( $subtitle ) : ?><p class="post-card__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php elseif ( has_excerpt() ) : ?><p class="post-card__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			<span class="post-card__more"><?php esc_html_e( 'Read article', 'teznevise' ); ?></span>
		</div>
	</a>
</article>
