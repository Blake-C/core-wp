<?php
/**
 * Accordion block registration and render callback.
 *
 * Server-side rendered block that wraps accordion items in a configurable
 * layout. Supports full-width, float (left/right), and 50/50 split with
 * a per-item image panel.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_accordion_render' ) ) {
	/**
	 * Render callback for the core-wp/accordion block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Inner block content (rendered accordion items).
	 * @param WP_Block $block      Block instance.
	 * @return string              Accordion HTML.
	 */
	function core_wp_accordion_render( $attributes, $content, $block ) {
		$layout          = isset( $attributes['layout'] ) ? $attributes['layout'] : 'full';
		$float_direction = isset( $attributes['floatDirection'] ) ? $attributes['floatDirection'] : 'left';
		$split_layout    = ! empty( $attributes['splitLayout'] );
		$single_open     = ! empty( $attributes['singleOpen'] );

		$classes = array( 'accordion' );

		if ( 'float' === $layout ) {
			$safe_direction = 'right' === $float_direction ? 'right' : 'left';
			$classes[]      = 'accordion--float-' . $safe_direction;
		} elseif ( 'full' === $layout && $split_layout ) {
			$classes[] = 'accordion--split';
		}

		// Single-open mode: split layout always enforces it; singleOpen attribute makes it opt-in for other layouts.
		$enforce_single = $split_layout || $single_open;
		$data_attrs     = $enforce_single ? ' data-single-open="true"' : '';

		$output  = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-accordion' . $data_attrs . '>';
		$output .= '<div class="accordion__items">' . $content . '</div>';

		if ( 'full' === $layout && $split_layout ) {
			$first_image_url = '';
			$first_image_alt = '';

			foreach ( $block->inner_blocks as $inner_block ) {
				if ( ! empty( $inner_block->attributes['imageUrl'] ) ) {
					$first_image_url = $inner_block->attributes['imageUrl'];
					$first_image_alt = isset( $inner_block->attributes['imageAlt'] ) ? $inner_block->attributes['imageAlt'] : '';
					break;
				}
			}

			$output .= '<div class="accordion__image-panel">';

			if ( $first_image_url ) {
				$output .= '<img class="accordion__image" src="' . esc_url( $first_image_url ) . '" alt="' . esc_attr( $first_image_alt ) . '" />';
			}

			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}
}

/**
 * Register the core-wp/accordion block.
 */
function core_wp_register_accordion_block() {
	wp_register_script(
		'core-wp-accordion-editor',
		get_template_directory_uri() . '/inc/blocks/accordion/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		'1.0.0',
		true
	);

	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'core_wp_accordion_render',
		)
	);
}
add_action( 'init', 'core_wp_register_accordion_block' );
