<?php
/**
 * Add tabindex="-1" to the main content landmark for skip-link focus.
 *
 * WordPress FSE block themes assign id="wp--skip-link--target" to the first
 * <main> element automatically. tabindex="-1" makes the element programmatically
 * focusable so browsers correctly move keyboard focus when the skip link is activated.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_skip_link' ) ) {
	/**
	 * Output the skip link as the first element in <body>.
	 *
	 * Using wp_body_open guarantees the link is before all other focusable
	 * elements and avoids block-rendering stacking context issues.
	 */
	function core_wp_skip_link() {
		echo '<a class="skip-link" href="#wp--skip-link--target">' . esc_html__( 'Skip to content', 'core_wp' ) . '</a>' . "\n";
	}
}
add_action( 'wp_body_open', 'core_wp_skip_link' );

/**
 * Inject tabindex="-1" onto the main content group block.
 *
 * Matches any core/group block rendered as <main> regardless of whether
 * an anchor attribute is set, since WordPress FSE themes assign the
 * id="wp--skip-link--target" attribute automatically.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data including blockName and attrs.
 * @return string
 */
function core_wp_skip_link_tabindex( $block_content, $block ) {
	if (
		'core/group' === $block['blockName'] &&
		isset( $block['attrs']['tagName'] ) && 'main' === $block['attrs']['tagName']
	) {
		if ( false === strpos( $block_content, 'tabindex=' ) ) {
			$block_content = preg_replace( '/<main\b/', '<main tabindex="-1"', $block_content, 1 );
		}
		$block_content = preg_replace( '/class="(wp-block-group[^"]*)"/', 'class="$1 skip-link-target"', $block_content, 1 );
	}
	return $block_content;
}
add_filter( 'render_block', 'core_wp_skip_link_tabindex', 10, 2 );
