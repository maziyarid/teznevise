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
	comment_form( array(
		'title_reply'          => __( 'نظر خود را بنویسید', 'teznevise' ),
		'label_submit'         => __( 'ارسال دیدگاه', 'teznevise' ),
		'comment_notes_before' => '',
	) );
	?>
</section>
