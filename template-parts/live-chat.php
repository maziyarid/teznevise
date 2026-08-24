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
$page_title = wp_get_document_title();
$hints      = array(
	__( 'از کجا مشاوره پایان‌نامه را شروع کنم؟', 'teznevise' ),
	__( 'کدام آزمون آماری برای داده من مناسب است؟', 'teznevise' ),
	__( 'ابزارهای آنلاین تزنویسه چه کمکی می‌کنند؟', 'teznevise' ),
);
if ( is_singular( 'post' ) ) {
	$hints = array(
		__( 'نمای کلی این مطلب را خلاصه کن', 'teznevise' ),
		__( 'قدم بعدی پژوهشی من چیست؟', 'teznevise' ),
		__( 'کدام عامل برای این موضوع مناسب‌تر است؟', 'teznevise' ),
	);
} elseif ( is_page() ) {
	$slug = get_post_field( 'post_name', get_the_ID() );
	if ( in_array( $slug, array( 'thesis', 'service-thesis' ), true ) ) {
		$hints = array(
			__( 'مسیر مشاوره پایان‌نامه چند مرحله است؟', 'teznevise' ),
			__( 'نقش دانشجو در مشاوره چیست؟', 'teznevise' ),
			__( 'چطور درخواست مشاوره ثبت کنم؟', 'teznevise' ),
		);
	} elseif ( false !== strpos( (string) $slug, 'tool' ) || 'online-calculation-tools' === $slug ) {
		$hints = array(
			__( 'کدام ماشین‌حساب را انتخاب کنم؟', 'teznevise' ),
			__( 'خروجی ابزار را چطور تفسیر کنم؟', 'teznevise' ),
			__( 'کی به تحلیل آماری تخصصی نیاز دارم؟', 'teznevise' ),
		);
	}
}
?>
<div class="tz-livechat" id="tzLiveChat">
	<button type="button" class="tz-livechat__fab" id="tzLiveChatToggle" aria-expanded="false" aria-controls="tzLiveChatPanel" aria-label="<?php esc_attr_e( 'باز کردن گفتگوی زنده پژوهشی', 'teznevise' ); ?>" title="<?php esc_attr_e( 'گفتگوی زنده', 'teznevise' ); ?>">
		<i class="fa-solid fa-comment-dots" aria-hidden="true"></i>
		<span class="tz-livechat__fab-label"><?php esc_html_e( 'گفتگو', 'teznevise' ); ?></span>
	</button>
	<section class="tz-ai-chat tz-gpt tz-livechat__panel" id="tzLiveChatPanel" hidden role="dialog" aria-modal="false" aria-label="<?php esc_attr_e( 'گفتگوی پژوهشی تزنویسه', 'teznevise' ); ?>" data-tool-id="general" data-agent-id="teznevise" data-collaboration-mode="single" data-thinking="0" data-live-chat="1" data-page-url="<?php echo esc_url( is_singular() ? get_permalink() : home_url( '/' ) ); ?>" data-page-title="<?php echo esc_attr( $page_title ); ?>">
		<header class="tz-gpt__top">
			<div class="tz-gpt-selectors">
				<?php
				if ( class_exists( 'TezNevise_AI_Chat' ) ) {
					TezNevise_AI_Chat::render_agent_dropdown( 'teznevise', $agents );
					TezNevise_AI_Chat::render_model_dropdown();
				}
				?>
			</div>
			<div class="tz-gpt__top-actions">
				<button type="button" class="tz-gpt__iconbtn" data-ai-new aria-label="<?php esc_attr_e( 'گفتگوی تازه', 'teznevise' ); ?>" title="<?php esc_attr_e( 'گفتگوی تازه', 'teznevise' ); ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
				<button type="button" class="tz-gpt__iconbtn" id="tzLiveChatClose" aria-label="<?php esc_attr_e( 'بستن گفتگو', 'teznevise' ); ?>" title="<?php esc_attr_e( 'بستن', 'teznevise' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
			</div>
		</header>
		<p class="tz-livechat__note"><?php esc_html_e( 'پاسخ‌ها بر اساس محتوای تزنویسه‌اند و مشاوره آموزشی‌اند، نه نوشتن پایان‌نامه.', 'teznevise' ); ?></p>
		<div class="tz-ai-chat__log tz-gpt__log" data-ai-log role="log" aria-live="polite" aria-relevant="additions text" tabindex="0">
			<article class="tz-ai-msg is-assistant">
				<span class="tz-ai-msg__avatar" aria-hidden="true">ت</span>
				<div class="tz-ai-msg__stack">
					<header class="tz-ai-msg__meta"><strong><?php echo esc_html( $agent_name ); ?></strong></header>
					<div class="tz-ai-msg__bubble"><?php esc_html_e( 'سلام. سؤال پژوهشی‌تان را بپرسید؛ مسیر مشاوره و ابزار مناسب را دقیق می‌گویم. مراحل آماده‌سازی پاسخ به‌صورت زنده نمایش داده می‌شود.', 'teznevise' ); ?></div>
				</div>
			</article>
		</div>
		<div class="tz-gpt-hints" data-ai-hints>
			<?php foreach ( $hints as $hint ) : ?>
				<button type="button" class="tz-gpt-hint" data-hint="<?php echo esc_attr( $hint ); ?>"><?php echo esc_html( $hint ); ?></button>
			<?php endforeach; ?>
		</div>
		<div class="tz-ai-status" data-ai-status hidden></div>
		<form class="tz-gpt-composer" data-ai-form>
			<div class="tz-gpt-box">
				<label class="screen-reader-text" for="tz-livechat-q"><?php esc_html_e( 'پیام', 'teznevise' ); ?></label>
				<textarea id="tz-livechat-q" data-ai-input rows="1" required minlength="4" placeholder="<?php esc_attr_e( 'سؤال پژوهشی خود را بنویسید…', 'teznevise' ); ?>"></textarea>
				<div class="tz-gpt-bar">
					<div class="tz-gpt-toggles" role="toolbar" aria-label="<?php esc_attr_e( 'ابزار گفتگو', 'teznevise' ); ?>">
						<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-thinking-btn aria-pressed="false" aria-label="<?php esc_attr_e( 'باز نگه داشتن فرآیند پاسخ', 'teznevise' ); ?>" title="<?php esc_attr_e( 'فرآیند پاسخ', 'teznevise' ); ?>"><i class="fa-solid fa-list-check" aria-hidden="true"></i></button>
						<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-collab-btn aria-pressed="false" aria-label="<?php esc_attr_e( 'هم‌فکری عامل‌ها', 'teznevise' ); ?>" title="<?php esc_attr_e( 'هم‌فکری', 'teznevise' ); ?>"><i class="fa-solid fa-users" aria-hidden="true"></i></button>
						<button type="button" class="tz-gpt__iconbtn is-toggle" data-ai-research-btn aria-pressed="false" aria-label="<?php esc_attr_e( 'پژوهش وب', 'teznevise' ); ?>" title="<?php esc_attr_e( 'پژوهش', 'teznevise' ); ?>"><i class="fa-solid fa-globe" aria-hidden="true"></i></button>
						<button type="button" class="tz-gpt__iconbtn" data-ai-handoff-toggle aria-expanded="false" aria-controls="tzLiveHandoff" aria-label="<?php esc_attr_e( 'رزرو تماس', 'teznevise' ); ?>" title="<?php esc_attr_e( 'رزرو تماس', 'teznevise' ); ?>"><i class="fa-solid fa-phone" aria-hidden="true"></i></button>
					</div>
					<input type="checkbox" data-ai-thinking hidden>
					<input type="checkbox" data-ai-collab-toggle hidden>
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
