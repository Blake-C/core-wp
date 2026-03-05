<?php
/**
 * Changes the title tag on the login pages logo to the sites name
 *
 * @link http://www.agentwp.com/how-to-replace-the-logo-on-wordpress-login-page
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_change_title_on_logo' ) ) {
	/**
	 * Login page logo title tag
	 *
	 * @method core_wp_change_title_on_logo
	 * @return string - site name
	 */
	function core_wp_change_title_on_logo() {
		return get_bloginfo( 'name' );
	}
}
add_filter( 'login_headertext', 'core_wp_change_title_on_logo' );
