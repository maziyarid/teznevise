<?php
/**
 * Single blog post template.
 *
 * @package Teznevise
 */
get_header();
while ( have_posts() ) :
	the_post();
	$post_id         = get_the_ID();
	$kicker          = teznevise_blog_field( 'kicker', $post_id );
	$subtitle        = teznevise_blog_field( 'subtitle', $post_id );
	$featured_label  = teznevise_blog_field( 'featured_label', $post_id );
	$author_label    = teznevise_blog_field( 'author_label', $post_id, function_exists( 'teznevise_public_author_name' ) ? teznevise_public_author_name() : get_bloginfo( 'name' ) );
	$hide_toc        = '1' === teznevise_blog_field( 'hide_toc', $post_id );
	$related_heading = teznevise_blog_field( 'related_heading', $post_id, __( 'مقالات مرتبط', 'teznevise' ) );
	$content         = teznevise_prepare_post_content( apply_filters( 'the_content', get_the_content() ) );
	$toc             = ! $hide_toc ? teznevise_render_toc( $content ) : '';
	$previous        = get_previous_post();
	$next            = get_next_post();
	$cats            = get_the_category();
	$primary_cat     = $cats ? $cats[0] : null;
	?>
	<div class="reading-progress" aria-hidden="true"><span></span></div>
	<section class="site-main blog-post section">
		<div class="container">
			<article <?php post_class( 'single-post' ); ?>>
				<nav class="blog-post__crumbs" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'teznevise' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'teznevise' ); ?></a>
					<span aria-hidden="true">/</span>
					<?php
					$posts_page_id = (int) get_option( 'page_for_posts' );
					$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
					?>
					<a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'بلاگ', 'teznevise' ); ?></a>
					<?php if ( $primary_cat ) : ?>
						<span aria-hidden="true">/</span>
						<a href="<?php echo esc_url( get_category_link( $primary_cat ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a>
					<?php endif; ?>
				</nav>
				<div class="article-tools" role="group" aria-label="<?php esc_attr_e( 'ابزارهای مطالعه', 'teznevise' ); ?>">
					<button class="article-tools__button" type="button" data-reading-focus aria-pressed="false"><?php esc_html_e( 'مطالعه متمرکز', 'teznevise' ); ?></button>
					<button class="article-tools__button" type="button" data-reading-size="increase" aria-label="<?php esc_attr_e( 'بزرگ‌تر کردن متن مقاله', 'teznevise' ); ?>">الف+</button>
					<button class="article-tools__button" type="button" data-reading-size="reset" aria-label="<?php esc_attr_e( 'بازنشانی اندازه متن مقاله', 'teznevise' ); ?>">الف</button>
				</div>
				<header class="blog-post__header article-header" data-reveal>
					<?php if ( $kicker ) : ?><p class="blog-post__kicker"><?php echo esc_html( $kicker ); ?></p><?php endif; ?>
					<?php if ( $featured_label ) : ?><p class="blog-post__featured"><?php echo esc_html( $featured_label ); ?></p><?php endif; ?>
					<h1 class="blog-post__title"><?php the_title(); ?></h1>
					<?php if ( $subtitle ) : ?><p class="blog-post__subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
					<div class="blog-post__meta article-meta">
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></time>
						<?php if ( get_the_modified_time( 'U' ) !== get_the_time( 'U' ) ) : ?>
							<time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>"><i class="fa-solid fa-rotate" aria-hidden="true"></i> <?php echo esc_html( sprintf( __( 'به‌روزرسانی %s', 'teznevise' ), get_the_modified_date() ) ); ?></time>
						<?php endif; ?>
						<?php if ( $author_label ) : ?><span><i class="fa-regular fa-user" aria-hidden="true"></i> <?php echo esc_html( $author_label ); ?></span><?php endif; ?>
						<span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( teznevise_read_time( $post_id ) ); ?></span>
						<?php if ( $primary_cat ) : ?><span><i class="fa-solid fa-folder-open" aria-hidden="true"></i> <?php echo esc_html( $primary_cat->name ); ?></span><?php endif; ?>
					</div>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="blog-post__featured-image article-cover" data-reveal>
						<?php the_post_thumbnail( 'teznevise-hero', array( 'alt' => esc_attr( get_the_title() ), 'loading' => 'eager', 'decoding' => 'async', 'fetchpriority' => 'high', 'sizes' => '(min-width: 1100px) 720px, 100vw' ) ); ?>
					</figure>
				<?php endif; ?>
				<?php if ( $toc ) : ?>
					<details class="blog-post__toc-mobile" data-reveal>
						<summary><span><?php esc_html_e( 'فهرست مطالب', 'teznevise' ); ?></span><span aria-hidden="true">⌄</span></summary>
						<?php echo wp_kses_post( $toc ); ?>
					</details>
				<?php endif; ?>
				<div class="blog-post__layout">
					<div class="blog-post__content entry-content article-content longcopy" data-reveal>
						<?php
						$takeaways = teznevise_blog_field( 'takeaways', $post_id );
						if ( function_exists( 'teznevise_render_ai_overview' ) ) {
							teznevise_render_ai_overview( $post_id );
						}
						$lines = function_exists( 'teznevise_lines' )
							? teznevise_lines( $takeaways )
							: array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', is_string( $takeaways ) ? $takeaways : '' ) ) );
						if ( $lines ) :
							?>
							<section class="tz-takeaways">
								<h2><?php esc_html_e( 'نکات کلیدی', 'teznevise' ); ?></h2>
								<ol>
									<?php foreach ( $lines as $line ) : ?>
										<li><?php echo esc_html( $line ); ?></li>
									<?php endforeach; ?>
								</ol>
							</section>
							<?php
						endif;
						echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>
					<aside class="blog-post__aside">
						<?php if ( $toc ) : ?>
							<nav class="blog-post__toc entry-toc" aria-label="<?php esc_attr_e( 'فهرست مطالب', 'teznevise' ); ?>">
								<p class="entry-toc__label"><?php esc_html_e( 'در این مقاله', 'teznevise' ); ?></p>
								<?php echo wp_kses_post( $toc ); ?>
							</nav>
						<?php endif; ?>
						<div class="blog-post__cta-card">
							<p class="blog-post__cta-card-kicker"><?php esc_html_e( 'مشاوره پژوهشی', 'teznevise' ); ?></p>
							<p><?php esc_html_e( 'اگر برای نگارش این بخش به همراه تخصصی نیاز دارید، مسیر درخواست را شروع کنید.', 'teznevise' ); ?></p>
							<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></a>
						</div>
					</aside>
				</div>
				<footer class="article-footer" data-reveal>
					<?php the_tags( '<div class="post-tags">', ' ', '</div>' ); ?>
					<div class="share-bar" aria-label="<?php esc_attr_e( 'اشتراک', 'teznevise' ); ?>">
						<span class="share-bar__label"><?php esc_html_e( 'اشتراک‌گذاری', 'teznevise' ); ?></span>
						<button type="button" data-share="telegram" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-slug="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>"><i class="fa-brands fa-telegram" aria-hidden="true"></i> تلگرام</button>
						<button type="button" data-share="whatsapp" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-slug="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> واتساپ</button>
						<button type="button" data-share="x" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-slug="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>">X</button>
						<button type="button" data-share="linkedin" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-slug="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>">LinkedIn</button>
					</div>
				</footer>
			</article>
			<?php if ( $previous || $next ) : ?>
				<nav class="blog-post__prev-next" aria-label="<?php esc_attr_e( 'مقاله قبلی و بعدی', 'teznevise' ); ?>">
					<div>
						<?php if ( $previous ) : ?>
							<a href="<?php echo esc_url( get_permalink( $previous ) ); ?>">
								<small><?php esc_html_e( 'مقاله قبلی', 'teznevise' ); ?></small>
								<span><?php echo esc_html( get_the_title( $previous ) ); ?></span>
							</a>
						<?php endif; ?>
					</div>
					<div>
						<?php if ( $next ) : ?>
							<a href="<?php echo esc_url( get_permalink( $next ) ); ?>">
								<small><?php esc_html_e( 'مقاله بعدی', 'teznevise' ); ?></small>
								<span><?php echo esc_html( get_the_title( $next ) ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</nav>
			<?php endif; ?>
			<?php if ( comments_open() || get_comments_number() ) : ?>
				<div data-reveal><?php comments_template(); ?></div>
			<?php endif; ?>
			<?php $related = teznevise_related_posts( $post_id ); ?>
			<?php if ( $related && $related->have_posts() ) : ?>
				<section class="blog-post__related" aria-labelledby="related-posts-title">
					<h2 id="related-posts-title"><?php echo esc_html( $related_heading ); ?></h2>
					<div class="post-grid post-grid--related">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							get_template_part( 'template-parts/post-card', null, array( 'heading_level' => 3 ) );
						endwhile;
						?>
					</div>
				</section>
			<?php endif; wp_reset_postdata(); ?>
		</div>
	</section>
<?php teznevise_builder_render_sections(); ?>
<?php
endwhile;
get_footer();
