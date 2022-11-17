<?php

class EASYWPSMTP_Debug_Logger {

	public function __construct() {

	}
        
	public static function get_log_file_path() {
		$log_file_name = 'logs' . DIRECTORY_SEPARATOR . '.' . uniqid( '', true ) . '.txt';
		$log_file_name = apply_filters( 'swpsmtp_log_file_path_override', $log_file_name );
		return $log_file_name;
	}
        
	public static function log_debug_string( $str, $overwrite = false ) {
		try {
			$log_file_name = '';
                        $easysmtp_options = get_option( 'swpsmtp_options' );
                        
			if ( isset( $easysmtp_options['smtp_settings']['log_file_name'] ) ) {
				$log_file_name = $easysmtp_options['smtp_settings']['log_file_name'];
			}
			if ( empty( $log_file_name ) || $overwrite ) {
				if ( ! empty( $log_file_name ) && file_exists( plugin_dir_path( __FILE__ ) . $log_file_name ) ) {
					unlink( plugin_dir_path( __FILE__ ) . $log_file_name );
				}
				$log_file_name = EASYWPSMTP_Debug_Logger::get_log_file_path();

				$easysmtp_options['smtp_settings']['log_file_name'] = $log_file_name;
				update_option( 'swpsmtp_options', $easysmtp_options );
				file_put_contents( plugin_dir_path( __FILE__ ) . $log_file_name, self::$reset_log_str );
			}
                        //Timestamp the log output
                        $str = '[' . date( 'm/d/Y g:i:s A' ) . '] - ' . $str;
                        //Write to the log file
                        return ( file_put_contents( plugin_dir_path( __FILE__ ) . $log_file_name, $str, ( ! $overwrite ? FILE_APPEND : 0 ) ) );
		} catch ( \Exception $e ) {
			return false;
		}
	}
        
}