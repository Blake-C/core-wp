<?php
/**
 * Add tabindex="-1" to the main content landmark for skip-link focus.
 *
 * The block template sets id="main-content" via the anchor attribute.
 * tabindex="-1" makes the element programmatically focusable so browsers
 * correctly move keyboard focus when the skip link is activated.
 *
 * @package core_wp
 */

/**
 * Inject tabindex="-1" onto the main content group block.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data including blockName and attrs.
 * @return string
 */
function core_wp_skip_link_tabindex( $block_content, $block ) {
	if (
		'core/group' === $block['blockName'] &&
		isset( $block['attrs']['tagName'] ) && 'main' === $block['attrs']['tagName'] &&
		isset( $block['attrs']['anchor'] ) && 'main-content' === $block['attrs']['anchor']
	) {
		$block_content = preg_replace( '/<main\b/', '<main tabindex="-1"', $block_content, 1 );
		$block_content = preg_replace( '/class="wp-block-group"/', 'class="wp-block-group skip-link-target"', $block_content, 1 );
	}
	return $block_content;
}
add_filter( 'render_block', 'core_wp_skip_link_tabindex', 10, 2 );
