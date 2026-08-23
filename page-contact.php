<?php
/**
 * Template Name: تماس / درخواست (Contact & Inquiry)
 * Description: NAP + form stay native; FAQ/CTA/hero come from the Flexible Page Builder when seeded.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$has_builder_hero = function_exists( 'teznevise_builder_has_type' ) && teznevise_builder_has_type( 'hero' );
	$eyebrow          = teznevise_page_field( 'eyebrow', 0, __( 'ارتباط با ما', 'teznevise' ) );
	$subtitle        = teznevise_page_field( 'subtitle', 0, __( 'مشاوره اولیه رایگان — پاسخ‌گویی سریع', 'teznevise' ) );

	if ( $has_builder_hero ) {
		teznevise_builder_render_sections( 0, array( 'only' => array( 'hero' ) ) );
	} else {
		?>
<section class="section">
	<div class="container">
		<div class="section-head" data-reveal>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
		<?php
	}
	?>

<section class="section">
	<div class="container">
		<div class="longcopy article-content" id="inquiry-form" data-reveal>
			<?php
			$slug        = get_post_field( 'post_name', get_the_ID() );
			$is_inquiry  = ( 'inquiry' === $slug || 'order' === $slug );
			$raw_content = get_post_field( 'post_content', get_the_ID() );
			$plain       = trim( wp_strip_all_tags( (string) $raw_content ) );
			$is_shortcode_only = (bool) preg_match( '/^\[[a-zA-Z][^\]]{0,120}\]$/u', html_entity_decode( $plain, ENT_QUOTES, 'UTF-8' ) );

			if ( $is_inquiry ) :
				?>
			<div class="inquiry-grid">
				<div>
					<?php echo function_exists( 'teznevise_render_native_lead_form' ) ? teznevise_render_native_lead_form( 'inquiry' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div>
					<p class="privacy-note"><?php esc_html_e( 'اگر ترجیح می‌دهید مستقیم پیام بدهید، از راه‌های زیر استفاده کنید. شرح پروژه را در نشانی واتساپ ننویسید.', 'teznevise' ); ?></p>
					<div class="inquiry-messengers" style="margin-top:16px;">
						<a class="inq-msg" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> واتساپ</a>
						<a class="inq-msg" href="<?php echo esc_url( teznevise_get_contact( 'telegram' ) ); ?>"><i class="fa-brands fa-telegram" aria-hidden="true"></i> تلگرام</a>
						<a class="inq-msg" href="<?php echo esc_attr( function_exists( 'teznevise_tel_href' ) ? teznevise_tel_href( teznevise_get_contact( 'phone_intl' ) ) : 'tel:+989302822091' ); ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i> تماس</a>
					</div>
				</div>
			</div>
			<?php elseif ( $is_shortcode_only || '' === $plain ) : ?>
				<?php echo function_exists( 'teznevise_render_native_lead_form' ) ? teznevise_render_native_lead_form( 'contact' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="faq-wrap" style="margin-top:28px;">
					<div class="faq-item">
						<button type="button" class="faq-q" aria-expanded="false"><?php esc_html_e( 'چقدر طول می‌کشد تا پاسخ بگیرم؟', 'teznevise' ); ?></button>
						<div class="faq-a"><?php esc_html_e( 'در ساعات کاری (شنبه تا پنجشنبه، ۹ تا ۲۱) معمولاً در کمتر از چند ساعت پاسخ اولیه داده می‌شود.', 'teznevise' ); ?></div>
					</div>
					<div class="faq-item">
						<button type="button" class="faq-q" aria-expanded="false"><?php esc_html_e( 'آیا مشاوره اولیه رایگان است؟', 'teznevise' ); ?></button>
						<div class="faq-a"><?php esc_html_e( 'بله. بررسی اولیه موضوع، مسیر و برآورد زمان بدون هزینه است.', 'teznevise' ); ?></div>
					</div>
					<div class="faq-item">
						<button type="button" class="faq-q" aria-expanded="false"><?php esc_html_e( 'چطور پروژه را شروع کنم؟', 'teznevise' ); ?></button>
						<div class="faq-a"><?php esc_html_e( 'موضوع، مقطع و فایل‌های موجود را بفرستید تا مسیر کار مشخص شود.', 'teznevise' ); ?></div>
					</div>
				</div>
			<?php elseif ( function_exists( 'teznevise_page_should_print_content' ) && teznevise_page_should_print_content() ) : ?>
				<?php
				if ( function_exists( 'teznevise_the_page_interactive_content' ) ) {
					teznevise_the_page_interactive_content();
				} else {
					the_content();
				}
				?>
			<?php elseif ( ! function_exists( 'teznevise_page_should_print_content' ) ) : ?>
				<?php the_content(); ?>
			<?php endif; ?>
		</div>

		<div class="reason-list contact-cards tz-contact-cards" data-reveal-stagger>
			<a class="reason-item tz-contact-card" href="<?php echo esc_attr( function_exists( 'teznevise_tel_href' ) ? teznevise_tel_href( teznevise_get_contact( 'phone_intl' ) ) : 'tel:+989302822091' ); ?>">
				<div class="icon-box icon-teal"><i class="fa-solid fa-phone" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'تلفن', 'teznevise' ); ?></b>
				<p><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></p>
			</a>
			<a class="reason-item tz-contact-card" href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>" target="_blank" rel="noopener">
				<div class="icon-box icon-indigo"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'واتساپ', 'teznevise' ); ?></b>
				<p><?php esc_html_e( 'شروع گفتگو', 'teznevise' ); ?></p>
			</a>
			<a class="reason-item tz-contact-card" href="mailto:<?php echo esc_attr( teznevise_get_contact( 'email' ) ); ?>">
				<div class="icon-box icon-cyan"><i class="fa-solid fa-envelope" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'ایمیل', 'teznevise' ); ?></b>
				<p><?php echo esc_html( teznevise_get_contact( 'email' ) ); ?></p>
			</a>
			<div class="reason-item tz-contact-card">
				<div class="icon-box icon-amber"><i class="fa-regular fa-clock" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'ساعات پاسخ‌گویی', 'teznevise' ); ?></b>
				<p><?php echo esc_html( teznevise_get_contact( 'hours' ) ); ?></p>
			</div>
		</div>
	</div>
</section>

	<?php
	if ( function_exists( 'teznevise_builder_render_sections' ) ) {
		teznevise_builder_render_sections( 0, array( 'except' => array( 'hero' ) ) );
	}

endwhile;

get_footer();
