<?php
/**
 * Archive template: case_study CPT.
 *
 * @package Teznevise
 */

get_header();

$term = is_tax() ? get_queried_object() : null;
?>

<section class="page-hero-new section">
	<div class="container">
		<div class="inner section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'نمونه‌کار', 'teznevise' ); ?></span>
			<h1>
				<?php
				if ( $term && ! is_wp_error( $term ) ) {
					echo esc_html( $term->name );
				} else {
					post_type_archive_title();
				}
				?>
			</h1>
			<p><?php esc_html_e( 'مطالعات موردی و پروژه‌های انجام‌شده', 'teznevise' ); ?></p>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="services-grid" data-reveal-stagger style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
				<?php
				while ( have_posts() ) :
					the_post();
					$field  = get_post_meta( get_the_ID(), '_tz_cs_field', true );
					$result = get_post_meta( get_the_ID(), '_tz_cs_result', true );
					?>
					<a class="service-card" href="<?php the_permalink(); ?>">
						<div class="icon-box icon-indigo"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></div>
						<h3><?php the_title(); ?></h3>
						<?php if ( $field ) : ?>
							<p><strong><?php echo esc_html( $field ); ?></strong></p>
						<?php endif; ?>
						<p><?php echo esc_html( $result ? wp_trim_words( $result, 20 ) : wp_trim_words( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 22 ) ); ?></p>
						<span class="link-arrow"><?php esc_html_e( 'مشاهده', 'teznevise' ); ?> <span>←</span></span>
					</a>
					<?php
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => '←', 'next_text' => '→' ) ); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'موردی یافت نشد.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
