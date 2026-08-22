<?php
/**
 * Template Name: ابزار تکی (Single Tool)
 * Description: Calculator stays in post content; surrounding sections use the Flexible Page Builder.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$has_builder_hero   = function_exists( 'teznevise_builder_has_type' ) && teznevise_builder_has_type( 'hero' );
	$has_builder_steps  = function_exists( 'teznevise_builder_has_type' ) && teznevise_builder_has_type( 'process_steps' );
	$has_builder_cta    = function_exists( 'teznevise_builder_has_type' ) && teznevise_builder_has_type( 'cta_band' );
	$eyebrow            = teznevise_page_field( 'eyebrow', 0, __( 'ابزار آنلاین', 'teznevise' ) );
	$subtitle          = teznevise_page_field( 'subtitle' );
	$icon              = function_exists( 'teznevise_get_page_icon' ) ? teznevise_get_page_icon() : 'fa-solid fa-calculator';
	if ( ! $icon ) {
		$icon = 'fa-solid fa-calculator';
	}
	$icon_color = teznevise_page_field( 'service_color', 0, 'icon-amber' );
	$cta_text   = teznevise_page_field( 'cta_text', 0, __( 'تحلیل آماری تخصصی', 'teznevise' ) );
	$cta_url    = teznevise_page_field( 'cta_url', 0, '/service-statistics/' );
	$features   = teznevise_page_field( 'features' );

	if ( $has_builder_hero ) {
		teznevise_builder_render_sections( 0, array( 'only' => array( 'hero' ) ) );
	} else {
		?>
<section class="service-hero section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<div class="icon-box <?php echo esc_attr( $icon_color ); ?>" style="margin-bottom:12px;">
				<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
			</div>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="service-lead"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<p style="font-size:13px;margin-top:8px;">
				<a href="<?php echo esc_url( home_url( '/online-calculation-tools/' ) ); ?>"><?php esc_html_e( '← بازگشت به همه ابزارها', 'teznevise' ); ?></a>
			</p>
		</div>
	</div>
</section>
		<?php
	}

	?>
<section class="section">
	<div class="container">
		<div class="longcopy article-content tool-body" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>
	<?php
	if ( ! $has_builder_steps && $features ) :
		?>
<section class="section bg-soft">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'راهنمای استفاده', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'چطور از این ابزار استفاده کنیم؟', 'teznevise' ); ?></h2>
		</div>
		<div class="steps" data-reveal-stagger style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
			<?php
			$i = 1;
			foreach ( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $features ) ) ) as $line ) :
				?>
				<div class="step">
					<div class="step-icon icon-box icon-teal"><i class="fa-solid fa-<?php echo esc_attr( $i ); ?>" aria-hidden="true"></i></div>
					<p><?php echo esc_html( $line ); ?></p>
				</div>
				<?php
				++$i;
			endforeach;
			?>
		</div>
	</div>
</section>
		<?php
	elseif ( $has_builder_steps ) :
		teznevise_builder_render_sections( 0, array( 'only' => array( 'process_steps' ) ) );
	endif;
	?>

	<?php
	if ( function_exists( 'teznevise_builder_render_sections' ) ) {
		teznevise_builder_render_sections( 0, array( 'except' => array( 'hero', 'process_steps' ) ) );
	}

	if ( ! $has_builder_cta ) :
		?>
<section class="section">
	<div class="container">
		<div class="cta-band" data-reveal>
			<div>
				<h2><?php esc_html_e( 'برای تفسیر نتایج کمک می‌خواهی؟', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'تیم تحلیل آماری تزنویسه نتایج را به‌صورت علمی برای پژوهش شما تفسیر می‌کند.', 'teznevise' ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
		</div>
	</div>
</section>
		<?php
	endif;

	$tool_slug = get_post_field( 'post_name', get_the_ID() );
	$tool_map  = array(
		't-test-calculator'                    => 't-test',
		'mann-whitney-calculator'              => 'mann-whitney',
		'pearson-correlation-calculator'       => 'correlation',
		'spearman-correlation-calculator'      => 'correlation',
		'regression-calculator'                => 'regression',
	);
	$ai_tool = isset( $tool_map[ $tool_slug ] ) ? $tool_map[ $tool_slug ] : 'general';
	if ( function_exists( 'teznevise_ai_shortcode' ) ) {
		echo '<div class="container" style="padding-bottom:48px">';
		echo teznevise_ai_shortcode( array( 'tool' => $ai_tool ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	} elseif ( function_exists( 'get_template_part' ) ) {
		get_template_part( 'template-parts/tools-ai' );
	}

endwhile;

get_footer();
