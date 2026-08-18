<?php
/**
 * Template Name: درباره ما (About)
 * Description: About page — story, mission, timeline, policy; editable via page meta + content.
 *
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: maziyarid/M-Z — A brand new repository with my complete brand identity, story, and website prototype.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$eyebrow   = teznevise_page_field( 'eyebrow', 0, __( 'تزنویسه', 'teznevise' ) );
	$subtitle = teznevise_page_field( 'subtitle', 0, __( 'همراه پژوهشی از ایده تا دفاع', 'teznevise' ) );
	$cta_text = teznevise_page_field( 'cta_text', 0, __( 'شروع مشاوره رایگان', 'teznevise' ) );
	$cta_url  = teznevise_page_field( 'cta_url', 0, '/inquiry/' );
	$features = teznevise_page_field( 'features' );
	$timeline = teznevise_page_field( 'timeline' );
	$policy   = teznevise_page_field( 'policy_points' );
	?>

<section class="page-hero-new section">
	<div class="container">
		<div class="inner section-head" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<div class="hero-actions" style="margin-top:20px;">
				<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>">
					<i class="fa-solid fa-rocket" aria-hidden="true"></i> <?php echo esc_html( $cta_text ); ?>
				</a>
				<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/team/' ) ); ?>">
					<?php esc_html_e( 'تیم پژوهشگران', 'teznevise' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<?php if ( $features ) : ?>
<section class="section bg-soft">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'مسیر و هدف', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'مأموریت و چشم‌انداز', 'teznevise' ); ?></h2>
		</div>
		<div class="reason-list" data-reveal-stagger style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
			<?php foreach ( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $features ) ) ) as $line ) : ?>
				<div class="reason-item">
					<div class="icon-box icon-teal"><i class="fa-solid fa-check" aria-hidden="true"></i></div>
					<p><?php echo esc_html( $line ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
$tl = function_exists( 'teznevise_parse_pipe_list' ) ? teznevise_parse_pipe_list( $timeline, 3 ) : array();
if ( $tl ) :
	?>
<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'مسیر رشد', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'خط زمانی تزنویسه', 'teznevise' ); ?></h2>
		</div>
		<div class="steps" data-reveal-stagger style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
			<?php foreach ( $tl as $row ) : ?>
				<div class="step">
					<span class="eyebrow" style="margin-bottom:8px;"><?php echo esc_html( $row[0] ); ?></span>
					<h3><?php echo esc_html( $row[1] ); ?></h3>
					<?php if ( ! empty( $row[2] ) ) : ?>
						<p><?php echo esc_html( $row[2] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $policy ) : ?>
<section class="section bg-soft">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'خط‌مشی', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'سیاست کاری ما', 'teznevise' ); ?></h2>
		</div>
		<ul class="reason-list" data-reveal-stagger style="list-style:none;padding:0;display:grid;gap:12px;">
			<?php foreach ( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $policy ) ) ) as $line ) : ?>
				<li class="reason-item" style="display:flex;gap:12px;align-items:flex-start;">
					<span class="icon-box icon-indigo" style="width:36px;height:36px;flex-shrink:0;"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>
					<span><?php echo esc_html( $line ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>

<section class="section">
	<div class="container">
		<div class="longcopy article-content about-story" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>

<?php teznevise_builder_render_sections(); ?>

<section class="section">
	<div class="container">
		<div class="cta-band" data-reveal>
			<div>
				<h2><?php esc_html_e( 'آماده‌اید مسیر پژوهش را شفاف شروع کنید؟', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'موضوع را بفرستید؛ مسیر و برآورد اولیه را با شما بررسی می‌کنیم.', 'teznevise' ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
		</div>
	</div>
</section>

	<?php
endwhile;

get_footer();
