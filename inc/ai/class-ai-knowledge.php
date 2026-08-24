<?php
/**
 * Site-knowledge corpus + rated training rows for named agents.
 *
 * Models are not fine-tuned. They are grounded on indexed Teznevise pages
 * and improved with thumbs-up replies stored in their own tables.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TezNevise_AI_Knowledge {

	const VERSION = '1.0.0';

	private static $booted = false;

	public static function init() {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
		add_action( 'init', array( __CLASS__, 'maybe_install' ), 4 );
		add_action( 'teznevise_ai_index_corpus', array( __CLASS__, 'index_batch' ) );
		add_action( 'save_post', array( __CLASS__, 'on_save_post' ), 40, 2 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_start_index' ) );
		add_action( 'init', array( __CLASS__, 'maybe_start_index' ), 40 );
	}

	public static function table( $which ) {
		global $wpdb;
		$prefix = $wpdb->prefix . TezNevise_AI_Database::PREFIX;
		return 'training' === $which ? $prefix . 'training' : $prefix . 'corpus';
	}

	public static function maybe_install() {
		if ( get_option( 'teznevise_ai_knowledge_v' ) === self::VERSION ) {
			return;
		}
		self::create_tables();
		update_option( 'teznevise_ai_knowledge_v', self::VERSION, false );
	}

	public static function create_tables() {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();
		$corpus  = self::table( 'corpus' );
		$train   = self::table( 'training' );
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(
			"CREATE TABLE {$corpus} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				source_type VARCHAR(32) NOT NULL DEFAULT 'post',
				source_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				title VARCHAR(255) NOT NULL DEFAULT '',
				url VARCHAR(500) NOT NULL DEFAULT '',
				chunk TEXT NOT NULL,
				hash CHAR(32) NOT NULL DEFAULT '',
				indexed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY source (source_type, source_id),
				KEY hash (hash)
			) {$charset};"
		);
		dbDelta(
			"CREATE TABLE {$train} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				agent_id VARCHAR(100) NOT NULL DEFAULT '',
				session_id VARCHAR(255) NOT NULL DEFAULT '',
				message_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				user_message TEXT NOT NULL,
				assistant_message TEXT NOT NULL,
				thought TEXT NULL,
				rating TINYINT NOT NULL DEFAULT 0,
				page_url VARCHAR(500) NOT NULL DEFAULT '',
				page_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY agent_rating (agent_id, rating),
				KEY session_id (session_id),
				KEY created_at (created_at)
			) {$charset};"
		);
		$suppress = $wpdb->hide_errors();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "ALTER TABLE {$corpus} ADD FULLTEXT KEY ft_chunk (title, chunk)" );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "ALTER TABLE {$train} ADD FULLTEXT KEY ft_qa (user_message, assistant_message)" );
		$wpdb->show_errors( $suppress );
		self::seed_facts();
	}

	public static function maybe_start_index() {
		if ( '1.9.22' === (string) get_option( 'teznevise_ai_corpus_v' ) ) {
			return;
		}
		self::create_tables();
		self::seed_facts();
		update_option( 'teznevise_ai_index_page', 1, false );
		if ( ! wp_next_scheduled( 'teznevise_ai_index_corpus' ) ) {
			wp_schedule_single_event( time() + 8, 'teznevise_ai_index_corpus' );
		}
		update_option( 'teznevise_ai_corpus_v', '1.9.22', false );
	}

	public static function reset_and_queue() {
		global $wpdb;
		self::create_tables();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( 'DELETE FROM ' . self::table( 'corpus' ) . " WHERE source_type != 'fact'" );
		self::seed_facts();
		update_option( 'teznevise_ai_index_page', 1, false );
		delete_option( 'teznevise_ai_corpus_v' );
		self::maybe_start_index();
		self::index_batch();
	}

	public static function counts() {
		global $wpdb;
		$corpus = self::table( 'corpus' );
		$train  = self::table( 'training' );
		return array(
			'corpus'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$corpus}" ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			'training' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$train}" ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			'up'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$train} WHERE rating = 1" ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);
	}

	public static function seed_facts() {
		$phone = function_exists( 'teznevise_get_contact' ) ? teznevise_get_contact( 'phone_display' ) : '';
		$email = function_exists( 'teznevise_get_contact' ) ? teznevise_get_contact( 'email' ) : '';
		$facts = array(
			array(
				'تزنویسه پلتفرم مشاوره پژوهشی است، نه سفارش نوشتن پایان‌نامه. خدمات: مشاوره انجام پایان‌نامه، مشاوره پروپوزال، تحلیل آماری، شبیه‌سازی، ابزارهای آنلاین و عامل‌های هوش مصنوعی. مشاوره اولیه رایگان است.',
				home_url( '/' ),
				'هویت تزنویسه',
			),
			array(
				'مسیر شروع: فرم درخواست مشاوره در ' . home_url( '/inquiry/' ) . ' . خدمات پایان‌نامه: ' . home_url( '/thesis/' ) . ' . پروپوزال: ' . home_url( '/proposal/' ) . ' . تحلیل آماری: ' . home_url( '/service-statistics/' ) . ' . ابزارهای محاسبه: ' . home_url( '/online-calculation-tools/' ) . '.',
				home_url( '/inquiry/' ),
				'مسیر خدمات',
			),
			array(
				'حساب کاربری در ' . home_url( '/account/' ) . ' است نه پیشخوان وردپرس. عضویت ۳۰ تزکوین هدیه می‌دهد. گذرواژه از همان صفحه بازیابی می‌شود.',
				home_url( '/account/' ),
				'حساب کاربری',
			),
			array(
				'هشت عامل نام‌دار: تزنویسه (ترکیب‌گر)، کریستینا (نگارش علمی)، آدا (داده و کد)، پروفسور (روش پژوهش)، پرانتز (آمار)، الارا ووس (کیفی و اخلاق)، کوروش لکس (استدلال حقوقی)، دکتر میرا ساتو (پزشکی و STEM). هویت قفل است؛ هرگز مدل زیربنایی را فاش نمی‌کنند. مشاوره می‌دهند و پایان‌نامه نمی‌نویسند.',
				home_url( '/' ),
				'عامل‌های هوش مصنوعی',
			),
			array(
				'تماس: ' . $phone . ' — ' . $email . ' . ساعات پاسخ‌گویی در صفحه تماس. نماد اعتماد اینماد در پاورقی است. دانشگاه‌های نمایش‌داده‌شده دانشجویان مسیر آشنا دارند، نه قرارداد شراکت رسمی.',
				home_url( '/contact-us/' ),
				'تماس و اعتماد',
			),
		);
		foreach ( $facts as $row ) {
			self::upsert_chunk( 'fact', 0, $row[2], $row[1], $row[0] );
		}
	}

	public static function on_save_post( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || ! $post ) {
			return;
		}
		if ( ! in_array( $post->post_type, array( 'post', 'page' ), true ) ) {
			return;
		}
		if ( 'publish' !== $post->post_status ) {
			self::delete_source( 'page' === $post->post_type ? 'page' : 'post', $post_id );
			return;
		}
		self::index_one( $post );
	}

	public static function index_batch() {
		$page = max( 1, (int) get_option( 'teznevise_ai_index_page', 1 ) );
		$q    = new WP_Query(
			array(
				'post_type'              => array( 'post', 'page' ),
				'post_status'            => 'publish',
				'posts_per_page'         => 20,
				'paged'                  => $page,
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'no_found_rows'          => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		foreach ( $q->posts as $post ) {
			self::index_one( $post );
		}
		if ( $page < (int) $q->max_num_pages && $page < 80 ) {
			update_option( 'teznevise_ai_index_page', $page + 1, false );
			wp_schedule_single_event( time() + 20, 'teznevise_ai_index_corpus' );
		}
		wp_reset_postdata();
	}

	public static function index_one( $post ) {
		$post = get_post( $post );
		if ( ! $post || 'publish' !== $post->post_status ) {
			return;
		}
		$type = 'page' === $post->post_type ? 'page' : 'post';
		self::delete_source( $type, (int) $post->ID );
		$title = wp_strip_all_tags( get_the_title( $post ) );
		$url   = get_permalink( $post );
		$body  = wp_strip_all_tags( apply_filters( 'the_content', $post->post_content ) );
		$body  = function_exists( 'teznevise_consult_copy' ) ? teznevise_consult_copy( $body ) : $body;
		$body  = trim( preg_replace( '/\s+/u', ' ', $body ) );
		if ( '' === $body ) {
			$body = wp_strip_all_tags( (string) $post->post_excerpt );
		}
		if ( '' === $body ) {
			return;
		}
		foreach ( self::chunk( $title . '. ' . $body ) as $chunk ) {
			self::upsert_chunk( $type, (int) $post->ID, $title, $url, $chunk );
		}
	}

	public static function delete_source( $type, $id ) {
		global $wpdb;
		$wpdb->delete(
			self::table( 'corpus' ),
			array(
				'source_type' => sanitize_key( $type ),
				'source_id'   => (int) $id,
			)
		);
	}

	public static function chunk( $text, $size = 420 ) {
		$text  = trim( (string) $text );
		$out   = array();
		$chars = function_exists( 'mb_strlen' ) ? mb_strlen( $text ) : strlen( $text );
		if ( $chars <= $size ) {
			return $text ? array( $text ) : array();
		}
		$words = preg_split( '/\s+/u', $text ) ?: array();
		$buf   = '';
		foreach ( $words as $w ) {
			$trial = trim( $buf . ' ' . $w );
			$len   = function_exists( 'mb_strlen' ) ? mb_strlen( $trial ) : strlen( $trial );
			if ( $len > $size && '' !== $buf ) {
				$out[] = $buf;
				$buf   = $w;
			} else {
				$buf = $trial;
			}
			if ( count( $out ) >= 8 ) {
				break;
			}
		}
		if ( '' !== $buf && count( $out ) < 8 ) {
			$out[] = $buf;
		}
		return $out;
	}

	public static function upsert_chunk( $type, $id, $title, $url, $chunk ) {
		global $wpdb;
		$chunk = trim( wp_strip_all_tags( (string) $chunk ) );
		if ( '' === $chunk ) {
			return 0;
		}
		$hash = md5( $type . '|' . $id . '|' . $chunk );
		$table = self::table( 'corpus' );
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE hash = %s", $hash ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $exists ) {
			return (int) $exists;
		}
		$wpdb->insert(
			$table,
			array(
				'source_type' => sanitize_key( $type ),
				'source_id'   => (int) $id,
				'title'       => function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 250 ) : substr( $title, 0, 250 ),
				'url'         => esc_url_raw( (string) $url ),
				'chunk'       => $chunk,
				'hash'        => $hash,
				'indexed_at'  => current_time( 'mysql' ),
			)
		);
		return (int) $wpdb->insert_id;
	}

	/**
	 * Grounding pack for the system prompt.
	 *
	 * @param string $query User question.
	 * @param array  $page  Optional page_url / page_title.
	 * @return string
	 */
	public static function prompt_pack( $query, $page = array() ) {
		$bits = array(
			'SITE POLICY: Teznevise is a research CONSULTING platform. Never offer to write a thesis, proposal, chapter, or paper. Point to tools and human consults. Invent no prices, no official university partnerships, no guaranteed grades.',
			'Be precise. Prefer SITE KNOWLEDGE below over generic training. If a fact is missing, say you do not know and suggest /inquiry/. Public reply in Persian when the user writes Persian. ≤140 words unless a method needs steps.',
		);
		$title = sanitize_text_field( (string) ( $page['page_title'] ?? '' ) );
		$url   = esc_url_raw( (string) ( $page['page_url'] ?? '' ) );
		if ( $title || $url ) {
			$bits[] = 'The user is currently on: ' . trim( $title . ' ' . $url );
		}
		$hits = self::retrieve( $query, 6 );
		if ( $hits ) {
			$lines = array( 'SITE KNOWLEDGE (cite as Teznevise pages, not invented journals):' );
			$n     = 1;
			foreach ( $hits as $hit ) {
				$lines[] = '[' . $n . '] ' . $hit['title'] . ' — ' . $hit['url'] . "\n" . $hit['chunk'];
				++$n;
			}
			$bits[] = implode( "\n", $lines );
		}
		$shots = self::few_shot( $query, 2 );
		if ( $shots ) {
			$ex = array( 'HIGH-RATED REPLIES FROM THIS SITE (match this precision and consulting voice):' );
			foreach ( $shots as $s ) {
				$ex[] = 'Q: ' . $s['user_message'] . "\nA: " . $s['assistant_message'];
			}
			$bits[] = implode( "\n\n", $ex );
		}
		return implode( "\n\n", $bits );
	}

	public static function retrieve( $query, $limit = 6 ) {
		global $wpdb;
		self::maybe_install();
		$table = self::table( 'corpus' );
		$query = trim( wp_strip_all_tags( (string) $query ) );
		$limit = max( 1, min( 8, (int) $limit ) );
		$rows  = array();
		$words = preg_split( '/\s+/u', $query ) ?: array();
		$keep  = array();
		foreach ( $words as $w ) {
			$w = trim( $w, "؟?!.،,;:\"'«»" );
			if ( ( function_exists( 'mb_strlen' ) ? mb_strlen( $w ) : strlen( $w ) ) >= 3 ) {
				$keep[] = $w;
			}
			if ( count( $keep ) >= 6 ) {
				break;
			}
		}
		if ( $keep ) {
			$against = implode( ' ', array_map( static function ( $w ) {
				return '+' . $w . '*';
			}, $keep ) );
			$sql     = $wpdb->prepare(
				"SELECT title, url, chunk FROM {$table} WHERE MATCH(title, chunk) AGAINST (%s IN BOOLEAN MODE) LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$against,
				$limit
			);
			$rows = $wpdb->get_results( $sql, ARRAY_A );
			if ( $wpdb->last_error ) {
				$rows = array();
			}
		}
		if ( ! $rows && $keep ) {
			$like = '%' . $wpdb->esc_like( $keep[0] ) . '%';
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT title, url, chunk FROM {$table} WHERE chunk LIKE %s OR title LIKE %s ORDER BY source_type = 'fact' DESC, id DESC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$like,
					$like,
					$limit
				),
				ARRAY_A
			);
		}
		if ( ! $rows ) {
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT title, url, chunk FROM {$table} WHERE source_type = 'fact' ORDER BY id ASC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$limit
				),
				ARRAY_A
			);
		}
		return is_array( $rows ) ? $rows : array();
	}

	public static function few_shot( $query, $limit = 2 ) {
		global $wpdb;
		self::maybe_install();
		$table = self::table( 'training' );
		$query = trim( wp_strip_all_tags( (string) $query ) );
		$limit = max( 1, min( 4, (int) $limit ) );
		if ( '' === $query ) {
			return array();
		}
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_message, assistant_message FROM {$table} WHERE rating = 1 AND MATCH(user_message, assistant_message) AGAINST (%s IN NATURAL LANGUAGE MODE) LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$query,
				$limit
			),
			ARRAY_A
		);
		if ( $wpdb->last_error ) {
			return array();
		}
		return is_array( $rows ) ? $rows : array();
	}

	public static function record( $data ) {
		global $wpdb;
		self::maybe_install();
		$wpdb->insert(
			self::table( 'training' ),
			array(
				'agent_id'           => sanitize_key( $data['agent_id'] ?? '' ),
				'session_id'         => sanitize_text_field( $data['session_id'] ?? '' ),
				'message_id'         => (int) ( $data['message_id'] ?? 0 ),
				'user_message'       => sanitize_textarea_field( $data['user_message'] ?? '' ),
				'assistant_message'  => sanitize_textarea_field( $data['assistant_message'] ?? '' ),
				'thought'            => sanitize_textarea_field( $data['thought'] ?? '' ),
				'rating'             => 0,
				'page_url'           => esc_url_raw( $data['page_url'] ?? '' ),
				'page_id'            => (int) ( $data['page_id'] ?? 0 ),
				'created_at'         => current_time( 'mysql' ),
			)
		);
		return (int) $wpdb->insert_id;
	}

	public static function rate( $id, $rating ) {
		global $wpdb;
		$id     = (int) $id;
		$rating = (int) $rating;
		if ( $id <= 0 || ! in_array( $rating, array( -1, 1 ), true ) ) {
			return false;
		}
		return false !== $wpdb->update(
			self::table( 'training' ),
			array( 'rating' => $rating ),
			array( 'id' => $id )
		);
	}
}
