<?php
/**
 * Admin-defined AI agents and markdown skill prompts.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_register_ai_agents() {
	if ( post_type_exists( 'tz_agent' ) ) {
		return;
	}
	register_post_type(
		'tz_agent',
		array(
			'labels'              => array(
				'name'          => __( 'عامل‌های هوش مصنوعی', 'teznevise' ),
				'singular_name' => __( 'عامل', 'teznevise' ),
				'add_new_item'  => __( 'افزودن عامل', 'teznevise' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'supports'            => array( 'title', 'editor', 'excerpt' ),
			'menu_icon'           => 'dashicons-superhero',
			'capability_type'     => 'page',
		)
	);
}
add_action( 'init', 'teznevise_register_ai_agents' );

function teznevise_seed_default_agents() {
	if ( get_option( 'teznevise_seeded_agents' ) ) {
		return;
	}
	$defaults = array(
		array(
			'مشاور روش تحقیق',
			'You are a Persian research-methods tutor for Teznevise. Help with research design, sampling, validity, and chapter structure. Answer in Persian. Be concise and practical. Never write a thesis for the student; coach them.',
		),
		array(
			'مشاور آمار',
			'You are a Persian statistics consultant for Teznevise. Recommend tests (t, ANOVA, regression, SEM), interpret SPSS-style output, and warn about assumptions. Answer in Persian. Do not invent p-values.',
		),
		array(
			'ویراستار علمی',
			'You are a Persian academic editor. Improve clarity, structure, and citation style. Flag plagiarism risks. Answer in Persian. Do not fabricate references.',
		),
	);
	foreach ( $defaults as $row ) {
		wp_insert_post(
			array(
				'post_type'    => 'tz_agent',
				'post_status'  => 'publish',
				'post_title'   => $row[0],
				'post_content' => $row[1],
			)
		);
	}
	update_option( 'teznevise_seeded_agents', 1, false );
}
add_action( 'init', 'teznevise_seed_default_agents', 40 );

function teznevise_list_ai_agents() {
	return get_posts(
		array(
			'post_type'      => 'tz_agent',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);
}

function teznevise_agent_skill_box() {
	add_meta_box(
		'tz_agent_skill',
		__( 'مهارت (فایل Markdown)', 'teznevise' ),
		'teznevise_agent_skill_box_html',
		'tz_agent',
		'side'
	);
}
add_action( 'add_meta_boxes', 'teznevise_agent_skill_box' );

function teznevise_agent_skill_box_html( $post ) {
	wp_nonce_field( 'tz_agent_skill', 'tz_agent_skill_nonce' );
	$current = get_post_meta( $post->ID, 'tz_skill_file', true );
	echo '<p>' . esc_html__( 'محتوای نوشته همان پرامپت سیستم است. می‌توانید فایل .md بارگذاری کنید تا جایگزین شود.', 'teznevise' ) . '</p>';
	if ( $current ) {
		echo '<p><code>' . esc_html( $current ) . '</code></p>';
	}
	echo '<input type="file" name="tz_skill_md" accept=".md,.txt,text/markdown">';
}

function teznevise_agent_form_enctype() {
	global $post;
	if ( $post && 'tz_agent' === $post->post_type ) {
		echo ' enctype="multipart/form-data"';
	}
}
add_action( 'post_edit_form_tag', 'teznevise_agent_form_enctype' );

function teznevise_save_agent_skill( $post_id ) {
	if ( ! isset( $_POST['tz_agent_skill_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tz_agent_skill_nonce'] ) ), 'tz_agent_skill' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( 'tz_agent' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( empty( $_FILES['tz_skill_md']['tmp_name'] ) || ! is_uploaded_file( $_FILES['tz_skill_md']['tmp_name'] ) ) {
		return;
	}
	if ( (int) $_FILES['tz_skill_md']['size'] > 200 * 1024 ) {
		return;
	}
	$orig = sanitize_file_name( (string) $_FILES['tz_skill_md']['name'] );
	$ext  = strtolower( pathinfo( $orig, PATHINFO_EXTENSION ) );
	if ( ! in_array( $ext, array( 'md', 'txt' ), true ) ) {
		return;
	}
	$raw = file_get_contents( $_FILES['tz_skill_md']['tmp_name'] );
	if ( ! is_string( $raw ) || '' === trim( $raw ) ) {
		return;
	}
	remove_action( 'save_post_tz_agent', 'teznevise_save_agent_skill' );
	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_content' => wp_kses_post( $raw ),
		)
	);
	add_action( 'save_post_tz_agent', 'teznevise_save_agent_skill' );
	update_post_meta( $post_id, 'tz_skill_file', $orig );
}
add_action( 'save_post_tz_agent', 'teznevise_save_agent_skill' );

function teznevise_tavily_context( $q ) {
	$key = (string) teznevise_tezcoin_get( 'tavily_key' );
	if ( ! $key ) {
		return '';
	}
	$res = wp_remote_post(
		'https://api.tavily.com/search',
		array(
			'timeout' => 18,
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => wp_json_encode(
				array(
					'api_key'      => $key,
					'query'        => $q,
					'search_depth' => 'basic',
					'max_results'  => 3,
				)
			),
		)
	);
	if ( is_wp_error( $res ) ) {
		return '';
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( empty( $body['results'] ) || ! is_array( $body['results'] ) ) {
		return '';
	}
	$bits = array();
	foreach ( array_slice( $body['results'], 0, 3 ) as $row ) {
		$title = isset( $row['title'] ) ? $row['title'] : '';
		$cont  = isset( $row['content'] ) ? $row['content'] : '';
		$bits[] = trim( $title . ': ' . $cont );
	}
	return implode( "\n", $bits );
}

function teznevise_ajax_ask_ai_v2() {
	check_ajax_referer( 'teznevise_share', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'login' ) );
	}
	$q = isset( $_POST['q'] ) ? sanitize_textarea_field( wp_unslash( $_POST['q'] ) ) : '';
	if ( strlen( $q ) < 4 ) {
		wp_send_json_error( array( 'message' => 'short' ) );
	}
	$user_id = get_current_user_id();
	$cost    = (int) teznevise_tezcoin_get( 'ai_cost' );
	if ( $cost < 0 ) {
		$cost = 0;
	}
	if ( $cost > 0 && teznevise_tezcoin_balance( $user_id ) < $cost ) {
		wp_send_json_error( array( 'message' => 'no-coins' ) );
	}
	$key = (string) teznevise_tezcoin_get( 'openrouter_key' );
	if ( ! $key ) {
		wp_send_json_error( array( 'message' => 'no-key' ) );
	}
	$agent_id = isset( $_POST['agent'] ) ? absint( $_POST['agent'] ) : 0;
	$system   = 'You are a Persian research-methods tutor for Teznevise. Answer in Persian. Be concise.';
	if ( $agent_id ) {
		$agent = get_post( $agent_id );
		if ( $agent && 'tz_agent' === $agent->post_type && 'publish' === $agent->post_status && $agent->post_content ) {
			$system = wp_strip_all_tags( $agent->post_content );
		}
	}
	$context = teznevise_tavily_context( $q );
	$user_msg = $q;
	if ( $context ) {
		$user_msg = $q . "\n\nWeb context:\n" . $context;
	}
	$res = wp_remote_post(
		'https://openrouter.ai/api/v1/chat/completions',
		array(
			'timeout' => 40,
			'headers' => array(
				'Authorization' => 'Bearer ' . $key,
				'Content-Type'  => 'application/json',
				'HTTP-Referer'  => home_url( '/' ),
				'X-Title'       => 'Teznevise',
			),
			'body'    => wp_json_encode(
				array(
					'model'      => 'openai/gpt-4o-mini',
					'max_tokens' => 700,
					'messages'   => array(
						array( 'role' => 'system', 'content' => $system ),
						array( 'role' => 'user', 'content' => $user_msg ),
					),
				)
			),
		)
	);
	if ( is_wp_error( $res ) ) {
		wp_send_json_error( array( 'message' => $res->get_error_message() ) );
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	$text = isset( $body['choices'][0]['message']['content'] ) ? $body['choices'][0]['message']['content'] : '';
	if ( ! $text ) {
		wp_send_json_error( array( 'message' => 'empty' ) );
	}
	if ( $cost > 0 ) {
		teznevise_tezcoin_credit( $user_id, -$cost, 'پرسش از هوش مصنوعی', (string) $agent_id );
	}
	wp_send_json_success(
		array(
			'text'    => $text,
			'balance' => teznevise_tezcoin_balance( $user_id ),
		)
	);
}
add_action( 'wp_ajax_teznevise_ask_ai', 'teznevise_ajax_ask_ai_v2', 1 );
