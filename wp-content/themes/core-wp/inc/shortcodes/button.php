<?php
/**
 * Adds the button class to anchor tags
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_button' ) ) {
	/**
	 * Adds the button class to anchor tags
	 *
	 * @link https://codex.wordpress.org/Shortcode_API
	 *
	 * @param  array  $atts    An associative array of attributes, or an empty string if no attributes are given.
	 * @param  string $content The enclosed content (if the shortcode is used in its enclosing form).
	 * @return string           Shortcode data
	 */
	function core_wp_button( $atts, $content = null ) {
		/**
		 * Combines user shortcode attributes with known attributes and fills in defaults when needed
		 *
		 * @param  array  $pairs     Entire list of supported attributes and their defaults.
		 * @param  array  $atts      User defined attributes in shortcode tag.
		 * @param  string $shortcode Optional. The name of the shortcode, provided for context to enable filtering
		 * @return array Combined and filtered attribute list.
		 */
		$attributes = shortcode_atts(
			array(
				'class' => 'button',
			),
			$atts,
			'button'
		);

		$content = str_replace( '<a ', '<a class="' . esc_attr( $attributes['class'] ) . '"', do_shortcode( $content ) );

		return $content;
	}
}
add_shortcode( 'button', 'core_wp_button' );
