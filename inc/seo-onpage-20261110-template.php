<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: MΛZ — https://github.com/maziyarid/M-Z
 * @package Teznevise
 * Managed SEO page: intentionally bypasses stale builder copy and legacy schema.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
?>
<section class="section"><div class="container"><article class="longcopy article-content">
<header><h1><?php the_title(); ?></h1></header>
<?php
// Authored package HTML was validated and saved through core post sanitization.
// Direct rendering avoids re-introducing builder FAQ and service content.
echo get_post_field( 'post_content', get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</article></div></section>
<?php
endwhile;
// The theme footer prints Classic Editor leftovers once per page. Mark the
// existing helper as consumed so our full article is not printed a second time.
if ( function_exists( 'teznevise_the_page_leftover_content' ) ) {
    ob_start(); teznevise_the_page_leftover_content(); ob_end_clean();
}
get_footer();
