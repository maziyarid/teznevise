<?php
/**
 * 404 template.
 *
 * @package Teznevise
 */

get_header();
?>

<section class="section">
	<div class="container" style="text-align:center;max-width:640px;margin-inline:auto;">
		<div class="section-head center" data-reveal>
			<span class="eyebrow">۴۰۴</span>
			<h1><?php esc_html_e( 'صفحه پیدا نشد', 'teznevise' ); ?></h1>
			<p><?php esc_html_e( 'متأسفانه صفحه‌ای که دنبال آن بودید وجود ندارد یا جابه‌جا شده است.', 'teznevise' ); ?></p>
		</div>
		<div class="hero-actions" style="justify-content:center;" data-reveal>
			<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<i class="fa-solid fa-house" aria-hidden="true"></i> <?php esc_html_e( 'بازگشت به خانه', 'teznevise' ); ?>
			</a>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>">
				<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> <?php esc_html_e( 'ثبت درخواست', 'teznevise' ); ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
