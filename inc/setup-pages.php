<?php
/**
 * Project: Teznevise WordPress Theme
 * Author: MAZ//ID (Maziyar)
 * Brand: maziyarid/M-Z — A brand new repository with my complete brand identity, story, and website prototype.
 *
 * One-click seed of recommended pages with correct templates.
 * Appearance → Teznevise Setup
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recommended pages map (static → WP).
 *
 * @return array
 */
function teznevise_recommended_pages() {
	return array(
		'service-thesis'     => array(
			'title'    => 'انجام پایان‌نامه',
			'template' => 'page-service.php',
			'meta'     => array(
				'eyebrow'       => 'خدمات پژوهشی',
				'subtitle'      => 'از انتخاب موضوع تا دفاع',
				'service_icon'  => 'fa-solid fa-graduation-cap',
				'service_color' => 'icon-indigo',
				'cta_text'      => 'شروع مشاوره رایگان',
				'cta_url'       => '/inquiry/',
			),
		),
		'service-proposal'   => array(
			'title'    => 'انجام پروپوزال',
			'template' => 'page-service.php',
			'meta'     => array(
				'eyebrow'       => 'خدمات پژوهشی',
				'subtitle'      => 'بیان مسئله و روش‌شناسی',
				'service_icon'  => 'fa-solid fa-file-circle-check',
				'service_color' => 'icon-teal',
				'cta_text'      => 'شروع مشاوره رایگان',
				'cta_url'       => '/inquiry/',
			),
		),
		'service-statistics' => array(
			'title'    => 'تحلیل آماری',
			'template' => 'page-service.php',
			'meta'     => array(
				'eyebrow'       => 'خدمات پژوهشی',
				'subtitle'      => 'SPSS, R, Python, AMOS',
				'service_icon'  => 'fa-solid fa-chart-line',
				'service_color' => 'icon-cyan',
				'cta_text'      => 'شروع مشاوره رایگان',
				'cta_url'       => '/inquiry/',
			),
		),
		'service-simulation' => array(
			'title'    => 'شبیه‌سازی',
			'template' => 'page-service.php',
			'meta'     => array(
				'eyebrow'       => 'خدمات پژوهشی',
				'subtitle'      => 'مدل‌سازی و شبیه‌سازی',
				'service_icon'  => 'fa-solid fa-flask',
				'service_color' => 'icon-amber',
				'cta_text'      => 'شروع مشاوره رایگان',
				'cta_url'       => '/inquiry/',
			),
		),
		'tools'              => array(
			'title'    => 'ابزارهای آنلاین',
			'template' => 'page-tools.php',
			'meta'     => array(
				'eyebrow'       => 'ابزار رایگان',
				'subtitle'      => 'ماشین‌حساب‌های آماری',
				'service_icon'  => 'fa-solid fa-calculator',
				'service_color' => 'icon-amber',
			),
		),
		'tool-descriptive-statistics' => array(
			'title'    => 'آمار توصیفی',
			'template' => 'page-tool.php',
			'meta'     => array(
				'eyebrow'       => 'ابزار آنلاین',
				'subtitle'      => 'میانگین، میانه، واریانس و شاخص‌های توصیفی',
				'service_icon'  => 'fa-solid fa-chart-simple',
				'service_color' => 'icon-amber',
				'features'      => "داده‌های عددی را وارد کنید (با ویرگول یا فاصله).\nدکمه محاسبه را بزنید.\nنتایج را کپی یا ذخیره کنید.",
				'cta_text'      => 'تحلیل آماری تخصصی',
				'cta_url'       => '/service-statistics/',
			),
		),
		'about'              => array(
			'title'    => 'درباره ما',
			'template' => 'page-about.php',
			'meta'     => array(
				'eyebrow'  => 'تزنویسه',
				'subtitle' => 'همراه پژوهشی از ایده تا دفاع',
				'features' => "ارائه خدمات پژوهشی استاندارد با راهنمایی تخصصی.\nمحرمانگی کامل اطلاعات و هویت پژوهشگر.\nمسیر شفاف از مشاوره تا تحویل.",
				'policy_points' => "محرمانگی اطلاعات پروژه و هویت پژوهشگر.\nشفافیت در زمان‌بندی و برآورد هزینه.\nپاسخ‌گویی منظم تا پایان همکاری.",
				'timeline' => "۱۳۹۸|شروع مشاوره تخصصی|آغاز فعالیت با تمرکز بر پایان‌نامه و پروپوزال\n۱۴۰۰|گسترش تحلیل آماری|افزودن خدمات SPSS و R\n۱۴۰۲|ابزارهای آنلاین|راه‌اندازی ماشین‌حساب‌های پژوهشی\n۱۴۰۴|تیم بین‌رشته‌ای|همکاری پژوهشگران چند حوزه",
			),
		),
		'team'               => array(
			'title'    => 'تیم پژوهشگران',
			'template' => 'page-team.php',
			'meta'     => array(
				'eyebrow'       => 'تخصص',
				'subtitle'      => 'پژوهشگران متخصص',
				'service_icon'  => 'fa-solid fa-user-group',
				'service_color' => 'icon-purple-soft',
				'cta_text'      => 'پیوستن به تیم',
				'cta_url'       => '/contact/',
			),
		),
		'contact'            => array(
			'title'    => 'تماس با ما',
			'template' => 'page-contact.php',
			'meta'     => array(
				'eyebrow'  => 'ارتباط',
				'subtitle' => 'مشاوره رایگان',
			),
		),
		'inquiry'            => array(
			'title'    => 'ثبت درخواست',
			'template' => 'page-contact.php',
			'meta'     => array(
				'eyebrow'  => 'سفارش',
				'subtitle' => 'موضوع را بفرستید؛ مسیر و برآورد اولیه را بررسی می‌کنیم',
			),
		),
		'privacy'            => array(
			'title'    => 'حریم خصوصی',
			'template' => 'page-privacy.php',
			'meta'     => array(
				'eyebrow'  => 'حقوق',
				'subtitle' => 'حریم خصوصی',
			),
		),
		'sitemap'            => array(
			'title'    => 'نقشه سایت',
			'template' => 'page-sitemap.php',
			'meta'     => array(
				'eyebrow'  => 'نقشه سایت',
				'subtitle' => 'دسترسی سریع',
			),
		),
		'downloads'          => array(
			'title'    => 'دانلودها',
			'template' => 'page-downloads.php',
			'meta'     => array(
				'eyebrow'  => 'منابع',
				'subtitle' => 'قالب‌ها و چک‌لیست‌های پژوهشی',
			),
		),
	);
}

/**
 * Seed pages.
 *
 * @param bool $replace_builder Overwrite existing builder JSON.
 * @return array{created:string[],skipped:string[],builder:array}
 */
function teznevise_seed_pages( $replace_builder = false ) {
	$created = array();
	$skipped = array();
	foreach ( teznevise_recommended_pages() as $slug => $cfg ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$skipped[] = $slug;
			if ( ! empty( $cfg['template'] ) ) {
				update_post_meta( $existing->ID, '_wp_page_template', $cfg['template'] );
			}
			if ( ! empty( $cfg['meta'] ) ) {
				foreach ( $cfg['meta'] as $key => $val ) {
					if ( '' === get_post_meta( $existing->ID, '_teznevise_' . $key, true ) ) {
						update_post_meta( $existing->ID, '_teznevise_' . $key, $val );
					}
				}
			}
			continue;
		}
		$page_id = wp_insert_post(
			array(
				'post_title'   => $cfg['title'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
				'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			$skipped[] = $slug;
			continue;
		}
		if ( ! empty( $cfg['template'] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $cfg['template'] );
		}
		if ( ! empty( $cfg['meta'] ) ) {
			foreach ( $cfg['meta'] as $key => $val ) {
				update_post_meta( $page_id, '_teznevise_' . $key, $val );
			}
		}
		$created[] = $slug;
	}

	$builder = function_exists( 'teznevise_builder_seed_all' )
		? teznevise_builder_seed_all( (bool) $replace_builder )
		: array();

	// Extracted source pages replace default-seed (or empty) sections via
	// provenance. Administrator-owned builder JSON is left untouched unless
	// $replace_builder is an explicit force-replace.
	$extracted = function_exists( 'teznevise_apply_extracted_to_pages' )
		? teznevise_apply_extracted_to_pages( (bool) $replace_builder, false )
		: array();

	return array(
		'created'   => $created,
		'skipped'   => $skipped,
		'builder'   => $builder,
		'extracted' => $extracted,
	);
}

/**
 * Admin menu.
 */
function teznevise_setup_admin_menu() {
	add_theme_page(
		__( 'راه‌اندازی تزنویسه', 'teznevise' ),
		__( 'راه‌اندازی تزنویسه', 'teznevise' ),
		'edit_theme_options',
		'teznevise-setup',
		'teznevise_setup_admin_page'
	);
}
add_action( 'admin_menu', 'teznevise_setup_admin_menu' );

/**
 * Admin page UI.
 */
function teznevise_setup_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$result = null;
	if ( isset( $_POST['teznevise_seed_pages'] ) && check_admin_referer( 'teznevise_seed_pages' ) ) {
		$replace = ! empty( $_POST['teznevise_replace_builder'] );
		$result  = teznevise_seed_pages( $replace );
	}
	$pages = teznevise_recommended_pages();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'راه‌اندازی تزنویسه', 'teznevise' ); ?></h1>
		<p><?php esc_html_e( 'صفحات پیشنهادی با قالب و فیلدهای پیش‌فرض ساخته می‌شوند. محتوای اصلی برگه‌های موجود (شورت‌کدها) به فیلدهای سفارشی صفحه‌ساز منتقل می‌شود؛ اسلاگ‌ها عوض نمی‌شوند.', 'teznevise' ); ?></p>
		<?php if ( $result ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php
			printf(
				esc_html__( 'صفحات — ایجاد: %1$d — رد/به‌روز: %2$d', 'teznevise' ),
				count( $result['created'] ),
				count( $result['skipped'] )
			);
			if ( ! empty( $result['builder'] ) ) {
				echo '<br>';
				printf(
					esc_html__( 'صفحه‌ساز HTML — ایجاد: %1$d — به‌روز: %2$d — رد: %3$d — بدون برگه: %4$d', 'teznevise' ),
					count( $result['builder']['created'] ?? array() ),
					count( $result['builder']['updated'] ?? array() ),
					count( $result['builder']['skipped'] ?? array() ),
					count( $result['builder']['missing'] ?? array() )
				);
			}
			if ( ! empty( $result['extracted'] ) ) {
				echo '<br>';
				printf(
					esc_html__( 'محتوای اصلی برگه‌ها — ایجاد: %1$d — به‌روز: %2$d — رد: %3$d', 'teznevise' ),
					(int) ( $result['extracted']['created'] ?? 0 ),
					(int) ( $result['extracted']['updated'] ?? 0 ),
					(int) ( $result['extracted']['skipped'] ?? 0 ) + (int) ( $result['extracted']['empty'] ?? 0 )
				);
			}
			?></p></div>
		<?php endif; ?>
		<table class="widefat striped" style="max-width:860px;margin:16px 0;">
			<thead>
				<tr>
					<th>slug</th>
					<th><?php esc_html_e( 'عنوان', 'teznevise' ); ?></th>
					<th><?php esc_html_e( 'قالب', 'teznevise' ); ?></th>
					<th><?php esc_html_e( 'وضعیت', 'teznevise' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $pages as $slug => $cfg ) :
				$exists = get_page_by_path( $slug );
				?>
				<tr>
					<td><code><?php echo esc_html( $slug ); ?></code></td>
					<td><?php echo esc_html( $cfg['title'] ); ?></td>
					<td><?php echo esc_html( $cfg['template'] ? $cfg['template'] : 'page.php' ); ?></td>
					<td><?php echo $exists ? 'موجود' : 'نیاز'; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<form method="post">
			<?php wp_nonce_field( 'teznevise_seed_pages' ); ?>
			<p>
				<label>
					<input type="checkbox" name="teznevise_replace_builder" value="1">
					<?php esc_html_e( 'بازنویسی بخش‌های صفحه‌ساز موجود (تغییرات دستی پاک می‌شود)', 'teznevise' ); ?>
				</label>
			</p>
			<button type="submit" name="teznevise_seed_pages" class="button button-primary button-hero">
				<?php esc_html_e( 'ایجاد / هم‌ترازسازی صفحات و بخش‌های صفحه‌ساز', 'teznevise' ); ?>
			</button>
		</form>
		<?php if ( function_exists( 'teznevise_render_promote_assets_section' ) ) {
			teznevise_render_promote_assets_section();
		} ?>

		<?php
		/**
		 * Shortcode → builder migration UI (inc/migration/shortcode-to-builder-migrator.php).
		 */
		do_action( 'teznevise_setup_after_seed' );
		?>

		<hr style="margin:28px 0;">
		<p><strong><?php esc_html_e( 'گام‌های بعدی:', 'teznevise' ); ?></strong>
			<?php esc_html_e( 'Customize + Menus + محتوای هر صفحه + لینک فایل‌های دانلود.', 'teznevise' ); ?></p>
	</div>
	<?php
}
