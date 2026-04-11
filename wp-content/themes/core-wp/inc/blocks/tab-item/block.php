<?php
/**
 * Tab Item block registration.
 *
 * This block has no render callback. The parent core-wp/tabs render callback
 * iterates $block->inner_blocks directly to build coordinated nav/panel HTML
 * with proper ARIA IDs. The save function (InnerBlocks.Content) persists the
 * inner content to post_content so ->render() works at query time.
 *
 * @package core_wp
 */

/**
 * Register the core-wp/tab-item block.
 */
function core_wp_register_tab_item_block() {
	wp_register_script(
		'core-wp-tab-item-editor',
		get_template_directory_uri() . '/inc/blocks/tab-item/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		'1.0.0',
		true
	);

	// No render_callback — the parent tabs block renders the full output.
	register_block_type( __DIR__ );
}
add_action( 'init', 'core_wp_register_tab_item_block' );
