<?php
/**
 * Promote reference assets into theme assets/ when missing.
 *
 * Copies from teznevise_work/assets → assets/ so production can stop
 * depending on the reference folder after first admin promote.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Relative paths under assets/ to promote from teznevise_work.
 * Public CSS from 1.8.5 (tokens/components/pages/chrome) ships in the
 * theme tree — do not copy styles.css back from the work folder.
 *
 * @return string[]
 */
function teznevise_promotable_assets() {
	return array(
		'css/redesign.css',
		'css/layout-refinements.css',
		'css/motion.css',
		'css/batch-fixes.css',
		'css/ui-round2.css',
		'css/site-polish.css',
		'css/service-thesis.css',
		'css/service-statistics.css',
		'css/service-simulation.css',
		'js/redesign.js',
		'js/main.js',
		'img/logo.jpg',
		'icons/sprite.svg',
	);
}

/**
 * Copy missing assets from teznevise_work into assets/.
 *
 * @return array{copied:string[],skipped:string[],errors:string[]}
 */
function teznevise_promote_assets() {
	$copied  = array();
	$skipped = array();
	$errors  = array();

	$src_root = TEZNEVISE_DIR . '/teznevise_work/assets';
	$dst_root = TEZNEVISE_DIR . '/assets';

	if ( ! is_dir( $src_root ) ) {
		return array(
			'copied'  => $copied,
			'skipped' => $skipped,
			'errors'  => array( 'teznevise_work/assets not found' ),
		);
	}

	foreach ( teznevise_promotable_assets() as $rel ) {
		$src = $src_root . '/' . $rel;
		$dst = $dst_root . '/' . $rel;

		if ( ! file_exists( $src ) ) {
			$skipped[] = $rel . ' (source missing)';
			continue;
		}
		if ( file_exists( $dst ) && filesize( $dst ) > 0 ) {
			$skipped[] = $rel;
			continue;
		}

		$dir = dirname( $dst );
		if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
			$errors[] = 'mkdir failed: ' . $dir;
			continue;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_copy
		if ( @copy( $src, $dst ) ) {
			$copied[] = $rel;
		} else {
			$errors[] = 'copy failed: ' . $rel;
		}
	}

	return array(
		'copied'  => $copied,
		'skipped' => $skipped,
		'errors'  => $errors,
	);
}

/**
 * Handle promote form POST.
 */
function teznevise_maybe_handle_promote_assets() {
	if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( empty( $_POST['teznevise_promote_assets'] ) ) {
		return;
	}
	if ( ! check_admin_referer( 'teznevise_promote_assets' ) ) {
		return;
	}
	$result = teznevise_promote_assets();
	set_transient( 'teznevise_promote_result', $result, 60 );
}
add_action( 'admin_init', 'teznevise_maybe_handle_promote_assets' );

/**
 * Render promote UI on the setup page.
 */
function teznevise_render_promote_assets_section() {
	$result = get_transient( 'teznevise_promote_result' );
	if ( $result ) {
		delete_transient( 'teznevise_promote_result' );
		echo '<div class="notice notice-success is-dismissible"><p>';
		printf(
			esc_html__( 'کپی شد: %1$d — موجود از قبل: %2$d', 'teznevise' ),
			count( $result['copied'] ),
			count( $result['skipped'] )
		);
		if ( ! empty( $result['errors'] ) ) {
			echo ' — ' . esc_html( implode( '; ', $result['errors'] ) );
		}
		echo '</p></div>';
	}
	?>
	<hr style="margin:28px 0;">
	<h2><?php esc_html_e( 'انتقال دارایی‌ها به assets/', 'teznevise' ); ?></h2>
	<p><?php esc_html_e( 'فایل‌های CSS/JS/لوگو را از پوشه مرجع teznevise_work به assets/ کپی می‌کند (فقط موارد گم‌شده).', 'teznevise' ); ?></p>
	<form method="post">
		<?php wp_nonce_field( 'teznevise_promote_assets' ); ?>
		<button type="submit" name="teznevise_promote_assets" class="button button-secondary">
			<?php esc_html_e( 'کپی دارایی‌ها به assets/', 'teznevise' ); ?>
		</button>
	</form>
	<?php
}
