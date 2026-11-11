<?php
/** Test the pure trust-reference transformation without booting WordPress. */
$source = file_get_contents( __DIR__ . '/../../inc/seo.php' );
$start = strpos( $source, 'function teznevise_organization_trust_reference(' );
$end = strpos( $source, "\nadd_filter( 'wpseo_schema_organization'", $start );
if ( false === $start || false === $end ) {
    throw new RuntimeException( 'Trust reference function not found' );
}
eval( substr( $source, $start, $end - $start ) );
$input = array( '@graph' => array( array(
    '@type' => array( 'Organization', 'EducationalOrganization' ),
    'name' => 'Original name',
    'sameAs' => array( 'https://example.org/identity' ),
) ) );
$output = teznevise_organization_trust_reference( $input );
if ( $output['@graph'][0]['name'] !== 'Original name' || count( $output['@graph'][0]['sameAs'] ) !== 2 ) {
    throw new RuntimeException( 'Existing identity fields were not retained' );
}
if ( teznevise_organization_trust_reference( $output ) !== $output ) {
    throw new RuntimeException( 'Trust reference is not idempotent' );
}
if ( isset( $output['@graph'][0]['identifier'] ) ) {
    throw new RuntimeException( 'Publisher identifier must remain absent' );
}
if ( 'text' !== teznevise_organization_trust_reference( 'text' ) ) {
    throw new RuntimeException( 'Non-array input changed' );
}
echo "Organization trust reference checks passed\n";
