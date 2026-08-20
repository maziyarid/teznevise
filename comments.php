<?php
/**
 * Comments template.
 *
 * @package Teznevise
 */

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area section-sm">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title" data-reveal>
			<?php
			$count = get_comments_number();
			printf(
				esc_html( _n( '%s دیدگاه', '%s دیدگاه', $count, 'teznevise' ) ),
				esc_html( number_format_i18n( $count ) )
			);
			?>
		</h2>
		<ol class="comment-list" data-reveal>
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 48,
			) );
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'دیدگاه‌ها بسته شده‌اند.', 'teznevise' ); ?></p>
	<?php endif; ?>

	<?php
	if ( ! is_user_logged_in() ) :
		?>
		<p class="no-comments" data-reveal>
			<?php esc_html_e( 'فقط اعضای واردشده می‌توانند نظر بگذارند. با تکمیل پروفایل ۱۰۰۰ تزکوین هدیه می‌گیرید.', 'teznevise' ); ?>
			<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'ورود / ثبت‌نام', 'teznevise' ); ?></a>
		</p>
		<?php
	else :
		comment_form( array(
			'title_reply'          => __( 'نظر خود را بنویسید', 'teznevise' ),
			'label_submit'         => __( 'ارسال دیدگاه', 'teznevise' ),
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'نظر تأییدشده پاداش تزکوین دارد.', 'teznevise' ) . '</p>',
		) );
	endif;
	?>
</section>
