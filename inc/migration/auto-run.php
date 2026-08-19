<?php
/**
 * One-shot automatic shortcode → builder migration for theme updates.
 *
 * Manual Setup UI remains available for dry-run, strip, and tool seeding.
 * This file ensures an already-active site migrates without a form submit.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether auto-migration is allowed right now.
 *
 * @return bool
 */
function teznevise_migration_should_auto_run() {
	if ( ! is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return false;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	if ( function_exists( 'teznevise_migration_is_complete' ) && teznevise_migration_is_complete() ) {
		return false;
	}
	if ( get_transient( 'teznevise_migration_auto_lock' ) ) {
		return false;
	}
	return true;
}

/**
 * Auto-run migration in batches until no migratable pages remain.
 */
function teznevise_migration_auto_run() {
	if ( ! teznevise_migration_should_auto_run() ) {
		return;
	}
	if ( ! function_exists( 'teznevise_migration_run' ) ) {
		return;
	}

	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 );
	}

	set_transient( 'teznevise_migration_auto_lock', 1, 5 * MINUTE_IN_SECONDS );

	$aggregate = array(
		'processed' => 0,
		'migrated'  => 0,
		'skipped'   => 0,
		'stripped'  => 0,
		'errors'    => array(),
		'dry_run'   => false,
	);

	// Up to 20 batches × 25 pages (handles large sites without one huge request).
	for ( $i = 0; $i < 20; $i++ ) {
		$stats = teznevise_migration_run( false, 25, false, false );
		$aggregate['processed'] += (int) ( $stats['processed'] ?? 0 );
		$aggregate['migrated']  += (int) ( $stats['migrated'] ?? 0 );
		$aggregate['skipped']   += (int) ( $stats['skipped'] ?? 0 );
		if ( ! empty( $stats['errors'] ) ) {
			$aggregate['errors'] = array_merge( $aggregate['errors'], $stats['errors'] );
		}
		// No more pages that needed writing in this batch.
		if ( empty( $stats['migrated'] ) ) {
			break;
		}
	}

	// Full pass (limit 0) so completion flag is set only when truly done.
	if ( function_exists( 'teznevise_migration_run' ) ) {
		$final = teznevise_migration_run( false, 0, false, false );
		$aggregate['processed'] += (int) ( $final['processed'] ?? 0 );
		$aggregate['migrated']  += (int) ( $final['migrated'] ?? 0 );
		$aggregate['skipped']   += (int) ( $final['skipped'] ?? 0 );
	}

	set_transient( 'teznevise_migration_auto_last', $aggregate, HOUR_IN_SECONDS );
	delete_transient( 'teznevise_migration_auto_lock' );

	// One-time rewrite flush for CPT permalinks on existing installs.
	if ( ! get_option( 'teznevise_cpt_rewrites_flushed_v1' ) ) {
		if ( function_exists( 'teznevise_cpt_flush_rewrites' ) ) {
			teznevise_cpt_flush_rewrites();
		} else {
			flush_rewrite_rules( false );
		}
		update_option( 'teznevise_cpt_rewrites_flushed_v1', 1, false );
	}
}
add_action( 'admin_init', 'teznevise_migration_auto_run', 5 );

/**
 * Admin notice when auto-migration ran.
 */
function teznevise_migration_auto_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$stats = get_transient( 'teznevise_migration_auto_last' );
	if ( ! is_array( $stats ) ) {
		return;
	}
	delete_transient( 'teznevise_migration_auto_last' );
	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<?php
			printf(
				esc_html__( 'تزنویسه: مهاجرت خودکار شورت‌کد → صفحه‌ساز — پردازش %1$d، مهاجرت %2$d، رد %3$d.', 'teznevise' ),
				(int) ( $stats['processed'] ?? 0 ),
				(int) ( $stats['migrated'] ?? 0 ),
				(int) ( $stats['skipped'] ?? 0 )
			);
			?>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=teznevise-setup' ) ); ?>"><?php esc_html_e( 'جزئیات در راه‌اندازی', 'teznevise' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'teznevise_migration_auto_admin_notice' );
