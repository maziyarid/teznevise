<?php
/**
 * Theme footer — chrome matches teznevise_work/index.html (.footer-new).
 *
 * @package Teznevise
 */
?></main>
<footer class="footer-new">
	<div class="container">
		<div class="footer-grid">
			<div class="footer-brand">
				<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
					$logo_url = teznevise_logo_url();
					if ( $logo_url ) {
						printf(
							'<img src="%s" alt="%s" width="106" height="48" loading="lazy">',
							esc_url( $logo_url ),
							esc_attr( get_bloginfo( 'name' ) )
						);
					} else {
						echo esc_html( get_bloginfo( 'name' ) );
					}
					?>
				</a>
				<p><?php esc_html_e( 'تزنویسه همراه پژوهشی دانشجویان و پژوهشگران؛ از انتخاب موضوع و تدوین پروپوزال تا تحلیل آماری، نگارش و آمادگی دفاع.', 'teznevise' ); ?></p>
				<div class="footer-quick-actions">
					<a href="tel:<?php echo esc_attr( teznevise_get_contact( 'phone_intl' ) ); ?>"><?php esc_html_e( 'تماس', 'teznevise' ); ?></a>
					<a class="wa" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>"><?php esc_html_e( 'واتساپ', 'teznevise' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><?php esc_html_e( 'ثبت سفارش', 'teznevise' ); ?></a>
				</div>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e( 'خدمات', 'teznevise' ); ?></h4>
				<a href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'انجام پایان‌نامه', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><?php esc_html_e( 'انجام پروپوزال', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e( 'دسترسی سریع', 'teznevise' ); ?></h4>
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'مرکز دانش', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'درباره ما', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/team/' ) ); ?>"><?php esc_html_e( 'تیم پژوهشگران', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'تماس با ما', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'teznevise' ); ?></a>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e( 'ارتباط با ما', 'teznevise' ); ?></h4>
				<a href="tel:<?php echo esc_attr( teznevise_get_contact( 'phone_intl' ) ); ?>"><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></a>
				<a href="mailto:<?php echo esc_attr( teznevise_get_contact( 'email' ) ); ?>"><?php echo esc_html( teznevise_get_contact( 'email' ) ); ?></a>
				<p><?php echo esc_html( teznevise_get_contact( 'address' ) ); ?></p>
				<a href="<?php echo esc_url( teznevise_get_contact( 'telegram' ) ); ?>"><?php esc_html_e( 'تلگرام: @Teznevise', 'teznevise' ); ?></a>
			</div>
		</div>
		<div class="footer-bottom">
			<span>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — <?php esc_html_e( 'تمامی حقوق محفوظ است.', 'teznevise' ); ?></span>
			<span><?php printf( esc_html__( 'طراحی RTL واکنش‌گرا — WordPress Theme %s', 'teznevise' ), esc_html( defined( 'TEZNEVISE_VERSION' ) ? TEZNEVISE_VERSION : '' ) ); ?></span>
		</div>
	</div>
</footer>
<?php get_template_part( 'template-parts/fab' ); ?>
<?php get_template_part( 'template-parts/bottom-nav' ); ?>
<?php wp_footer(); ?>
</body>
</html>
