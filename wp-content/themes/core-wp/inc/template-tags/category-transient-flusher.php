<?php
/**
 * Category Transient Flusher
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_category_transient_flusher' ) ) {
	/**
	 * Flush out the transients used in core_wp_categorized_blog.
	 *
	 * @method core_wp_category_transient_flusher.
	 */
	function core_wp_category_transient_flusher() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		delete_transient( 'core_wp_categories' );
	}
}
add_action( 'edit_category', 'core_wp_category_transient_flusher' );
add_action( 'save_post', 'core_wp_category_transient_flusher' );
