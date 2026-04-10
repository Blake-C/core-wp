<?php
/**
 * Post listing card link wrap and term link normalization.
 *
 * Transforms the standard WordPress query-loop post items so each card
 * behaves like the Related Posts block: a single <a> wraps the entire card
 * and individual links inside (terms, title, image) are removed.
 *
 * Applies only to post-template blocks that carry the "post-grid" className,
 * keeping any other query loops on the site unaffected.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_post_terms_plain_text' ) ) {
	/**
	 * Output post terms as plain comma-separated text inside query loops.
	 *
	 * The individual <a> term links are stripped because the whole card is
	 * wrapped in a single <a> by core_wp_listing_card_link_wrap(). Nesting
	 * interactive elements inside an <a> is invalid HTML.
	 *
	 * @param string   $block_content Rendered block HTML.
	 * @param array    $block         Block definition array.
	 * @param WP_Block $instance      Block instance with query context.
	 * @return string                 Plain-text terms div, or original if not in a query loop.
	 */
	function core_wp_post_terms_plain_text( $block_content, $block, $instance ) {
		// Only strip links on listing pages (archive, category, tag, blog, search).
		// is_singular() is false for all listing page types and is the most
		// reliable scope guard — block context flags (queryId, postId) are also
		// set on singular FSE templates and should not be used here.
		if ( is_singular() || empty( trim( $block_content ) ) ) {
			return $block_content;
		}

		// Extract term names from <a> tags to avoid stray separator punctuation.
		preg_match_all( '/<a\b[^>]*>([^<]+)<\/a>/', $block_content, $matches );

		if ( empty( $matches[1] ) ) {
			return $block_content;
		}

		$terms_text = implode( ', ', array_map( 'trim', $matches[1] ) );

		// Preserve the wrapper div's class/attribute string.
		if ( preg_match( '/<div([^>]*)>/', $block_content, $wrapper ) ) {
			return '<div' . $wrapper[1] . '>' . esc_html( $terms_text ) . '</div>';
		}

		return $block_content;
	}
}
add_filter( 'render_block_core/post-terms', 'core_wp_post_terms_plain_text', 10, 3 );

if ( ! function_exists( 'core_wp_listing_card_link_wrap' ) ) {
	/**
	 * Wrap each post card in a single <a> link for post-grid listings.
	 *
	 * Scoped to post-template blocks with className containing "post-grid" so
	 * other query loops are not affected. Extracts the post ID from the
	 * wp-block-post class (e.g. "post-150") to build the permalink without an
	 * additional database query.
	 *
	 * @param string   $block_content Rendered post-template HTML.
	 * @param array    $block         Block definition array.
	 * @param WP_Block $instance      Block instance.
	 * @return string                 HTML with each <li> content wrapped in <a>.
	 */
	function core_wp_listing_card_link_wrap( $block_content, $block, $instance ) {
		if (
			empty( $block['attrs']['className'] ) ||
			! str_contains( $block['attrs']['className'], 'post-grid' )
		) {
			return $block_content;
		}

		return preg_replace_callback(
			'/(<li class="[^"]*\bwp-block-post\b[^"]*\bpost-(\d+)\b[^"]*">)(.*?)(<\/li>)/s',
			function ( $matches ) {
				$post_id   = (int) $matches[2];
				$permalink = get_permalink( $post_id );

				return $matches[1]
					. '<a href="' . esc_url( $permalink ) . '" target="_self">'
					. $matches[3]
					. '</a>'
					. $matches[4];
			},
			$block_content
		);
	}
}
add_filter( 'render_block_core/post-template', 'core_wp_listing_card_link_wrap', 10, 3 );
