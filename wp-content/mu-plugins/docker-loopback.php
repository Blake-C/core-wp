<?php
/**
 * Plugin Name: Docker Loopback Fix
 * Description: Rewrites loopback HTTP requests from localhost to the nginx
 *              Docker service. Fixes Site Health, REST API, and WP-Cron
 *              loopback failures in split nginx + PHP-FPM Docker setups.
 * Version: 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * In Docker, the wordpress (PHP-FPM) container cannot reach port 80 on
 * localhost — that port belongs to the nginx container. Rewrite any internal
 * loopback request URL to use the nginx service hostname so it routes
 * correctly inside the Docker network.
 *
 * The original Host header is preserved so WordPress does not issue a
 * canonical redirect (WP_HOME is 'localhost'; without Host: localhost,
 * nginx receives Host: nginx and WordPress redirects back to localhost,
 * causing a cURL error 7 on the follow-up request).
 */
add_filter(
	'pre_http_request',
	static function ( $pre, $parsed_args, $url ) {
		static $in_loopback = false;

		if ( $in_loopback ) {
			return $pre;
		}

		$loopback_pattern = '#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/|$)#';
		if ( ! preg_match( $loopback_pattern, $url ) ) {
			return $pre;
		}

		$rewrite_pattern = '#^(https?://)(localhost|127\.0\.0\.1)(:\d+)?#';
		$rewritten_url   = preg_replace( $rewrite_pattern, '$1nginx', $url );

		$original_host   = parse_url( $url, PHP_URL_HOST );
		$original_port   = parse_url( $url, PHP_URL_PORT );
		$host_header     = $original_host;
		if ( $original_port && 80 !== (int) $original_port ) {
			$host_header .= ':' . $original_port;
		}

		$parsed_args['headers']         = isset( $parsed_args['headers'] )
			? (array) $parsed_args['headers']
			: array();
		$parsed_args['headers']['Host'] = $host_header;

		$in_loopback = true;
		$response    = wp_remote_request( $rewritten_url, $parsed_args );
		$in_loopback = false;

		return $response;
	},
	10,
	3
);
