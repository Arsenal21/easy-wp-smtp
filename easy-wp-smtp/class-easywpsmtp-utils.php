<?php

use ioncube\phpOpensslCryptor\Cryptor;

class EasyWPSMTP_Utils {

	public $enc_key;
	protected static $instance = null;

	public function __construct() {
		require_once 'inc/Cryptor.php';
		$key = get_option( 'swpsmtp_enc_key', false );
		if ( empty( $key ) ) {
			$key = wp_salt();
			update_option( 'swpsmtp_enc_key', $key );
		}
		$this->enc_key = $key;
	}

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function base64_decode_maybe( $str ) {
		if ( ! function_exists( 'mb_detect_encoding' ) ) {
			return base64_decode( $str ); //phpcs:ignore
		}
		if ( mb_detect_encoding( $str ) === mb_detect_encoding( base64_decode( base64_encode( base64_decode( $str ) ) ) ) ) { //phpcs:ignore
			$str = base64_decode( $str ); //phpcs:ignore
		}
		return $str;
	}

	public function encrypt_password( $pass ) {
		if ( '' === $pass ) {
			return '';
		}

		$password = Cryptor::Encrypt( $pass, $this->enc_key );
		return $password;
	}

	public function decrypt_password( $pass ) {
		$password = Cryptor::Decrypt( $pass, $this->enc_key );
		return $password;
	}

	/**
 * Sanitizes textarea. Tries to use wp sanitize_textarea_field() function. If that's not available, uses its own methods
 * @return string
 */
	public static function sanitize_textarea( $str ) {
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			return sanitize_textarea_field( $str );
		}
		$filtered = wp_check_invalid_utf8( $str );

		if ( strpos( $filtered, '<' ) !== false ) {
			$filtered = wp_pre_kses_less_than( $filtered );
			// This will strip extra whitespace for us.
			$filtered = wp_strip_all_tags( $filtered, false );

			// Use html entities in a special case to make sure no later
			// newline stripping stage could lead to a functional tag
			$filtered = str_replace( "<\n", "&lt;\n", $filtered );
		}

		$filtered = trim( $filtered );

		$found = false;
		while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
			$filtered = str_replace( $match[0], '', $filtered );
			$found    = true;
		}

		if ( $found ) {
			// Strip out the whitespace that may now exist after removing the octets.
			$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
		}

		return $filtered;
	}

	//checks the imported json file
	//for any malicious or suspected input
	public static function safe_unserialize($json_string)
	{

			//if a object is passed in json, check and return false, as plugin do not encode objects while exporting.
			//since all object start with O:
			$is_object= strpos($json_string,"O:");

			if($is_object!==false)
			{
				$error  = new WP_Error();
				$error->add(1002, __("A malicious import file is passed. Aborting import!",'easy-wp-smtp'));
				return $error;
			}

		return unserialize($json_string);	
	}

}
