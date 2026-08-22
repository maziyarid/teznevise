<?php
/**
 * You.com primary research, Tavily fallback, hashed cache, graceful brief.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Research_Oracle {

	public static function hash( $title, $body ) {
		return sha1( wp_json_encode( array( (string) $title, (string) $body ) ) );
	}

	public static function brief( $query, $post_id = 0 ) {
		$query = wp_strip_all_tags( (string) $query );
		$hash  = self::hash( $query, '' );
		$cached = get_transient( 'tz_research_' . $hash );
		if ( is_array( $cached ) && ! empty( $cached['brief'] ) ) {
			return $cached;
		}
		if ( $post_id ) {
			$stored = get_post_meta( $post_id, '_teznevise_ai_research', true );
			if ( is_array( $stored ) && ! empty( $stored['hash'] ) && $stored['hash'] === $hash && ! empty( $stored['brief'] ) ) {
				return $stored;
			}
			if ( is_string( $stored ) && '' !== $stored ) {
				$legacy_hash = (string) get_post_meta( $post_id, '_teznevise_ai_research_hash', true );
				if ( $legacy_hash && $legacy_hash === $hash ) {
					$payload = array(
						'brief'     => $stored,
						'sources'   => array(),
						'provider'  => 'cache',
						'timestamp' => time(),
						'hash'      => $hash,
						'version'   => 1,
					);
					set_transient( 'tz_research_' . $hash, $payload, DAY_IN_SECONDS );
					return $payload;
				}
			}
		}

		$you = self::you_search( $query, $post_id );
		if ( is_array( $you ) ) {
			set_transient( 'tz_research_' . $hash, $you, DAY_IN_SECONDS );
			return $you;
		}
		$tavily = self::tavily_search( $query, $post_id );
		if ( is_array( $tavily ) ) {
			set_transient( 'tz_research_' . $hash, $tavily, DAY_IN_SECONDS );
			return $tavily;
		}

		$fallback = array(
			'brief'     => __( 'پژوهش زنده در دسترس نبود. عامل‌ها باید فقط به متن همین مطلب تکیه کنند و منبع اختراع نکنند.', 'teznevise' ),
			'sources'   => array(),
			'provider'  => 'intrinsic',
			'timestamp' => time(),
			'hash'      => $hash,
			'version'   => 1,
		);
		return $fallback;
	}

	private static function you_search( $query, $post_id ) {
		$key = class_exists( 'Teznevise_Key_Vault' ) ? Teznevise_Key_Vault::get_provider_key( 'you', $post_id ) : '';
		if ( '' === $key ) {
			Teznevise_Logger::log( 'you_missing', 'You.com key empty', array( 'post' => (string) $post_id ) );
			return null;
		}
		if ( class_exists( 'TezNevise_AI_API' ) && method_exists( 'TezNevise_AI_API', 'research' ) ) {
			$agent = array(
				'provider'     => 'you',
				'api_key'      => $key,
				'api_endpoint' => 'https://api.ydc-index.io/v1/search',
				'model'        => 'you-search',
			);
			$res = TezNevise_AI_API::research( $query, $agent );
			if ( ! is_wp_error( $res ) && ! empty( $res['content'] ) ) {
				return array(
					'brief'     => (string) $res['content'],
					'sources'   => self::extract_urls( (string) $res['content'] ),
					'provider'  => 'you',
					'timestamp' => time(),
					'hash'      => self::hash( $query, '' ),
					'version'   => 1,
				);
			}
			if ( is_wp_error( $res ) ) {
				Teznevise_Logger::log( 'you_fail', $res->get_error_message(), array( 'post' => (string) $post_id ) );
			}
		}
		return null;
	}

	private static function tavily_search( $query, $post_id ) {
		$key = class_exists( 'Teznevise_Key_Vault' ) ? Teznevise_Key_Vault::get_provider_key( 'tavily', $post_id ) : '';
		if ( '' === $key ) {
			Teznevise_Logger::log( 'tavily_missing', 'Tavily key empty', array( 'post' => (string) $post_id ) );
			return null;
		}
		$response = wp_remote_post(
			'https://api.tavily.com/search',
			array(
				'timeout'     => 40,
				'redirection' => 0,
				'headers'     => array( 'Content-Type' => 'application/json' ),
				'body'        => wp_json_encode(
					array(
						'api_key'        => $key,
						'query'          => $query,
						'search_depth'   => 'advanced',
						'max_results'    => 8,
						'include_answer' => true,
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			Teznevise_Logger::log( 'tavily_fail', $response->get_error_message(), array( 'post' => (string) $post_id ) );
			return null;
		}
		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			Teznevise_Logger::log( 'tavily_http', 'HTTP ' . $code, array( 'post' => (string) $post_id ) );
			return null;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) ) {
			return null;
		}
		$lines   = array( '## یافته‌های Tavily' );
		$sources = array();
		if ( ! empty( $body['answer'] ) ) {
			$lines[] = (string) $body['answer'];
		}
		foreach ( (array) ( $body['results'] ?? array() ) as $i => $hit ) {
			$title = $hit['title'] ?? '';
			$url   = $hit['url'] ?? '';
			$snip  = $hit['content'] ?? '';
			$lines[] = ( $i + 1 ) . '. **' . $title . '** — ' . wp_strip_all_tags( (string) $snip ) . ( $url ? ' (' . $url . ')' : '' );
			if ( $url ) {
				$sources[] = array(
					'title' => $title,
					'url'   => $url,
				);
			}
		}
		return array(
			'brief'     => implode( "\n", $lines ),
			'sources'   => $sources,
			'provider'  => 'tavily',
			'timestamp' => time(),
			'hash'      => self::hash( $query, '' ),
			'version'   => 1,
		);
	}

	private static function extract_urls( $text ) {
		preg_match_all( '#https?://[^\s\)]+#', $text, $m );
		$out = array();
		foreach ( (array) ( $m[0] ?? array() ) as $url ) {
			$out[] = array(
				'title' => $url,
				'url'   => $url,
			);
		}
		return $out;
	}
}
