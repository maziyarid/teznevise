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
$agents     = class_exists( 'TezNevise_AI_Database' ) ? (array) TezNevise_AI_Database::get_all_agents() : array();
if ( class_exists( 'Teznevise_Agent_Registry' ) ) {
	$agents = array_map( array( 'Teznevise_Agent_Registry', 'hydrate' ), $agents );
}
?>
<div class="tz-livechat" id="tzLiveChat">
	<button type="button" class="tz-livechat__fab" id="tzLiveChatToggle" aria-expanded="false" aria-controls="tzLiveChatPanel" aria-label="<?php esc_attr_e( 'باز کردن گفتگوی زنده پژوهشی', 'teznevise' ); ?>" title="<?php esc_attr_e( 'گفتگوی زنده', 'teznevise' ); ?>">
		<i class="fa-solid fa-headset" aria-hidden="true"></i>
	</button>
	<section class="tz-ai-chat tz-gpt tz-livechat__panel" id="tzLiveChatPanel" hidden data-tool-id="general" data-agent-id="teznevise" data-collaboration-mode="collaborative" data-thinking="1" data-live-chat="1">
		<header class="tz-gpt__top">
			<?php
			if ( class_exists( 'TezNevise_AI_Chat' ) ) {
				TezNevise_AI_Chat::render_agent_dropdown( 'teznevise', $agents );
			}
			?>
			<div class="tz-gpt__top-actions">
				<button type="button" class="tz-gpt__iconbtn" data-ai-new aria-label="<?php esc_attr_e( 'گفتگوی تازه', 'teznevise' ); ?>" title="<?php esc_attr_e( 'گفتگوی تازه', 'teznevise' ); ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
				<button type="button" class="tz-gpt__iconbtn" id="tzLiveChatClose" aria-label="<?php esc_attr_e( 'بستن گفتگو', 'teznevise' ); ?>" title="<?php esc_attr_e( 'بستن', 'teznevise' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
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
					<div class="tz-gpt-toggles" role="toolbar" aria-label="<?php esc_attr_e( 'ابزار گفتگو', 'teznevise' ); ?>">
						<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-thinking-btn aria-pressed="true" aria-label="<?php esc_attr_e( 'نمایش استدلال', 'teznevise' ); ?>" title="<?php esc_attr_e( 'استدلال', 'teznevise' ); ?>"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></button>
						<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-collab-btn aria-pressed="true" aria-label="<?php esc_attr_e( 'هم‌فکری عامل‌ها', 'teznevise' ); ?>" title="<?php esc_attr_e( 'هم‌فکری', 'teznevise' ); ?>"><i class="fa-solid fa-users" aria-hidden="true"></i></button>
						<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-research-btn aria-pressed="false" aria-label="<?php esc_attr_e( 'پژوهش وب', 'teznevise' ); ?>" title="<?php esc_attr_e( 'پژوهش', 'teznevise' ); ?>"><i class="fa-solid fa-globe" aria-hidden="true"></i></button>
						<button type="button" class="tz-gpt__iconbtn" data-ai-handoff-toggle aria-expanded="false" aria-controls="tzLiveHandoff" aria-label="<?php esc_attr_e( 'رزرو تماس', 'teznevise' ); ?>" title="<?php esc_attr_e( 'رزرو تماس', 'teznevise' ); ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i></button>
					</div>
					<input type="checkbox" data-ai-thinking checked hidden>
					<input type="checkbox" data-ai-collab-toggle checked hidden>
					<input type="checkbox" data-ai-research hidden>
					<button type="button" class="tz-gpt-stop" data-ai-stop hidden aria-label="<?php esc_attr_e( 'توقف', 'teznevise' ); ?>" title="<?php esc_attr_e( 'توقف', 'teznevise' ); ?>">
						<i class="fa-solid fa-stop" aria-hidden="true"></i>
					</button>
					<button class="tz-gpt-send" type="submit" aria-label="<?php esc_attr_e( 'ارسال', 'teznevise' ); ?>" title="<?php esc_attr_e( 'ارسال', 'teznevise' ); ?>">
						<i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
					</button>
				</div>
			</div>
		</form>
		<form class="tz-livechat__handoff" data-ai-handoff id="tzLiveHandoff" hidden>
			<p><?php esc_html_e( 'رزرو تماس و ارسال تاریخچه گفتگو', 'teznevise' ); ?></p>
			<div class="tz-livechat__handoff-grid">
				<label><span><?php esc_html_e( 'نام', 'teznevise' ); ?></span><input type="text" name="name" required maxlength="80" autocomplete="name"></label>
				<label><span><?php esc_html_e( 'موبایل', 'teznevise' ); ?></span><input type="tel" name="phone" required inputmode="tel" dir="ltr" placeholder="0912xxxxxxx" autocomplete="tel"></label>
				<label class="full"><span><?php esc_html_e( 'ایمیل', 'teznevise' ); ?></span><input type="email" name="email" required autocomplete="email"></label>
			</div>
			<button type="submit" class="btn-tz btn-light-tz"><?php esc_html_e( 'زمان‌بندی تماس', 'teznevise' ); ?></button>
			<p class="tz-livechat__handoff-status" data-handoff-status hidden></p>
		</form>
	</section>
</div>
