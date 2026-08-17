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

<section class="section bg-soft">
	<div class="container">
		<div class="reason-wrap">
			<div class="reason-panel about-panel" data-reveal>
				<span class="eyebrow"><?php esc_html_e( 'درباره تزنویسه', 'teznevise' ); ?></span>
				<h3><?php esc_html_e( 'پژوهش خوب فقط تحویل فایل نیست.', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'تزنویسه با تمرکز بر کیفیت علمی، شفافیت مسیر و پاسخ‌گویی، دانشجویان و پژوهشگران را از انتخاب موضوع تا دفاع همراهی می‌کند. تیم متخصص، محرمانگی کامل و پشتیبانی منظم بخشی از همین مسیر است.', 'teznevise' ); ?></p>
				<a class="btn-tz btn-light-tz btn-lg-tz" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">
					<i class="fa-regular fa-circle-question" aria-hidden="true"></i> <?php esc_html_e( 'درباره ما', 'teznevise' ); ?>
				</a>
			</div>
			<div class="reason-list" data-reveal-stagger>
				<div class="reason-item">
					<div class="icon-box icon-indigo"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></div>
					<b><?php esc_html_e( 'محرمانگی کامل', 'teznevise' ); ?></b>
					<p><?php esc_html_e( 'اطلاعات و فایل‌های پروژه با رویکرد محرمانه مدیریت می‌شوند.', 'teznevise' ); ?></p>
				</div>
				<div class="reason-item">
					<div class="icon-box icon-teal"><i class="fa-solid fa-compass-drafting" aria-hidden="true"></i></div>
					<b><?php esc_html_e( 'روش‌مندی علمی', 'teznevise' ); ?></b>
					<p><?php esc_html_e( 'ساختار پژوهش بر اساس متدولوژی استاندارد و منابع علمی به‌روز پیش می‌رود.', 'teznevise' ); ?></p>
				</div>
				<div class="reason-item">
					<div class="icon-box icon-cyan"><i class="fa-solid fa-bolt" aria-hidden="true"></i></div>
					<b><?php esc_html_e( 'پاسخ‌گویی سریع', 'teznevise' ); ?></b>
					<p><?php esc_html_e( 'درخواست اولیه شما در کوتاه‌ترین زمان بررسی و مسیر بعدی شفاف می‌شود.', 'teznevise' ); ?></p>
				</div>
				<div class="reason-item">
					<div class="icon-box icon-danger-soft"><i class="fa-solid fa-pen-ruler" aria-hidden="true"></i></div>
					<b><?php esc_html_e( 'خلاقیت آکادمیک', 'teznevise' ); ?></b>
					<p><?php esc_html_e( 'تم بصری سایت با عناصر نموداری، ماتریسی و لکه‌های جوهر، حس آکادمی و پژوهش را تقویت می‌کند.', 'teznevise' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head center" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'از کجا شروع کنم؟', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'چهار قدم تا یک مسیر پژوهشی روشن', 'teznevise' ); ?></h2>
			<p><?php esc_html_e( 'هر مرحله خروجی مشخص دارد؛ بنابراین همیشه می‌دانید پروژه در چه وضعیتی است و قدم بعدی چیست.', 'teznevise' ); ?></p>
		</div>
		<div class="steps" data-reveal-stagger>
			<div class="step">
				<div class="step-icon icon-teal"><i class="fa-regular fa-comments" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'مشاوره رایگان', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'موضوع، نیاز، زمان و بودجه بررسی می‌شود و برآورد اولیه دریافت می‌کنید.', 'teznevise' ); ?></p>
			</div>
			<div class="step">
				<div class="step-icon icon-teal"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'طرح و پروپوزال', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'ساختار پژوهش، پرسش‌ها، فرضیه‌ها و روش اجرا منسجم می‌شود.', 'teznevise' ); ?></p>
			</div>
			<div class="step">
				<div class="step-icon icon-teal"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'اجرا و تحلیل', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'داده‌ها با روش مناسب تحلیل و نتایج به‌صورت علمی تفسیر می‌شوند.', 'teznevise' ); ?></p>
			</div>
			<div class="step">
				<div class="step-icon icon-teal"><i class="fa-solid fa-person-chalkboard" aria-hidden="true"></i></div>
				<h3><?php esc_html_e( 'نگارش و دفاع', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'فصل‌ها تکمیل، اصلاحات اعمال و برای ارائه نهایی آماده می‌شوید.', 'teznevise' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="section bg-soft">
	<div class="container">
		<div class="section-head" data-reveal>
			<div>
				<span class="eyebrow"><?php esc_html_e( 'تازه‌های مرکز دانش', 'teznevise' ); ?></span>
				<h2><?php esc_html_e( 'مطالب جدید و کاربردی', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'به‌جای لینک‌های ساده با فلش، اقدام‌ها به‌صورت دکمه‌های واضح و مدرن طراحی شده‌اند تا تجربه کاربر حرفه‌ای‌تر باشد.', 'teznevise' ); ?></p>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'مشاهده همه مقالات', 'teznevise' ); ?></a>
		</div>
		<div class="article-grid" data-reveal-stagger>
			<?php
			$recent = new WP_Query( array(
				'posts_per_page' => 3,
				'post_status'    => 'publish',
				'ignore_sticky_posts' => true,
			) );
			if ( $recent->have_posts() ) :
				while ( $recent->have_posts() ) :
					$recent->the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				wp_reset_postdata();
			else :
				$placeholders = array(
					array( 'date' => '۱۲ مرداد ۱۴۰۵', 'cat' => 'مسیر پایان‌نامه', 'title' => 'آموزش نگارش فصل اول پایان‌نامه: تبدیل پروپوزال به فصل اول', 'excerpt' => 'ساختار کلیات پژوهش، بیان مسئله، اهداف و فرضیه‌ها را مرحله‌به‌مرحله مرور کنید.' ),
					array( 'date' => '۸ مرداد ۱۴۰۵', 'cat' => 'هوش مصنوعی', 'title' => 'هوش مصنوعی برای ایده‌یابی موضوع پایان‌نامه', 'excerpt' => 'روش استفاده از ابزارهای هوش مصنوعی برای توسعه ایده بدون افت کیفیت علمی.' ),
					array( 'date' => '۲ مرداد ۱۴۰۵', 'cat' => 'تحلیل آماری', 'title' => 'آموزش گام‌به‌گام آزمون تی مستقل در SPSS', 'excerpt' => 'اجرای آزمون، بررسی پیش‌فرض‌ها و تفسیر خروجی را به زبان ساده یاد بگیرید.' ),
				);
				foreach ( $placeholders as $i => $ph ) :
					$cover = $i === 1 ? ' alt' : ( $i === 2 ? ' dark' : '' );
					?>
					<article class="article-card">
						<div class="article-cover<?php echo esc_attr( $cover ); ?>"></div>
						<div class="article-body">
							<div class="article-meta"><span><?php echo esc_html( $ph['date'] ); ?></span><span><?php echo esc_html( $ph['cat'] ); ?></span></div>
							<h3><?php echo esc_html( $ph['title'] ); ?></h3>
							<p><?php echo esc_html( $ph['excerpt'] ); ?></p>
							<a class="link-arrow" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'مطالعه مقاله', 'teznevise' ); ?></a>
						</div>
					</article>
					<?php
				endforeach;
			endif;
			?>
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
