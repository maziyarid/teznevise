<?php
/**
 * Two-tab comments: human readers vs curated AI discussion.
 *
 * The discussion lives in post meta `_teznevise_ai_discussion` (JSON) plus
 * `_teznevise_ai_research` (You.com brief). WordPress comments with type
 * `tz_ai` mirror the thread for moderation and schema.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function teznevise_ai_comment_defaults() {
	$speakers = array();
	$order    = 1;
	if ( function_exists( 'teznevise_core_agent_roster' ) ) {
		foreach ( teznevise_core_agent_roster() as $id => $row ) {
			$speakers[] = array(
				'name'   => $row['name'],
				'slug'   => $id,
				'role'   => $row['role'],
				'tags'   => $row['role'],
				'prompt' => $row['system_prompt'],
				'color'  => $row['color'],
				'order'  => $order,
				'active' => 1,
			);
			++$order;
		}
	}
	if ( ! $speakers ) {
		$speakers = array(
			array(
				'name'   => 'Teznevise',
				'slug'   => 'teznevise',
				'role'   => 'synthesizer',
				'tags'   => 'ترکیب',
				'prompt' => 'Synthesize the panel in Persian.',
				'color'  => '#145d4a',
				'order'  => 1,
				'active' => 1,
			),
		);
	}
	return array(
		'enabled'           => '1',
		'auto_on_publish'   => '1',
		'interaction'       => 'named_roster',
		'max_turns'         => 8,
		'model'             => '',
		'agent_id'          => 'teznevise',
		'discussion_prompt' => 'You are a named Teznevise consulting panel. Be specific, cite the post and the research brief, disagree politely, and never ghostwrite. Never invent sources.',
		'speakers'          => $speakers,
	);
}

function teznevise_ai_comment_settings() {
	$stored   = get_option( 'teznevise_ai_comments', array() );
	$stored   = is_array( $stored ) ? $stored : array();
	$defaults = teznevise_ai_comment_defaults();
	$out      = array_merge( $defaults, $stored );
	if ( ! array_key_exists( 'enabled', $stored ) ) {
		$out['enabled'] = '1';
	}
	if ( ! array_key_exists( 'auto_on_publish', $stored ) ) {
		$out['auto_on_publish'] = '1';
	}
	$slugs    = array();
	foreach ( (array) ( $out['speakers'] ?? array() ) as $row ) {
		$slugs[] = $row['slug'] ?? '';
	}
	$legacy = array( 'ava-method', 'parsa-stats', 'nika-writing' );
	if ( $slugs === $legacy || ! array_intersect( $slugs, function_exists( 'teznevise_core_named_ids' ) ? teznevise_core_named_ids() : array() ) ) {
		$out['speakers'] = $defaults['speakers'];
		$out['agent_id'] = 'teznevise';
		$out['interaction'] = 'named_roster';
	}
	if ( empty( $out['speakers'] ) || ! is_array( $out['speakers'] ) ) {
		$out['speakers'] = $defaults['speakers'];
	}
	foreach ( $out['speakers'] as $i => $row ) {
		if ( empty( $row['color'] ) ) {
			$out['speakers'][ $i ]['color'] = teznevise_commenter_color_from_key( $row['slug'] ?? $row['name'] ?? (string) $i );
		}
	}
	usort(
		$out['speakers'],
		static function ( $a, $b ) {
			return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 );
		}
	);
	return $out;
}

function teznevise_commenter_color_palette() {
	return array( '#0f766e', '#1d4ed8', '#7c3aed', '#b45309', '#be123c', '#0369a1', '#145d4a', '#9333ea', '#0e7490', '#a16207' );
}

function teznevise_commenter_color_from_key( $key ) {
	$palette = teznevise_commenter_color_palette();
	$index   = abs( crc32( (string) $key ) ) % count( $palette );
	return $palette[ $index ];
}

function teznevise_commenter_color_for_comment( $comment ) {
	$comment = get_comment( $comment );
	if ( ! $comment ) {
		return '#145d4a';
	}
	$stored = (string) get_comment_meta( $comment->comment_ID, 'tz_ai_color', true );
	if ( $stored && preg_match( '/^#[0-9A-Fa-f]{6}$/', $stored ) ) {
		return $stored;
	}
	$key = $comment->comment_author_email ? $comment->comment_author_email : $comment->comment_author;
	return teznevise_commenter_color_from_key( $key );
}

function teznevise_ai_discussion_get( $post_id ) {
	$raw = get_post_meta( (int) $post_id, '_teznevise_ai_discussion', true );
	if ( is_array( $raw ) ) {
		$decoded = $raw;
	} elseif ( is_string( $raw ) && '' !== $raw ) {
		$decoded = json_decode( $raw, true );
	} else {
		$decoded = null;
	}
	if ( ! is_array( $decoded ) ) {
		$decoded = array();
	}
	$items = isset( $decoded['items'] ) && is_array( $decoded['items'] ) ? $decoded['items'] : ( isset( $decoded[0] ) ? $decoded : array() );
	if ( ! $items ) {
		$comments = get_comments(
			array(
				'post_id' => (int) $post_id,
				'type'    => 'tz_ai',
				'status'  => 'approve',
				'orderby' => 'comment_date_gmt',
				'order'   => 'ASC',
				'number'  => 40,
			)
		);
		foreach ( (array) $comments as $c ) {
			$items[] = array(
				'id'      => (string) $c->comment_ID,
				'parent'  => (string) $c->comment_parent,
				'name'    => $c->comment_author,
				'slug'    => (string) get_comment_meta( $c->comment_ID, 'tz_ai_slug', true ),
				'role'    => (string) get_comment_meta( $c->comment_ID, 'tz_ai_role', true ),
				'tags'    => (string) get_comment_meta( $c->comment_ID, 'tz_ai_tags', true ),
				'color'   => teznevise_commenter_color_for_comment( $c ),
				'content' => $c->comment_content,
				'thought' => (string) get_comment_meta( $c->comment_ID, 'tz_ai_thought', true ),
				'human'   => (bool) get_comment_meta( $c->comment_ID, 'tz_human_moderator', true ),
				'avatar'  => (string) get_comment_meta( $c->comment_ID, 'tz_ai_avatar', true ),
			);
		}
	}
	$research_meta = get_post_meta( (int) $post_id, '_teznevise_ai_research', true );
	if ( is_array( $research_meta ) ) {
		$research_meta = (string) ( $research_meta['brief'] ?? '' );
	}
	return array(
		'research' => isset( $decoded['research'] ) ? (string) $decoded['research'] : (string) $research_meta,
		'items'    => $items,
	);
}

function teznevise_ai_discussion_save( $post_id, $thread ) {
	$payload = array(
		'research' => isset( $thread['research'] ) ? (string) $thread['research'] : '',
		'items'    => isset( $thread['items'] ) && is_array( $thread['items'] ) ? $thread['items'] : array(),
	);
	update_post_meta( (int) $post_id, '_teznevise_ai_discussion', wp_slash( wp_json_encode( $payload, JSON_UNESCAPED_UNICODE ) ) );
	if ( $payload['research'] ) {
		update_post_meta( (int) $post_id, '_teznevise_ai_research', $payload['research'] );
	}
}

function teznevise_render_ai_discussion_thread( $post_id ) {
	$thread = teznevise_ai_discussion_get( $post_id );
	$items  = $thread['items'];
	if ( ! $items ) {
		echo '<p>' . esc_html__( 'هنوز گفتگویی تولید نشده است.', 'teznevise' ) . '</p>';
		return;
	}
	$by_parent = array();
	foreach ( $items as $item ) {
		$parent = (string) ( $item['parent'] ?? '0' );
		if ( '' === $parent ) {
			$parent = '0';
		}
		$by_parent[ $parent ][] = $item;
	}
	echo '<ol class="comment-list tz-ai-thread tz-thread">';
	teznevise_render_ai_discussion_branch( $by_parent, '0' );
	echo '</ol>';
}

function teznevise_render_ai_discussion_branch( $by_parent, $parent_id ) {
	if ( empty( $by_parent[ $parent_id ] ) ) {
		return;
	}
	foreach ( $by_parent[ $parent_id ] as $item ) {
		$id    = (string) ( $item['id'] ?? uniqid( 'ai', false ) );
		$color = ! empty( $item['color'] ) && preg_match( '/^#[0-9A-Fa-f]{6}$/', $item['color'] ) ? $item['color'] : teznevise_commenter_color_from_key( $item['name'] ?? $id );
		$human = ! empty( $item['human'] );
		echo '<li id="ai-comment-' . esc_attr( $id ) . '" class="tz-ai-comment tz-thread-item' . ( $human ? ' is-human' : '' ) . '" style="--tz-commenter:' . esc_attr( $color ) . '">';
		echo '<article>';
		echo '<header class="comment-author tz-thread-item__meta">';
		$avatar = (string) ( $item['avatar'] ?? '' );
		$slug   = sanitize_key( $item['slug'] ?? '' );
		if ( ! $avatar && $slug && function_exists( 'teznevise_core_agent_logo_url' ) ) {
			$avatar = teznevise_core_agent_logo_url( $slug );
		}
		if ( $avatar ) {
			echo '<img class="tz-agent-mark" src="' . esc_url( $avatar ) . '" width="36" height="36" alt="' . esc_attr( $item['name'] ?? '' ) . '" title="' . esc_attr( $item['name'] ?? '' ) . '" />';
		}
		echo '<strong class="tz-thread-item__name">' . esc_html( $item['name'] ?? '' ) . '</strong>';
		if ( ! empty( $item['role'] ) ) {
			echo ' <span class="tz-ai-role tz-thread-item__role">' . esc_html( $item['role'] ) . '</span>';
		}
		echo '</header>';
		echo '<div class="comment-content">' . wp_kses_post( wpautop( (string) ( $item['content'] ?? '' ) ) ) . '</div>';
		if ( ! empty( $item['thought'] ) ) {
			echo '<details class="tz-ai-think"><summary class="tz-ai-think__sum"><i class="fa-solid fa-lightbulb" aria-hidden="true"></i> ' . esc_html__( 'استدلال', 'teznevise' ) . '</summary><pre class="tz-ai-think__stream">' . esc_html( $item['thought'] ) . '</pre></details>';
		}
		if ( ! empty( $item['tags'] ) ) {
			echo '<p class="tz-ai-tags">' . esc_html( $item['tags'] ) . '</p>';
		}
		echo '</article>';
		if ( ! empty( $by_parent[ $id ] ) ) {
			echo '<ol class="tz-thread children">';
			teznevise_render_ai_discussion_branch( $by_parent, $id );
			echo '</ol>';
		}
		echo '</li>';
	}
}

function teznevise_ai_comments_admin_menu() {
	add_submenu_page(
		'edit.php',
		__( 'گفتگوی هوش مصنوعی', 'teznevise' ),
		__( 'گفتگوی هوش مصنوعی', 'teznevise' ),
		'manage_options',
		'teznevise-ai-comments',
		'teznevise_ai_comments_render_settings'
	);
}
add_action( 'admin_menu', 'teznevise_ai_comments_admin_menu' );

function teznevise_ai_comments_register_meta_box() {
	add_meta_box(
		'teznevise_ai_discussion',
		__( 'گفتگوی هوش مصنوعی این مطلب', 'teznevise' ),
		'teznevise_ai_comments_render_meta_box',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_post', 'teznevise_ai_comments_register_meta_box' );

function teznevise_ai_comments_render_meta_box( $post ) {
	wp_nonce_field( 'teznevise_ai_discuss', '_tz_ai_discuss' );
	$thread   = teznevise_ai_discussion_get( $post->ID );
	$research_raw = get_post_meta( $post->ID, '_teznevise_ai_research', true );
	if ( is_array( $research_raw ) ) {
		$research = (string) ( $research_raw['brief'] ?? '' );
	} else {
		$research = (string) $research_raw;
	}
	if ( ! $research && ! empty( $thread['research'] ) ) {
		$research = (string) $thread['research'];
	}
	$settings = teznevise_ai_comment_settings();
	echo '<p>' . esc_html__( 'گفتگو در فیلد سفارشی همین مطلب ذخیره می‌شود. You اول پژوهش می‌کند؛ بعد عامل‌ها متن مقاله + پژوهش را می‌خوانند و شاخه‌به‌شاخه جواب می‌دهند.', 'teznevise' ) . '</p>';
	echo '<p><label><input type="checkbox" name="teznevise_ai_generate" value="1" /> ' . esc_html__( 'با ذخیره مطلب، پژوهش You و گفتگوی عامل‌ها تولید/نو شود', 'teznevise' ) . '</label></p>';
	if ( $research ) {
		echo '<details open><summary>' . esc_html__( 'پژوهش You', 'teznevise' ) . '</summary><textarea class="widefat" rows="8" name="teznevise_ai_research">' . esc_textarea( $research ) . '</textarea></details>';
	} else {
		echo '<p class="description">' . esc_html__( 'هنوز پژوهشی ذخیره نشده.', 'teznevise' ) . '</p>';
		echo '<textarea class="widefat" rows="4" name="teznevise_ai_research" placeholder="' . esc_attr__( 'پس از تولید، خلاصه پژوهش You اینجا می‌آید.', 'teznevise' ) . '"></textarea>';
	}
	echo '<details><summary>' . esc_html__( 'رشته گفتگو (JSON)', 'teznevise' ) . '</summary>';
	echo '<textarea class="widefat" rows="10" name="teznevise_ai_discussion" dir="ltr">' . esc_textarea( wp_json_encode( $thread, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) ) . '</textarea></details>';
	if ( empty( $settings['enabled'] ) ) {
		echo '<p>' . esc_html__( 'گفتگوی هوش مصنوعی در تنظیمات خاموش است.', 'teznevise' ) . '</p>';
	}
}

function teznevise_ai_comments_maybe_generate( $post_id, $post, $update ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || 'post' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['_tz_ai_discuss'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_ai_discuss'] ) ), 'teznevise_ai_discuss' ) ) {
		if ( isset( $_POST['teznevise_ai_research'] ) ) {
			$brief    = sanitize_textarea_field( wp_unslash( $_POST['teznevise_ai_research'] ) );
			$existing = get_post_meta( $post_id, '_teznevise_ai_research', true );
			if ( is_array( $existing ) ) {
				$existing['brief'] = $brief;
				update_post_meta( $post_id, '_teznevise_ai_research', $existing );
			} else {
				update_post_meta( $post_id, '_teznevise_ai_research', $brief );
			}
		}
		if ( isset( $_POST['teznevise_ai_discussion'] ) && ! isset( $_POST['teznevise_ai_generate'] ) ) {
			$raw = json_decode( wp_unslash( $_POST['teznevise_ai_discussion'] ), true );
			if ( is_array( $raw ) ) {
				teznevise_ai_discussion_save( $post_id, $raw );
			}
		}
	}
	$settings = teznevise_ai_comment_settings();
	$clicked  = isset( $_POST['teznevise_ai_generate'] ) && isset( $_POST['_tz_ai_discuss'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_ai_discuss'] ) ), 'teznevise_ai_discuss' );
	$auto     = ! empty( $settings['auto_on_publish'] ) && ! $update && 'publish' === $post->post_status;
	if ( $clicked || $auto ) {
		if ( class_exists( 'Teznevise_Debate_Orchestrator' ) ) {
			Teznevise_Debate_Orchestrator::schedule( $post_id, true );
		} else {
			teznevise_ai_comments_generate( $post_id );
		}
	}
}
add_action( 'save_post_post', 'teznevise_ai_comments_maybe_generate', 30, 3 );

function teznevise_ai_comment_count( $post_id ) {
	$thread = teznevise_ai_discussion_get( $post_id );
	if ( ! empty( $thread['items'] ) ) {
		return count( $thread['items'] );
	}
	$n = get_comments(
		array(
			'post_id' => (int) $post_id,
			'type'    => 'tz_ai',
			'count'   => true,
			'status'  => 'approve',
		)
	);
	return (int) $n;
}

function teznevise_ai_comments_generate( $post_id ) {
	$settings = teznevise_ai_comment_settings();
	if ( empty( $settings['enabled'] ) ) {
		return 0;
	}
	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return 0;
	}
	$speakers = array_values(
		array_filter(
			(array) $settings['speakers'],
			static function ( $row ) {
				return ! empty( $row['active'] ) && ! empty( $row['name'] );
			}
		)
	);
	if ( ! $speakers ) {
		return 0;
	}

	$old_ids = get_comments(
		array(
			'post_id' => (int) $post_id,
			'type'    => 'tz_ai',
			'status'  => 'all',
			'fields'  => 'ids',
			'number'  => 80,
		)
	);
	foreach ( (array) $old_ids as $old_id ) {
		wp_delete_comment( (int) $old_id, true );
	}

	$article = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 900, '' );
	if ( '' === $article ) {
		$article = (string) $post->post_excerpt;
	}

	$research = '';
	if ( class_exists( 'TezNevise_AI_API' ) && method_exists( 'TezNevise_AI_API', 'research' ) ) {
		$query = $post->post_title . "\n\n" . $article;
		$found = TezNevise_AI_API::research( $query );
		if ( ! is_wp_error( $found ) && ! empty( $found['content'] ) ) {
			$research = (string) $found['content'];
		} elseif ( is_wp_error( $found ) ) {
			$research = 'You research unavailable: ' . $found->get_error_message();
		}
	}
	update_post_meta( $post_id, '_teznevise_ai_research', $research );

	$prior   = '';
	$turns   = max( 1, min( 8, (int) $settings['max_turns'] ) );
	$created = 0;
	$parent  = 0;
	$items   = array();
	$mode    = (string) $settings['interaction'];

	for ( $i = 0; $i < $turns; $i++ ) {
		$speaker = $speakers[ $i % count( $speakers ) ];
		$color   = ! empty( $speaker['color'] ) ? $speaker['color'] : teznevise_commenter_color_from_key( $speaker['slug'] ?? $speaker['name'] );
		$prompt  = trim( (string) $settings['discussion_prompt'] );
		$prompt .= "\n\nSpeaker: " . $speaker['name'] . "\nRole: " . ( $speaker['role'] ?? '' ) . "\nSpeaker instructions: " . ( $speaker['prompt'] ?? '' );
		$prompt .= "\nYou MUST ground every claim in the article body and the You research brief. Do not invent citations.";
		if ( $research ) {
			$prompt .= "\n\nYou.com research brief:\n" . $research;
		}
		if ( $prior ) {
			$prompt .= "\n\nPrevious panel remarks:\n" . $prior;
		}
		$message  = 'Article title: ' . $post->post_title . "\n\nFull article:\n" . $article . "\n\nWrite the next discussion comment in Persian (150–220 words). Reply to the previous speaker when one exists. Do not repeat earlier speakers.";
		$body     = '';
		if ( class_exists( 'TezNevise_AI_API' ) && class_exists( 'TezNevise_AI_Database' ) ) {
			$agent = TezNevise_AI_Database::get_agent( $settings['agent_id'] ?: 'general' );
			if ( ! $agent ) {
				$agent = TezNevise_AI_Database::get_agent( 'general' );
			}
			if ( $agent && method_exists( 'TezNevise_AI_API', 'complete' ) ) {
				$model    = $settings['model'] ?: ( $agent['model'] ?? 'gpt-4o-mini' );
				$response = TezNevise_AI_API::complete( $message, $prompt, $agent, $model, false );
				if ( ! is_wp_error( $response ) && ! empty( $response['content'] ) ) {
					$body = $response['content'];
				}
			}
		}
		if ( '' === $body ) {
			continue;
		}
		$parsed = class_exists( 'Teznevise_Debate_Orchestrator' ) ? Teznevise_Debate_Orchestrator::split_thought( $body ) : array( 'public' => $body, 'thought' => '' );
		$body   = $parsed['public'];

		$comment_parent = 0;
		if ( $i > 0 ) {
			$comment_parent = ( 'build' === $mode || 'debate' === $mode || 'round_robin' === $mode ) ? $parent : 0;
		}

		$comment_id = wp_insert_comment(
			array(
				'comment_post_ID'      => (int) $post_id,
				'comment_author'       => $speaker['name'],
				'comment_author_email' => sanitize_key( $speaker['slug'] ?? $speaker['name'] ) . '@ai.teznevise.ir',
				'comment_author_url'   => home_url( '/' ),
				'comment_content'      => wp_kses_post( $body ),
				'comment_type'         => 'tz_ai',
				'comment_parent'       => $comment_parent,
				'comment_approved'     => 1,
				'user_id'              => 0,
			)
		);
		if ( $comment_id ) {
			update_comment_meta( $comment_id, 'tz_ai_slug', sanitize_title( $speaker['slug'] ?? $speaker['name'] ) );
			update_comment_meta( $comment_id, 'tz_ai_role', sanitize_text_field( $speaker['role'] ?? '' ) );
			update_comment_meta( $comment_id, 'tz_ai_tags', sanitize_text_field( $speaker['tags'] ?? '' ) );
			update_comment_meta( $comment_id, 'tz_ai_name', sanitize_text_field( $speaker['name'] ) );
			update_comment_meta( $comment_id, 'tz_ai_color', sanitize_hex_color( $color ) ?: '#145d4a' );
			update_comment_meta( $comment_id, '_is_ai_agent', '1' );
			if ( ! empty( $parsed['thought'] ) ) {
				update_comment_meta( $comment_id, 'tz_ai_thought', $parsed['thought'] );
			}
			$items[] = array(
				'id'      => (string) $comment_id,
				'parent'  => (string) $comment_parent,
				'name'    => $speaker['name'],
				'slug'    => sanitize_title( $speaker['slug'] ?? $speaker['name'] ),
				'role'    => $speaker['role'] ?? '',
				'tags'    => $speaker['tags'] ?? '',
				'color'   => sanitize_hex_color( $color ) ?: '#145d4a',
				'content' => $body,
				'thought' => $parsed['thought'] ?? '',
				'human'   => false,
			);
			$parent = (int) $comment_id;
			$prior .= "\n- " . $speaker['name'] . ': ' . wp_strip_all_tags( $body );
			++$created;
		}
	}

	teznevise_ai_discussion_save(
		$post_id,
		array(
			'research' => $research,
			'items'    => $items,
		)
	);
	return $created;
}

function teznevise_ai_comments_placeholder( $speaker, $post, $index, $article = '' ) {
	$openers = array(
		'نکته اصلی این مطلب برای پژوهشگر این است که ادعا را به روش و داده گره بزند، نه به شعار.',
		'از نگاه آماری، مقاله وقتی قوی است که واحد تحلیل، حجم نمونه و آزمون را شفاف بگوید.',
		'ساختار نگارش اینجا می‌تواند الگوی فصل مرور ادبیات باشد؛ فقط ارجاع‌ها باید دقیق بمانند.',
	);
	$lead    = $openers[ $index % count( $openers ) ];
	$snippet = $article ? wp_trim_words( $article, 28, '…' ) : get_the_title( $post );
	return '<p><strong>' . esc_html( $speaker['name'] ) . '</strong> — ' . esc_html( $lead ) . ' با تکیه بر متن «' . esc_html( $snippet ) . '» از زاویه ' . esc_html( $speaker['role'] ?? 'پژوهش' ) . ' می‌خوانم. کلیدواژه‌ها: ' . esc_html( $speaker['tags'] ?? '' ) . '.</p>';
}

function teznevise_ai_comments_render_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['teznevise_ai_comments_save'] ) && isset( $_POST['_tz_ai_c'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_ai_c'] ) ), 'teznevise_ai_comments' ) ) {
		$speakers = array();
		$names    = isset( $_POST['speaker_name'] ) ? (array) wp_unslash( $_POST['speaker_name'] ) : array();
		foreach ( $names as $i => $name ) {
			$name = sanitize_text_field( $name );
			if ( '' === $name ) {
				continue;
			}
			$color = isset( $_POST['speaker_color'][ $i ] ) ? sanitize_hex_color( wp_unslash( $_POST['speaker_color'][ $i ] ) ) : '';
			$speakers[] = array(
				'name'   => $name,
				'slug'   => sanitize_title( isset( $_POST['speaker_slug'][ $i ] ) ? wp_unslash( $_POST['speaker_slug'][ $i ] ) : $name ),
				'role'   => sanitize_text_field( isset( $_POST['speaker_role'][ $i ] ) ? wp_unslash( $_POST['speaker_role'][ $i ] ) : '' ),
				'tags'   => sanitize_text_field( isset( $_POST['speaker_tags'][ $i ] ) ? wp_unslash( $_POST['speaker_tags'][ $i ] ) : '' ),
				'prompt' => sanitize_textarea_field( isset( $_POST['speaker_prompt'][ $i ] ) ? wp_unslash( $_POST['speaker_prompt'][ $i ] ) : '' ),
				'color'  => $color ? $color : teznevise_commenter_color_from_key( $name ),
				'order'  => isset( $_POST['speaker_order'][ $i ] ) ? (int) $_POST['speaker_order'][ $i ] : ( $i + 1 ),
				'active' => ! empty( $_POST['speaker_active'][ $i ] ) ? 1 : 0,
			);
		}
		update_option(
			'teznevise_ai_comments',
			array(
				'enabled'           => empty( $_POST['enabled'] ) ? '0' : '1',
				'auto_on_publish'   => empty( $_POST['auto_on_publish'] ) ? '0' : '1',
				'interaction'       => sanitize_key( wp_unslash( $_POST['interaction'] ?? 'round_robin' ) ),
				'max_turns'         => max( 1, min( 8, (int) ( $_POST['max_turns'] ?? 4 ) ) ),
				'model'             => sanitize_text_field( wp_unslash( $_POST['model'] ?? '' ) ),
				'agent_id'          => sanitize_key( wp_unslash( $_POST['agent_id'] ?? 'general' ) ),
				'discussion_prompt' => sanitize_textarea_field( wp_unslash( $_POST['discussion_prompt'] ?? '' ) ),
				'speakers'          => $speakers,
			),
			false
		);
		echo '<div class="updated"><p>' . esc_html__( 'تنظیمات گفتگوی هوش مصنوعی ذخیره شد.', 'teznevise' ) . '</p></div>';
	}
	$s      = teznevise_ai_comment_settings();
	$agents = class_exists( 'TezNevise_AI_Database' ) ? TezNevise_AI_Database::get_all_agents_admin() : array();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'گفتگوی هوش مصنوعی مطالب', 'teznevise' ); ?></h1>
		<p><?php esc_html_e( 'You اول پژوهش می‌کند. هر گوینده نام، رنگ، نقش و پرامپت خودش را دارد. گفتگو در فیلد سفارشی همان مطلب ذخیره می‌شود.', 'teznevise' ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'teznevise_ai_comments', '_tz_ai_c' ); ?>
			<input type="hidden" name="teznevise_ai_comments_save" value="1" />
			<table class="form-table">
				<tr><th><?php esc_html_e( 'فعال', 'teznevise' ); ?></th><td><label><input type="checkbox" name="enabled" value="1" <?php checked( $s['enabled'], '1' ); ?> /> <?php esc_html_e( 'تب گفتگوی هوش مصنوعی در مطالب', 'teznevise' ); ?></label></td></tr>
				<tr><th><?php esc_html_e( 'تولید خودکار', 'teznevise' ); ?></th><td><label><input type="checkbox" name="auto_on_publish" value="1" <?php checked( $s['auto_on_publish'], '1' ); ?> /> <?php esc_html_e( 'با انتشار مطلب تازه', 'teznevise' ); ?></label></td></tr>
				<tr><th><?php esc_html_e( 'شیوه تعامل', 'teznevise' ); ?></th>
					<td>
						<select name="interaction">
							<option value="round_robin" <?php selected( $s['interaction'], 'round_robin' ); ?>><?php esc_html_e( 'نوبتی شاخه‌ای (هر عامل پاسخ قبلی)', 'teznevise' ); ?></option>
							<option value="debate" <?php selected( $s['interaction'], 'debate' ); ?>><?php esc_html_e( 'مناظره (مخالفت مؤدبانه)', 'teznevise' ); ?></option>
							<option value="build" <?php selected( $s['interaction'], 'build' ); ?>><?php esc_html_e( 'تکمیل زنجیره‌ای', 'teznevise' ); ?></option>
						</select>
					</td>
				</tr>
				<tr><th><?php esc_html_e( 'تعداد نوبت', 'teznevise' ); ?></th><td><input type="number" min="1" max="8" name="max_turns" value="<?php echo esc_attr( $s['max_turns'] ); ?>" /></td></tr>
				<tr><th><?php esc_html_e( 'عامل API', 'teznevise' ); ?></th>
					<td>
						<select name="agent_id">
							<?php foreach ( $agents as $ag ) : $ag = (array) $ag; ?>
								<option value="<?php echo esc_attr( $ag['agent_id'] ?? '' ); ?>" <?php selected( $s['agent_id'], $ag['agent_id'] ?? '' ); ?>><?php echo esc_html( $ag['name'] ?? '' ); ?></option>
							<?php endforeach; ?>
						</select>
						<input name="model" class="regular-text" placeholder="<?php esc_attr_e( 'مدل اختیاری', 'teznevise' ); ?>" value="<?php echo esc_attr( $s['model'] ); ?>" />
					</td>
				</tr>
				<tr><th><?php esc_html_e( 'پرامپت گفتگو', 'teznevise' ); ?></th><td><textarea name="discussion_prompt" class="large-text" rows="5"><?php echo esc_textarea( $s['discussion_prompt'] ); ?></textarea></td></tr>
			</table>
			<h2><?php esc_html_e( 'گویندگان', 'teznevise' ); ?></h2>
			<p><?php esc_html_e( 'نام، رنگ، نقش، برچسب و پرامپت هر گوینده را مشخص کنید. ترتیب عدد کوچک‌تر زودتر حرف می‌زند.', 'teznevise' ); ?></p>
			<table class="widefat">
				<thead><tr><th><?php esc_html_e( 'نام', 'teznevise' ); ?></th><th>slug</th><th><?php esc_html_e( 'نقش', 'teznevise' ); ?></th><th><?php esc_html_e( 'برچسب', 'teznevise' ); ?></th><th><?php esc_html_e( 'رنگ', 'teznevise' ); ?></th><th><?php esc_html_e( 'ترتیب', 'teznevise' ); ?></th><th><?php esc_html_e( 'فعال', 'teznevise' ); ?></th></tr></thead>
				<tbody>
					<?php
					$speakers   = $s['speakers'];
					$speakers[] = array( 'name' => '', 'slug' => '', 'role' => '', 'tags' => '', 'prompt' => '', 'color' => '#145d4a', 'order' => count( $speakers ) + 1, 'active' => 1 );
					foreach ( $speakers as $i => $sp ) :
						?>
						<tr>
							<td><input name="speaker_name[<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $sp['name'] ?? '' ); ?>" /></td>
							<td><input name="speaker_slug[<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $sp['slug'] ?? '' ); ?>" /></td>
							<td><input name="speaker_role[<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $sp['role'] ?? '' ); ?>" /></td>
							<td><input name="speaker_tags[<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $sp['tags'] ?? '' ); ?>" /></td>
							<td><input type="color" name="speaker_color[<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $sp['color'] ?? '#145d4a' ); ?>" /></td>
							<td><input type="number" name="speaker_order[<?php echo (int) $i; ?>]" value="<?php echo esc_attr( $sp['order'] ?? $i ); ?>" /></td>
							<td><input type="checkbox" name="speaker_active[<?php echo (int) $i; ?>]" value="1" <?php checked( ! empty( $sp['active'] ) ); ?> /></td>
						</tr>
						<tr><td colspan="7"><textarea name="speaker_prompt[<?php echo (int) $i; ?>]" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'پرامپت این گوینده', 'teznevise' ); ?>"><?php echo esc_textarea( $sp['prompt'] ?? '' ); ?></textarea></td></tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function teznevise_ai_comments_schema( $post_id ) {
	$thread = teznevise_ai_discussion_get( $post_id );
	$items  = $thread['items'];
	if ( ! $items ) {
		return '';
	}
	$graph = array();
	foreach ( $items as $item ) {
		$graph[] = array(
			'@type'         => 'Comment',
			'@id'           => get_permalink( $post_id ) . '#ai-comment-' . ( $item['id'] ?? '' ),
			'text'          => wp_strip_all_tags( (string) ( $item['content'] ?? '' ) ),
			'author'        => array(
				'@type'    => 'Person',
				'name'     => $item['name'] ?? '',
				'jobTitle' => $item['role'] ?? '',
			),
			'keywords'      => $item['tags'] ?? '',
		);
	}
	$data = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'DiscussionForumPosting',
		'headline'     => get_the_title( $post_id ),
		'url'          => get_permalink( $post_id ),
		'commentCount' => count( $graph ),
		'comment'      => $graph,
	);
	return '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}

function teznevise_handle_human_ai_reply() {
	if ( empty( $_POST['teznevise_ai_human_reply'] ) || ! is_user_logged_in() ) {
		return;
	}
	if ( ! isset( $_POST['_tz_ai_human'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_tz_ai_human'] ) ), 'teznevise_ai_human' ) ) {
		return;
	}
	if ( ! current_user_can( 'moderate_comments' ) ) {
		return;
	}
	$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
	$body    = isset( $_POST['ai_human_body'] ) ? wp_kses_post( wp_unslash( $_POST['ai_human_body'] ) ) : '';
	$parent  = isset( $_POST['ai_human_parent'] ) ? (int) $_POST['ai_human_parent'] : 0;
	if ( $post_id <= 0 || strlen( wp_strip_all_tags( $body ) ) < 4 ) {
		return;
	}
	$user = wp_get_current_user();
	$cid  = wp_insert_comment(
		array(
			'comment_post_ID'      => $post_id,
			'comment_author'       => $user->display_name . ' — ' . __( 'کارشناس تزنویسه', 'teznevise' ),
			'comment_author_email' => $user->user_email,
			'comment_content'      => $body,
			'comment_type'         => 'tz_ai',
			'comment_parent'       => $parent,
			'comment_approved'     => 1,
			'user_id'              => (int) $user->ID,
		)
	);
	if ( $cid ) {
		$color = teznevise_commenter_color_from_key( $user->user_email );
		update_comment_meta( $cid, 'tz_ai_role', 'human-moderator' );
		update_comment_meta( $cid, 'tz_ai_name', $user->display_name );
		update_comment_meta( $cid, 'tz_human_moderator', '1' );
		update_comment_meta( $cid, 'tz_ai_color', $color );
		$thread            = teznevise_ai_discussion_get( $post_id );
		$thread['items'][] = array(
			'id'      => (string) $cid,
			'parent'  => (string) $parent,
			'name'    => $user->display_name . ' — ' . __( 'کارشناس تزنویسه', 'teznevise' ),
			'slug'    => sanitize_title( $user->user_login ),
			'role'    => 'human-moderator',
			'tags'    => '',
			'color'   => $color,
			'content' => $body,
			'human'   => true,
		);
		teznevise_ai_discussion_save( $post_id, $thread );
	}
	wp_safe_redirect( get_permalink( $post_id ) . '#ai-discussion' );
	exit;
}
add_action( 'admin_post_teznevise_ai_human_reply', 'teznevise_handle_human_ai_reply' );

if ( class_exists( 'Walker_Comment' ) && ! class_exists( 'Teznevise_Walker_Comment' ) ) {
	class Teznevise_Walker_Comment extends Walker_Comment {
		protected function html5_comment( $comment, $depth, $args ) {
			$tag   = ( 'div' === $args['style'] ) ? 'div' : 'li';
			$color = function_exists( 'teznevise_commenter_color_for_comment' ) ? teznevise_commenter_color_for_comment( $comment ) : '#145d4a';
			$classes = $this->has_children ? 'parent tz-thread-item' : 'tz-thread-item';
			?>
			<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $classes, $comment ); ?> style="--tz-commenter: <?php echo esc_attr( $color ); ?>">
				<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
					<header class="comment-author tz-thread-item__meta">
						<?php
						if ( 0 !== $args['avatar_size'] ) {
							echo get_avatar( $comment, $args['avatar_size'] );
						}
						?>
						<strong class="tz-thread-item__name"><?php echo get_comment_author_link( $comment ); ?></strong>
						<time datetime="<?php echo esc_attr( get_comment_time( 'c' ) ); ?>"><?php echo esc_html( get_comment_date() ); ?></time>
					</header>
					<?php if ( '0' === $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'دیدگاه شما در انتظار تأیید است.', 'teznevise' ); ?></p>
					<?php endif; ?>
					<div class="comment-content"><?php comment_text(); ?></div>
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<div class="reply">',
								'after'     => '</div>',
							)
						)
					);
					?>
				</article>
			<?php
		}
	}
}
