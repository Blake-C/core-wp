<?php
/**
 * Custom Post Password Form
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_custom_password_form' ) ) {
	/**
	 * Customizes the password form
	 *
	 * @method core_wp_custom_password_form
	 * @return string - Form element
	 */
	function core_wp_custom_password_form() {
		global $post;

		$label = 'pwbox-' . ( empty( $post->ID ) ? wp_rand() : $post->ID );

		$o = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
			<p>' . __( 'To view this protected post, enter the password below:', 'core_wp' ) . '</p>' .
		'<div class="row collapse">' .
		'<div>' .
		'<label class="pass-label" for="' . esc_attr( $label ) . '">' . __( 'Password:', 'core_wp' ) . ' </label>' .
		'</div>' .
		'<div class="small-8 columns">' .
		'<input name="post_password" id="' . esc_attr( $label ) . '" type="password" />' .
		'</div>' .
		'<div class="small-4 columns">' .
		'<input type="submit" name="Submit" class="button expanded" value="' . esc_attr_x( 'Submit', 'Button value attribute on password form', 'core_wp' ) . '" />' .
		'</div>' .
		'</div>' .
		'</form>';

		return $o;
	}
}
add_filter( 'the_password_form', 'core_wp_custom_password_form' );
