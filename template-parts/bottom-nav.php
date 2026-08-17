<?php
/**
 * Bottom mobile navigation.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<nav class="bottom-nav" aria-label="<?php esc_attr_e( 'ناوبری سریع', 'teznevise' ); ?>"><?php echo wp_nav_menu( array( 'theme_location' => 'bottom', 'container' => false, 'menu_class' => 'bottom-menu-list', 'fallback_cb' => 'teznevise_fallback_menu', 'echo' => false, 'depth' => 1 ) ); ?></nav>
