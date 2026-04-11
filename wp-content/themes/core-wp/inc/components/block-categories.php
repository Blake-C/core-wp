<?php
/**
 * Register a custom block category for theme blocks.
 *
 * Prepends the category so it appears at the top of the block inserter.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_block_categories' ) ) {
	/**
	 * Add the Core WP theme block category to the block inserter.
	 *
	 * @param array $categories Existing block categories.
	 * @return array Modified block categories with theme category prepended.
	 */
	function core_wp_block_categories( $categories ) {
		$theme_category = array(
			array(
				'slug'  => 'core-wp',
				'title' => __( 'Core WP Theme Blocks', 'core_wp' ),
				'icon'  => null,
			),
		);

		return array_merge( $theme_category, $categories );
	}
}
add_filter( 'block_categories_all', 'core_wp_block_categories' );
