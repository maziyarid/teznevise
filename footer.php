<?php
/**
 * Theme footer.
 *
 * @package Teznevise
 */
if ( is_page() && function_exists( 'teznevise_the_page_leftover_content' ) ) {
	echo '<div class="container tz-classic-more-wrap">';
	teznevise_the_page_leftover_content();
	echo '</div>';
}
?></main>
<footer class="site-footer site-footer-new footer-new">
	<div class="container">
		<div class="footer-grid">
			<div class="footer-brand">
				<a class="footer-logo footer-logo-wrap" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php
					$logo_url = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : '';
					if ( ! $logo_url && has_custom_logo() ) {
						$logo_url = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
					}
					if ( $logo_url ) {
						printf( '<img src="%s" alt="%s" width="116" height="44" loading="lazy" decoding="async">', esc_url( $logo_url ), esc_attr( get_bloginfo( 'name' ) ) );
					} else {
						echo esc_html( get_bloginfo( 'name' ) );
					}
					?>
				</a>
				<p><?php esc_html_e( 'تزنویسه همراه پژوهشی دانشجویان همه رشته‌هاست: ابزارهای آنلاین، عامل‌های هوش مصنوعی و مشاوران متخصص برای مسیر پایان‌نامه، پروپوزال، تحلیل آماری و آمادگی دفاع.', 'teznevise' ); ?></p>
				<p><?php esc_html_e( 'پژوهش بهتر، آینده روشن‌تر.', 'teznevise' ); ?></p>
				<div class="footer-social">
					<a href="<?php echo esc_url( teznevise_get_contact( 'telegram' ) ); ?>" aria-label="<?php esc_attr_e( 'تلگرام', 'teznevise' ); ?>"><i class="fa-brands fa-telegram"></i></a>
					<a href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>" aria-label="<?php esc_attr_e( 'واتساپ', 'teznevise' ); ?>"><i class="fa-brands fa-whatsapp"></i></a>
				</div>
			</div>
			<div class="footer-col">
				<p class="footer-heading"><?php esc_html_e( 'خدمات', 'teznevise' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/thesis/' ) ); ?>"><?php esc_html_e( 'مشاوره انجام پایان‌نامه', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/proposal/' ) ); ?>"><?php esc_html_e( 'مشاوره انجام پروپوزال', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/online-calculation-tools/' ) ); ?>"><?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a>
			</div>
			<div class="footer-col">
				<p class="footer-heading"><?php esc_html_e( 'ارتباط با ما', 'teznevise' ); ?></p>
				<a href="<?php echo esc_attr( function_exists( 'teznevise_tel_href' ) ? teznevise_tel_href( teznevise_get_contact( 'phone_intl' ) ) : 'tel:+989302822091' ); ?>"><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></a>
				<a href="mailto:<?php echo esc_attr( teznevise_get_contact( 'email' ) ); ?>"><?php echo esc_html( teznevise_get_contact( 'email' ) ); ?></a>
				<p><?php echo esc_html( teznevise_get_contact( 'address' ) ); ?></p>
			</div>
		</div>
		<nav class="footer-utility" aria-label="<?php esc_attr_e( 'پیوندهای تکمیلی', 'teznevise' ); ?>">
			<?php echo wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-menu-list', 'fallback_cb' => 'teznevise_fallback_menu', 'echo' => false, 'depth' => 1 ) ); ?>
			<div class="footer-legal" aria-label="<?php esc_attr_e( 'صفحات حقوقی', 'teznevise' ); ?>">
				<a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'قوانین استفاده', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/cookies/' ) ); ?>"><?php esc_html_e( 'کوکی‌ها', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/refund/' ) ); ?>"><?php esc_html_e( 'بازپرداخت', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/research-rules/' ) ); ?>"><?php esc_html_e( 'ضوابط پژوهش', 'teznevise' ); ?></a>
			</div>
		</nav>
		<div class="footer-certs" aria-label="<?php esc_attr_e( 'نمادهای اعتماد', 'teznevise' ); ?>">
			<div class="footer-certs__enamad">
<a referrerpolicy='origin' target='_blank' href='https://trustseal.enamad.ir/?id=7413817&Code=HcAFYmDgGupv1YV2E6OiOkBVVihO5OpP'><img referrerpolicy='origin' src='https://trustseal.enamad.ir/logo.aspx?id=7413817&Code=HcAFYmDgGupv1YV2E6OiOkBVVihO5OpP' alt='' style='cursor:pointer' code='HcAFYmDgGupv1YV2E6OiOkBVVihO5OpP'></a>
			</div>
			<div class="footer-certs__trusted">
<script type="text/javascript" src="https://cdn.ywxi.net/js/1.js" async></script>
			</div>
			<span class="trust-seal is-ssl" title="<?php esc_attr_e( 'ارتباط امن HTTPS', 'teznevise' ); ?>">
				<svg viewBox="0 0 48 48" width="22" height="22" aria-hidden="true"><rect x="12" y="20" width="24" height="16" rx="4" fill="#0f4a3b"/><path d="M18 20v-4a6 6 0 0112 0v4" fill="none" stroke="#82d8b9" stroke-width="3"/></svg>
				<span>SSL</span>
			</span>
		</div>
		<div class="footer-bottom">
			<span>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — <?php esc_html_e( 'تمامی حقوق محفوظ است.', 'teznevise' ); ?></span>
		</div>
	</div>
</footer>
<?php get_template_part( 'template-parts/fab' ); ?>
<?php get_template_part( 'template-parts/bottom-nav' ); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
