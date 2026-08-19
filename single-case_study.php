<?php
/**
 * Single template: case_study CPT.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$meta_keys = array(
		'_tz_cs_client'    => __( 'مشتری', 'teznevise' ),
		'_tz_cs_field'     => __( 'حوزه', 'teznevise' ),
		'_tz_cs_region'    => __( 'منطقه', 'teznevise' ),
		'_tz_cs_duration'  => __( 'مدت زمان', 'teznevise' ),
		'_tz_cs_degree'    => __( 'مقطع', 'teznevise' ),
		'_tz_cs_service'   => __( 'خدمت', 'teznevise' ),
		'_tz_cs_tools'     => __( 'ابزارها', 'teznevise' ),
	);
	$challenge = get_post_meta( get_the_ID(), '_tz_cs_challenge', true );
	$solution  = get_post_meta( get_the_ID(), '_tz_cs_solution', true );
	$result    = get_post_meta( get_the_ID(), '_tz_cs_result', true );
	$quote     = get_post_meta( get_the_ID(), '_tz_cs_quote', true );
	$metrics   = get_post_meta( get_the_ID(), '_tz_cs_metrics', true );
	?>

<section class="service-hero section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'مطالعه موردی', 'teznevise' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="service-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section bg-soft">
	<div class="container">
		<div class="services-grid" data-reveal style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
			<?php foreach ( $meta_keys as $key => $label ) :
				$val = get_post_meta( get_the_ID(), $key, true );
				if ( ! $val ) {
					continue;
				}
				?>
				<div class="service-card">
					<strong><?php echo esc_html( $label ); ?></strong>
					<p><?php echo esc_html( $val ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( $challenge || $solution || $result ) : ?>
<section class="section">
	<div class="container">
		<div class="services-grid" data-reveal-stagger style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
			<?php if ( $challenge ) : ?>
				<div class="service-card">
					<div class="icon-box icon-amber"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i></div>
					<h3><?php esc_html_e( 'چالش', 'teznevise' ); ?></h3>
					<p><?php echo esc_html( $challenge ); ?></p>
				</div>
			<?php endif; ?>
			<?php if ( $solution ) : ?>
				<div class="service-card">
					<div class="icon-box icon-teal"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></div>
					<h3><?php esc_html_e( 'راهکار', 'teznevise' ); ?></h3>
					<p><?php echo esc_html( $solution ); ?></p>
				</div>
			<?php endif; ?>
			<?php if ( $result ) : ?>
				<div class="service-card">
					<div class="icon-box icon-indigo"><i class="fa-solid fa-flag-checkered" aria-hidden="true"></i></div>
					<h3><?php esc_html_e( 'نتیجه', 'teznevise' ); ?></h3>
					<p><?php echo esc_html( $result ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $quote ) : ?>
<section class="section bg-soft">
	<div class="container">
		<blockquote class="cta-band" data-reveal style="margin:0;">
			<p><?php echo esc_html( $quote ); ?></p>
		</blockquote>
	</div>
</section>
<?php endif; ?>

<?php if ( $metrics ) : ?>
<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'معیارها', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'شاخص‌های کلیدی', 'teznevise' ); ?></h2>
		</div>
		<div class="longcopy" data-reveal>
			<p><?php echo nl2br( esc_html( $metrics ) ); ?></p>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section">
	<div class="container">
		<div class="longcopy article-content" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>

	<?php
endwhile;

get_footer();
