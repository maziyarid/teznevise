<?php
/**
 * Site-wide search overlay (data-search-open / .search-overlay).
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'جستجو', 'teznevise' ); ?>">
	<div class="search-box">
		<form role="search" method="get" class="search-field" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="search-input-wrap">
				<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
				<label class="screen-reader-text" for="teznevise-overlay-search"><?php esc_html_e( 'جستجو', 'teznevise' ); ?></label>
				<input id="teznevise-overlay-search" class="search-input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جستجو در تزنویسه...', 'teznevise' ); ?>" autocomplete="off">
				<button type="button" class="icon-btn search-close" aria-label="<?php esc_attr_e( 'بستن جستجو', 'teznevise' ); ?>">
					<i class="fa-solid fa-xmark" aria-hidden="true"></i>
				</button>
			</div>
		</form>
		<div class="search-hints">
			<a class="search-hint" href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'پایان‌نامه ارشد', 'teznevise' ); ?></a>
			<a class="search-hint" href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><?php esc_html_e( 'پروپوزال دکتری', 'teznevise' ); ?></a>
			<a class="search-hint" href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><?php esc_html_e( 'تحلیل آماری SPSS', 'teznevise' ); ?></a>
			<a class="search-hint" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></a>
		</div>
	</div>
</div>
