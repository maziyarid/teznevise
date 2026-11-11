<?php
/** SEO-plugin ownership must suppress every manual emitter before WP calls. */
define( 'ABSPATH', __DIR__ );
define( 'RANK_MATH_VERSION', 'test' );
function add_action() {}
function add_filter() {}
function is_admin() { return false; }
require dirname( __DIR__, 2 ) . '/inc/seo.php';
ob_start();
teznevise_output_schema();
teznevise_output_faq_schema();
teznevise_output_service_schema();
$output = ob_get_clean();
if ( '' !== $output ) {
    throw new RuntimeException( 'Mixed schema owners were emitted' );
}
echo "Rank Math ownership suppresses manual schema output\n";
