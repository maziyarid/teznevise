<?php
/**
 * Comments template — human tab + AI discussion tab.
 *
 * @package Teznevise
 */

if ( post_password_required() ) {
	return;
}

$post_id     = get_the_ID();
$ai_enabled  = function_exists( 'teznevise_ai_comment_settings' ) ? ! empty( teznevise_ai_comment_settings()['enabled'] ) : false;
$ai_comments = $ai_enabled ? get_comments(
	array(
		'post_id' => $post_id,
		'type'    => 'tz_ai',
		'status'  => 'approve',
		'orderby' => 'comment_date_gmt',
		'order'   => 'ASC',
	)
) : array();
$human_count = get_comments(
	array(
		'post_id' => $post_id,
		'type'    => 'comment',
		'status'  => 'approve',
		'count'   => true,
	)
);
$ai_count = is_array( $ai_comments ) ? count( $ai_comments ) : 0;
?>

<section id="comments" class="comments-area section-sm tz-comments">
	<?php if ( $ai_enabled ) : ?>
		<div class="tz-comment-tabs" role="tablist" data-comment-tabs>
			<button type="button" role="tab" aria-selected="true" aria-controls="human-comments" id="tab-human"><?php echo esc_html( sprintf( __( 'دیدگاه خوانندگان (%s)', 'teznevise' ), number_format_i18n( (int) $human_count ) ) ); ?></button>
			<button type="button" role="tab" aria-selected="false" aria-controls="ai-discussion" id="tab-ai"><?php echo esc_html( sprintf( __( 'گفتگوی هوش مصنوعی (%s)', 'teznevise' ), number_format_i18n( $ai_count ) ) ); ?></button>
		</div>
	<?php endif; ?>

	<div id="human-comments" class="tz-comment-panel" role="tabpanel" aria-labelledby="tab-human">
		<?php if ( have_comments() ) : ?>
			<h2 class="comments-title" data-reveal>
				<?php
				printf(
					esc_html( _n( '%s دیدگاه', '%s دیدگاه', (int) $human_count, 'teznevise' ) ),
					esc_html( number_format_i18n( (int) $human_count ) )
				);
				?>
			</h2>
			<ol class="comment-list" data-reveal>
				<?php
				wp_list_comments(
					array(
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 48,
						'type'        => 'comment',
					)
				);
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
			comment_form(
				array(
					'class_form'           => 'comment-form tez-comment-form',
					'class_submit'         => 'submit btn-tz btn-primary-tz',
					'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
					'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
					'title_reply'          => __( 'نظر خود را بنویسید', 'teznevise' ),
					'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title">',
					'title_reply_after'    => '</h2>',
					'label_submit'         => __( 'ارسال دیدگاه', 'teznevise' ),
					'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'نظر تأییدشده پاداش تزکوین دارد. بخش‌های ضروری با ستاره مشخص شده‌اند.', 'teznevise' ) . '</p>',
					'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'دیدگاه', 'teznevise' ) . ' <span class="required" aria-hidden="true">*</span><span class="screen-reader-text">' . esc_html__( 'ضروری', 'teznevise' ) . '</span></label><textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required placeholder="' . esc_attr__( 'تجربه یا پرسش خود را بنویسید…', 'teznevise' ) . '"></textarea></p>',
				)
			);
		endif;
		?>
	</div>

	<?php if ( $ai_enabled ) : ?>
		<div id="ai-discussion" class="tz-comment-panel" role="tabpanel" hidden aria-labelledby="tab-ai">
			<h2><?php esc_html_e( 'گفتگوی عامل‌های پژوهشی', 'teznevise' ); ?></h2>
			<p class="tz-ai-discuss-lead"><?php esc_html_e( 'این تب بحث ساختگی عامل‌های نام‌دار است؛ برای غنای مطلب و خزش‌پذیری، با نام، نقش و برچسب منتشر می‌شود. کارشناس تزنویسه می‌تواند به‌عنوان انسان وارد گفتگو شود.', 'teznevise' ); ?></p>
			<?php if ( $ai_comments ) : ?>
				<ol class="comment-list tz-ai-thread">
					<?php foreach ( $ai_comments as $c ) : ?>
						<li id="ai-comment-<?php echo (int) $c->comment_ID; ?>" class="tz-ai-comment <?php echo get_comment_meta( $c->comment_ID, 'tz_human_moderator', true ) ? 'is-human' : ''; ?>">
							<article>
								<header class="comment-author">
									<strong><?php echo esc_html( $c->comment_author ); ?></strong>
									<?php
									$role = get_comment_meta( $c->comment_ID, 'tz_ai_role', true );
									$tags = get_comment_meta( $c->comment_ID, 'tz_ai_tags', true );
									if ( $role ) {
										echo ' <span class="tz-ai-role">' . esc_html( $role ) . '</span>';
									}
									?>
									<time datetime="<?php echo esc_attr( mysql2date( 'c', $c->comment_date_gmt, false ) ); ?>"><?php echo esc_html( mysql2date( get_option( 'date_format' ), $c->comment_date ) ); ?></time>
								</header>
								<div class="comment-content"><?php echo wp_kses_post( wpautop( $c->comment_content ) ); ?></div>
								<?php if ( $tags ) : ?>
									<p class="tz-ai-tags"><?php echo esc_html( $tags ); ?></p>
								<?php endif; ?>
							</article>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php else : ?>
				<p><?php esc_html_e( 'هنوز گفتگویی تولید نشده است.', 'teznevise' ); ?></p>
			<?php endif; ?>
			<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
				<form class="tez-comment-form tz-ai-human-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'teznevise_ai_human', '_tz_ai_human' ); ?>
					<input type="hidden" name="action" value="teznevise_ai_human_reply" />
					<input type="hidden" name="teznevise_ai_human_reply" value="1" />
					<input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />
					<label for="ai-human-body"><?php esc_html_e( 'ورود به گفتگو به‌عنوان کارشناس', 'teznevise' ); ?>
						<textarea id="ai-human-body" name="ai_human_body" rows="4" required minlength="4"></textarea>
					</label>
					<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'ارسال به‌عنوان انسان', 'teznevise' ); ?></button>
				</form>
			<?php endif; ?>
			<?php
			if ( function_exists( 'teznevise_ai_comments_schema' ) ) {
				echo teznevise_ai_comments_schema( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	<?php endif; ?>
</section>
