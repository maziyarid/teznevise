<?php
/** No database or WordPress configuration is needed for the early refusal. */
define( 'ABSPATH', __DIR__ );
define( 'TEZNEVISE_DIR', dirname( __DIR__, 2 ) );
function add_action() {}
function add_filter() {}
require TEZNEVISE_DIR . '/inc/migration/shortcode-to-builder-migrator.php';
$result = teznevise_migration_run( false );
if ( empty( $result['errors'] ) || 0 !== $result['processed'] || 0 !== $result['migrated'] ) {
    throw new RuntimeException( 'An unreviewed import reached the write path' );
}
echo "Pending legal review blocks imports before any write path\n";
