<?php
/**
 * Theme footer.
 *
 * @package Teznevise
 */
// Close main content landmark opened in header.php
?></main>
<footer class="site-footer-new"><div class="container"><div class="footer-grid"><div class="footer-brand"><a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php $logo_url = function_exists( 'teznevise_logo_url' ) ? teznevise_logo_url() : ''; if ( $logo_url ) { printf( '<img src="%s" alt="%s" width="106" height="48" loading="lazy">', esc_url( $logo_url ), esc_attr( get_bloginfo( 'name' ) ) ); } else { echo esc_html( get_bloginfo( 'name' ) ); } ?></a><p><?php esc_html_e( 'پژوهش بهتر، آینده روشن‌تر.', 'teznevise' ); ?></p><div class="footer-social"><a href="<?php echo esc_url( teznevise_get_contact( 'telegram' ) ); ?>" aria-label="<?php esc_attr_e( 'تلگرام', 'teznevise' ); ?>"><i class="fa-brands fa-telegram"></i></a><a href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>" aria-label="<?php esc_attr_e( 'واتساپ', 'teznevise' ); ?>"><i class="fa-brands fa-whatsapp"></i></a></div></div><div class="footer-col"><h4><?php esc_html_e( 'ناوبری', 'teznevise' ); ?></h4><?php echo wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-menu-list', 'fallback_cb' => 'teznevise_fallback_menu', 'echo' => false, 'depth' => 2 ) ); ?></div><div class="footer-col"><h4><?php esc_html_e( 'ارتباط با ما', 'teznevise' ); ?></h4><a href="tel:<?php echo esc_attr( teznevise_get_contact( 'phone_intl' ) ); ?>"><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></a><a href="mailto:<?php echo esc_attr( teznevise_get_contact( 'email' ) ); ?>"><?php echo esc_html( teznevise_get_contact( 'email' ) ); ?></a><p><?php echo esc_html( teznevise_get_contact( 'address' ) ); ?></p></div></div><div class="footer-bottom"><span>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — <?php esc_html_e( 'تمامی حقوق محفوظ است.', 'teznevise' ); ?></span><span><?php printf( esc_html__( 'طراحی RTL واکنش‌گرا — WordPress Theme %s', 'teznevise' ), esc_html( TEZNEVISE_VERSION ) ); ?></span></div></div></footer>
<?php get_template_part( 'template-parts/fab' ); ?>
<?php get_template_part( 'template-parts/bottom-nav' ); ?>
<?php wp_footer(); ?>
</body>
</html>
