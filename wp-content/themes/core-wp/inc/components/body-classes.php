<?php
/**
 * Custom classes to body.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_body_classes' ) ) {
	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param  string|array $classes - One or more classes to add to the class list.
	 * @return array - Array of classes.
	 */
	function core_wp_body_classes( $classes ) {
		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		return $classes;
	}
}
add_filter( 'body_class', 'core_wp_body_classes' );

/**
 * Add no-js class to the <html> element.
 * global-scripts.js swaps this to 'js' once JavaScript has loaded.
 *
 * @param string $output The existing language attributes string.
 * @return string
 */
function core_wp_html_no_js_class( $output ) {
	return 'class="no-js" ' . $output;
}
add_filter( 'language_attributes', 'core_wp_html_no_js_class' );
