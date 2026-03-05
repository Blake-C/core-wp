<?php
/**
 * Adds stylesheet to admin and login screens
 *
 * @link https://codex.wordpress.org/Customizing_the_Login_Form
 * @link https://css-tricks.com/snippets/wordpress/apply-custom-css-to-admin-area/
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_login_page_styles' ) ) {
	/**
	 * Login page style sheet
	 *
	 * @method core_wp_login_page_styles
	 */
	function core_wp_login_page_styles() {
		$login_styles = get_template_directory_uri() . '/assets/css/login-admin.min.css';

		wp_enqueue_style( 'login_page_styles', $login_styles, array(), core_wp_cache_bust( $login_styles ) );
	}
}
add_action( 'login_enqueue_scripts', 'core_wp_login_page_styles' );
add_action( 'admin_head', 'core_wp_login_page_styles' );
