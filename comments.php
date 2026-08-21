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
			'class_form'           => 'comment-form tez-comment-form',
			'class_submit'         => 'submit btn-tz btn-primary-tz',
			'title_reply'          => __( 'نظر خود را بنویسید', 'teznevise' ),
			'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h2>',
			'label_submit'         => __( 'ارسال دیدگاه', 'teznevise' ),
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'نظر تأییدشده پاداش تزکوین دارد. بخش‌های ضروری با ستاره مشخص شده‌اند.', 'teznevise' ) . '</p>',
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'دیدگاه', 'teznevise' ) . ' <span class="required" aria-hidden="true">*</span><span class="screen-reader-text">' . esc_html__( 'ضروری', 'teznevise' ) . '</span></label><textarea id="comment" name="comment" cols="45" rows="7" maxlength="65525" required></textarea></p>',
		) );
	endif;
	?>
</section>
