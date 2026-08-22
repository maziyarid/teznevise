<?php
/**
 * University coworker marks — typographic SVG wordmarks.
 *
 * FLAG: official university crests were not available as licensed SVG sources
 * in this repository. These are name wordmarks (not fake crests). Replace
 * with official SVG assets when the universities provide them.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$unis = array(
	array( 'تهران', 'دانشگاه تهران' ),
	array( 'شریف', 'دانشگاه صنعتی شریف' ),
	array( 'امیرکبیر', 'دانشگاه صنعتی امیرکبیر' ),
	array( 'علم و صنعت', 'دانشگاه علم و صنعت ایران' ),
	array( 'شهید بهشتی', 'دانشگاه شهید بهشتی' ),
	array( 'تربیت مدرس', 'دانشگاه تربیت مدرس' ),
	array( 'اصفهان', 'دانشگاه اصفهان' ),
	array( 'شیراز', 'دانشگاه شیراز' ),
);
?>
<section class="uni-strip" aria-label="<?php esc_attr_e( 'همکاری دانشگاهی', 'teznevise' ); ?>">
	<div class="container">
		<p class="uni-kicker"><?php esc_html_e( 'همکاران دانشگاهی', 'teznevise' ); ?></p>
		<h2><?php esc_html_e( 'مسیر آشنا برای دانشجویان دانشگاه‌های کشور', 'teznevise' ); ?></h2>
		<ul class="uni-row">
			<?php foreach ( $unis as $u ) : ?>
				<li>
					<span class="uni-mark" role="img" aria-label="<?php echo esc_attr( $u[1] ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 64" width="140" height="56" focusable="false">
							<title><?php echo esc_html( $u[1] ); ?></title>
							<rect x="1" y="1" width="158" height="62" rx="12" fill="#f4faf7" stroke="#145d4a" stroke-width="2"/>
							<circle cx="28" cy="32" r="14" fill="#145d4a"/>
							<text x="28" y="37" text-anchor="middle" fill="#fff" font-size="11" font-family="Tahoma,sans-serif"><?php echo esc_html( mb_substr( $u[0], 0, 1 ) ); ?></text>
							<text x="50" y="38" fill="#10231d" font-size="13" font-family="Tahoma,sans-serif"><?php echo esc_html( $u[0] ); ?></text>
						</svg>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
