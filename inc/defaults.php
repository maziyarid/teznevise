<?php
/**
 * Default content for Customizer and page meta.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Homepage / theme defaults (Persian).
 *
 * @return array
 */
function teznevise_content_defaults() {
	return array(
		'phone'         => '09302822091',
		'phone_display' => '۰۹۳۰۲۸۲۲۰۹۱',
		'phone_intl'    => '+989302822091',
		'whatsapp'      => 'https://wa.me/989302822091',
		'telegram'      => 'https://t.me/Teznevise',
		'bale'          => 'https://ble.ir/teznevise',
		'email'         => 'teznevisan@gmail.com',
		'address'       => 'تهران، انقلاب، خیابان ۱۲ فروردین',
		'hours'         => 'شنبه تا پنجشنبه، ۹ تا ۲۱',

		'hero_eyebrow'   => 'همراهی علمی از ایده تا دفاع',
		'hero_title_1'   => 'از موضوع تا تحویل نهایی،',
		'hero_title_grad'=> 'پژوهش‌تان را حرفه‌ای‌تر',
		'hero_title_2'   => 'پیش ببرید.',
		'hero_text'      => 'تزنویسه برای پایان‌نامه، پروپوزال، پروژه دانشگاهی و تحلیل آماری یک مسیر منظم، خلاقانه و قابل اتکا می‌سازد؛ با پشتیبانی تخصصی، محرمانگی کامل و پاسخ‌گویی سریع.',
		'hero_btn_primary' => 'ثبت سفارش و شروع مشاوره',
		'hero_btn_primary_url' => '/inquiry/',
		'hero_btn_secondary' => 'مشاهده خدمات',
		'hero_point_1'   => 'مشاوره اولیه رایگان',
		'hero_point_2'   => 'متخصص هر رشته',
		'hero_point_3'   => 'پشتیبانی تا تحویل',

		'services_eyebrow' => 'خدمات پژوهشی تزنویسه',
		'services_title'   => 'هر مرحله از پروژه را با یک پل تخصصی جلو ببرید',
		'services_text'    => 'ساختار کلی صفحه به‌صورت پیوسته و Bridge طراحی شده تا کاربر از یک خدمت به خدمت بعدی، روان و هدفمند حرکت کند.',

		'svc1_title' => 'انجام پایان‌نامه',
		'svc1_text'  => 'از انتخاب موضوع تا نگارش فصل‌ها، تحلیل آماری و آمادگی دفاع برای ارشد و دکتری.',
		'svc1_url'   => '/service-thesis/',
		'svc1_icon'  => 'fa-solid fa-graduation-cap',
		'svc1_color' => 'icon-indigo',

		'svc2_title' => 'انجام پروپوزال',
		'svc2_text'  => 'بیان مسئله، مرور ادبیات، اهداف، فرضیه‌ها و روش‌شناسی با ساختار علمی و منابع به‌روز.',
		'svc2_url'   => '/service-proposal/',
		'svc2_icon'  => 'fa-solid fa-file-circle-check',
		'svc2_color' => 'icon-teal',

		'svc3_title' => 'تحلیل آماری',
		'svc3_text'  => 'تحلیل داده‌ها با SPSS، R، Python، LISREL، Matlab و AMOS همراه با تفسیر علمی نتایج.',
		'svc3_url'   => '/service-statistics/',
		'svc3_icon'  => 'fa-solid fa-chart-line',
		'svc3_color' => 'icon-cyan',

		'svc4_title' => 'ابزارهای آنلاین',
		'svc4_text'  => 'ماشین‌حساب‌های آماری رایگان برای آمار توصیفی، همبستگی و آزمون‌های پرکاربرد.',
		'svc4_url'   => '/tools/',
		'svc4_icon'  => 'fa-solid fa-calculator',
		'svc4_color' => 'icon-amber',

		'svc5_title' => 'مرکز دانش',
		'svc5_text'  => 'راهنماهای کاربردی درباره روش تحقیق، نگارش دانشگاهی، تحلیل آماری و ابزارهای پژوهشی.',
		'svc5_url'   => '/blog/',
		'svc5_icon'  => 'fa-regular fa-lightbulb',
		'svc5_color' => 'icon-danger-soft',

		'svc6_title' => 'تیم پژوهشگران',
		'svc6_text'  => 'همکاری با پژوهشگر متخصص متناسب با رشته و موضوع پروژه، از علوم انسانی تا مهندسی.',
		'svc6_url'   => '/team/',
		'svc6_icon'  => 'fa-solid fa-user-group',
		'svc6_color' => 'icon-purple-soft',

		'about_eyebrow' => 'درباره تزنویسه',
		'about_title'   => 'پژوهش خوب فقط تحویل فایل نیست.',
		'about_text'    => 'تزنویسه با تمرکز بر کیفیت علمی، شفافیت مسیر و پاسخ‌گویی، دانشجویان و پژوهشگران را از انتخاب موضوع تا دفاع همراهی می‌کند. تیم متخصص، محرمانگی کامل و پشتیبانی منظم بخشی از همین مسیر است.',
		'about_btn'     => 'درباره ما',
		'about_btn_url' => '/about/',
		'reason1_title' => 'محرمانگی کامل',
		'reason1_text'  => 'اطلاعات و فایل‌های پروژه با رویکرد محرمانه مدیریت می‌شوند.',
		'reason2_title' => 'روش‌مندی علمی',
		'reason2_text'  => 'ساختار پژوهش بر اساس متدولوژی استاندارد و منابع علمی به‌روز پیش می‌رود.',
		'reason3_title' => 'پاسخ‌گویی سریع',
		'reason3_text'  => 'درخواست اولیه شما در کوتاه‌ترین زمان بررسی و مسیر بعدی شفاف می‌شود.',
		'reason4_title' => 'خلاقیت آکادمیک',
		'reason4_text'  => 'تم بصری سایت با عناصر نموداری، ماتریسی و لکه‌های جوهر، حس آکادمی و پژوهش را تقویت می‌کند.',

		'steps_eyebrow' => 'از کجا شروع کنم؟',
		'steps_title'   => 'چهار قدم تا یک مسیر پژوهشی روشن',
		'steps_text'    => 'هر مرحله خروجی مشخص دارد؛ بنابراین همیشه می‌دانید پروژه در چه وضعیتی است و قدم بعدی چیست.',
		'step1_title'   => 'مشاوره رایگان',
		'step1_text'    => 'موضوع، نیاز، زمان و بودجه بررسی می‌شود و برآورد اولیه دریافت می‌کنید.',
		'step2_title'   => 'طرح و پروپوزال',
		'step2_text'    => 'ساختار پژوهش، پرسش‌ها، فرضیه‌ها و روش اجرا منسجم می‌شود.',
		'step3_title'   => 'اجرا و تحلیل',
		'step3_text'    => 'داده‌ها با روش مناسب تحلیل و نتایج به‌صورت علمی تفسیر می‌شوند.',
		'step4_title'   => 'نگارش و دفاع',
		'step4_text'    => 'فصل‌ها تکمیل، اصلاحات اعمال و برای ارائه نهایی آماده می‌شوید.',

		'articles_eyebrow' => 'تازه‌های مرکز دانش',
		'articles_title'   => 'مطالب جدید و کاربردی',
		'articles_text'    => 'به‌جای لینک‌های ساده با فلش، اقدام‌ها به‌صورت دکمه‌های واضح و مدرن طراحی شده‌اند تا تجربه کاربر حرفه‌ای‌تر باشد.',

		'cta_title' => 'پروژه پژوهشی‌ات را همین امروز شروع کن',
		'cta_text'  => 'موضوع را بفرست؛ کارشناسان تزنویسه مسیر، زمان و برآورد اولیه را با شما بررسی می‌کنند.',
		'cta_btn'   => 'درخواست مشاوره رایگان',
		'cta_btn_url' => '/inquiry/',
	);
}

/**
 * Get a theme_mod with default fallback.
 *
 * @param string $key Setting key without prefix.
 * @param string $default Optional override default.
 * @return string
 */
function teznevise_mod( $key, $default = null ) {
	$defaults = teznevise_content_defaults();
	if ( null === $default ) {
		$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
	return get_theme_mod( 'teznevise_' . $key, $default );
}

/**
 * Resolve relative internal URL to absolute.
 *
 * @param string $url Path or absolute URL.
 * @return string
 */
function teznevise_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' ) {
		return home_url( '/' );
	}
	if ( preg_match( '#^https?://#i', $url ) ) {
		return $url;
	}
	return home_url( $url );
}

/**
 * Get page custom field with default.
 *
 * @param string $key Meta key without _teznevise_ prefix.
 * @param int    $post_id Post ID.
 * @param mixed  $default Default.
 * @return mixed
 */
function teznevise_page_field( $key, $post_id = 0, $default = '' ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return $default;
	}
	$value = get_post_meta( $post_id, '_teznevise_' . $key, true );
	if ( $value === '' || $value === null || $value === false ) {
		return $default;
	}
	return $value;
}
