<?php
/**
 * SERP-style search: AI overview + grouped tools / posts / images.
 *
 * @package Teznevise
 */

get_header();
$query = get_search_query();
$q     = sanitize_text_field( $query );

$tool_query = new WP_Query(
	array(
		's'              => $q,
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => '_wp_page_template',
				'value'   => array( 'page-tool.php', 'page-tools.php' ),
				'compare' => 'IN',
			),
		),
	)
);
$content_query = new WP_Query(
	array(
		's'              => $q,
		'post_type'      => array( 'post', 'case_study' ),
		'post_status'    => 'publish',
		'posts_per_page' => 8,
	)
);
$image_query = new WP_Query(
	array(
		's'              => $q,
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'post_mime_type' => 'image',
		'posts_per_page' => 8,
	)
);
?>

<section class="section search-results-section tz-serp">
	<div class="container">
		<div class="section-head center" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'جستجو', 'teznevise' ); ?></span>
			<h1>
				<?php
				if ( $q ) {
					printf( esc_html__( 'نتایج برای: %s', 'teznevise' ), esc_html( $q ) );
				} else {
					esc_html_e( 'جستجو در سایت', 'teznevise' );
				}
				?>
			</h1>
		</div>
		<div class="search-again" data-reveal>
			<?php get_search_form(); ?>
		</div>

		<?php if ( $q ) : ?>
			<section class="tz-serp-overview" data-search-overview data-q="<?php echo esc_attr( $q ); ?>" data-reveal>
				<h2><?php esc_html_e( 'نمای کلی هوش مصنوعی', 'teznevise' ); ?></h2>
				<p class="tz-serp-overview__body" data-overview-body><?php esc_html_e( 'در حال جمع‌بندی نتایج…', 'teznevise' ); ?></p>
			</section>
		<?php endif; ?>

		<?php
		$sections = array(
			array(
				'id'    => 'tools',
				'label' => __( 'ابزارها', 'teznevise' ),
				'query' => $tool_query,
				'all'   => home_url( '/online-calculation-tools/' ),
			),
			array(
				'id'    => 'posts',
				'label' => __( 'مطالب', 'teznevise' ),
				'query' => $content_query,
				'all'   => add_query_arg( array( 's' => $q, 'post_type' => 'post' ), home_url( '/' ) ),
			),
			array(
				'id'    => 'images',
				'label' => __( 'تصاویر', 'teznevise' ),
				'query' => $image_query,
				'all'   => '',
			),
		);
		$any = false;
		foreach ( $sections as $sec ) :
			$qq = $sec['query'];
			if ( ! $qq->have_posts() ) {
				continue;
			}
			$any = true;
			?>
			<section class="tz-serp-group tz-serp-group--<?php echo esc_attr( $sec['id'] ); ?>" data-reveal>
				<header class="tz-serp-group__head">
					<h2><?php echo esc_html( $sec['label'] ); ?> <span>(<?php echo esc_html( number_format_i18n( (int) $qq->found_posts ) ); ?>)</span></h2>
					<?php if ( $sec['all'] ) : ?>
						<a class="link-arrow" href="<?php echo esc_url( $sec['all'] ); ?>"><?php esc_html_e( 'مشاهده همه', 'teznevise' ); ?></a>
					<?php endif; ?>
				</header>
				<?php if ( 'images' === $sec['id'] ) : ?>
					<ul class="tz-serp-images">
						<?php
						while ( $qq->have_posts() ) :
							$qq->the_post();
							echo '<li><a href="' . esc_url( get_attachment_link() ) . '">';
							echo wp_get_attachment_image( get_the_ID(), 'medium', false, array( 'alt' => get_the_title() ) );
							echo '<span>' . esc_html( get_the_title() ) . '</span></a></li>';
						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				<?php else : ?>
					<div class="article-grid" data-reveal-stagger>
						<?php
						while ( $qq->have_posts() ) :
							$qq->the_post();
							get_template_part( 'template-parts/post-card' );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>

		<?php if ( ! $any ) : ?>
			<p data-reveal><?php esc_html_e( 'نتیجه‌ای یافت نشد. عبارت دیگری را امتحان کنید یا از نقشه سایت استفاده کنید.', 'teznevise' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
