<?php
/**
 * Theme footer.
 *
 * @package Teznevise
 */
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
				<p><?php esc_html_e( 'تزنویسه همراه پژوهشی دانشجویان و پژوهشگران؛ از انتخاب موضوع و تدوین پروپوزال تا تحلیل آماری، نگارش و آمادگی دفاع.', 'teznevise' ); ?></p>
				<p><?php esc_html_e( 'پژوهش بهتر، آینده روشن‌تر.', 'teznevise' ); ?></p>
				<div class="footer-social">
					<a href="<?php echo esc_url( teznevise_get_contact( 'telegram' ) ); ?>" aria-label="<?php esc_attr_e( 'تلگرام', 'teznevise' ); ?>"><i class="fa-brands fa-telegram"></i></a>
					<a href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>" aria-label="<?php esc_attr_e( 'واتساپ', 'teznevise' ); ?>"><i class="fa-brands fa-whatsapp"></i></a>
				</div>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e( 'خدمات', 'teznevise' ); ?></h4>
				<a href="<?php echo esc_url( home_url( '/thesis/' ) ); ?>"><?php esc_html_e( 'انجام پایان‌نامه', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/proposal/' ) ); ?>"><?php esc_html_e( 'انجام پروپوزال', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/statistics/' ) ); ?>"><?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e( 'ارتباط با ما', 'teznevise' ); ?></h4>
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
			<?php
			$enamad    = function_exists( 'teznevise_tezcoin_get' ) ? teznevise_tezcoin_get( 'enamad_url' ) : '';
			$samandehi = function_exists( 'teznevise_tezcoin_get' ) ? teznevise_tezcoin_get( 'samandehi_url' ) : '';
			?>
			<?php if ( $enamad && false === strpos( $enamad, '/privacy' ) ) : ?>
			<a class="trust-seal" href="<?php echo esc_url( $enamad ); ?>" rel="noopener noreferrer" target="_blank" title="<?php esc_attr_e( 'اینماد', 'teznevise' ); ?>">
				<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="20" fill="#145D4A"/><path d="M16 25.2l5 5 11-13" fill="none" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				<span><?php esc_html_e( 'اینماد', 'teznevise' ); ?></span>
			</a>
			<?php endif; ?>
			<?php if ( $samandehi && false === strpos( $samandehi, '/privacy' ) ) : ?>
			<a class="trust-seal" href="<?php echo esc_url( $samandehi ); ?>" rel="noopener noreferrer" target="_blank" title="<?php esc_attr_e( 'ساماندهی', 'teznevise' ); ?>">
				<svg viewBox="0 0 48 48" aria-hidden="true"><rect x="8" y="10" width="32" height="28" rx="6" fill="#1b765f"/><path d="M16 24h16M24 16v16" stroke="#fff" stroke-width="3" stroke-linecap="round"/></svg>
				<span><?php esc_html_e( 'ساماندهی', 'teznevise' ); ?></span>
			</a>
			<?php endif; ?>
			<span class="trust-seal" title="<?php esc_attr_e( 'SSL', 'teznevise' ); ?>">
				<svg viewBox="0 0 48 48" aria-hidden="true"><rect x="12" y="20" width="24" height="16" rx="4" fill="#0f4a3b"/><path d="M18 20v-4a6 6 0 0112 0v4" fill="none" stroke="#82d8b9" stroke-width="3"/></svg>
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
