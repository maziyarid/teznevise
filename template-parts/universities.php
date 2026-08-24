<?php
/**
 * University partner marks — official SVG/WebP crests with alt text.
 *
 * Sources documented in assets/img/universities/SOURCES.txt
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$unis = array(
	array( 'tehran.svg', 'دانشگاه تهران', 'University of Tehran' ),
	array( 'sharif.webp', 'دانشگاه صنعتی شریف', 'Sharif University of Technology' ),
	array( 'amirkabir.svg', 'دانشگاه صنعتی امیرکبیر', 'Amirkabir University of Technology' ),
	array( 'iust.webp', 'دانشگاه علم و صنعت ایران', 'Iran University of Science and Technology' ),
	array( 'sbu.svg', 'دانشگاه شهید بهشتی', 'Shahid Beheshti University' ),
	array( 'tmu.webp', 'دانشگاه تربیت مدرس', 'Tarbiat Modares University' ),
	array( 'isfahan.webp', 'دانشگاه اصفهان', 'University of Isfahan' ),
	array( 'shiraz.webp', 'دانشگاه شیراز', 'Shiraz University' ),
);
$base = trailingslashit( get_template_directory_uri() ) . 'assets/img/universities/';
?>
<section class="uni-strip" aria-label="<?php esc_attr_e( 'دانشگاه‌های کشور', 'teznevise' ); ?>">
	<div class="container">
		<p class="uni-kicker"><?php esc_html_e( 'دانشجویان دانشگاه‌های کشور', 'teznevise' ); ?></p>
		<h2><?php esc_html_e( 'مسیر آشنا برای دانشجویان دانشگاه‌های کشور', 'teznevise' ); ?></h2>
		<ul class="tz-uni-grid uni-row">
			<?php foreach ( $unis as $u ) : ?>
				<li>
					<figure class="tz-uni-card">
						<img src="<?php echo esc_url( $base . $u[0] ); ?>" alt="<?php echo esc_attr( $u[1] ); ?>" width="88" height="88" loading="lazy" decoding="async" />
						<figcaption><?php echo esc_html( $u[1] ); ?></figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
