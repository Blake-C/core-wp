<?php
/**
 * Core WP utility functions
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_cache_bust' ) ) {
	/**
	 * Gets the time when the content of the file was changed.
	 *
	 * @method core_wp_cache_bust
	 * @param  string $src Path to files to get time last changed.
	 * @return string      Returns the time when the data blocks of a file were being written to, that is, the time when the content of the file was changed.
	 */
	function core_wp_cache_bust( $src ) {
		static $request_cache = array();

		if ( isset( $request_cache[ $src ] ) ) {
			return $request_cache[ $src ];
		}

		$file_path = realpath( '.' . wp_parse_url( $src, PHP_URL_PATH ) );

		if ( ! $file_path || ! file_exists( $file_path ) ) {
			$request_cache[ $src ] = null;
			return null;
		}

		$request_cache[ $src ] = filemtime( $file_path );

		return $request_cache[ $src ];
	}
}

if ( ! function_exists( 'core_wp_print_pre' ) ) {
	/**
	 * Outputs array in HTML pre tags
	 *
	 * @method core_wp_print_pre
	 * @param  [array] $data - Array to be displayed in pre tags.
	 */
	function core_wp_print_pre( $data ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}
		echo '<pre>';
      print_r( $data ); // phpcs:ignore
		echo '</pre>';
	}
}

if ( ! function_exists( 'core_wp_theme_error_log' ) ) {
	/**
	 * Custom theme error logging
	 *
	 * @method core_wp_theme_error_log
	 * @param  string $message Message to pass to error log.
	 */
	function core_wp_theme_error_log( $message ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}
		$time_stamp = new DateTime( 'NOW' );
		$time_stamp->setTimezone( new DateTimeZone( 'America/Chicago' ) );
		$error_time  = $time_stamp->format( 'F j, Y @ G:i:s' );
		$dir         = get_template_directory();
		$message_log = "<-------->\n" . $error_time . "\n" . $message . "\n\n";

     error_log( $message_log, 3, $dir . '/theme-error.log' ); // phpcs:ignore
	}
}
