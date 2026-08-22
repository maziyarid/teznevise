<?php
/**
 * Template Name: حساب کاربری
 *
 * @package Teznevise
 */
if ( ! is_user_logged_in() ) {
	auth_redirect();
}
get_header();
$user    = wp_get_current_user();
$uid     = (int) $user->ID;
$tab     = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'profile';
$tabs    = array(
	'profile'  => 'پروفایل',
	'wallet'   => 'کیف پول',
	'tools'    => 'ابزارهای من',
	'ai'       => 'گفتگوهای هوش مصنوعی',
	'tickets'  => 'درخواست‌ها',
	'projects' => 'پروژه‌ها',
	'settings' => 'تنظیمات',
);
$balance = function_exists( 'teznevise_tezcoin_balance' ) ? teznevise_tezcoin_balance( $uid ) : 0;
$ledger  = get_user_meta( $uid, 'teznevise_tezcoin_ledger', true );
$ledger  = is_array( $ledger ) ? array_reverse( $ledger ) : array();
$code    = function_exists( 'teznevise_user_ref_code' ) ? teznevise_user_ref_code( $uid ) : '';
$ref_url = add_query_arg( 'ref', $code, home_url( '/' ) );
$pay     = isset( $_GET['pay'] ) ? sanitize_key( wp_unslash( $_GET['pay'] ) ) : '';
?>
<section class="section account-shell tz-dash">
	<div class="container tz-dash__grid">
		<aside class="tz-dash__nav" aria-label="<?php esc_attr_e( 'ناوبری حساب', 'teznevise' ); ?>">
			<div class="tz-dash__avatar">
				<?php echo get_avatar( $uid, 72, '', $user->display_name ); ?>
				<strong><?php echo esc_html( $user->display_name ); ?></strong>
				<span><?php echo esc_html( number_format_i18n( $balance ) ); ?> تزکوین</span>
			</div>
			<nav class="account-tabs">
				<?php foreach ( $tabs as $key => $label ) : ?>
					<a class="<?php echo $tab === $key ? 'is-on' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'tab', $key, home_url( '/account/' ) ) ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>
		</aside>
		<div class="tz-dash__main">
		<header class="account-head">
			<span class="eyebrow"><?php esc_html_e( 'پنل کاربری', 'teznevise' ); ?></span>
			<h1><?php echo esc_html( $user->display_name ); ?></h1>
			<p class="account-balance"><i class="fa-solid fa-coins" aria-hidden="true"></i> <?php echo esc_html( number_format_i18n( $balance ) ); ?> تزکوین</p>
		</header>
		<?php if ( 'ok' === $pay ) : ?>
			<p class="account-flash"><?php esc_html_e( 'پرداخت تأیید شد و تزکوین به حساب نشست.', 'teznevise' ); ?></p>
		<?php elseif ( 'fail' === $pay ) : ?>
			<p class="account-flash is-warn"><?php esc_html_e( 'پرداخت تأیید نشد.', 'teznevise' ); ?></p>
		<?php elseif ( 'need-gateway' === $pay ) : ?>
			<p class="account-flash is-warn"><?php esc_html_e( 'مرچنت زرین‌پال یا پین آقای پرداخت در تنظیمات تزکوین وارد نشده است.', 'teznevise' ); ?></p>
		<?php elseif ( ! empty( $_GET['saved'] ) ) : ?>
			<p class="account-flash"><?php esc_html_e( 'پروفایل ذخیره شد. اگر کامل باشد ۱۰۰۰ تزکوین هدیه اعمال می‌شود.', 'teznevise' ); ?></p>
		<?php endif; ?>

		<div class="tz-dash__panel">

		<?php if ( 'wallet' === $tab ) : ?>
			<div class="account-grid">
				<section class="surface-card">
					<h2><?php esc_html_e( 'خرید تزکوین', 'teznevise' ); ?></h2>
					<form method="post">
						<?php wp_nonce_field( 'teznevise_account', '_tz_acc' ); ?>
						<input type="hidden" name="teznevise_account_action" value="buy">
						<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
							<label class="pack-row">
								<input type="radio" name="pack" value="<?php echo (int) $i; ?>" <?php checked( 1, $i ); ?>>
								<span><?php echo esc_html( number_format_i18n( (int) teznevise_tezcoin_get( 'pack_' . $i . '_coins' ) ) ); ?> تزکوین — <?php echo esc_html( number_format_i18n( (int) teznevise_tezcoin_get( 'pack_' . $i . '_irr' ) ) ); ?> ریال</span>
							</label>
						<?php endfor; ?>
						<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'پرداخت زرین‌پال / آقای پرداخت', 'teznevise' ); ?></button>
					</form>
				</section>
				<section class="surface-card">
					<h2><?php esc_html_e( 'معرفی دوستان', 'teznevise' ); ?></h2>
					<p><?php esc_html_e( 'لینک دعوت:', 'teznevise' ); ?></p>
					<input class="ltr-field" dir="ltr" readonly value="<?php echo esc_attr( $ref_url ); ?>">
					<form method="post">
						<?php wp_nonce_field( 'teznevise_account', '_tz_acc' ); ?>
						<input type="hidden" name="teznevise_account_action" value="referral">
						<label><?php esc_html_e( 'کد معرف', 'teznevise' ); ?>
							<input name="ref_code" dir="ltr" maxlength="12">
						</label>
						<button class="btn-tz btn-light-tz" type="submit"><?php esc_html_e( 'ثبت کد', 'teznevise' ); ?></button>
					</form>
				</section>
			</div>
			<section class="surface-card">
				<h2><?php esc_html_e( 'دفتر حساب', 'teznevise' ); ?></h2>
				<?php if ( ! $ledger ) : ?>
					<p><?php esc_html_e( 'هنوز تراکنشی نیست.', 'teznevise' ); ?></p>
				<?php else : ?>
					<ul class="ledger">
						<?php foreach ( array_slice( $ledger, 0, 20 ) as $row ) : ?>
							<li>
								<b><?php echo ( (int) $row['amount'] > 0 ? '+' : '' ) . esc_html( number_format_i18n( (int) $row['amount'] ) ); ?></b>
								<span><?php echo esc_html( $row['reason'] ); ?></span>
								<small><?php echo esc_html( date_i18n( 'Y/m/d H:i', (int) $row['time'] ) ); ?></small>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php if ( 'profile' === $tab ) : ?>
			<form class="surface-card account-form" method="post">
				<?php wp_nonce_field( 'teznevise_account', '_tz_acc' ); ?>
				<input type="hidden" name="teznevise_account_action" value="profile">
				<label><?php esc_html_e( 'نام', 'teznevise' ); ?><input name="first_name" value="<?php echo esc_attr( $user->first_name ? $user->first_name : $user->display_name ); ?>" required></label>
				<label><?php esc_html_e( 'نام خانوادگی', 'teznevise' ); ?><input name="last_name" value="<?php echo esc_attr( $user->last_name ); ?>"></label>
				<label><?php esc_html_e( 'موبایل', 'teznevise' ); ?><input name="teznevise_phone" dir="ltr" value="<?php echo esc_attr( get_user_meta( $uid, 'teznevise_phone', true ) ); ?>" required></label>
				<label><?php esc_html_e( 'دانشگاه', 'teznevise' ); ?><input name="teznevise_university" value="<?php echo esc_attr( get_user_meta( $uid, 'teznevise_university', true ) ); ?>" required></label>
				<label><?php esc_html_e( 'رشته', 'teznevise' ); ?><input name="teznevise_field" value="<?php echo esc_attr( get_user_meta( $uid, 'teznevise_field', true ) ); ?>" required></label>
				<label><?php esc_html_e( 'مقطع', 'teznevise' ); ?>
					<?php $deg = get_user_meta( $uid, 'teznevise_degree', true ); ?>
					<select name="teznevise_degree" required>
						<option value=""><?php esc_html_e( 'انتخاب کنید', 'teznevise' ); ?></option>
						<?php foreach ( array( 'کارشناسی', 'کارشناسی ارشد', 'دکتری', 'پسادکتری' ) as $opt ) : ?>
							<option <?php selected( $deg, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label><?php esc_html_e( 'شهر', 'teznevise' ); ?><input name="teznevise_city" value="<?php echo esc_attr( get_user_meta( $uid, 'teznevise_city', true ) ); ?>"></label>
				<label><?php esc_html_e( 'ORCID', 'teznevise' ); ?><input name="teznevise_orcid" dir="ltr" value="<?php echo esc_attr( get_user_meta( $uid, 'teznevise_orcid', true ) ); ?>"></label>
				<label><?php esc_html_e( 'تلگرام', 'teznevise' ); ?><input name="teznevise_telegram" dir="ltr" value="<?php echo esc_attr( get_user_meta( $uid, 'teznevise_telegram', true ) ); ?>"></label>
				<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'ذخیره و دریافت هدیه پروفایل', 'teznevise' ); ?></button>
			</form>
		<?php endif; ?>

		<?php if ( 'tickets' === $tab ) : ?>
			<form class="surface-card account-form" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'teznevise_account', '_tz_acc' ); ?>
				<input type="hidden" name="teznevise_account_action" value="ticket">
				<label><?php esc_html_e( 'موضوع', 'teznevise' ); ?><input name="subject" required minlength="4"></label>
				<label><?php esc_html_e( 'پیام', 'teznevise' ); ?><textarea name="body" rows="4" required minlength="4"></textarea></label>
				<label><?php esc_html_e( 'پیوست امن (حداکثر ۲ مگ؛ خارج از پوشه عمومی)', 'teznevise' ); ?><input type="file" name="vault" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.zip"></label>
				<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'ارسال تیکت', 'teznevise' ); ?></button>
			</form>
			<ul class="ticket-list">
				<?php
				$tickets = get_posts(
					array(
						'post_type'      => 'tz_ticket',
						'author'         => $uid,
						'post_status'    => array( 'private', 'publish' ),
						'posts_per_page' => 20,
					)
				);
				foreach ( $tickets as $t ) :
					$files = get_post_meta( $t->ID, 'tz_vault' );
					?>
					<li class="surface-card">
						<b><?php echo esc_html( $t->post_title ); ?></b>
						<p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $t->post_content ), 28 ) ); ?></p>
						<?php foreach ( $files as $f ) : ?>
							<?php if ( is_array( $f ) && ! empty( $f['file'] ) ) : ?>
								<a href="<?php echo esc_url( add_query_arg( array( 'tz_vault' => $t->ID, 'f' => $f['file'] ), home_url( '/' ) ) ); ?>"><?php echo esc_html( $f['orig'] ); ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php
						$replies = get_comments(
							array(
								'post_id' => $t->ID,
								'status'  => 'approve',
								'number'  => 8,
							)
						);
						if ( $replies ) :
							echo '<ul class="ticket-replies">';
							foreach ( $replies as $c ) {
								echo '<li><b>' . esc_html( $c->comment_author ) . ':</b> ' . esc_html( wp_trim_words( wp_strip_all_tags( $c->comment_content ), 40 ) ) . '</li>';
							}
							echo '</ul>';
						endif;
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( 'projects' === $tab ) : ?>
			<form class="surface-card account-form" method="post">
				<?php wp_nonce_field( 'teznevise_account', '_tz_acc' ); ?>
				<input type="hidden" name="teznevise_account_action" value="project">
				<label><?php esc_html_e( 'عنوان پروژه', 'teznevise' ); ?><input name="project_title" required minlength="4"></label>
				<label><?php esc_html_e( 'خدمت', 'teznevise' ); ?>
					<select name="project_service">
						<option>پایان‌نامه</option><option>پروپوزال</option><option>تحلیل آماری</option><option>شبیه‌سازی</option>
					</select>
				</label>
				<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'ثبت پروژه', 'teznevise' ); ?></button>
			</form>
			<div class="account-grid">
				<?php
				$projects = get_posts(
					array(
						'post_type'      => 'tz_project',
						'author'         => $uid,
						'post_status'    => array( 'private', 'publish' ),
						'posts_per_page' => 12,
					)
				);
				foreach ( $projects as $p ) :
					$st = get_post_meta( $p->ID, 'tz_status', true );
					$pr = (int) get_post_meta( $p->ID, 'tz_progress', true );
					?>
					<article class="surface-card">
						<h3><?php echo esc_html( $p->post_title ); ?></h3>
						<p><?php echo esc_html( $p->post_content ); ?> — <?php echo esc_html( teznevise_project_status_label( $st ) ); ?></p>
						<div class="prog"><span style="width:<?php echo (int) $pr; ?>%"></span></div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( 'tools' === $tab ) : ?>
			<section class="surface-card">
				<h2><?php esc_html_e( 'ابزارهای من', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'محاسبه‌گرهای آماری و گفتگوی پژوهشی در یک صفحه.', 'teznevise' ); ?></p>
				<p><a class="btn-tz btn-primary-tz" href="<?php echo esc_url( home_url( '/online-calculation-tools/' ) ); ?>"><?php esc_html_e( 'رفتن به ابزارها', 'teznevise' ); ?></a></p>
			</section>
		<?php endif; ?>

		<?php if ( 'settings' === $tab ) : ?>
			<form class="surface-card account-form" method="post">
				<?php wp_nonce_field( 'teznevise_account', '_tz_acc' ); ?>
				<input type="hidden" name="teznevise_account_action" value="settings">
				<label><?php esc_html_e( 'نام نمایشی', 'teznevise' ); ?><input name="display_name" value="<?php echo esc_attr( $user->display_name ); ?>" required></label>
				<label><?php esc_html_e( 'ایمیل', 'teznevise' ); ?><input type="email" value="<?php echo esc_attr( $user->user_email ); ?>" readonly></label>
				<label><?php esc_html_e( 'تلگرام', 'teznevise' ); ?><input name="teznevise_telegram" dir="ltr" value="<?php echo esc_attr( get_user_meta( $uid, 'teznevise_telegram', true ) ); ?>"></label>
				<button class="btn-tz btn-primary-tz" type="submit"><?php esc_html_e( 'ذخیره تنظیمات', 'teznevise' ); ?></button>
			</form>
			<p><a class="btn-tz btn-light-tz" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'تغییر گذرواژه', 'teznevise' ); ?></a></p>
		<?php endif; ?>

		<?php if ( 'ai' === $tab ) : ?>
			<section class="surface-card account-ai">
				<h2><?php esc_html_e( 'تاریخچه گفتگو', 'teznevise' ); ?></h2>
				<p><?php esc_html_e( 'پیام‌های ابزارهای آنلاین برای حساب شما ذخیره می‌شود.', 'teznevise' ); ?></p>
				<?php
				$history = array();
				if ( class_exists( 'TezNevise_AI_API' ) && is_user_logged_in() ) {
					$req = new WP_REST_Request( 'GET', '/teznevise-ai/v1/chat/history' );
					$res = TezNevise_AI_API::get_history( $req );
					if ( ! is_wp_error( $res ) && ! empty( $res['messages'] ) ) {
						$history = $res['messages'];
					}
				}
				if ( ! $history ) :
					?>
					<p><?php esc_html_e( 'هنوز گفتگویی ثبت نشده است.', 'teznevise' ); ?></p>
				<?php else : ?>
					<ol class="account-ai-log">
						<?php foreach ( $history as $row ) : ?>
							<li class="tz-ai-msg is-<?php echo esc_attr( $row['role'] ); ?>">
								<div class="tz-ai-msg__bubble"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $row['content'] ), 80 ) ); ?></div>
								<footer class="tz-ai-msg__name"><?php echo esc_html( $row['agent_name'] ? $row['agent_name'] : $row['role'] ); ?> · <?php echo esc_html( $row['tool_id'] ); ?></footer>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</section>
		<?php endif; ?>
		</div>
		</div>
	</div>
</section>
<?php
get_footer();
