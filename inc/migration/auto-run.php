<?php
/**
 * One-shot automatic shortcode → builder migration for theme updates.
 *
 * Manual Setup UI remains available for dry-run, strip, and tool seeding.
 * This file only ensures that an already-active site still migrates without
 * requiring a form submit.
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
 * Auto-run migration in small batches until complete.
 */
function teznevise_migration_auto_run() {
	if ( ! teznevise_migration_should_auto_run() ) {
		return;
	}
	if ( ! function_exists( 'teznevise_migration_run' ) ) {
		return;
	}

	set_transient( 'teznevise_migration_auto_lock', 1, 5 * MINUTE_IN_SECONDS );

	// Live write, no strip, no tool seed, limited batch to avoid timeouts.
	$stats = teznevise_migration_run( false, 25, false, false );

	set_transient( 'teznevise_migration_auto_last', $stats, HOUR_IN_SECONDS );
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
