<?php
/**
 * Plugin Name: Docker Loopback Fix
 * Description: Rewrites loopback HTTP requests from localhost to the nginx Docker service name.
 *              Fixes REST API and WP-Cron loopback failures in split nginx + PHP-FPM Docker setups.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * In Docker, the wordpress (PHP-FPM) container cannot reach port 80 on localhost —
 * that port belongs to the nginx container. Rewrite any internal loopback request
 * URL to use the nginx service hostname so it routes correctly inside the Docker network.
 */
add_filter(
	'pre_http_request',
	static function ( $pre, $parsed_args, $url ) {
		static $in_loopback = false;

		if ( $in_loopback ) {
			return $pre;
		}

		if ( ! preg_match( '#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/|$)#', $url ) ) {
			return $pre;
		}

		$rewritten_url = preg_replace( '#^(https?://)(localhost|127\.0\.0\.1)(:\d+)?#', '$1nginx', $url );

		$in_loopback = true;
		$response    = wp_remote_request( $rewritten_url, $parsed_args );
		$in_loopback = false;

		return $response;
	},
	10,
	3
);
