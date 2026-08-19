<?php
/**
 * Blog posts index (when a static front page is set).
 *
 * Maps from teznevise_work/blog.html. Native WP_Query loop — not seeded into
 * the Flexible Page Builder. See docs/HTML-TO-BUILDER-ROADMAP.md.
 *
 * @package Teznevise
 */
get_header();
?>

<section class="site-main blog-archive blog-archive--home section">
	<div class="container blog-layout">
		<div>
		<header class="blog-archive__header" data-reveal>
			<p class="blog-archive__eyebrow"><?php esc_html_e( 'مرکز دانش', 'teznevise' ); ?></p>
			<h1><?php esc_html_e( 'بلاگ', 'teznevise' ); ?></h1>
			<p class="blog-archive__intro"><?php esc_html_e( 'راهنماهای کاربردی درباره روش تحقیق، نگارش دانشگاهی و تحلیل آماری.', 'teznevise' ); ?></p>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid" data-reveal-stagger>
				<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/post-card' ); endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => __( 'مقالات جدیدتر', 'teznevise' ), 'next_text' => __( 'مقالات قدیمی‌تر', 'teznevise' ) ) ); ?>
		<?php else : ?>
			<p class="blog-archive__empty" data-reveal><?php esc_html_e( 'هنوز مطلبی منتشر نشده است.', 'teznevise' ); ?></p>
		<?php endif; ?>
		</div>
		<aside class="blog-sidebar" aria-label="<?php esc_attr_e( 'نوار کناری بلاگ', 'teznevise' ); ?>">
			<div class="blog-widget side-card">
				<h2><?php esc_html_e( 'جستجو', 'teznevise' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
			<div class="blog-widget side-card">
				<h2><?php esc_html_e( 'دسته‌ها', 'teznevise' ); ?></h2>
				<ul class="cat-list">
					<?php wp_list_categories( array( 'title_li' => '', 'show_count' => false ) ); ?>
				</ul>
			</div>
			<div class="blog-widget side-card">
				<h2><?php esc_html_e( 'شروع پروژه', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'موضوع را بفرستید تا مسیر و برآورد اولیه مشخص شود.', 'teznevise' ); ?></p>
				<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><?php esc_html_e( 'درخواست مشاوره', 'teznevise' ); ?></a>
			</div>
		</aside>
	</div>
</section>

<?php get_footer();
