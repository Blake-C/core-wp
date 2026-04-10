<?php
/**
 * Copyright block registration and render callback.
 *
 * Server-side rendered block that outputs a dynamic copyright line containing
 * the current year and optionally the site name. Supports custom prefix and
 * suffix text around the copyright string.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_copyright_render' ) ) {
	/**
	 * Render callback for the core-wp/copyright block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Inner block content (unused).
	 * @param WP_Block $block      Block instance (unused).
	 * @return string              Copyright HTML.
	 */
	function core_wp_copyright_render( $attributes, $content, $block ) {
		$show_site_name = isset( $attributes['showSiteName'] ) ? (bool) $attributes['showSiteName'] : true;
		$prefix_text    = isset( $attributes['prefixText'] ) ? trim( $attributes['prefixText'] ) : '';
		$suffix_text    = isset( $attributes['suffixText'] ) ? trim( $attributes['suffixText'] ) : '';

		$output = '<p class="site-copyright">';

		if ( '' !== $prefix_text ) {
			$output .= esc_html( $prefix_text ) . ' ';
		}

		$output .= '&copy; ' . gmdate( 'Y' );

		if ( $show_site_name ) {
			$output .= ' ' . esc_html( get_bloginfo( 'name' ) );
		}

		if ( '' !== $suffix_text ) {
			$output .= ' ' . esc_html( $suffix_text );
		}

		$output .= '</p>';

		return $output;
	}
}

/**
 * Register the core-wp/copyright block.
 */
function core_wp_register_copyright_block() {
	wp_register_script(
		'core-wp-copyright-editor',
		get_template_directory_uri() . '/inc/blocks/copyright/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		'1.0.0',
		true
	);

	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'core_wp_copyright_render',
		)
	);
}
add_action( 'init', 'core_wp_register_copyright_block' );
