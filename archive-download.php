<?php
/**
 * Archive template: download CPT.
 *
 * @package Teznevise
 */

get_header();

$term = is_tax() ? get_queried_object() : null;
?>

<section class="page-hero-new section">
	<div class="container">
		<div class="inner section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'منابع', 'teznevise' ); ?></span>
			<h1>
				<?php
				if ( $term && ! is_wp_error( $term ) ) {
					echo esc_html( $term->name );
				} else {
					post_type_archive_title();
				}
				?>
			</h1>
			<?php if ( $term && ! empty( $term->description ) ) : ?>
				<p><?php echo esc_html( $term->description ); ?></p>
			<?php else : ?>
				<p><?php esc_html_e( 'قالب‌ها، چک‌لیست‌ها و فایل‌های قابل دانلود', 'teznevise' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="services-grid tez-dl-grid" data-reveal-stagger style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<a class="service-card" href="<?php the_permalink(); ?>">
						<div class="icon-box icon-teal"><i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i></div>
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 22 ) ); ?></p>
						<span class="link-arrow"><?php esc_html_e( 'مشاهده و دانلود', 'teznevise' ); ?> <span>←</span></span>
					</a>
					<?php
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => '←',
					'next_text' => '→',
				)
			);
			?>
		<?php else : ?>
			<p class="tez-dl-empty"><?php esc_html_e( 'موردی یافت نشد.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
