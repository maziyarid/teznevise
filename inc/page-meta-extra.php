<?php
/**
 * Page Meta Extra: Pipe-delimited field parser helper.
 *
 * Provides the teznevise_parse_pipe_list() function for parsing pipe-delimited
 * configuration strings into arrays for About, Team, Downloads, and Tools pages.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Parse a pipe-delimited string into an array of rows.
 *
 * Each line represents a row. Within each line, values are separated by pipes (|).
 * The number of expected columns determines how many values are extracted from each line.
 *
 * @param string $input  The pipe-delimited string to parse.
 * @param int    $cols   The number of columns (values) expected per row.
 * @return array        Array of rows, each row being an array of column values.
 */
function teznevise_parse_pipe_list( $input, $cols = 2 ) {
	if ( ! is_string( $input ) || '' === trim( $input ) ) {
		return array();
	}

	$rows = array();
	$lines = preg_split( '/\r\n|\r|\n/', trim( $input ) );

	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}

		// Split by pipe and trim values - DO NOT filter to preserve positional empty/zero values
		$values = array_map( 'trim', explode( '|', $line ) );

		// Pad or truncate to the expected number of columns
		$row = array_pad( $values, $cols, '' );
		$row = array_slice( $row, 0, $cols );

		$rows[] = $row;
	}

	return $rows;
}
