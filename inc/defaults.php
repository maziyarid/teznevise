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
		'services_text'    => 'از انتخاب موضوع تا دفاع، هر خدمت به خدمت بعدی وصل است تا مسیر پژوهش‌تان یکپارچه بماند.',

		'svc1_title' => 'انجام پایان‌نامه',
		'svc1_text'  => 'از انتخاب موضوع تا نگارش فصل‌ها، تحلیل آماری و آمادگی دفاع برای ارشد و دکتری.',
		'svc1_url'   => '/thesis/',
		'svc1_icon'  => 'fa-solid fa-graduation-cap',
		'svc1_color' => 'icon-indigo',

		'svc2_title' => 'انجام پروپوزال',
		'svc2_text'  => 'بیان مسئله، مرور ادبیات، اهداف، فرضیه‌ها و روش‌شناسی با ساختار علمی و منابع به‌روز.',
		'svc2_url'   => '/proposal/',
		'svc2_icon'  => 'fa-solid fa-file-circle-check',
		'svc2_color' => 'icon-teal',

		'svc3_title' => 'تحلیل آماری',
		'svc3_text'  => 'تحلیل داده‌ها با SPSS، R، Python، LISREL، Matlab و AMOS همراه با تفسیر علمی نتایج.',
		'svc3_url'   => '/statistics/',
		'svc3_icon'  => 'fa-solid fa-chart-line',
		'svc3_color' => 'icon-cyan',

		'svc4_title' => 'ابزارهای آنلاین',
		'svc4_text'  => 'ماشین‌حساب‌های آماری رایگان برای آمار توصیفی، همبستگی و آزمون‌های پرکاربرد.',
		'svc4_url'   => '/online-calculation-tools/',
		'svc4_icon'  => 'fa-solid fa-calculator',
		'svc4_color' => 'icon-amber',

		'svc5_title' => 'مرکز دانش',
		'svc5_text'  => 'راهنماهای کاربردی درباره روش تحقیق، نگارش دانشگاهی، تحلیل آماری و ابزارهای پژوهشی.',
		'svc5_url'   => '/blog/',
		'svc5_icon'  => 'fa-regular fa-lightbulb',
		'svc5_color' => 'icon-danger-soft',

		'svc6_title' => 'شبیه‌سازی',
		'svc6_text'  => 'مدل‌سازی عددی، شبیه‌سازی سیستم‌ها و تحلیل‌های مهندسی با MATLAB و Python.',
		'svc6_url'   => '/service-simulation/',
		'svc6_icon'  => 'fa-solid fa-microchip',
		'svc6_color' => 'icon-purple-soft',

		'svc7_title' => 'تحلیل کیفی',
		'svc7_text'  => 'تحلیل مضمون، گراندد تئوری و پدیدارشناسی با کدگذاری منظم و قابل دفاع.',
		'svc7_url'   => '/service-qualitative/',
		'svc7_icon'  => 'fa-solid fa-comments',
		'svc7_color' => 'icon-indigo',

		'svc8_title' => 'انجام پروژه دانشجویی',
		'svc8_text'  => 'پروژه‌های درسی، کارورزی و گزارش‌های دانشگاهی با ساختار علمی و تحویل مرحله‌ای.',
		'svc8_url'   => '/service-project/',
		'svc8_icon'  => 'fa-solid fa-folder-open',
		'svc8_color' => 'icon-teal',

		'svc9_title' => 'انجام مقاله',
		'svc9_text'  => 'نگارش، استخراج و آماده‌سازی مقاله علمی از پایان‌نامه برای مجلات داخلی و ISI.',
		'svc9_url'   => '/service-article/',
		'svc9_icon'  => 'fa-solid fa-newspaper',
		'svc9_color' => 'icon-cyan',

		'about_eyebrow' => 'درباره تزنویسه',
		'about_title'   => 'پژوهش خوب فقط تحویل فایل نیست.',
		'about_text'    => 'تزنویسه با تمرکز بر کیفیت علمی، شفافیت مسیر و پاسخ‌گویی، دانشجویان و پژوهشگران را از انتخاب موضوع تا دفاع همراهی می‌کند. تیم متخصص، محرمانگی کامل و پشتیبانی منظم بخشی از همین مسیر است.',
		'about_btn'     => 'درباره ما',
		'about_btn_url' => '/about-us/',
		'reason1_title' => 'محرمانگی کامل',
		'reason1_text'  => 'اطلاعات و فایل‌های پروژه با رویکرد محرمانه مدیریت می‌شوند.',
		'reason2_title' => 'روش‌مندی علمی',
		'reason2_text'  => 'ساختار پژوهش بر اساس متدولوژی استاندارد و منابع علمی به‌روز پیش می‌رود.',
		'reason3_title' => 'پاسخ‌گویی سریع',
		'reason3_text'  => 'درخواست اولیه شما در کوتاه‌ترین زمان بررسی و مسیر بعدی شفاف می‌شود.',
		'reason4_title' => 'خلاقیت آکادمیک',
		'reason4_text'  => 'ایده‌پژوهی و نگارش خلاق، در کنار دقت روش‌شناختی، خروجی قابل دفاع می‌سازد.',

		'steps_eyebrow' => 'از کجا شروع کنم؟',
		'steps_title'   => 'شش قدم تا یک مسیر پژوهشی روشن',
		'steps_text'    => 'هر مرحله خروجی مشخص دارد؛ بنابراین همیشه می‌دانید پروژه در چه وضعیتی است و قدم بعدی چیست.',
		'step1_title'   => 'مشاوره رایگان',
		'step1_text'    => 'موضوع، نیاز، زمان و بودجه بررسی می‌شود و برآورد اولیه دریافت می‌کنید.',
		'step2_title'   => 'طرح و پروپوزال',
		'step2_text'    => 'ساختار پژوهش، پرسش‌ها، فرضیه‌ها و روش اجرا منسجم می‌شود.',
		'step3_title'   => 'گردآوری داده',
		'step3_text'    => 'ابزار، نمونه و پروتکل اجرا مشخص می‌شود تا داده قابل‌اتکا باشد.',
		'step4_title'   => 'اجرا و تحلیل',
		'step4_text'    => 'داده‌ها با روش مناسب تحلیل و نتایج به‌صورت علمی تفسیر می‌شوند.',
		'step5_title'   => 'نگارش فصل‌ها',
		'step5_text'    => 'فصل‌ها تکمیل، انسجام منطقی حفظ و اصلاحات استاد اعمال می‌شود.',
		'step6_title'   => 'دفاع و تحویل',
		'step6_text'    => 'آمادگی جلسه دفاع، فایل نهایی و پشتیبانی تا تأیید دانشگاه.',

		'articles_eyebrow' => 'تازه‌های مرکز دانش',
		'articles_title'   => 'مطالب جدید و کاربردی',
		'articles_text'    => 'راهنماهای کوتاه برای انتخاب موضوع، ساختار فصل‌ها، تحلیل داده و آمادگی دفاع.',

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
