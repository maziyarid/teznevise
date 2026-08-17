<?php
/**
 * Front page template — richest motion.
 *
 * @package Teznevise
 */
get_header();
?>

<section class="hero-new">
	<div class="container">
		<div class="hero-grid">
			<div class="hero-copy">
				<span class="eyebrow"><?php esc_html_e( 'همراهی علمی از ایده تا دفاع', 'teznevise' ); ?></span>
				<h1><?php esc_html_e( 'از موضوع تا تحویل نهایی،', 'teznevise' ); ?> <span class="grad"><?php esc_html_e( 'پژوهش‌تان را حرفه‌ای‌تر', 'teznevise' ); ?></span> <?php esc_html_e( 'پیش ببرید.', 'teznevise' ); ?></h1>
				<p><?php esc_html_e( 'تزنویسه برای پایان‌نامه، پروپوزال، پروژه دانشگاهی و تحلیل آماری یک مسیر منظم، خلاقانه و قابل اتکا می‌سازد؛ با پشتیبانی تخصصی، محرمانگی کامل و پاسخ‌گویی سریع.', 'teznevise' ); ?></p>
				<div class="hero-actions">
					<a class="btn-tz btn-primary-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>">
						<i class="fa-solid fa-rocket" aria-hidden="true"></i> <?php esc_html_e( 'ثبت سفارش و شروع مشاوره', 'teznevise' ); ?>
					</a>
					<a class="btn-tz btn-light-tz btn-lg-tz" href="#services">
						<i class="fa-solid fa-layer-group" aria-hidden="true"></i> <?php esc_html_e( 'مشاهده خدمات', 'teznevise' ); ?>
					</a>
				</div>
				<div class="hero-points">
					<span class="hero-point"><i>✓</i> <?php esc_html_e( 'مشاوره اولیه رایگان', 'teznevise' ); ?></span>
					<span class="hero-point"><i>✓</i> <?php esc_html_e( 'متخصص هر رشته', 'teznevise' ); ?></span>
					<span class="hero-point"><i>✓</i> <?php esc_html_e( 'پشتیبانی تا تحویل', 'teznevise' ); ?></span>
				</div>
			</div>
			<div class="hero-visual" aria-label="<?php esc_attr_e( 'نمایی خلاقانه از خدمات تزنویسه', 'teznevise' ); ?>">
				<div class="ink-blot blot-one"></div>
				<div class="ink-blot blot-two"></div>
				<div class="ink-blot blot-three"></div>
				<div class="hero-particles" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span></div>
				<div class="hero-network">
					<div class="network-ring ring-one"></div>
					<div class="network-ring ring-two"></div>
					<div class="network-ring ring-three"></div>
					<a class="hero-order-button" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>" aria-label="<?php esc_attr_e( 'ثبت سفارش', 'teznevise' ); ?>">
						<i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><span><?php esc_html_e( 'ثبت سفارش', 'teznevise' ); ?></span>
					</a>
					<span class="orbit-tag tag-1">SPSS</span>
					<span class="orbit-tag tag-2">Matlab</span>
					<span class="orbit-tag tag-3"><?php esc_html_e( 'پایان‌نامه', 'teznevise' ); ?></span>
					<span class="orbit-tag tag-4"><?php esc_html_e( 'پروژه دانشگاهی', 'teznevise' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section" id="services">
	<div class="container">
		<div class="section-head" data-reveal>
			<div>
				<span class="eyebrow"><?php esc_html_e( 'خدمات پژوهشی تزنویسه', 'teznevise' ); ?></span>
				<h2><?php esc_html_e( 'هر مرحله از پروژه را با یک پل تخصصی جلو ببرید', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'ساختار کلی صفحه به‌صورت پیوسته و Bridge طراحی شده تا کاربر از یک خدمت به خدمت بعدی، روان و هدفمند حرکت کند.', 'teznevise' ); ?></p>
			</div>
		</div>
		<div class="services-grid" data-reveal-stagger>
			<article class="service-card">
				<div class="icon-box icon-indigo"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'انجام پایان‌نامه', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'از انتخاب موضوع تا نگارش فصل‌ها، تحلیل آماری و آمادگی دفاع برای ارشد و دکتری.', 'teznevise' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( home_url( '/service-thesis/' ) ); ?>"><?php esc_html_e( 'جزئیات خدمت', 'teznevise' ); ?></a>
			</article>
			<article class="service-card">
				<div class="icon-box icon-teal"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'انجام پروپوزال', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'بیان مسئله، مرور ادبیات، اهداف، فرضیه‌ها و روش‌شناسی با ساختار علمی و منابع به‌روز.', 'teznevise' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( home_url( '/service-proposal/' ) ); ?>"><?php esc_html_e( 'جزئیات خدمت', 'teznevise' ); ?></a>
			</article>
			<article class="service-card">
				<div class="icon-box icon-cyan"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'تحلیل آماری', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'تحلیل داده‌ها با SPSS، R، Python، LISREL، Matlab و AMOS همراه با تفسیر علمی نتایج.', 'teznevise' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( home_url( '/service-statistics/' ) ); ?>"><?php esc_html_e( 'جزئیات خدمت', 'teznevise' ); ?></a>
			</article>
			<article class="service-card">
				<div class="icon-box icon-amber"><i class="fa-solid fa-calculator" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'ابزارهای آنلاین', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'ماشین‌حساب‌های آماری رایگان برای آمار توصیفی، همبستگی و آزمون‌های پرکاربرد.', 'teznevise' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'مشاهده ابزارها', 'teznevise' ); ?></a>
			</article>
			<article class="service-card">
				<div class="icon-box icon-danger-soft"><i class="fa-regular fa-lightbulb" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'مرکز دانش', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'راهنماهای کاربردی درباره روش تحقیق، نگارش دانشگاهی، تحلیل آماری و ابزارهای پژوهشی.', 'teznevise' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'مطالعه مقالات', 'teznevise' ); ?></a>
			</article>
			<article class="service-card">
				<div class="icon-box icon-purple-soft"><i class="fa-solid fa-user-group" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'تیم پژوهشگران', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'همکاری با پژوهشگر متخصص متناسب با رشته و موضوع پروژه، از علوم انسانی تا مهندسی.', 'teznevise' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( home_url( '/team/' ) ); ?>"><?php esc_html_e( 'آشنایی با تیم', 'teznevise' ); ?></a>
			</article>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="cta-band" data-reveal>
			<div>
				<h2><?php esc_html_e( 'پروژه پژوهشی‌ات را همین امروز شروع کن', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'موضوع را بفرست؛ کارشناسان تزنویسه مسیر، زمان و برآورد اولیه را با شما بررسی می‌کنند.', 'teznevise' ); ?></p>
			</div>
			<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/inquiry/' ) ); ?>"><?php esc_html_e( 'درخواست مشاوره رایگان', 'teznevise' ); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
