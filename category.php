<?php
/**
 * Category hub template.
 *
 * @package Teznevise
 */

get_header();

$term = get_queried_object();
?>

<section class="section archive-hub category-hub">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'دسته‌بندی', 'teznevise' ); ?></span>
			<?php
			if ( $term && ! is_wp_error( $term ) ) {
				$term_icon = get_term_meta( $term->term_id, '_teznevise_term_icon', true );
				if ( $term_icon ) {
					echo '<div class="icon-box icon-teal" style="margin-bottom:12px;"><i class="' . esc_attr( $term_icon ) . '" aria-hidden="true"></i></div>';
				}
			}
			?>
			<h1><?php single_cat_title(); ?></h1>
			<?php
			$desc = category_description();
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
		$cats = get_categories( array(
			'hide_empty' => true,
			'number'     => 12,
			'exclude'    => $term && ! is_wp_error( $term ) ? array( (int) $term->term_id ) : array(),
		) );
		if ( $cats ) :
			?>
			<div class="hub-chips" data-reveal style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:28px;">
				<?php foreach ( $cats as $c ) : ?>
					<a class="btn-tz btn-light-tz" href="<?php echo esc_url( get_category_link( $c->term_id ) ); ?>">
						<?php echo esc_html( $c->name ); ?>
						<span style="opacity:.7;">(<?php echo (int) $c->count; ?>)</span>
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
			<?php
			the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => '‹',
				'next_text' => '›',
			) );
			?>
		<?php else : ?>
			<p data-reveal><?php esc_html_e( 'هنوز مطلبی در این دسته نیست.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
