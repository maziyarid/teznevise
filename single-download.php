<?php
/**
 * Single template: download CPT.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$links_raw = get_post_meta( get_the_ID(), '_teznevise_download_links', true );
	$links     = array();
	if ( is_string( $links_raw ) && '' !== trim( $links_raw ) ) {
		foreach ( preg_split( '/\r\n|\r|\n/', $links_raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$parts = array_map( 'trim', explode( '|', $line ) );
			$links[] = array(
				'title' => isset( $parts[0] ) ? $parts[0] : '',
				'url'   => isset( $parts[1] ) ? $parts[1] : '',
				'size'  => isset( $parts[2] ) ? $parts[2] : '',
			);
		}
	}

	$version = get_post_meta( get_the_ID(), '_teznevise_version', true );
	$license = get_post_meta( get_the_ID(), '_teznevise_license', true );
	$lang    = get_post_meta( get_the_ID(), '_teznevise_lang', true );
	$count   = get_post_meta( get_the_ID(), '_teznevise_download_count', true );
	?>

<section class="service-hero section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'دانلود', 'teznevise' ); ?></span>
			<div class="icon-box icon-teal" style="margin-bottom:12px;">
				<i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
			</div>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="service-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<p style="font-size:13px;margin-top:8px;">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'download' ) ); ?>"><?php esc_html_e( '← بازگشت به همه دانلودها', 'teznevise' ); ?></a>
			</p>
		</div>
	</div>
</section>

<?php if ( $version || $license || $lang || $count ) : ?>
<section class="section bg-soft">
	<div class="container">
		<div class="services-grid" style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));" data-reveal>
			<?php if ( $version ) : ?>
				<div class="service-card"><strong><?php esc_html_e( 'نسخه', 'teznevise' ); ?></strong><p><?php echo esc_html( $version ); ?></p></div>
			<?php endif; ?>
			<?php if ( $license ) : ?>
				<div class="service-card"><strong><?php esc_html_e( 'مجوز', 'teznevise' ); ?></strong><p><?php echo esc_html( $license ); ?></p></div>
			<?php endif; ?>
			<?php if ( $lang ) : ?>
				<div class="service-card"><strong><?php esc_html_e( 'زبان', 'teznevise' ); ?></strong><p><?php echo esc_html( $lang ); ?></p></div>
			<?php endif; ?>
			<?php if ( $count ) : ?>
				<div class="service-card"><strong><?php esc_html_e( 'دانلودها', 'teznevise' ); ?></strong><p><?php echo esc_html( $count ); ?></p></div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $links ) : ?>
<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'فایل‌ها', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'لینک‌های دانلود', 'teznevise' ); ?></h2>
		</div>
		<div class="services-grid" data-reveal-stagger style="display:grid;gap:14px;">
			<?php foreach ( $links as $link ) : ?>
				<?php if ( empty( $link['url'] ) ) { continue; } ?>
				<div class="service-card" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
					<div>
						<strong><?php echo esc_html( $link['title'] ? $link['title'] : __( 'دانلود فایل', 'teznevise' ) ); ?></strong>
						<?php if ( ! empty( $link['size'] ) ) : ?>
							<span class="text-muted" style="margin-inline-start:8px;font-size:13px;"><?php echo esc_html( $link['size'] ); ?></span>
						<?php endif; ?>
					</div>
					<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( $link['url'] ); ?>" download>
						<i class="fa-solid fa-download" aria-hidden="true"></i> <?php esc_html_e( 'دانلود', 'teznevise' ); ?>
					</a>
				</div>
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

	<?php
endwhile;

get_footer();
