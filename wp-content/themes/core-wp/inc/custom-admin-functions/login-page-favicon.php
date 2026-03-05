<?php
/**
 * Changes the login and admin page favicon
 *
 * @link http://www.kriesi.at/support/topic/adding-favicon-to-wordpress-back-end/
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_add_login_favicon' ) ) {
	/**
	 * Login page favicon
	 *
	 * @method core_wp_add_login_favicon
	 */
	function core_wp_add_login_favicon() {
		$favicon_path = get_template_directory_uri() . '/assets/icons/favicon.ico';

		echo '<link rel="shortcut icon" href="' . esc_html( $favicon_path ) . '" />';
	}
}
add_action( 'login_head', 'core_wp_add_login_favicon' );
add_action( 'admin_head', 'core_wp_add_login_favicon' );
