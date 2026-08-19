<?php
/**
 * Template Name: تیم پژوهشگران (Team)
 * Description: Team hub via Flexible Page Builder, with page-meta fallback.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$use_builder = function_exists( 'teznevise_builder_has_sections' ) && teznevise_builder_has_sections();

	if ( $use_builder ) {
		teznevise_builder_render_sections();
		if ( get_the_content() ) :
			?>
<section class="section">
	<div class="container">
		<div class="longcopy article-content" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>
			<?php
		endif;
	} else {
		$eyebrow   = teznevise_page_field( 'eyebrow', 0, __( 'تخصص', 'teznevise' ) );
		$subtitle = teznevise_page_field( 'subtitle', 0, __( 'پژوهشگرانی متخصص از حوزه‌های گوناگون', 'teznevise' ) );
		$cta_text = teznevise_page_field( 'cta_text', 0, __( 'پیوستن به تیم', 'teznevise' ) );
		$cta_url  = teznevise_page_field( 'cta_url', 0, '/contact/' );
		$stats    = function_exists( 'teznevise_parse_pipe_list' ) ? teznevise_parse_pipe_list( teznevise_page_field( 'team_stats' ), 2 ) : array();
		if ( ! $stats ) {
			$stats = array(
				array( '۲۷+', __( 'پژوهشگر متخصص', 'teznevise' ) ),
				array( '۴+', __( 'کشور حضور', 'teznevise' ) ),
				array( '۱۹۷۲+', __( 'پروژه انجام‌شده', 'teznevise' ) ),
				array( '۹۸٪', __( 'رضایت مراجعان', 'teznevise' ) ),
			);
		}
		$members = function_exists( 'teznevise_parse_pipe_list' ) ? teznevise_parse_pipe_list( teznevise_page_field( 'team_members' ), 4 ) : array();
		?>

<section class="page-hero-new section">
	<div class="container">
		<div class="inner section-head" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section bg-soft">
	<div class="container">
		<div class="stats-grid" data-reveal-stagger style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
			<?php foreach ( $stats as $s ) : ?>
				<div class="stat-card reason-item" style="text-align:center;padding:24px;">
					<b class="stat-value" style="font-size:32px;color:var(--tz-primary,#145D4A);display:block;"><?php echo esc_html( $s[0] ); ?></b>
					<span class="stat-label"><?php echo esc_html( $s[1] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

		<?php if ( $members ) : ?>
<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'پژوهشگران', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'با متخصصان ما آشنا شوید', 'teznevise' ); ?></h2>
		</div>
		<div class="services-grid" data-reveal-stagger style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
			<?php foreach ( $members as $m ) : ?>
				<article class="service-card team-card">
					<div class="icon-box icon-purple-soft"><i class="fa-solid fa-user" aria-hidden="true"></i></div>
					<h3><?php echo esc_html( $m[0] ); ?></h3>
					<?php if ( ! empty( $m[1] ) ) : ?>
						<p class="team-role" style="font-weight:700;color:var(--tz-primary,#145D4A);margin:0 0 6px;"><?php echo esc_html( $m[1] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $m[2] ) ) : ?>
						<p class="team-field" style="font-size:13px;opacity:.85;"><?php echo esc_html( $m[2] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $m[3] ) ) : ?>
						<p><?php echo esc_html( $m[3] ); ?></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
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

<section class="section">
	<div class="container">
		<div class="cta-band" data-reveal>
			<div>
				<h2><?php esc_html_e( 'پژوهشگر هستی؟ به تیم ما بپیوند', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'اگر در حوزه‌ای تخصص داری و علاقه‌مند به همراهی پروژه‌های پژوهشی هستی، با ما در تماس باش.', 'teznevise' ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
		</div>
	</div>
</section>
		<?php
	}

endwhile;

get_footer();
