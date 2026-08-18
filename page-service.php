<?php
/**
 * Template Name: صفحه خدمت (Service)
 * Description: Service landing with page custom fields + content.
 *
 * Reproduces the static service page structure (hero + lead card, challenges,
 * field coverage, process showcase, content, CTA, SEO disclosure) for the
 * thesis / proposal / statistics / simulation service pages.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();
	$slug     = get_post_field( 'post_name', $post_id );

	/*
	 * Per-slug Persian default copy. Mirrors the static reference HTML so each
	 * service page renders correctly out-of-the-box; admins may still override
	 * the eyebrow / subtitle / features / CTA via page meta.
	 */
	$service_defaults = array(
		'service-thesis' => array(
			'eyebrow'      => 'خدمات تخصصی پایان‌نامه',
			'lead'         => 'از انتخاب موضوع و پروپوزال تا نگارش فصل‌ها، تحلیل آماری و آمادگی دفاع؛ مسیر پایان‌نامه را با ساختار روشن و همراهی پژوهشگر متخصص پیش ببرید.',
			'bullets'      => array( 'حفظ محرمانگی اطلاعات', 'پژوهشگر متناسب با رشته', 'مسیر و زمان‌بندی شفاف', 'امکان اعمال اصلاحات' ),
			'lead_title'   => 'درخواست مشاوره رایگان پایان‌نامه',
			'lead_sub'     => 'اطلاعات اولیه را وارد کنید تا موضوع و نیاز شما بررسی شود.',
			'lead_label'   => 'توضیح کوتاه درباره پروژه',
			'lead_ph'      => 'موضوع، مرحله فعلی و زمان موردنظر...',
			'ch_eyebrow'   => 'چالش‌های رایج',
			'ch_h2'        => 'مشکل را دقیق‌تر تعریف کنیم',
			'ch_p'         => 'وقتی مسئله روشن باشد، انتخاب مسیر مناسب برای پایان‌نامه بسیار ساده‌تر می‌شود.',
			'challenges'   => array(
				array( 'icon-danger-soft', 'fa-regular fa-clock', 'فشار زمان', 'ددلاین نزدیک است و فصل‌ها هنوز کامل نشده‌اند؟ مسیر پروژه را مرحله‌بندی و زمان‌بندی می‌کنیم.' ),
				array( 'icon-amber', 'fa-regular fa-lightbulb', 'انتخاب موضوع', 'برای انتخاب موضوعی نو، قابل اجرا و متناسب با گرایش و داده‌های در دسترس راهنمایی می‌شوید.' ),
				array( 'icon-cyan', 'fa-solid fa-chart-simple', 'تحلیل آماری', 'انتخاب آزمون، اجرای تحلیل با نرم‌افزار مناسب و تفسیر علمی نتایج یکپارچه انجام می‌شود.' ),
				array( 'icon-indigo', 'fa-regular fa-pen-to-square', 'اصلاحات استاد', 'بازخوردها و اصلاحات در چارچوب پروژه ثبت و مرحله‌به‌مرحله اعمال می‌شوند.' ),
			),
			'pr_eyebrow'   => 'فرآیند همکاری',
			'pr_h2'        => 'چگونه مسیر پایان‌نامه را پیش می‌بریم؟',
			'pr_lead'      => 'سه قدم مشخص؛ در هر لحظه می‌دانید کجای مسیر هستید و خروجی بعدی چیست.',
			'steps'        => array(
				array( 'قدم ۱', '<strong>مشاوره و نیازسنجی رایگان:</strong> از طریق فرم ثبت سفارش و یا سایر پل‌های ارتباطی با ما تماس بگیرید و پس از نیازسنجی، پروژه شما در حالت انجام قرار خواهد گرفت.' ),
				array( 'قدم ۲', '<strong>اجرا و پیگیری پروژه:</strong> نگارش، تحلیل یا اصلاحات با هماهنگی شما پیش می‌رود و وضعیت پروژه در هر مرحله شفاف می‌ماند.' ),
				array( 'قدم ۳', '<strong>تحویل و آمادگی دفاع:</strong> خروجی نهایی تحویل می‌شود و برای ارائه و پاسخ به سوالات جلسه دفاع آماده می‌شوید.' ),
			),
			'pr_cta'       => 'ثبت درخواست پروژه',
			'panels'       => array(
				array( 'ثبت نیازمندی‌ها', 'موضوع، رشته و ددلاین را مشخص کنید.', 'مشاوره اولیه رایگان — مسیر پیشنهادی آماده است.', 'در حال بررسی', '' ),
				array( 'پیگیری پیشرفت', 'پیش‌نویس فصل اول آماده شد.', 'بازخورد شما ثبت شد؛ اصلاحات اعمال می‌شود.', 'در حال انجام', 'tz-progress' ),
				array( 'تحویل نهایی', 'فایل نهایی و نکات دفاع آماده است.', 'می‌توانید دانلود و برای جلسه دفاع مرور کنید.', 'تکمیل شده', 'tz-done' ),
			),
			'cta_h2'       => 'همین امروز مسیر پایان‌نامه‌ات را روشن کن',
			'cta_p'        => 'موضوع و مرحله فعلی را بفرست؛ مسیر و برآورد اولیه را بررسی می‌کنیم.',
			'seo_h2'       => 'راهنمای انتخاب خدمات پژوهشی تزنویسه',
			'seo_reading'  => array(
				'تزنویسه برای دانشجویان و پژوهشگرانی طراحی شده است که می‌خواهند مسیر پایان‌نامه، پروپوزال یا تحلیل آماری خود را با نظم بیشتر و تصمیم‌های علمی روشن‌تر پیش ببرند. کیفیت مراحل بعدی به هماهنگی میان مسئله پژوهش، اهداف، روش تحقیق، ابزار گردآوری داده و شیوه تحلیل وابسته است. به همین دلیل مشاوره اولیه کمک می‌کند قبل از شروع نگارش یا تحلیل، وضعیت واقعی پروژه مشخص شود و کاربر بداند از کدام مرحله باید ادامه دهد. این رویکرد برای پایان‌نامه کارشناسی ارشد، رساله دکتری، پروژه‌های دانشگاهی و پژوهش‌های کاربردی قابل استفاده است و باعث می‌شود مسیر کار از ابتدا قابل پیگیری باشد.',
				'در بخش انجام پایان‌نامه، نیاز هر پروژه با توجه به رشته، گرایش، مقطع تحصیلی، زمان در دسترس و مرحله فعلی بررسی می‌شود. ممکن است یک دانشجو فقط برای انتخاب موضوع یا تدوین پروپوزال به راهنمایی نیاز داشته باشد و دانشجوی دیگری برای تحلیل داده، نگارش فصل چهارم یا آماده‌سازی دفاع درخواست مشاوره کند. ساختار خدمات به‌صورت مرحله‌ای در نظر گرفته شده تا هر بخش مستقل باشد اما ارتباط منطقی خود را با مراحل قبل و بعد حفظ کند. هدف این است که متن نهایی، روش تحقیق و نتایج آماری با پرسش‌ها و فرضیه‌های پژوهش هماهنگ باشند و اصلاحات استاد راهنما نیز بدون برهم‌زدن انسجام کلی پروژه اعمال شوند.',
				'برای تحلیل آماری نیز انتخاب نرم‌افزار به‌تنهایی تعیین‌کننده نیست. SPSS، Matlab، R، Python، AMOS یا سایر ابزارها زمانی نتیجه قابل دفاع ایجاد می‌کنند که روش تحلیل متناسب با نوع داده، مقیاس متغیرها و سوال پژوهش انتخاب شده باشد. پیش از اجرای آزمون‌ها، کیفیت داده‌ها، مقادیر گمشده، پیش‌فرض‌های آماری و ساختار فایل بررسی می‌شود و سپس خروجی‌ها در ارتباط با مسئله پژوهش تفسیر می‌شوند. این فرایند به پژوهشگر کمک می‌کند به‌جای دریافت مجموعه‌ای از جدول‌ها و اعداد، منطق نتایج را درک کند و بتواند آن‌ها را در فصل نتایج و جلسه دفاع توضیح دهد.',
			),
			'seo_more'     => array(
				'در بخش پروپوزال، تمرکز اصلی روی ایجاد ارتباط میان عنوان، بیان مسئله، پیشینه، اهداف، سوال‌ها یا فرضیه‌ها و روش اجرا است. یک پروپوزال خوب باید علاوه بر رعایت قالب دانشگاه، نشان دهد پژوهش چرا اهمیت دارد، چه شکافی را بررسی می‌کند و داده‌های مورد نیاز چگونه جمع‌آوری و تحلیل خواهند شد. اگر این ارتباط از ابتدا شفاف باشد، احتمال بازنویسی‌های سنگین در مراحل بعد کمتر می‌شود و مسیر نگارش فصل‌های پایان‌نامه نیز منظم‌تر خواهد بود.',
				'برای شروع همکاری لازم نیست همه فایل‌ها کامل باشند. رشته و گرایش، مقطع تحصیلی، موضوع یا ایده اولیه، مرحله فعلی پروژه، فایل‌های موجود و زمان تقریبی مورد نیاز اطلاعاتی هستند که بررسی اولیه را دقیق‌تر می‌کنند. پس از این بررسی می‌توان مشخص کرد کدام خدمت برای پروژه مناسب‌تر است و چه خروجی‌هایی باید در هر مرحله آماده شوند. حفظ محرمانگی فایل‌ها و اطلاعات پروژه نیز در تمام مراحل اهمیت دارد و ارتباط با پژوهشگر باید به شکلی باشد که کاربر بتواند سوال‌های خود را روشن مطرح کند و از وضعیت پروژه اطلاع داشته باشد.',
				'این بخش متنی برای پاسخ‌دادن به سوال‌های رایج کاربران و توضیح دقیق‌تر خدمات در صفحه اصلی قرار گرفته است. در نسخه وردپرس می‌توان همین محتوا را به‌صورت یک بخش قابل ویرایش مدیریت کرد و بر اساس نیاز سئو، موضوعات مرتبط با انجام پایان‌نامه، انجام پروپوزال، تحلیل آماری، پروژه دانشگاهی، SPSS و Matlab را بدون تغییر در طراحی صفحه توسعه داد.',
			),
		),
		'service-proposal' => array(
			'eyebrow'      => 'خدمات تخصصی پروپوزال',
			'lead'         => 'از بیان مسئله و مرور ادبیات تا اهداف، سوال‌ها، فرضیه‌ها و روش‌شناسی؛ پروپوزال را با ساختار روشن و قابل دفاع آماده کنید.',
			'bullets'      => array( 'حفظ محرمانگی اطلاعات', 'پژوهشگر متناسب با رشته', 'مسیر و زمان‌بندی شفاف', 'امکان اعمال اصلاحات' ),
			'lead_title'   => 'درخواست مشاوره رایگان پروپوزال',
			'lead_sub'     => 'اطلاعات اولیه را وارد کنید تا موضوع و چارچوب پروپوزال بررسی شود.',
			'lead_label'   => 'توضیح کوتاه درباره موضوع پروپوزال',
			'lead_ph'      => 'موضوع، الزامات دانشگاه و زمان موردنظر...',
			'ch_eyebrow'   => 'چالش‌های رایج پروپوزال',
			'ch_h2'        => 'مسئله پروپوزال را دقیق‌تر تعریف کنیم',
			'ch_p'         => 'وقتی مسئله پژوهش روشن باشد، تدوین پروپوزال منسجم و قابل تصویب بسیار ساده‌تر می‌شود.',
			'challenges'   => array(
				array( 'icon-danger-soft', 'fa-regular fa-clock', 'کمبود زمان', 'ددلاین تصویب نزدیک است؟ ساختار پروپوزال را مرحله‌بندی و زمان‌بندی می‌کنیم.' ),
				array( 'icon-amber', 'fa-regular fa-lightbulb', 'بیان مسئله', 'بیان مسئله، اهمیت پژوهش و شکاف علمی به‌صورت شفاف و علمی تدوین می‌شود.' ),
				array( 'icon-cyan', 'fa-solid fa-chart-simple', 'روش‌شناسی', 'اهداف، سوال‌ها یا فرضیه‌ها و روش اجرا متناسب با موضوع و داده در دسترس طراحی می‌شود.' ),
				array( 'icon-indigo', 'fa-regular fa-pen-to-square', 'اصلاحات داوری', 'بازخورد استاد و کمیته در چارچوب پروپوزال ثبت و مرحله‌به‌مرحله اعمال می‌شود.' ),
			),
			'pr_eyebrow'   => 'فرآیند همکاری',
			'pr_h2'        => 'چگونه مسیر پروپوزال را پیش می‌بریم؟',
			'pr_lead'      => 'سه قدم مشخص؛ از ایده اولیه تا نسخه قابل ارسال برای تصویب.',
			'steps'        => array(
				array( 'قدم ۱', '<strong>مشاوره و نیازسنجی:</strong> موضوع، مقطع و الزامات دانشگاه بررسی می‌شود و چارچوب اولیه پروپوزال پیشنهاد می‌گردد.' ),
				array( 'قدم ۲', '<strong>تدوین و تکمیل:</strong> بیان مسئله، پیشینه، اهداف و روش‌شناسی با هماهنگی شما تکمیل و وضعیت شفاف می‌ماند.' ),
				array( 'قدم ۳', '<strong>تحویل و آمادگی تصویب:</strong> نسخه نهایی پروپوزال تحویل می‌شود و برای ارسال و پاسخ به اصلاحات آماده می‌شوید.' ),
			),
			'pr_cta'       => 'ثبت درخواست پروپوزال',
			'panels'       => array(
				array( 'ثبت ایده و نیاز', 'موضوع، مقطع و الزامات دانشگاه را مشخص کنید.', 'چارچوب اولیه پروپوزال پیشنهاد شد.', 'در حال بررسی', '' ),
				array( 'تدوین پروپوزال', 'پیش‌نویس بیان مسئله و اهداف آماده شد.', 'بازخورد شما ثبت شد؛ اصلاحات اعمال می‌شود.', 'در حال انجام', 'tz-progress' ),
				array( 'نسخه نهایی', 'فایل نهایی پروپوزال آماده ارسال است.', 'می‌توانید برای تصویب و کمیته ارسال کنید.', 'تکمیل شده', 'tz-done' ),
			),
			'cta_h2'       => 'همین امروز مسیر پروپوزال‌ات را روشن کن',
			'cta_p'        => 'موضوع و الزامات دانشگاه را بفرست؛ مسیر و برآورد اولیه را بررسی می‌کنیم.',
			'seo_h2'       => 'راهنمای انتخاب خدمات پژوهشی تزنویسه',
			'seo_reading'  => array(
				'تزنویسه برای دانشجویان و پژوهشگرانی طراحی شده است که می‌خواهند مسیر پایان‌نامه، پروپوزال یا تحلیل آماری خود را با نظم بیشتر و تصمیم‌های علمی روشن‌تر پیش ببرند. کیفیت مراحل بعدی به هماهنگی میان مسئله پژوهش، اهداف، روش تحقیق، ابزار گردآوری داده و شیوه تحلیل وابسته است.',
				'در بخش پروپوزال، تمرکز اصلی روی ایجاد ارتباط میان عنوان، بیان مسئله، پیشینه، اهداف، سوال‌ها یا فرضیه‌ها و روش اجرا است. یک پروپوزال خوب باید علاوه بر رعایت قالب دانشگاه، نشان دهد پژوهش چرا اهمیت دارد و چه شکافی را بررسی می‌کند.',
			),
			'seo_more'     => array(
				'اگر این ارتباط از ابتدا شفاف باشد، احتمال بازنویسی‌های سنگین در مراحل بعد کمتر می‌شود و مسیر نگارش فصل‌های پایان‌نامه نیز منظم‌تر خواهد بود.',
				'برای شروع همکاری لازم نیست همه فایل‌ها کامل باشند. رشته و گرایش، مقطع تحصیلی، موضوع یا ایده اولیه و زمان تقریبی بررسی اولیه را دقیق‌تر می‌کنند.',
			),
		),
		'service-statistics' => array(
			'eyebrow'      => 'خدمات تحلیل آماری',
			'lead'         => 'از آماده‌سازی داده و انتخاب آزمون تا اجرای تحلیل، مدل‌سازی و تفسیر علمی نتایج؛ مسیر آمار پژوهش را شفاف و قابل دفاع پیش ببرید.',
			'bullets'      => array( 'محرمانگی کامل داده‌ها', 'تحلیل‌گر متناسب با روش', 'گزارش خروجی شفاف', 'امکان بازنگری نتایج' ),
			'lead_title'   => 'درخواست مشاوره رایگان تحلیل آماری',
			'lead_sub'     => 'اطلاعات اولیه پروژه آماری را وارد کنید تا مسیر تحلیل بررسی شود.',
			'lead_label'   => 'توضیح کوتاه درباره داده و هدف تحلیل',
			'lead_ph'      => 'نوع داده، نرم‌افزار موردنظر و ددلاین...',
			'ch_eyebrow'   => 'چالش‌های رایج آماری',
			'ch_h2'        => 'مسئله آماری را دقیق‌تر تعریف کنیم',
			'ch_p'         => 'وقتی نوع داده، سوال پژوهش و هدف تحلیل روشن باشد، انتخاب آزمون و نرم‌افزار بسیار دقیق‌تر می‌شود.',
			'challenges'   => array(
				array( 'icon-danger-soft', 'fa-regular fa-clock', 'ددلاین تحلیل', 'زمان تحویل نزدیک است و خروجی آماری هنوز آماده نیست؟ مسیر تحلیل را مرحله‌بندی و زمان‌بندی می‌کنیم.' ),
				array( 'icon-amber', 'fa-regular fa-lightbulb', 'انتخاب آزمون', 'آزمون مناسب با نوع داده، مقیاس متغیرها و سوال پژوهش انتخاب و توجیه می‌شود.' ),
				array( 'icon-cyan', 'fa-solid fa-chart-simple', 'اجرای نرم‌افزار', 'تحلیل در SPSS، AMOS، R، Python یا SmartPLS با گزارش خروجی قابل دفاع اجرا می‌شود.' ),
				array( 'icon-indigo', 'fa-regular fa-pen-to-square', 'تفسیر نتایج', 'خروجی‌ها به‌صورت علمی تفسیر می‌شوند تا در فصل نتایج و جلسه دفاع قابل توضیح باشند.' ),
			),
			'pr_eyebrow'   => 'فرآیند همکاری',
			'pr_h2'        => 'چگونه مسیر تحلیل آماری را پیش می‌بریم؟',
			'pr_lead'      => 'سه قدم مشخص؛ از دریافت فایل تا تفسیر نتایج، مسیر همیشه شفاف است.',
			'steps'        => array(
				array( 'قدم ۱', '<strong>مشاوره و بررسی داده:</strong> فایل داده، سوال‌ها و هدف تحلیل بررسی می‌شود و مسیر آزمون و نرم‌افزار پیشنهاد می‌گردد.' ),
				array( 'قدم ۲', '<strong>اجرای تحلیل:</strong> آزمون‌ها اجرا می‌شوند، خروجی‌ها کنترل می‌شوند و وضعیت پروژه در هر مرحله شفاف می‌ماند.' ),
				array( 'قدم ۳', '<strong>تفسیر و تحویل:</strong> خروجی نهایی همراه با تفسیر علمی تحویل می‌شود تا برای فصل نتایج و دفاع آماده باشید.' ),
			),
			'pr_cta'       => 'ثبت درخواست تحلیل',
			'panels'       => array(
				array( 'بررسی فایل داده', 'فایل داده و سوال پژوهش را ارسال کنید.', 'مسیر آزمون و نرم‌افزار پیشنهادی آماده است.', 'در حال بررسی', '' ),
				array( 'اجرای تحلیل', 'خروجی آزمون‌های اصلی آماده شد.', 'کنترل پیش‌فرض‌ها انجام شد؛ گزارش تکمیل می‌شود.', 'در حال انجام', 'tz-progress' ),
				array( 'تفسیر و تحویل', 'خروجی نهایی و تفسیر علمی آماده است.', 'می‌توانید در فصل نتایج و دفاع استفاده کنید.', 'تکمیل شده', 'tz-done' ),
			),
			'cta_h2'       => 'همین امروز مسیر تحلیل آماری‌ات را روشن کن',
			'cta_p'        => 'فایل داده و سوال پژوهش را بفرست؛ مسیر آزمون و برآورد اولیه را بررسی می‌کنیم.',
			'seo_h2'       => 'راهنمای انتخاب خدمات پژوهشی تزنویسه',
			'seo_reading'  => array(
				'تزنویسه برای دانشجویان و پژوهشگرانی طراحی شده است که می‌خواهند مسیر پایان‌نامه، پروپوزال یا تحلیل آماری خود را با نظم بیشتر و تصمیم‌های علمی روشن‌تر پیش ببرند. کیفیت مراحل بعدی به هماهنگی میان مسئله پژوهش، اهداف، روش تحقیق، ابزار گردآوری داده و شیوه تحلیل وابسته است. به همین دلیل مشاوره اولیه کمک می‌کند قبل از شروع نگارش یا تحلیل، وضعیت واقعی پروژه مشخص شود و کاربر بداند از کدام مرحله باید ادامه دهد.',
				'برای تحلیل آماری نیز انتخاب نرم‌افزار به‌تنهایی تعیین‌کننده نیست. SPSS، R، Python، AMOS یا سایر ابزارها زمانی نتیجه قابل دفاع ایجاد می‌کنند که روش تحلیل متناسب با نوع داده و سوال پژوهش انتخاب شده باشد.',
			),
			'seo_more'     => array(
				'پیش از اجرای آزمون‌ها، کیفیت داده‌ها، مقادیر گمشده، پیش‌فرض‌های آماری و ساختار فایل بررسی می‌شود و سپس خروجی‌ها در ارتباط با مسئله پژوهش تفسیر می‌شوند.',
				'برای شروع همکاری لازم نیست همه فایل‌ها کامل باشند. رشته، گرایش، مقطع، فایل داده و زمان تقریبی بررسی اولیه را دقیق‌تر می‌کنند.',
			),
		),
		'service-simulation' => array(
			'eyebrow'      => 'خدمات شبیه‌سازی',
			'lead'         => 'از تعریف مسئله و ساخت مدل تا اجرای شبیه‌سازی و تفسیر خروجی‌ها؛ مسیر پروژه شبیه‌سازی را شفاف و قابل دفاع پیش ببرید.',
			'bullets'      => array( 'محرمانگی کامل پروژه', 'متخصص متناسب با حوزه', 'گزارش و خروجی شفاف', 'امکان بازنگری مدل' ),
			'lead_title'   => 'درخواست مشاوره رایگان شبیه‌سازی',
			'lead_sub'     => 'اطلاعات اولیه پروژه شبیه‌سازی را وارد کنید تا مسیر مدل‌سازی بررسی شود.',
			'lead_label'   => 'توضیح کوتاه درباره سیستم و هدف شبیه‌سازی',
			'lead_ph'      => 'نوع سیستم، نرم‌افزار موردنظر و ددلاین...',
			'ch_eyebrow'   => 'چالش‌های رایج شبیه‌سازی',
			'ch_h2'        => 'مسئله شبیه‌سازی را دقیق‌تر تعریف کنیم',
			'ch_p'         => 'وقتی هدف مدل، دامنه سیستم و معیارهای خروجی روشن باشد، انتخاب نرم‌افزار و روش شبیه‌سازی دقیق‌تر می‌شود.',
			'challenges'   => array(
				array( 'icon-danger-soft', 'fa-regular fa-clock', 'ددلاین پروژه', 'زمان تحویل نزدیک است و مدل هنوز کامل نیست؟ مسیر شبیه‌سازی را مرحله‌بندی و زمان‌بندی می‌کنیم.' ),
				array( 'icon-amber', 'fa-regular fa-lightbulb', 'انتخاب نرم‌افزار', 'نرم‌افزار مناسب با نوع سیستم، فیزیک مسئله و خروجی موردنیاز انتخاب و توجیه می‌شود.' ),
				array( 'icon-cyan', 'fa-solid fa-chart-simple', 'اجرای مدل', 'مدل در نرم‌افزار تخصصی اجرا، اعتبارسنجی و با گزارش خروجی قابل دفاع ارائه می‌شود.' ),
				array( 'icon-indigo', 'fa-regular fa-pen-to-square', 'تفسیر خروجی', 'خروجی شبیه‌سازی تفسیر می‌شود تا در گزارش فنی، مقاله یا دفاع قابل توضیح باشد.' ),
			),
			'pr_eyebrow'   => 'فرآیند همکاری',
			'pr_h2'        => 'چگونه مسیر شبیه‌سازی را پیش می‌بریم؟',
			'pr_lead'      => 'سه قدم مشخص؛ از تعریف مسئله تا تحویل مدل و گزارش، مسیر همیشه شفاف است.',
			'steps'        => array(
				array( 'قدم ۱', '<strong>مشاوره و تعریف مسئله:</strong> هدف مدل، مرز سیستم و خروجی‌های موردنیاز بررسی می‌شود و مسیر نرم‌افزار پیشنهاد می‌گردد.' ),
				array( 'قدم ۲', '<strong>ساخت و اجرای مدل:</strong> مدل ساخته و اجرا می‌شود، اعتبارسنجی انجام می‌شود و وضعیت پروژه شفاف می‌ماند.' ),
				array( 'قدم ۳', '<strong>گزارش و تحویل:</strong> فایل مدل و گزارش خروجی تحویل می‌شود تا برای ارائه فنی یا دفاع آماده باشید.' ),
			),
			'pr_cta'       => 'ثبت درخواست شبیه‌سازی',
			'panels'       => array(
				array( 'تعریف مسئله', 'هدف مدل و دامنه سیستم را مشخص کنید.', 'مسیر نرم‌افزار و ساختار مدل پیشنهاد شد.', 'در حال بررسی', '' ),
				array( 'اجرای مدل', 'اجرای اولیه مدل انجام شد.', 'اعتبارسنجی انجام شد؛ گزارش تکمیل می‌شود.', 'در حال انجام', 'tz-progress' ),
				array( 'گزارش و تحویل', 'فایل مدل و گزارش خروجی آماده است.', 'می‌توانید در گزارش فنی یا دفاع استفاده کنید.', 'تکمیل شده', 'tz-done' ),
			),
			'cta_h2'       => 'همین امروز مسیر شبیه‌سازی‌ات را روشن کن',
			'cta_p'        => 'توضیح سیستم و هدف مدل را بفرست؛ مسیر نرم‌افزار و برآورد اولیه را بررسی می‌کنیم.',
			'seo_h2'       => 'راهنمای انتخاب خدمات پژوهشی تزنویسه',
			'seo_reading'  => array(
				'تزنویسه برای دانشجویان و پژوهشگرانی طراحی شده است که می‌خواهند مسیر پایان‌نامه، پروپوزال یا تحلیل آماری خود را با نظم بیشتر و تصمیم‌های علمی روشن‌تر پیش ببرند.',
				'برای شبیه‌سازی نیز انتخاب نرم‌افزار به‌تنهایی تعیین‌کننده نیست. نوع سیستم، فیزیک مسئله و خروجی موردنیاز مسیر مدل‌سازی را مشخص می‌کند.',
			),
			'seo_more'     => array(
				'از تعریف مسئله تا ساخت مدل، اجرا، اعتبارسنجی و گزارش، مسیر پروژه باید شفاف و قابل پیگیری باشد.',
				'برای شروع همکاری، توضیح سیستم، رشته، مقطع و ددلاین بررسی اولیه را دقیق‌تر می‌کند.',
			),
		),
	);

	if ( ! isset( $service_defaults[ $slug ] ) ) {
		$slug = 'service-thesis';
	}
	$d = $service_defaults[ $slug ];

	// Shared field-coverage cards (identical across service variants).
	$field_cards = array(
		array( 'icon-indigo', 'fa-solid fa-briefcase', 'علوم مدیریت', 'مدیریت بازرگانی، صنعتی، مالی، بازاریابی، منابع انسانی و سیستم‌های اطلاعاتی.' ),
		array( 'icon-teal', 'fa-solid fa-coins', 'حسابداری و مالی', 'حسابداری مالی، مدیریت مالی، حسابرسی، مالیات و بانکداری.' ),
		array( 'icon-cyan', 'fa-solid fa-scale-balanced', 'حقوق', 'حقوق خصوصی، عمومی، تجاری، کیفری و موضوعات میان‌رشته‌ای.' ),
		array( 'icon-amber', 'fa-solid fa-brain', 'روان‌شناسی', 'روان‌شناسی بالینی، عمومی، صنعتی، تربیتی و رشد.' ),
		array( 'icon-danger-soft', 'fa-solid fa-heart-pulse', 'علوم پزشکی', 'پرستاری، مامایی، بهداشت عمومی، اپیدمیولوژی و علوم آزمایشگاهی.' ),
		array( 'icon-purple-soft', 'fa-solid fa-gears', 'مهندسی', 'مهندسی صنایع، کامپیوتر، عمران، مکانیک، برق و شیمی.' ),
	);

	// Meta-driven overrides (with sensible defaults baked in).
	$eyebrow    = teznevise_page_field( 'eyebrow', $post_id, $d['eyebrow'] );
	$subtitle   = teznevise_page_field( 'subtitle', $post_id, $d['lead'] );
	$hide_title = (bool) teznevise_page_field( 'hide_title', $post_id, false );
	$features   = teznevise_page_field( 'features', $post_id, '' );
	$cta_text   = teznevise_page_field( 'cta_text', $post_id, __( 'شروع مشاوره رایگان', 'teznevise' ) );
	$cta_url    = teznevise_page_field( 'cta_url', $post_id, '/inquiry/' );

	// Hero bullets: page-meta features (one per line) override default bullets.
	if ( $features ) {
		$bullets = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $features ) ) );
	} else {
		$bullets = $d['bullets'];
	}

	$inquiry_url = teznevise_url( '/inquiry/' );
	?>

<section class="service-hero service-hero-align" data-test="service-page">
	<div class="container">
		<div class="service-hero-grid service-hero-grid-align">
			<div>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php if ( ! $hide_title ) : ?>
					<h1><?php the_title(); ?></h1>
				<?php endif; ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
				<div class="hero-actions">
					<a class="btn-tz btn-primary-tz btn-lg-tz" href="#consult"><?php esc_html_e( 'مشاوره رایگان', 'teznevise' ); ?></a>
					<a class="btn-tz btn-light-tz btn-lg-tz" href="#process"><?php esc_html_e( 'مراحل انجام کار', 'teznevise' ); ?></a>
				</div>
				<div class="service-bullets">
					<?php foreach ( $bullets as $bullet ) : ?>
						<div><i class="fa-regular fa-circle-check" aria-hidden="true"></i> <?php echo esc_html( $bullet ); ?></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="lead-card" id="consult">
				<div class="lead-card-head">
					<h3><?php echo esc_html( $d['lead_title'] ); ?></h3>
					<p><?php echo esc_html( $d['lead_sub'] ); ?></p>
				</div>
				<form>
					<div class="form-grid">
						<div class="field">
							<label><?php esc_html_e( 'نام و نام خانوادگی', 'teznevise' ); ?></label>
							<input placeholder="<?php esc_attr_e( 'نام شما', 'teznevise' ); ?>" type="text" />
						</div>
						<div class="field">
							<label><?php esc_html_e( 'شماره تماس', 'teznevise' ); ?></label>
							<input placeholder="09xxxxxxxxx" type="tel" />
						</div>
						<div class="field">
							<label><?php esc_html_e( 'مقطع', 'teznevise' ); ?></label>
							<select>
								<option><?php esc_html_e( 'کارشناسی ارشد', 'teznevise' ); ?></option>
								<option><?php esc_html_e( 'دکتری', 'teznevise' ); ?></option>
							</select>
						</div>
						<div class="field">
							<label><?php esc_html_e( 'رشته / گرایش', 'teznevise' ); ?></label>
							<input placeholder="<?php esc_attr_e( 'مثلاً مدیریت بازرگانی', 'teznevise' ); ?>" type="text" />
						</div>
						<div class="field full">
							<label><?php echo esc_html( $d['lead_label'] ); ?></label>
							<textarea placeholder="<?php echo esc_attr( $d['lead_ph'] ); ?>"></textarea>
						</div>
					</div>
					<button class="btn-tz btn-primary-tz" type="button"><?php esc_html_e( 'ارسال درخواست مشاوره', 'teznevise' ); ?></button>
					<p class="privacy-note"><?php esc_html_e( 'اطلاعات شما برای بررسی درخواست استفاده می‌شود و محرمانه خواهد ماند.', 'teznevise' ); ?></p>
				</form>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head center">
			<span class="eyebrow"><?php echo esc_html( $d['ch_eyebrow'] ); ?></span>
			<h2><?php echo esc_html( $d['ch_h2'] ); ?></h2>
			<p><?php echo esc_html( $d['ch_p'] ); ?></p>
		</div>
		<div class="challenge-grid challenge-grid-centered">
			<?php foreach ( $d['challenges'] as $challenge ) : ?>
				<article class="challenge challenge-centered">
					<span class="num num-icon <?php echo esc_attr( $challenge[0] ); ?>"><i class="<?php echo esc_attr( $challenge[1] ); ?>" aria-hidden="true"></i></span>
					<h3><?php echo esc_html( $challenge[2] ); ?></h3>
					<p><?php echo esc_html( $challenge[3] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section bg-soft">
	<div class="container">
		<div class="section-head center">
			<span class="eyebrow"><?php esc_html_e( 'حوزه‌های پوشش', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'متناسب با رشته و نوع پروژه', 'teznevise' ); ?></h2>
			<p><?php esc_html_e( 'ساختار کار و پژوهشگر پروژه با توجه به حوزه تخصصی و نیاز واقعی شما انتخاب می‌شود.', 'teznevise' ); ?></p>
		</div>
		<div class="services-grid field-as-services" data-reveal-stagger>
			<?php foreach ( $field_cards as $card ) : ?>
				<article class="service-card">
					<div class="icon-box <?php echo esc_attr( $card[0] ); ?>"><i class="<?php echo esc_attr( $card[1] ); ?>" aria-hidden="true"></i></div>
					<h3><?php echo esc_html( $card[2] ); ?></h3>
					<p><?php echo esc_html( $card[3] ); ?></p>
					<a class="link-arrow" href="<?php echo esc_url( $inquiry_url ); ?>"><?php esc_html_e( 'ادامه مطلب', 'teznevise' ); ?></a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" id="process">
	<div class="container">
		<div class="tz-process-showcase" data-process-showcase>
			<div class="tz-process-steps">
				<span class="eyebrow"><?php echo esc_html( $d['pr_eyebrow'] ); ?></span>
				<h2><?php echo esc_html( $d['pr_h2'] ); ?></h2>
				<p class="tz-process-lead"><?php echo esc_html( $d['pr_lead'] ); ?></p>
				<div class="tz-steps-list" role="tablist">
					<?php foreach ( $d['steps'] as $idx => $step ) : ?>
						<button type="button" class="tz-step <?php echo 0 === $idx ? 'is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $idx ? 'true' : 'false'; ?>" data-step="<?php echo esc_attr( $idx ); ?>">
							<span class="tz-step-label"><?php echo esc_html( $step[0] ); ?></span>
							<p class="tz-step-line"><?php echo wp_kses_post( $step[1] ); ?></p>
						</button>
					<?php endforeach; ?>
				</div>
				<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( $inquiry_url ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> <?php echo esc_html( $d['pr_cta'] ); ?></a>
			</div>
			<div class="tz-process-visual" aria-live="polite">
				<?php foreach ( $d['panels'] as $idx => $panel ) : ?>
					<div class="tz-process-panel <?php echo 0 === $idx ? 'is-active' : ''; ?>" data-panel="<?php echo esc_attr( $idx ); ?>">
						<div class="tz-panel-window">
							<div class="tz-panel-bar"><span></span><span></span><span></span></div>
							<div class="tz-panel-body">
								<h4><?php echo esc_html( $panel[0] ); ?></h4>
								<div class="tz-chat-bubble tz-in"><?php echo esc_html( $panel[1] ); ?></div>
								<div class="tz-chat-bubble tz-out"><?php echo esc_html( $panel[2] ); ?></div>
								<div class="tz-status-pill<?php echo $panel[4] ? ' ' . esc_attr( $panel[4] ) : ''; ?>"><?php echo esc_html( $panel[3] ); ?></div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="longcopy article-content" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>

<?php teznevise_builder_render_sections(); ?>

<section class="section">
	<div class="container">
		<div class="cta-band" data-reveal>
			<div>
				<h2><?php echo esc_html( $d['cta_h2'] ); ?></h2>
				<p><?php echo esc_html( $d['cta_p'] ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( teznevise_url( $cta_url ) ); ?>"><?php echo esc_html( $cta_text ); ?></a>
		</div>
	</div>
</section>

<section class="section section-sm">
	<div class="container">
		<div class="seo-panel seo-disclosure" id="seoGuide">
			<div class="seo-label"><?php esc_html_e( 'فضای اختصاصی محتوا برای سئو', 'teznevise' ); ?></div>
			<h2><?php echo esc_html( $d['seo_h2'] ); ?></h2>
			<div class="seo-reading" id="seoPreview">
				<?php foreach ( $d['seo_reading'] as $para ) : ?>
					<p><?php echo esc_html( $para ); ?></p>
				<?php endforeach; ?>
			</div>
			<div class="seo-more-content" id="seoMoreContent">
				<?php foreach ( $d['seo_more'] as $para ) : ?>
					<p><?php echo esc_html( $para ); ?></p>
				<?php endforeach; ?>
			</div>
			<button aria-controls="seoMoreContent" aria-expanded="false" class="seo-more-btn" data-seo-toggle type="button"><span aria-hidden="true" class="seo-more-mark">‹</span><span class="seo-more-text"><?php esc_html_e( 'مشاهده بیشتر', 'teznevise' ); ?></span></button>
		</div>
	</div>
</section>

	<?php
endwhile;

get_footer();
