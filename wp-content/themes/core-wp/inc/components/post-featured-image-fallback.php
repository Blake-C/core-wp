<?php
/**
 * Fallback image for the core/post-featured-image block.
 *
 * When a post has no featured image the block renders an empty string.
 * This filter intercepts that case and outputs a placeholder image so that
 * every card in the post listing grid has a consistent image slot.
 *
 * The same placeholder used by the Related Posts block is reused here so
 * both contexts share a single source asset.
 *
 * Note: no <a> link is added here. Post listing cards use a single outer
 * <a> wrapper (added by core_wp_listing_card_link_wrap in post-listing-card-wrap.php)
 * so individual block links are intentionally omitted.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_post_featured_image_fallback' ) ) {
	/**
	 * Replace an empty post-featured-image render with a placeholder.
	 *
	 * Only fires in query-loop contexts (listing pages). Singular post fallbacks
	 * are handled by core_wp_featured_image_fallback in template-tags/single-post.php.
	 *
	 * @param string   $block_content Rendered block HTML (empty when no image).
	 * @param array    $block         Block definition array.
	 * @param WP_Block $instance      Block instance carrying query context.
	 * @return string                 Original HTML, or placeholder figure HTML.
	 */
	function core_wp_post_featured_image_fallback( $block_content, $block, $instance ) {
		// Only inject a fallback on listing pages (archive, category, tag, blog,
		// search). Singular post fallbacks are handled by single-post.php.
		// is_singular() is false for all listing page types, making it the most
		// reliable scope guard — block context flags like queryId/postId are also
		// set on singular FSE templates and cannot be used here.
		if ( ! empty( $block_content ) || is_singular() ) {
			return $block_content;
		}

		$post_id = isset( $instance->context['postId'] ) ? (int) $instance->context['postId'] : 0;

		if ( ! $post_id ) {
			return $block_content;
		}

		$fallback_url = get_template_directory_uri() . '/assets/images/related-post-placeholder.webp';

		return '<figure class="wp-block-post-featured-image">'
			. '<img src="' . esc_url( $fallback_url ) . '" alt="" width="800" height="600" />'
			. '</figure>';
	}
}

add_filter( 'render_block_core/post-featured-image', 'core_wp_post_featured_image_fallback', 10, 3 );
