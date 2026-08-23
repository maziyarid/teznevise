<?php
/**
 * Encrypt API keys at rest with OpenSSL + WordPress salts.
 *
 * Legacy plaintext values still decrypt (pass-through) so existing agent keys keep working.
 *
 * @package Teznevise_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Teznevise_Key_Vault {
	const PREFIX = 'enc.v1.';

	public static function key_bytes() {
		return hash( 'sha256', wp_salt( 'auth' ) . '|' . wp_salt( 'secure_auth' ), true );
	}

	public static function encrypt( $plain ) {
		$plain = (string) $plain;
		if ( '' === $plain || 0 === strpos( $plain, self::PREFIX ) ) {
			return $plain;
		}
		$iv     = random_bytes( 16 );
		$cipher = openssl_encrypt( $plain, 'AES-256-CBC', self::key_bytes(), OPENSSL_RAW_DATA, $iv );
		if ( false === $cipher ) {
			return $plain;
		}
		return self::PREFIX . base64_encode( $iv . $cipher );
	}

	public static function decrypt( $stored ) {
		$stored = (string) $stored;
		if ( '' === $stored || 0 !== strpos( $stored, self::PREFIX ) ) {
			return $stored;
		}
		$raw = base64_decode( substr( $stored, strlen( self::PREFIX ) ), true );
		if ( ! is_string( $raw ) || strlen( $raw ) < 17 ) {
			return '';
		}
		$iv      = substr( $raw, 0, 16 );
		$cipher  = substr( $raw, 16 );
		$plain   = openssl_decrypt( $cipher, 'AES-256-CBC', self::key_bytes(), OPENSSL_RAW_DATA, $iv );
		return is_string( $plain ) ? $plain : '';
	}

	public static function get_option_key( $option, $default = '' ) {
		return self::decrypt( (string) get_option( $option, $default ) );
	}

	public static function get_post_key( $post_id, $meta_key ) {
		return self::decrypt( (string) get_post_meta( $post_id, $meta_key, true ) );
	}

	/**
	 * Global key, then per-post override if the post opted into a custom API.
	 *
	 * @param string $provider Provider id (openai, you, tavily, …).
	 * @param int    $post_id  Optional post.
	 * @return string
	 */
	public static function get_provider_key( $provider, $post_id = 0 ) {
		$provider = sanitize_key( $provider );
		$map      = array(
			'openai'     => 'teznevise_ai_openai_key',
			'gemini'     => 'teznevise_ai_gemini_key',
			'openrouter' => 'teznevise_ai_openrouter_key',
			'groq'       => 'teznevise_ai_groq_key',
			'xai'        => 'teznevise_ai_xai_key',
			'anthropic'  => 'teznevise_ai_anthropic_key',
			'mistral'    => 'teznevise_ai_mistral_key',
			'together'   => 'teznevise_ai_together_key',
			'deepseek'   => 'teznevise_ai_deepseek_key',
			'you'        => 'teznevise_ai_you_key',
			'tavily'     => 'teznevise_ai_tavily_key',
			'genspark'   => 'teznevise_ai_genspark_key',
			'perplexity' => 'teznevise_ai_perplexity_key',
		);
		$option = $map[ $provider ] ?? '';
		$global = $option ? self::get_option_key( $option ) : '';
		if ( 'you' === $provider && '' === $global && function_exists( 'teznevise_tezcoin_get' ) ) {
			$global = self::decrypt( (string) teznevise_tezcoin_get( 'youcom_key' ) );
		}
		if ( 'tavily' === $provider && '' === $global && function_exists( 'teznevise_tezcoin_get' ) ) {
			$global = self::decrypt( (string) teznevise_tezcoin_get( 'tavily_key' ) );
		}
		$post_id = (int) $post_id;
		if ( $post_id > 0 && 'custom' === get_post_meta( $post_id, '_teznevise_api_source', true ) ) {
			$agent_id = sanitize_key( (string) get_post_meta( $post_id, '_teznevise_active_agent', true ) );
			if ( $agent_id ) {
				$agent_key = self::get_post_key( $post_id, '_teznevise_api_key_agent_' . $agent_id );
				if ( '' !== $agent_key ) {
					return $agent_key;
				}
			}
			$custom = self::get_post_key( $post_id, '_teznevise_api_key_' . $provider );
			if ( '' !== $custom ) {
				return $custom;
			}
		}
		return $global;
	}

	public static function hook_option_encryption() {
		$keys = array( 'openai_key', 'gemini_key', 'openrouter_key', 'groq_key', 'xai_key', 'anthropic_key', 'mistral_key', 'together_key', 'deepseek_key', 'you_key', 'tavily_key', 'genspark_key', 'perplexity_key' );
		foreach ( $keys as $suffix ) {
			$option = 'teznevise_ai_' . $suffix;
			add_filter(
				'pre_update_option_' . $option,
				static function ( $value ) use ( $option ) {
					$value = is_string( $value ) ? $value : '';
					if ( '' === $value ) {
						return get_option( $option, '' );
					}
					if ( '-' === $value ) {
						return '';
					}
					return Teznevise_Key_Vault::encrypt( $value );
				}
			);
		}
	}
}
