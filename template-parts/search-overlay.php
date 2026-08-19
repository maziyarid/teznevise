<?php
/**
 * Search overlay — matches inner pages in teznevise_work/.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'جستجو', 'teznevise' ); ?>">
	<form class="search-box" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div class="search-input-wrap">
			<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
			<label class="screen-reader-text" for="teznevise-overlay-search"><?php esc_html_e( 'جستجو در تزنویسه', 'teznevise' ); ?></label>
			<input id="teznevise-overlay-search" class="search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو در تزنویسه…', 'teznevise' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
			<button type="button" class="search-close" aria-label="<?php esc_attr_e( 'بستن جستجو', 'teznevise' ); ?>">
				<i class="fa-solid fa-xmark" aria-hidden="true"></i>
			</button>
		</div>
		<div class="search-hints">
			<a class="search-hint" href="<?php echo esc_url( home_url( '/?s=' . rawurlencode( 'پایان‌نامه ارشد' ) ) ); ?>"><?php esc_html_e( 'پایان‌نامه ارشد', 'teznevise' ); ?></a>
			<a class="search-hint" href="<?php echo esc_url( home_url( '/?s=' . rawurlencode( 'پروپوزال دکتری' ) ) ); ?>"><?php esc_html_e( 'پروپوزال دکتری', 'teznevise' ); ?></a>
			<a class="search-hint" href="<?php echo esc_url( home_url( '/?s=' . rawurlencode( 'تحلیل آماری SPSS' ) ) ); ?>"><?php esc_html_e( 'تحلیل آماری SPSS', 'teznevise' ); ?></a>
			<a class="search-hint" href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>">SPSS</a>
		</div>
	</form>
</div>
