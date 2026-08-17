<?php
/**
 * Default page template — uses registered page custom fields.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$eyebrow     = teznevise_page_field( 'eyebrow', 0, __( 'صفحه', 'teznevise' ) );
	$subtitle   = teznevise_page_field( 'subtitle' );
	$cta_text   = teznevise_page_field( 'cta_text' );
	$cta_url    = teznevise_page_field( 'cta_url' );
	$hero_note  = teznevise_page_field( 'hero_note' );
	$icon       = teznevise_page_field( 'service_icon', 0, '' );
	$icon_color = teznevise_page_field( 'service_color', 0, 'icon-teal' );
	$features   = teznevise_page_field( 'features' );
	$price_note = teznevise_page_field( 'price_note' );
	$sec_text   = teznevise_page_field( 'secondary_cta_text' );
	$sec_url    = teznevise_page_field( 'secondary_cta_url' );
	$hide_title = (bool) teznevise_page_field( 'hide_title', 0, false );
	?>

<section class="section page-content-section">
	<div class="container">
		<div class="section-head" data-reveal>
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( ! $hide_title ) : ?>
				<?php if ( $icon ) : ?>
					<div class="icon-box <?php echo esc_attr( $icon_color ); ?>" style="margin-bottom:12px;">
						<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
					</div>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
			<?php endif; ?>

			<?php if ( $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>

			<?php if ( $hero_note ) : ?>
				<p class="hero-note" style="opacity:.85;"><?php echo esc_html( $hero_note ); ?></p>
			<?php endif; ?>

			<?php if ( $price_note ) : ?>
				<p class="price-note"><strong><?php echo esc_html( $price_note ); ?></strong></p>
			<?php endif; ?>

			<?php if ( $cta_text || $sec_text ) : ?>
				<div class="hero-actions" style="margin-top:16px;display:flex;flex-wrap:wrap;gap:12px;">
					<?php if ( $cta_text ) : ?>
						<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ? $cta_url : '/inquiry/' ) ); ?>">
							<?php echo esc_html( $cta_text ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $sec_text ) : ?>
						<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $sec_url ? $sec_url : '#' ) ); ?>">
							<?php echo esc_html( $sec_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $features ) : ?>
			<ul class="reason-list page-features" data-reveal-stagger style="margin:24px 0;list-style:none;padding:0;display:grid;gap:12px;">
				<?php
				$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $features ) ) );
				foreach ( $lines as $line ) :
					?>
					<li class="reason-item" style="display:flex;gap:10px;align-items:flex-start;">
						<span class="icon-box icon-teal" style="width:36px;height:36px;flex-shrink:0;"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
						<span><?php echo esc_html( $line ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="longcopy article-content" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>

	<?php
endwhile;

get_footer();
