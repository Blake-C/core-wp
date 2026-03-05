<?php
/**
 * Changes login pages logo URL to return to sites home page
 *
 * @link https://thomas.vanhoutte.be/miniblog/add-a-custom-logo-to-wordpress-wp-admin-login-page/
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_login_page_custom_link' ) ) {
	/**
	 * Login page logo URL
	 *
	 * @method core_wp_login_page_custom_link
	 * @return string - Link to home page
	 */
	function core_wp_login_page_custom_link() {
		return get_home_url();
	}
}
add_filter( 'login_headerurl', 'core_wp_login_page_custom_link' );
