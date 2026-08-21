<?php
/**
 * Floating action button (contact channels).
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="tz-fab-wrap" id="tzFab">
	<div class="tz-fab-menu" id="tzFabMenu" hidden>
		<a class="tz-fab-item" href="<?php echo esc_url( teznevise_get_contact( 'telegram' ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'تلگرام', 'teznevise' ); ?>">
			<i class="fa-brands fa-telegram" aria-hidden="true"></i><span><?php esc_html_e( 'تلگرام', 'teznevise' ); ?></span>
		</a>
		<a class="tz-fab-item" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'واتساپ', 'teznevise' ); ?>">
			<i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span><?php esc_html_e( 'واتساپ', 'teznevise' ); ?></span>
		</a>
		<a class="tz-fab-item" href="<?php echo esc_url( teznevise_get_contact( 'bale' ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'بله', 'teznevise' ); ?>">
			<span class="tz-fab-bale"><?php esc_html_e( 'بله', 'teznevise' ); ?></span><span><?php esc_html_e( 'بله', 'teznevise' ); ?></span>
		</a>
		<a class="tz-fab-item" href="<?php echo esc_attr( function_exists( 'teznevise_tel_href' ) ? teznevise_tel_href( teznevise_get_contact( 'phone_intl' ) ) : 'tel:+989302822091' ); ?>" aria-label="<?php esc_attr_e( 'تماس تلفنی', 'teznevise' ); ?>">
			<i class="fa-solid fa-phone" aria-hidden="true"></i><span><?php esc_html_e( 'تماس', 'teznevise' ); ?></span>
		</a>
	</div>
	<button type="button" class="tz-fab-toggle" id="tzFabToggle" aria-haspopup="true" aria-expanded="false" aria-controls="tzFabMenu" aria-label="<?php esc_attr_e( 'راه‌های ارتباطی', 'teznevise' ); ?>">
		<i class="fa-regular fa-comments" data-fab-icon aria-hidden="true"></i>
	</button>
</div>
