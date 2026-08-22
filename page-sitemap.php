<?php
/**
 * Template Name: نقشه سایت بصری (Visual Sitemap)
 * Description: Human-readable visual sitemap of pages, services, categories, and posts.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();
	$eyebrow   = teznevise_page_field( 'eyebrow', 0, __( 'نقشه سایت', 'teznevise' ) );
	$subtitle = teznevise_page_field( 'subtitle', 0, __( 'دسترسی سریع به همه بخش‌های تزنویسه', 'teznevise' ) );
	?>

<section class="section visual-sitemap">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( get_the_content() ) : ?>
			<div class="longcopy" data-reveal style="margin-bottom:32px;"><?php the_content(); ?></div>
		<?php endif; ?>

		<div class="sitemap-grid" style="display:grid;gap:28px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">

			<div class="sitemap-col" data-reveal>
				<h2 class="sitemap-heading"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> <?php esc_html_e( 'خدمات', 'teznevise' ); ?></h2>
				<ul class="sitemap-list" style="list-style:none;padding:0;margin:0;display:grid;gap:8px;">
					<?php
					$services = array(
						'/service-thesis/'     => __( 'مشاوره انجام پایان‌نامه', 'teznevise' ),
						'/service-proposal/'   => __( 'مشاوره انجام پروپوزال', 'teznevise' ),
						'/service-statistics/' => __( 'تحلیل آماری', 'teznevise' ),
						'/service-simulation/' => __( 'شبیه‌سازی', 'teznevise' ),
						'/tools/'              => __( 'ابزارهای آنلاین', 'teznevise' ),
					);
					foreach ( $services as $path => $label ) :
						?>
						<li><a class="link-arrow" href="<?php echo esc_url( home_url( $path ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="sitemap-col" data-reveal>
				<h2 class="sitemap-heading"><i class="fa-solid fa-file-lines" aria-hidden="true"></i> <?php esc_html_e( 'صفحات', 'teznevise' ); ?></h2>
				<ul class="sitemap-list" style="list-style:none;padding:0;margin:0;display:grid;gap:8px;">
					<?php
					$pages = get_pages( array(
						'sort_column'  => 'menu_order,post_title',
						'hierarchical' => 0,
						'post_status'  => 'publish',
					) );
					if ( $pages ) {
						foreach ( $pages as $p ) {
							printf(
								'<li><a class="link-arrow" href="%s">%s</a></li>',
								esc_url( get_permalink( $p ) ),
								esc_html( $p->post_title )
							);
						}
					} else {
						echo '<li>' . esc_html__( 'هنوز صفحه‌ای منتشر نشده.', 'teznevise' ) . '</li>';
					}
					?>
				</ul>
			</div>

			<div class="sitemap-col" data-reveal>
				<h2 class="sitemap-heading"><i class="fa-solid fa-folder" aria-hidden="true"></i> <?php esc_html_e( 'دسته‌ها', 'teznevise' ); ?></h2>
				<ul class="sitemap-list" style="list-style:none;padding:0;margin:0;display:grid;gap:8px;">
					<?php
					$cats = get_categories( array( 'hide_empty' => false ) );
					if ( $cats ) {
						foreach ( $cats as $c ) {
							printf(
								'<li><a class="link-arrow" href="%s">%s <span style="opacity:.65">(%d)</span></a></li>',
								esc_url( get_category_link( $c->term_id ) ),
								esc_html( $c->name ),
								(int) $c->count
							);
						}
					} else {
						echo '<li>' . esc_html__( 'دسته‌ای تعریف نشده.', 'teznevise' ) . '</li>';
					}
					?>
				</ul>
			</div>

			<div class="sitemap-col" data-reveal>
				<h2 class="sitemap-heading"><i class="fa-solid fa-tags" aria-hidden="true"></i> <?php esc_html_e( 'برچسب‌ها', 'teznevise' ); ?></h2>
				<ul class="sitemap-list" style="list-style:none;padding:0;margin:0;display:grid;gap:8px;">
					<?php
					$tags = get_tags( array( 'hide_empty' => true, 'number' => 30, 'orderby' => 'count', 'order' => 'DESC' ) );
					if ( $tags ) {
						foreach ( $tags as $t ) {
							printf(
								'<li><a class="link-arrow" href="%s">#%s <span style="opacity:.65">(%d)</span></a></li>',
								esc_url( get_tag_link( $t->term_id ) ),
								esc_html( $t->name ),
								(int) $t->count
							);
						}
					} else {
						echo '<li>' . esc_html__( 'برچسبی ثبت نشده.', 'teznevise' ) . '</li>';
					}
					?>
				</ul>
			</div>

			<div class="sitemap-col" data-reveal style="grid-column:1/-1;">
				<h2 class="sitemap-heading"><i class="fa-solid fa-newspaper" aria-hidden="true"></i> <?php esc_html_e( 'آخرین مطالب', 'teznevise' ); ?></h2>
				<div class="article-grid" data-reveal-stagger>
					<?php
					$recent = new WP_Query( array(
						'posts_per_page' => 6,
						'post_status'    => 'publish',
					) );
					if ( $recent->have_posts() ) {
						while ( $recent->have_posts() ) {
							$recent->the_post();
							get_template_part( 'template-parts/post-card' );
						}
						wp_reset_postdata();
					} else {
						echo '<p>' . esc_html__( 'مطلبی منتشر نشده.', 'teznevise' ) . '</p>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>

	<?php
endwhile;

get_footer();
