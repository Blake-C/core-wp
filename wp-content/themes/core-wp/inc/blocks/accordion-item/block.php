<?php
/**
 * Accordion Item block registration and render callback.
 *
 * Server-side rendered child block of core-wp/accordion. Outputs a single
 * collapsible item with an accessible button/panel pair. Inner blocks provide
 * the rich content (headings, paragraphs, lists, etc.).
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_accordion_item_render' ) ) {
	/**
	 * Render callback for the core-wp/accordion-item block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Inner block content (rich content area).
	 * @param WP_Block $block      Block instance (unused).
	 * @return string              Accordion item HTML.
	 */
	function core_wp_accordion_item_render( $attributes, $content, $block ) {
		$title        = isset( $attributes['title'] ) ? $attributes['title'] : '';
		$image_url    = isset( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
		$image_alt    = isset( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';
		$default_open = ! empty( $attributes['defaultOpen'] );

		$button_id = wp_unique_id( 'accordion-btn-' );
		$panel_id  = wp_unique_id( 'accordion-panel-' );

		$data_attrs = '';
		if ( $image_url ) {
			$data_attrs .= ' data-image-url="' . esc_attr( $image_url ) . '"';
			$data_attrs .= ' data-image-alt="' . esc_attr( $image_alt ) . '"';
		}

		$output  = '<div class="accordion__item"' . $data_attrs . '>';
		$output .= '<button class="accordion__header" id="' . esc_attr( $button_id ) . '" aria-expanded="' . ( $default_open ? 'true' : 'false' ) . '" aria-controls="' . esc_attr( $panel_id ) . '">';
		$output .= '<span class="accordion__title">' . esc_html( $title ) . '</span>';
		$output .= '<span class="accordion__icon" aria-hidden="true"></span>';
		$output .= '</button>';
		$output .= '<div class="accordion__content" id="' . esc_attr( $panel_id ) . '" role="region" aria-labelledby="' . esc_attr( $button_id ) . '"';

		if ( ! $default_open ) {
			$output .= ' hidden';
		}

		$output .= '>';
		$output .= $content;
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
}

/**
 * Register the core-wp/accordion-item block.
 */
function core_wp_register_accordion_item_block() {
	wp_register_script(
		'core-wp-accordion-item-editor',
		get_template_directory_uri() . '/inc/blocks/accordion-item/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		'1.0.0',
		true
	);

	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'core_wp_accordion_item_render',
		)
	);
}
add_action( 'init', 'core_wp_register_accordion_item_block' );
