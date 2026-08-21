<?php
/**
 * Header search overlay with popular queries.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$popular = function_exists( 'teznevise_popular_searches' ) ? teznevise_popular_searches() : array();
?>
<div id="teznevise-search-overlay" class="search-overlay" hidden aria-hidden="true">
	<div class="search-overlay-panel" role="dialog" aria-modal="true" aria-labelledby="tz-overlay-q">
		<form class="search-overlay-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-instant-search data-search-endpoint="<?php echo esc_url( rest_url( 'wp/v2/search' ) ); ?>">
			<label class="screen-reader-text" for="tz-overlay-q"><?php esc_html_e( 'جستجو', 'teznevise' ); ?></label>
			<input class="search-input" id="tz-overlay-q" type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو در خدمات، ابزارها و مقالات…', 'teznevise' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'جستجو', 'teznevise' ); ?></button>
			<button class="search-close icon-btn" type="button" aria-label="<?php esc_attr_e( 'بستن', 'teznevise' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
		</form>
		<div class="tz-search-results" data-search-results hidden>
			<p class="tz-search-results__status" data-search-status role="status" aria-live="polite"></p>
			<ul class="tz-search-results__list" data-search-list></ul>
		</div>
		<?php if ( $popular ) : ?>
			<p class="search-pop-label"><?php esc_html_e( 'پرجستجوترین‌ها', 'teznevise' ); ?></p>
			<div class="search-pills">
				<?php foreach ( $popular as $term ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 's', $term, home_url( '/' ) ) ); ?>"><?php echo esc_html( $term ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
