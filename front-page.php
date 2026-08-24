<?php
/**
 * Front page template — content driven by Customizer (teznevise_mod).
 *
 * @package Teznevise
 */
get_header();
?>

<section class="hero-new">
	<div class="container">
		<div class="hero-grid">
			<div class="hero-copy">
				<span class="eyebrow"><?php echo esc_html( teznevise_mod( 'hero_eyebrow' ) ); ?></span>
				<h1><?php echo esc_html( teznevise_mod( 'hero_title_1' ) ); ?> <span class="grad"><?php echo esc_html( teznevise_mod( 'hero_title_grad' ) ); ?></span> <?php echo esc_html( teznevise_mod( 'hero_title_2' ) ); ?></h1>
				<p><?php echo esc_html( teznevise_mod( 'hero_text' ) ); ?></p>
				<div class="hero-actions">
					<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( teznevise_mod( 'hero_btn_primary_url' ) ) ); ?>"><i class="fa-solid fa-rocket" aria-hidden="true"></i> <?php echo esc_html( teznevise_mod( 'hero_btn_primary' ) ); ?></a>
					<a class="btn-tz btn-light-tz btn-lg-tz" href="#services"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> <?php echo esc_html( teznevise_mod( 'hero_btn_secondary' ) ); ?></a>
				</div>
				<div class="hero-points">
					<span class="hero-point"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> <?php echo esc_html( teznevise_mod( 'hero_point_1' ) ); ?></span>
					<span class="hero-point"><i class="fa-solid fa-flask" aria-hidden="true"></i> <?php echo esc_html( teznevise_mod( 'hero_point_2' ) ); ?></span>
					<span class="hero-point"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> <?php echo esc_html( teznevise_mod( 'hero_point_3' ) ); ?></span>
				</div>
			</div>
			<div class="hero-aside">
			<div class="hero-visual tz-hero-orbit" aria-label="<?php esc_attr_e( 'نمایی خلاقانه از خدمات تزنویسه', 'teznevise' ); ?>">
				<div class="hero-orb" aria-hidden="true"></div>
				<div class="ink-blot blot-one"></div><div class="ink-blot blot-two"></div><div class="ink-blot blot-three"></div>
				<div class="hero-particles" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span></div>
				<div class="hero-network">
					<div class="network-ring ring-one"></div><div class="network-ring ring-two"></div><div class="network-ring ring-three"></div>
					<a class="hero-order-button" href="<?php echo esc_url( teznevise_url( teznevise_mod( 'hero_btn_primary_url' ) ) ); ?>" aria-label="<?php echo esc_attr( teznevise_mod( 'hero_btn_primary' ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span><?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?></span></a>
					<span class="orbit-tag tag-1">SPSS</span><span class="orbit-tag tag-2">Matlab</span><span class="orbit-tag tag-3"><?php esc_html_e( 'پایان‌نامه', 'teznevise' ); ?></span><span class="orbit-tag tag-4"><?php esc_html_e( 'پروژه دانشگاهی', 'teznevise' ); ?></span>
				</div>
			</div>
			</div>
		</div>
	</div>
</section>

<?php
$use_builder     = function_exists( 'teznevise_builder_has_sections' ) && teznevise_builder_has_sections();
$has_builder_cta = function_exists( 'teznevise_builder_has_type' ) && teznevise_builder_has_type( 'cta_band' );

if ( $use_builder ) {
	echo '<div id="services">';
	teznevise_builder_render_sections( 0, array( 'only' => array( 'service_cards' ) ) );
	echo '</div>';
	teznevise_builder_render_sections( 0, array( 'except' => array( 'cta_band', 'hero', 'service_cards' ) ) );
} else {
	?>

<section class="section" id="services">
	<div class="container">
		<div class="section-head" data-reveal><div><span class="eyebrow"><?php echo esc_html( teznevise_mod( 'services_eyebrow' ) ); ?></span><h2><?php echo esc_html( teznevise_mod( 'services_title' ) ); ?></h2><p><?php echo esc_html( teznevise_mod( 'services_text' ) ); ?></p></div></div>
		<div class="services-grid" data-reveal-stagger>
			<?php for ( $i = 1; $i <= 9; $i++ ) : ?>
				<article class="service-card tone-<?php echo (int) $i; ?>"><div class="icon-box <?php echo esc_attr( teznevise_mod( "svc{$i}_color" ) ); ?>"><i class="<?php echo esc_attr( teznevise_mod( "svc{$i}_icon" ) ); ?>" aria-hidden="true"></i></div><h3><?php echo esc_html( teznevise_mod( "svc{$i}_title" ) ); ?></h3><p><?php echo esc_html( teznevise_mod( "svc{$i}_text" ) ); ?></p><a class="link-arrow" href="<?php echo esc_url( teznevise_url( teznevise_mod( "svc{$i}_url" ) ) ); ?>"><?php esc_html_e( 'جزئیات خدمت', 'teznevise' ); ?></a></article>
			<?php endfor; ?>
		</div>
	</div>
</section>

<section class="section bg-soft"><div class="container"><div class="reason-wrap">
	<div class="reason-panel about-panel" data-reveal><span class="eyebrow"><?php echo esc_html( teznevise_mod( 'about_eyebrow' ) ); ?></span><h3><?php echo esc_html( teznevise_mod( 'about_title' ) ); ?></h3><p><?php echo esc_html( teznevise_mod( 'about_text' ) ); ?></p><a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( teznevise_mod( 'about_btn_url' ) ) ); ?>"><i class="fa-regular fa-circle-question" aria-hidden="true"></i> <?php echo esc_html( teznevise_mod( 'about_btn' ) ); ?></a></div>
	<div class="reason-list" data-reveal-stagger><?php $reason_icons = array( 1 => array( 'icon-indigo', 'fa-solid fa-shield-halved' ), 2 => array( 'icon-teal', 'fa-solid fa-compass-drafting' ), 3 => array( 'icon-cyan', 'fa-solid fa-bolt' ), 4 => array( 'icon-danger-soft', 'fa-solid fa-pen-ruler' ) ); for ( $i = 1; $i <= 4; $i++ ) : ?><div class="reason-item"><div class="icon-box <?php echo esc_attr( $reason_icons[ $i ][0] ); ?>"><i class="<?php echo esc_attr( $reason_icons[ $i ][1] ); ?>" aria-hidden="true"></i></div><b><?php echo esc_html( teznevise_mod( "reason{$i}_title" ) ); ?></b><p><?php echo esc_html( teznevise_mod( "reason{$i}_text" ) ); ?></p></div><?php endfor; ?></div>
</div></div></section>

<section class="section"><div class="container"><div class="section-head center" data-reveal><span class="eyebrow"><?php echo esc_html( teznevise_mod( 'steps_eyebrow' ) ); ?></span><h2><?php echo esc_html( teznevise_mod( 'steps_title' ) ); ?></h2><p><?php echo esc_html( teznevise_mod( 'steps_text' ) ); ?></p></div><div class="steps steps-6" data-reveal-stagger><?php $step_icons = array( 1 => 'fa-regular fa-comments', 2 => 'fa-solid fa-file-circle-check', 3 => 'fa-solid fa-database', 4 => 'fa-solid fa-chart-simple', 5 => 'fa-solid fa-pen-nib', 6 => 'fa-solid fa-person-chalkboard' ); for ( $i = 1; $i <= 6; $i++ ) : ?><div class="step tone-<?php echo (int) $i; ?>"><div class="step-icon icon-teal"><span class="tez-builder-step-number" aria-hidden="true"><?php echo esc_html( number_format_i18n( $i ) ); ?></span><i class="<?php echo esc_attr( $step_icons[ $i ] ); ?>" aria-hidden="true"></i></div><h3><?php echo esc_html( teznevise_mod( "step{$i}_title" ) ); ?></h3><p><?php echo esc_html( teznevise_mod( "step{$i}_text" ) ); ?></p></div><?php endfor; ?></div></div></section>

	<?php
}
?>

<?php get_template_part( 'template-parts/universities' ); ?>

<section class="section bg-soft"><div class="container"><div class="section-head" data-reveal><div><span class="eyebrow"><?php echo esc_html( teznevise_mod( 'articles_eyebrow' ) ); ?></span><h2><?php echo esc_html( teznevise_mod( 'articles_title' ) ); ?></h2><p><?php echo esc_html( teznevise_mod( 'articles_text' ) ); ?></p></div><a class="link-arrow" href="<?php echo esc_url( function_exists( 'teznevise_posts_url' ) ? teznevise_posts_url() : home_url( '/' ) ); ?>"><?php esc_html_e( 'مشاهده همه مقالات', 'teznevise' ); ?></a></div>
	<div class="article-grid" data-reveal-stagger><?php $recent = new WP_Query( array( 'posts_per_page' => 3, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'no_found_rows' => true ) ); if ( $recent->have_posts() ) : while ( $recent->have_posts() ) : $recent->the_post(); get_template_part( 'template-parts/post-card' ); endwhile; wp_reset_postdata(); else : ?><p class="blog-archive__empty"><?php esc_html_e( 'هنوز مطلبی منتشر نشده است.', 'teznevise' ); ?></p><?php endif; ?></div>
</div></section>

<section class="section tz-faq-band" aria-label="<?php esc_attr_e( 'سوالات متداول', 'teznevise' ); ?>">
	<div class="container">
		<div class="section-head center" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'سوالات متداول', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'پرسش‌های پرتکرار دانشجویان', 'teznevise' ); ?></h2>
			<p><?php esc_html_e( 'پاسخ‌ها مشاوره آموزشی‌اند؛ تزنویسه پایان‌نامه را به‌جای دانشجو نمی‌نویسد.', 'teznevise' ); ?></p>
		</div>
		<?php
		$home_faqs = array(
			array(
				'q' => __( 'آیا مشاوره اولیه رایگان است؟', 'teznevise' ),
				'a' => __( 'بله. بررسی اولیه موضوع، مسیر و برآورد زمان در ساعات کاری بدون هزینه است.', 'teznevise' ),
			),
			array(
				'q' => __( 'آیا تزنویسه پایان‌نامه را به‌جای دانشجو می‌نویسد؟', 'teznevise' ),
				'a' => __( 'خیر. تزنویسه مشاوره، ساختار، روش و ابزار می‌دهد؛ نگارش نهایی با دانشجو است.', 'teznevise' ),
			),
			array(
				'q' => __( 'چطور درخواست مشاوره ثبت کنم؟', 'teznevise' ),
				'a' => __( 'از «ثبت درخواست» یا گفتگوی زنده، نام و موبایل را بفرستید تا در ساعات کاری تماس بگیریم.', 'teznevise' ),
			),
			array(
				'q' => __( 'آیا ابزارهای آنلاین هزینه‌ای دارند؟', 'teznevise' ),
				'a' => __( 'ماشین‌حساب‌های آماری تزنویسه برای استفاده رایگان‌اند و نیازی به خرید تزکوین ندارند.', 'teznevise' ),
			),
			array(
				'q' => __( 'پاسخ اولیه چقدر طول می‌کشد؟', 'teznevise' ),
				'a' => __( 'در ساعات کاری (شنبه تا پنجشنبه، ۹ تا ۲۱) معمولاً در کمتر از چند ساعت پاسخ اولیه داده می‌شود.', 'teznevise' ),
			),
		);
		echo function_exists( 'teznevise_faq_items_markup' ) ? teznevise_faq_items_markup( $home_faqs ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</section>

<?php if ( $has_builder_cta ) : ?>
	<?php teznevise_builder_render_sections( 0, array( 'only' => array( 'cta_band' ) ) ); ?>
<?php else : ?>
<section class="section"><div class="container"><div class="cta-band" data-reveal><div><h2><?php echo esc_html( teznevise_mod( 'cta_title' ) ); ?></h2><p><?php echo esc_html( teznevise_mod( 'cta_text' ) ); ?></p></div><a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( teznevise_mod( 'cta_btn_url' ) ) ); ?>"><?php echo esc_html( teznevise_mod( 'cta_btn' ) ); ?></a></div></div></section>
<?php endif; ?>

<?php get_footer();
