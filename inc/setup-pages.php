<?php
/**
 * One-click seed of recommended pages with correct templates.
 * Appearance → Teznevise Setup
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_recommended_pages() {
	return array(
		'service-thesis' => array( 'title' => 'انجام پایان‌نامه', 'template' => 'page-service.php', 'meta' => array( 'eyebrow' => 'خدمات پژوهشی', 'subtitle' => 'از انتخاب موضوع تا دفاع', 'service_icon' => 'fa-solid fa-graduation-cap', 'service_color' => 'icon-indigo', 'cta_text' => 'شروع مشاوره رایگان', 'cta_url' => '/inquiry/' ) ),
		'service-proposal' => array( 'title' => 'انجام پروپوزال', 'template' => 'page-service.php', 'meta' => array( 'eyebrow' => 'خدمات پژوهشی', 'subtitle' => 'بیان مسئله و روش‌شناسی', 'service_icon' => 'fa-solid fa-file-circle-check', 'service_color' => 'icon-teal', 'cta_text' => 'شروع مشاوره رایگان', 'cta_url' => '/inquiry/' ) ),
		'service-statistics' => array( 'title' => 'تحلیل آماری', 'template' => 'page-service.php', 'meta' => array( 'eyebrow' => 'خدمات پژوهشی', 'subtitle' => 'SPSS, R, Python, AMOS', 'service_icon' => 'fa-solid fa-chart-line', 'service_color' => 'icon-cyan', 'cta_text' => 'شروع مشاوره رایگان', 'cta_url' => '/inquiry/' ) ),
		'service-simulation' => array( 'title' => 'شبیه‌سازی', 'template' => 'page-service.php', 'meta' => array( 'eyebrow' => 'خدمات پژوهشی', 'subtitle' => 'مدل‌سازی و شبیه‌سازی', 'service_icon' => 'fa-solid fa-flask', 'service_color' => 'icon-amber', 'cta_text' => 'شروع مشاوره رایگان', 'cta_url' => '/inquiry/' ) ),
		'tools' => array( 'title' => 'ابزارهای آنلاین', 'template' => '', 'meta' => array( 'eyebrow' => 'ابزار رایگان', 'subtitle' => 'ماشین‌حساب‌ها', 'service_icon' => 'fa-solid fa-calculator', 'service_color' => 'icon-amber' ) ),
		'about' => array( 'title' => 'درباره ما', 'template' => '', 'meta' => array( 'eyebrow' => 'تزنویسه', 'subtitle' => 'همراه پژوهشی' ) ),
		'team' => array( 'title' => 'تیم پژوهشگران', 'template' => '', 'meta' => array( 'eyebrow' => 'تخصص', 'subtitle' => 'پژوهشگران', 'service_icon' => 'fa-solid fa-user-group', 'service_color' => 'icon-purple-soft' ) ),
		'contact' => array( 'title' => 'تماس با ما', 'template' => 'page-contact.php', 'meta' => array( 'eyebrow' => 'ارتباط', 'subtitle' => 'مشاوره رایگان' ) ),
		'inquiry' => array( 'title' => 'ثبت درخواست', 'template' => 'page-contact.php', 'meta' => array( 'eyebrow' => 'سفارش', 'subtitle' => 'موضوع را بفرستید' ) ),
		'privacy' => array( 'title' => 'حریم خصوصی', 'template' => 'page-privacy.php', 'meta' => array( 'eyebrow' => 'حقوق', 'subtitle' => 'حریم خصوصی' ) ),
		'sitemap' => array( 'title' => 'نقشه سایت', 'template' => 'page-sitemap.php', 'meta' => array( 'eyebrow' => 'نقشه سایت', 'subtitle' => 'دسترسی سریع' ) ),
		'downloads' => array( 'title' => 'دانلودها', 'template' => '', 'meta' => array( 'eyebrow' => 'منابع', 'subtitle' => 'فایل‌ها' ) ),
	);
}

function teznevise_seed_pages() {
	$created = array();
	$skipped = array();
	foreach ( teznevise_recommended_pages() as $slug => $cfg ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$skipped[] = $slug;
			if ( ! empty( $cfg['template'] ) ) {
				update_post_meta( $existing->ID, '_wp_page_template', $cfg['template'] );
			}
			continue;
		}
		$page_id = wp_insert_post( array( 'post_title' => $cfg['title'], 'post_name' => $slug, 'post_status' => 'publish', 'post_type' => 'page', 'post_content' => '', 'post_author' => get_current_user_id() ? get_current_user_id() : 1 ), true );
		if ( is_wp_error( $page_id ) ) { $skipped[] = $slug; continue; }
		if ( ! empty( $cfg['template'] ) ) { update_post_meta( $page_id, '_wp_page_template', $cfg['template'] ); }
		if ( ! empty( $cfg['meta'] ) ) { foreach ( $cfg['meta'] as $key => $val ) { update_post_meta( $page_id, '_teznevise_' . $key, $val ); } }
		$created[] = $slug;
	}
	return array( 'created' => $created, 'skipped' => $skipped );
}

function teznevise_setup_admin_menu() {
	add_theme_page( __( 'راه‌اندازی تزنویسه', 'teznevise' ), __( 'راه‌اندازی تزنویسه', 'teznevise' ), 'edit_theme_options', 'teznevise-setup', 'teznevise_setup_admin_page' );
}
add_action( 'admin_menu', 'teznevise_setup_admin_menu' );

function teznevise_setup_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) { return; }
	$result = null;
	if ( isset( $_POST['teznevise_seed_pages'] ) && check_admin_referer( 'teznevise_seed_pages' ) ) {
		$result = teznevise_seed_pages();
	}
	$pages = teznevise_recommended_pages();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'راه‌اندازی تزنویسه', 'teznevise' ); ?></h1>
		<p><?php esc_html_e( 'صفحات پیشنهادی با قالب و فیلدهای پیش‌فرض ساخته می‌شوند.', 'teznevise' ); ?></p>
		<?php if ( $result ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php printf( esc_html__( 'ایجاد: %1$d — رد: %2$d', 'teznevise' ), count( $result['created'] ), count( $result['skipped'] ) ); ?></p></div>
		<?php endif; ?>
		<table class="widefat striped" style="max-width:720px;margin:16px 0;"><thead><tr><th>slug</th><th><?php esc_html_e( 'عنوان', 'teznevise' ); ?></th><th><?php esc_html_e( 'قالب', 'teznevise' ); ?></th><th><?php esc_html_e( 'وضعیت', 'teznevise' ); ?></th></tr></thead><tbody>
		<?php foreach ( $pages as $slug => $cfg ) : $exists = get_page_by_path( $slug ); ?>
			<tr><td><code><?php echo esc_html( $slug ); ?></code></td><td><?php echo esc_html( $cfg['title'] ); ?></td><td><?php echo esc_html( $cfg['template'] ? $cfg['template'] : 'page.php' ); ?></td><td><?php echo $exists ? 'موجود' : 'نیاز'; ?></td></tr>
		<?php endforeach; ?>
		</tbody></table>
		<form method="post"><?php wp_nonce_field( 'teznevise_seed_pages' ); ?><button type="submit" name="teznevise_seed_pages" class="button button-primary button-hero"><?php esc_html_e( 'ایجاد صفحات پیشنهادی', 'teznevise' ); ?></button></form>
		<?php if ( function_exists( 'teznevise_render_promote_assets_section' ) ) { teznevise_render_promote_assets_section(); } ?>
		<hr style="margin:28px 0;"><p><strong><?php esc_html_e( 'گام‌های بعدی:', 'teznevise' ); ?></strong> <?php esc_html_e( 'Customize + Menus + page content.', 'teznevise' ); ?></p>
	</div>
	<?php
}
