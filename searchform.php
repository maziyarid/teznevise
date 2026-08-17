<?php
/**
 * Custom search form.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-field" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="teznevise-search"><?php esc_html_e( 'جستجو', 'teznevise' ); ?></label>
	<input type="search" id="teznevise-search" placeholder="<?php esc_attr_e( 'جستجو…', 'teznevise' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" aria-label="<?php esc_attr_e( 'جستجو', 'teznevise' ); ?>">
		<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
	</button>
</form>
