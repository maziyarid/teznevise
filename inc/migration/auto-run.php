<?php
/**
 * Incremental automatic shortcode → builder migration for theme updates.
 *
 * Runs on admin_init in small batches so an already-active site migrates
 * without a Setup form submit, without one unbounded request, and with a
 * persisted remaining-set so a timeout can resume on the next admin load.
 *
 * Manual Setup UI remains available for dry-run, strip, and tool seeding.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Pages written per batch. Keep this well under typical max_execution_time. */
define( 'TEZNEVISE_MIGRATION_AUTO_BATCH', 25 );

/**
 * Max batches per admin request.
 *
 * Never invoke teznevise_migration_run() with limit 0 from this file: that
 * uncapped pass defeats batching and, on timeout, leaves no resume cursor.
 */
define( 'TEZNEVISE_MIGRATION_AUTO_MAX_BATCHES', 3 );

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
 * Auto-run migration in bounded batches until this request's budget is spent.
 *
 * Remaining pages stay in the candidate query (unmigrated, not skipped) so
 * the next admin load continues instead of restarting from page 1 or
 * incorrectly marking the job complete.
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
		'has_more'  => false,
	);

	$batch_size = (int) TEZNEVISE_MIGRATION_AUTO_BATCH;
	$max        = (int) TEZNEVISE_MIGRATION_AUTO_MAX_BATCHES;

	for ( $i = 0; $i < $max; $i++ ) {
		$stats = teznevise_migration_run( false, $batch_size, false, false );
		$aggregate['processed'] += (int) ( $stats['processed'] ?? 0 );
		$aggregate['migrated']  += (int) ( $stats['migrated'] ?? 0 );
		$aggregate['skipped']   += (int) ( $stats['skipped'] ?? 0 );
		if ( ! empty( $stats['errors'] ) ) {
			$aggregate['errors'] = array_merge( $aggregate['errors'], $stats['errors'] );
		}

		$has_more              = ! empty( $stats['has_more'] );
		$aggregate['has_more'] = $has_more;

		if ( empty( $stats['processed'] ) || ! $has_more ) {
			break;
		}
	}

	if ( function_exists( 'teznevise_migration_maybe_mark_complete' ) ) {
		teznevise_migration_maybe_mark_complete( $aggregate );
	}

	set_transient( 'teznevise_migration_auto_last', $aggregate, HOUR_IN_SECONDS );
	delete_transient( 'teznevise_migration_auto_lock' );

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
	$has_more = ! empty( $stats['has_more'] );
	$class    = $has_more ? 'notice-info' : 'notice-success';
	?>
	<div class="notice <?php echo esc_attr( $class ); ?> is-dismissible">
		<p>
			<?php
			printf(
				esc_html__( 'تزنویسه: مهاجرت خودکار شورت‌کد → صفحه‌ساز — پردازش %1$d، مهاجرت %2$d، رد %3$d.', 'teznevise' ),
				(int) ( $stats['processed'] ?? 0 ),
				(int) ( $stats['migrated'] ?? 0 ),
				(int) ( $stats['skipped'] ?? 0 )
			);
			if ( $has_more ) {
				echo ' ';
				esc_html_e( 'صفحات باقی‌مانده در بارگذاری بعدی پیشخوان ادامه می‌یابند.', 'teznevise' );
			}
			?>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=teznevise-setup' ) ); ?>"><?php esc_html_e( 'جزئیات در راه‌اندازی', 'teznevise' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'teznevise_migration_auto_admin_notice' );
