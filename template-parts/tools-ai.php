<?php
/**
 * Ask-AI panel on tools pages.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$agents = function_exists( 'teznevise_list_ai_agents' ) ? teznevise_list_ai_agents() : array();
$cost   = function_exists( 'teznevise_tezcoin_get' ) ? (int) teznevise_tezcoin_get( 'ai_cost' ) : 5;
?>
<section class="section tools-ai">
	<div class="container">
		<div class="surface-card tools-ai-card">
			<span class="eyebrow"><?php esc_html_e( 'دستیار پژوهشی', 'teznevise' ); ?></span>
			<h2><?php esc_html_e( 'از هوش مصنوعی بپرسید', 'teznevise' ); ?></h2>
			<p><?php esc_html_e( 'عامل را انتخاب کنید — روش تحقیق، آمار یا ویرایش علمی. پاسخ برای اعضای واردشده است.', 'teznevise' ); ?></p>
			<?php if ( $cost > 0 ) : ?>
				<p class="ai-cost"><?php echo esc_html( sprintf( __( 'هزینه هر پرسش: %s تزکوین', 'teznevise' ), number_format_i18n( $cost ) ) ); ?></p>
			<?php endif; ?>
			<?php if ( ! is_user_logged_in() ) : ?>
				<a class="btn-tz btn-primary-tz" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'ورود برای پرسش از هوش مصنوعی', 'teznevise' ); ?></a>
			<?php else : ?>
				<form id="tzAskAi" class="account-form">
					<?php if ( $agents ) : ?>
						<fieldset class="agent-picks">
							<legend><?php esc_html_e( 'عامل', 'teznevise' ); ?></legend>
							<?php foreach ( $agents as $i => $ag ) : ?>
								<label class="agent-chip">
									<input type="radio" name="agent" value="<?php echo (int) $ag->ID; ?>" <?php checked( 0, $i ); ?>>
									<span><?php echo esc_html( $ag->post_title ); ?></span>
								</label>
							<?php endforeach; ?>
						</fieldset>
					<?php endif; ?>
					<label><?php esc_html_e( 'سؤال', 'teznevise' ); ?>
						<textarea name="q" rows="4" required minlength="4" placeholder="<?php esc_attr_e( 'مثلاً: برای مقایسه سه گروه کدام آزمون مناسب است؟', 'teznevise' ); ?>"></textarea>
					</label>
					<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'بپرس', 'teznevise' ); ?></button>
				</form>
				<div id="tzAskOut" class="ai-out" hidden></div>
			<?php endif; ?>
		</div>
	</div>
</section>
