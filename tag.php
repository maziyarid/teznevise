<?php
/**
 * Tag hub template.
 *
 * @package Teznevise
 */

get_header();

$term = get_queried_object();
?>

<section class="section archive-hub tag-hub">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'برچسب', 'teznevise' ); ?></span>
			<?php
			if ( $term && ! is_wp_error( $term ) ) {
				$term_icon = get_term_meta( $term->term_id, '_teznevise_term_icon', true );
				if ( $term_icon ) {
					echo '<div class="icon-box icon-teal" style="margin-bottom:12px;"><i class="' . esc_attr( $term_icon ) . '" aria-hidden="true"></i></div>';
				}
			}
			?>
			<h1>#<?php single_tag_title(); ?></h1>
			<?php
			$desc = tag_description();
			if ( $desc ) {
				echo '<div class="archive-desc">' . wp_kses_post( $desc ) . '</div>';
			}
			?>
			<?php if ( $term && ! is_wp_error( $term ) ) : ?>
				<p class="archive-count" style="opacity:.8;">
					<?php
					printf(
						esc_html( _n( '%d مطلب', '%d مطلب', (int) $term->count, 'teznevise' ) ),
						(int) $term->count
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<?php
		$tags = get_tags( array(
			'hide_empty' => true,
			'number'     => 20,
			'orderby'    => 'count',
			'order'      => 'DESC',
		) );
		if ( $tags ) :
			?>
			<div class="hub-chips" data-reveal style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:28px;">
				<?php foreach ( $tags as $t ) : ?>
					<a class="btn-tz btn-light-tz" href="<?php echo esc_url( get_tag_link( $t->term_id ) ); ?>">
						#<?php echo esc_html( $t->name ); ?>
						<span style="opacity:.7;">(<?php echo (int) $t->count; ?>)</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="article-grid" data-reveal-stagger>
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p data-reveal><?php esc_html_e( 'مطلبی با این برچسب یافت نشد.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
