<?php
/**
 * Changes the "Howdy" text on admin pages to "Welcome"
 *
 * @link http://coffeecupweb.com/how-to-change-or-remove-howdy-text-on-wordpress-admin-bar/
 * @link https://developer.wordpress.org/reference/hooks/admin_bar_menu/
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_change_howdy_text_toolbar' ) ) {
	/**
	 * This is the hook used to add, remove, or manipulate admin bar items.
	 *
	 * @method core_wp_change_howdy_text_toolbar
	 * @param  WP_Admin_Bar $wp_admin_bar - WP_Admin_Bar instance, passed by reference.
	 */
	function core_wp_change_howdy_text_toolbar( $wp_admin_bar ) {
		$getgreetings = $wp_admin_bar->get_node( 'my-account' );

		$rpctitle = str_replace( 'Howdy', 'Welcome', $getgreetings->title );

		$wp_admin_bar->add_node(
			array(
				'id'    => 'my-account',
				'title' => $rpctitle,
			)
		);
	}
}
add_filter( 'admin_bar_menu', 'core_wp_change_howdy_text_toolbar', 9999 );
