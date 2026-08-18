<?php
/**
 * Template Name: تماس / درخواست (Contact & Inquiry)
 * Description: Contact page with NAP from Customizer + page fields + content/form.
 *
 * @package Teznevise
 */

get_header();

while ( have_posts() ) :
	the_post();

	$eyebrow   = teznevise_page_field( 'eyebrow', 0, __( 'ارتباط با ما', 'teznevise' ) );
	$subtitle = teznevise_page_field( 'subtitle', 0, __( 'مشاوره اولیه رایگان — پاسخ‌گویی سریع', 'teznevise' ) );
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

		<div class="reason-list contact-cards" data-reveal-stagger style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));margin-bottom:32px;">
			<div class="reason-item">
				<div class="icon-box icon-teal"><i class="fa-solid fa-phone" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'تلفن', 'teznevise' ); ?></b>
				<p><a href="tel:<?php echo esc_attr( teznevise_get_contact( 'phone_intl' ) ); ?>"><?php echo esc_html( teznevise_get_contact( 'phone_display' ) ); ?></a></p>
			</div>
			<div class="reason-item">
				<div class="icon-box icon-indigo"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'واتساپ', 'teznevise' ); ?></b>
				<p><a href="<?php echo esc_url( teznevise_get_contact( 'whatsapp' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'شروع گفتگو', 'teznevise' ); ?></a></p>
			</div>
			<div class="reason-item">
				<div class="icon-box icon-cyan"><i class="fa-solid fa-envelope" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'ایمیل', 'teznevise' ); ?></b>
				<p><a href="mailto:<?php echo esc_attr( teznevise_get_contact( 'email' ) ); ?>"><?php echo esc_html( teznevise_get_contact( 'email' ) ); ?></a></p>
			</div>
			<div class="reason-item">
				<div class="icon-box icon-amber"><i class="fa-regular fa-clock" aria-hidden="true"></i></div>
				<b><?php esc_html_e( 'ساعات پاسخ‌گویی', 'teznevise' ); ?></b>
				<p><?php echo esc_html( teznevise_get_contact( 'hours' ) ); ?></p>
			</div>
		</div>

		<div class="longcopy article-content" data-reveal>
			<?php the_content(); ?>
		</div>
	</div>
</section>

<?php teznevise_builder_render_sections(); ?>

	<?php
endwhile;

get_footer();
