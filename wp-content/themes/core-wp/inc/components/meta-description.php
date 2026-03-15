<?php
/**
 * Output a <meta name="description"> tag in <head>.
 *
 * Provides a sensible fallback for every page type. SEO plugins such as
 * Yoast SEO or RankMath will automatically replace this output when active.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_meta_description' ) ) {
	/**
	 * Output a <meta name="description"> tag in <head>.
	 */
	function core_wp_meta_description() {
		if ( is_singular() ) {
			$description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );
		} elseif ( is_front_page() || is_home() ) {
			$description = get_bloginfo( 'description' );
		} elseif ( is_search() ) {
			$description = sprintf(
				/* translators: %s: search query */
				esc_html_x( 'Search results for: %s', 'meta description', 'core_wp' ),
				get_search_query()
			);
		} elseif ( is_archive() ) {
			$description = wp_strip_all_tags( get_the_archive_description() );
		} else {
			$description = get_bloginfo( 'description' );
		}

		if ( $description ) {
			echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '">' . "\n";
		}
	}
}
add_action( 'wp_head', 'core_wp_meta_description' );
