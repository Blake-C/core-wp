<?php
/**
 * Tabs block registration and render callback.
 *
 * Server-side rendered block. The render callback bypasses the pre-rendered
 * $content string and iterates $block->inner_blocks directly so it can build
 * both the tab nav (<ul role="tablist">) and the tab panels from a single pass,
 * assigning coordinated ARIA IDs to each button/panel pair.
 *
 * When mobileAccordion is enabled, each panel also receives a
 * .tabs__mobile-header button and a .tabs__panel-content wrapper. JS detects
 * the viewport via matchMedia and switches between tab and accordion behaviour.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_tabs_render' ) ) {
	/**
	 * Render callback for the core-wp/tabs block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Pre-rendered inner block HTML (unused; see above).
	 * @param WP_Block $block      Block instance, used to access inner_blocks.
	 * @return string              Complete tabs HTML with nav and panels.
	 */
	function core_wp_tabs_render( $attributes, $content, $block ) {
		if ( empty( $block->inner_blocks ) ) {
			return '';
		}

		$mobile_accordion   = ! empty( $attributes['mobileAccordion'] );
		$mobile_single_open = ! empty( $attributes['mobileSingleOpen'] );

		$nav_items   = '';
		$panel_items = '';
		$first       = true;

		foreach ( $block->inner_blocks as $tab_item ) {
			$title    = isset( $tab_item->attributes['title'] ) ? $tab_item->attributes['title'] : '';
			$tab_id   = wp_unique_id( 'tab-btn-' );
			$panel_id = wp_unique_id( 'tab-panel-' );

			// ── Nav button ────────────────────────────────────────────────

			$nav_items .= '<li class="tabs__nav-item" role="presentation">';
			$nav_items .= '<button';
			$nav_items .= ' class="tabs__tab' . ( $first ? ' tabs__tab--active' : '' ) . '"';
			$nav_items .= ' role="tab"';
			$nav_items .= ' id="' . esc_attr( $tab_id ) . '"';
			$nav_items .= ' aria-selected="' . ( $first ? 'true' : 'false' ) . '"';
			$nav_items .= ' aria-controls="' . esc_attr( $panel_id ) . '"';
			$nav_items .= ' tabindex="' . ( $first ? '0' : '-1' ) . '"';
			$nav_items .= '>';
			$nav_items .= esc_html( $title );
			$nav_items .= '</button>';
			$nav_items .= '</li>';

			// ── Panel ─────────────────────────────────────────────────────

			$panel_content = '';
			foreach ( $tab_item->inner_blocks as $inner ) {
				$panel_content .= $inner->render();
			}

			$panel_items .= '<div';
			$panel_items .= ' class="tabs__panel' . ( $first ? ' tabs__panel--active' : '' ) . '"';
			$panel_items .= ' id="' . esc_attr( $panel_id ) . '"';
			$panel_items .= ' role="tabpanel"';
			$panel_items .= ' aria-labelledby="' . esc_attr( $tab_id ) . '"';
			$panel_items .= ' tabindex="0"';

			if ( ! $first ) {
				$panel_items .= ' hidden';
			}

			$panel_items .= '>';

			if ( $mobile_accordion ) {
				// Mobile accordion: header button + content wrapper with separate ID
				// so JS can toggle the content independently of the tab panel.
				$content_id = wp_unique_id( 'tab-content-' );

				$panel_items .= '<button class="tabs__mobile-header"';
				$panel_items .= ' aria-expanded="' . ( $first ? 'true' : 'false' ) . '"';
				$panel_items .= ' aria-controls="' . esc_attr( $content_id ) . '"';
				$panel_items .= '>';
				$panel_items .= '<span class="tabs__mobile-header-label">' . esc_html( $title ) . '</span>';
				$panel_items .= '<span class="tabs__mobile-icon" aria-hidden="true"></span>';
				$panel_items .= '</button>';

				$panel_items .= '<div class="tabs__panel-content" id="' . esc_attr( $content_id ) . '">';
				$panel_items .= $panel_content;
				$panel_items .= '</div>';
			} else {
				$panel_items .= $panel_content;
			}

			$panel_items .= '</div>';

			$first = false;
		}

		$data_attrs = ' data-tabs';
		if ( $mobile_accordion ) {
			$data_attrs .= ' data-mobile-accordion';
		}
		if ( $mobile_accordion && $mobile_single_open ) {
			$data_attrs .= ' data-mobile-single-open';
		}

		$output  = '<div class="tabs"' . $data_attrs . '>';
		$output .= '<ul class="tabs__nav" role="tablist">' . $nav_items . '</ul>';
		$output .= '<div class="tabs__panels">' . $panel_items . '</div>';
		$output .= '</div>';

		return $output;
	}
}

/**
 * Register the core-wp/tabs block.
 */
function core_wp_register_tabs_block() {
	wp_register_script(
		'core-wp-tabs-editor',
		get_template_directory_uri() . '/inc/blocks/tabs/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		'1.0.0',
		true
	);

	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'core_wp_tabs_render',
		)
	);
}
add_action( 'init', 'core_wp_register_tabs_block' );
