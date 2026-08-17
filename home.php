<?php
/**
 * Blog posts index (when a static front page is set).
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'مرکز دانش', 'teznevise' ); ?></span>
			<h1><?php esc_html_e( 'بلاگ', 'teznevise' ); ?></h1>
			<p><?php esc_html_e( 'راهنماهای کاربردی درباره روش تحقیق، نگارش دانشگاهی و تحلیل آماری.', 'teznevise' ); ?></p>
		</div>

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
			<p data-reveal><?php esc_html_e( 'هنوز مطلبی منتشر نشده است.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
