<?php
/**
 * Custom Excerpt Length.
 *
 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/excerpt_length
 * @link https://codex.wordpress.org/Function_Reference/the_excerpt
 * @link https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/formatting.php#L3274
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_custom_excerpt_length' ) ) {
	/**
	 * Filters the number of words in an excerpt.
	 *
	 * @method core_wp_custom_excerpt_length
	 * @param  int $length - The number of words. Default 55.
	 * @return int - Length of excerpt
	 */
	function core_wp_custom_excerpt_length( $length ) {
		return 30;
	}
}
add_filter( 'excerpt_length', 'core_wp_custom_excerpt_length', 999 );
