<?php
/**
 * Extra visual band for the our-story / about page.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$slug = get_post_field( 'post_name', get_the_ID() );
if ( ! in_array( $slug, array( 'our-story', 'about', 'about-us' ), true ) ) {
	return;
}
?>
<section class="section tz-story-visual" aria-label="<?php esc_attr_e( 'مسیر تزنویسه', 'teznevise' ); ?>">
	<div class="container">
		<div class="tz-story-stats" data-reveal-stagger>
			<article><strong>+۸۵۰۰</strong><span><?php esc_html_e( 'دانشجو همراهی‌شده', 'teznevise' ); ?></span></article>
			<article><strong>+۱۲۰</strong><span><?php esc_html_e( 'رشته دانشگاهی', 'teznevise' ); ?></span></article>
			<article><strong>+۲۰</strong><span><?php esc_html_e( 'مشاور متخصص', 'teznevise' ); ?></span></article>
			<article><strong>۹۸٪</strong><span><?php esc_html_e( 'رضایت مسیر مشاوره', 'teznevise' ); ?></span></article>
		</div>
		<div class="section-head center" data-reveal>
			<span class="eyebrow"><?php esc_html_e( 'داستان ما', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'از یک ایده در خوابگاه تا شبکه پژوهشی', 'teznevise' ); ?></h2>
			<p><?php esc_html_e( 'مسیر تزنویسه خطی و تزئینی نیست؛ هر ایستگاه یک قابلیت واقعی به همراهی دانشجو اضافه کرده است.', 'teznevise' ); ?></p>
		</div>
		<ol class="tz-story-rail" data-reveal-stagger>
			<li>
				<span class="tz-story-year">۱۳۹۸</span>
				<h3><?php esc_html_e( 'مشاوره موضوع', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'شروع با انتخاب موضوع و بیان مسئله برای چند رشته هم‌دانشگاهی.', 'teznevise' ); ?></p>
			</li>
			<li>
				<span class="tz-story-year">۱۴۰۰</span>
				<h3><?php esc_html_e( 'آمار و روش', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'تحلیل آماری و طراحی روش به مسیر مشاوره اضافه شد.', 'teznevise' ); ?></p>
			</li>
			<li>
				<span class="tz-story-year">۱۴۰۲</span>
				<h3><?php esc_html_e( 'مسیر کامل', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'از پروپوزال تا دفاع، یک جریان واحد با مشاور هم‌رشته.', 'teznevise' ); ?></p>
			</li>
			<li>
				<span class="tz-story-year">۱۴۰۴</span>
				<h3><?php esc_html_e( 'ابزار آنلاین', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'ماشین‌حساب‌ها و عامل‌های هوش مصنوعی برای کار روزمره پژوهش.', 'teznevise' ); ?></p>
			</li>
			<li>
				<span class="tz-story-year">۱۴۰۵</span>
				<h3><?php esc_html_e( 'شبکه بین‌المللی', 'teznevise' ); ?></h3>
				<p><?php esc_html_e( 'پنل، مرکز دانش و همراهی دانشجویان در چند کشور.', 'teznevise' ); ?></p>
			</li>
		</ol>
		<blockquote class="tz-story-quote" data-reveal>
			<p><?php esc_html_e( 'ما تضمین نتیجه علمی نمی‌دهیم؛ مسیر را شفاف می‌کنیم، روش را درست می‌چینیم و دانشجو را تا دفاع همراهی می‌کنیم.', 'teznevise' ); ?></p>
		</blockquote>
	</div>
</section>
