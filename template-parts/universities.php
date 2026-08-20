<?php
/**
 * University coworker marks (letter badges, not official logos).
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$unis = array(
	array( 'تهران', 'ته' ),
	array( 'شریف', 'شر' ),
	array( 'امیرکبیر', 'ام' ),
	array( 'علم و صنعت', 'صن' ),
	array( 'شهید بهشتی', 'به' ),
	array( 'تربیت مدرس', 'تم' ),
	array( 'اصفهان', 'اص' ),
	array( 'شیراز', 'شی' ),
);
?>
<section class="uni-strip" aria-label="<?php esc_attr_e( 'همکاری دانشگاهی', 'teznevise' ); ?>">
	<div class="container">
		<p class="uni-kicker"><?php esc_html_e( 'همکاران دانشگاهی', 'teznevise' ); ?></p>
		<h2><?php esc_html_e( 'مسیر آشنا برای دانشجویان دانشگاه‌های کشور', 'teznevise' ); ?></h2>
		<ul class="uni-row">
			<?php foreach ( $unis as $u ) : ?>
				<li>
					<span class="uni-mark" aria-hidden="true"><?php echo esc_html( $u[1] ); ?></span>
					<span><?php echo esc_html( $u[0] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
