<?php
/**
 * Floating live-chat widget (bottom-right). Messengers stay on the left.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_admin() ) {
	return;
}
$agent_name = __( 'تزنویسه', 'teznevise' );
?>
<div class="tz-livechat" id="tzLiveChat">
	<button type="button" class="tz-livechat__fab" id="tzLiveChatToggle" aria-expanded="false" aria-controls="tzLiveChatPanel" aria-label="<?php esc_attr_e( 'گفتگوی زنده پژوهشی', 'teznevise' ); ?>">
		<i class="fa-solid fa-headset" aria-hidden="true"></i>
		<span><?php esc_html_e( 'گفتگو', 'teznevise' ); ?></span>
	</button>
	<section class="tz-ai-chat tz-gpt tz-livechat__panel" id="tzLiveChatPanel" hidden data-tool-id="general" data-agent-id="teznevise" data-collaboration-mode="collaborative" data-thinking="1" data-live-chat="1">
		<header class="tz-gpt__top">
			<div class="tz-gpt__brand">
				<span class="tz-gpt__dot" aria-hidden="true"></span>
				<div>
					<strong><?php esc_html_e( 'گفتگوی زنده پژوهشی', 'teznevise' ); ?></strong>
					<small><?php echo esc_html( $agent_name ); ?></small>
				</div>
			</div>
			<div class="tz-gpt__top-actions">
				<button type="button" class="tz-gpt__iconbtn" data-ai-new><?php esc_html_e( 'تازه', 'teznevise' ); ?></button>
				<button type="button" class="tz-gpt__iconbtn" id="tzLiveChatClose"><?php esc_html_e( 'بستن', 'teznevise' ); ?></button>
			</div>
		</header>
		<p class="tz-livechat__note"><?php esc_html_e( 'پاسخ‌ها آموزشی‌اند و دقت علمی را تضمین نمی‌کنند. برای بررسی تخصصی، تماس رزرو کنید تا تاریخچه گفتگو برایتان ایمیل شود.', 'teznevise' ); ?></p>
		<div class="tz-ai-chat__log tz-gpt__log" data-ai-log role="log" aria-live="polite">
			<article class="tz-ai-msg is-assistant">
				<span class="tz-ai-msg__avatar" aria-hidden="true">ت</span>
				<div class="tz-ai-msg__stack">
					<header class="tz-ai-msg__meta"><strong><?php echo esc_html( $agent_name ); ?></strong></header>
					<div class="tz-ai-msg__bubble"><?php esc_html_e( 'سلام. سؤال پژوهشی‌تان را بپرسید تا مسیر را برایتان توضیح بدهم. اگر به بررسی انسانی نیاز داشتید، زمان تماس رزرو کنید.', 'teznevise' ); ?></div>
				</div>
			</article>
		</div>
		<div class="tz-ai-status" data-ai-status hidden></div>
		<form class="tz-gpt-composer" data-ai-form>
			<div class="tz-gpt-box">
				<label class="screen-reader-text" for="tz-livechat-q"><?php esc_html_e( 'پیام', 'teznevise' ); ?></label>
				<textarea id="tz-livechat-q" data-ai-input rows="1" required minlength="4" placeholder="<?php esc_attr_e( 'سؤال خود را بنویسید…', 'teznevise' ); ?>"></textarea>
				<div class="tz-gpt-bar">
					<label class="tz-ai-chat__check"><input type="checkbox" data-ai-thinking checked> <?php esc_html_e( 'نمایش فکر', 'teznevise' ); ?></label>
					<label class="tz-ai-chat__check"><input type="checkbox" data-ai-collab-toggle checked> <?php esc_html_e( 'هم‌فکری عامل‌ها', 'teznevise' ); ?></label>
					<label class="tz-ai-chat__check"><input type="checkbox" data-ai-research> <?php esc_html_e( 'پژوهش', 'teznevise' ); ?></label>
					<button class="tz-gpt-send" type="submit" aria-label="<?php esc_attr_e( 'ارسال', 'teznevise' ); ?>">
						<span><?php esc_html_e( 'ارسال', 'teznevise' ); ?></span>
					</button>
				</div>
			</div>
		</form>
		<form class="tz-livechat__handoff" data-ai-handoff>
			<p><?php esc_html_e( 'رزرو تماس و ارسال تاریخچه گفتگو', 'teznevise' ); ?></p>
			<div class="tz-livechat__handoff-grid">
				<label><span><?php esc_html_e( 'نام', 'teznevise' ); ?></span><input type="text" name="name" required maxlength="80"></label>
				<label><span><?php esc_html_e( 'موبایل', 'teznevise' ); ?></span><input type="tel" name="phone" required inputmode="tel" dir="ltr" placeholder="0912xxxxxxx"></label>
				<label class="full"><span><?php esc_html_e( 'ایمیل', 'teznevise' ); ?></span><input type="email" name="email" required></label>
			</div>
			<button type="submit" class="btn-tz btn-light-tz"><?php esc_html_e( 'زمان‌بندی تماس', 'teznevise' ); ?></button>
			<p class="tz-livechat__handoff-status" data-handoff-status hidden></p>
		</form>
	</section>
</div>
