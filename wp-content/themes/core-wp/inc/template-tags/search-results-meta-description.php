<?php
/**
 * The template for displaying meta information for the current post-date/time and author.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_search_results_meta_description' ) ) {
	/**
	 * By default the WordPress seach results page does not inlcude a meta
	 * description. This function will take the output of the get_search_query
	 * and add it to the pages meta description.
	 */
	function core_wp_search_results_meta_description() {
		if ( is_search() ) {
			$core_wp_search_page_meta_description = sprintf(
			// translators: Title for search results page.
				esc_html_x(
					'Search Results for: %s',
					'Search results for search page meta description',
					'core_wp'
				),
				get_search_query()
			);

         echo '<meta name="description" content="' . $core_wp_search_page_meta_description . '" />'; // phpcs:ignore
		}
	}
}
