<?php
/**
 * Template tags for single blog posts.
 *
 * Registers filters and shortcodes used in the single post template.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_featured_image_fallback' ) ) {
	/**
	 * Renders a fallback image when the post has no featured image set.
	 *
	 * Hooks into the block render pipeline for core/post-featured-image. When the
	 * block produces empty output (no thumbnail assigned), this replaces it with
	 * the shared placeholder SVG. The block's className attribute is preserved so
	 * the existing CSS grid and height rules still apply.
	 *
	 * Scoped to singular posts only so archive/query-loop contexts are unaffected.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block definition array.
	 * @return string               Block HTML, or a fallback <figure> if empty.
	 */
	function core_wp_featured_image_fallback( $block_content, $block ) {
		if ( ! is_singular( 'post' ) || ! empty( trim( $block_content ) ) ) {
			return '<div class="single-post__featured-image-wrapper">' . $block_content . '</div>';
		}

		$class_name   = ! empty( $block['attrs']['className'] ) ? ' ' . esc_attr( $block['attrs']['className'] ) : '';
		$fallback_url = get_template_directory_uri() . '/assets/images/related-post-placeholder.webp';

		return '<div class="single-post__featured-image-wrapper"><figure class="wp-block-post-featured-image' . $class_name . '">'
			. '<img src="' . esc_url( $fallback_url ) . '" alt="" width="800" height="600" />'
			. '</figure></div>';
	}
}
add_filter( 'render_block_core/post-featured-image', 'core_wp_featured_image_fallback', 10, 2 );

if ( ! function_exists( 'core_wp_social_share_shortcode' ) ) {
	/**
	 * Outputs anchor-based social share links for the current post.
	 *
	 * Uses share URL schemes (no JavaScript embeds) for LinkedIn, X (Twitter),
	 * and Facebook. Safe to call only when in the loop (single post context).
	 *
	 * @param  array  $atts    Shortcode attributes (unused).
	 * @param  string $content Enclosed content (unused).
	 * @return string          Share links HTML.
	 */
	function core_wp_social_share_shortcode( $atts, $content = null ) {
		$url   = rawurlencode( (string) get_permalink() );
		$title = rawurlencode( (string) get_the_title() );

		$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url;
		$twitter_url  = 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title;
		$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;

		$output  = '<div class="single-post__social-links">';
		$output .= '<span class="single-post__social-label">' . esc_html__( 'Share:', 'core_wp' ) . '</span>';
		$output .= '<a href="' . esc_url( $linkedin_url ) . '" class="single-post__social-link single-post__social-link--linkedin" target="_blank" rel="noopener noreferrer">' . esc_html__( 'LinkedIn', 'core_wp' ) . '</a>';
		$output .= '<a href="' . esc_url( $twitter_url ) . '" class="single-post__social-link single-post__social-link--twitter" target="_blank" rel="noopener noreferrer">' . esc_html__( 'X (Twitter)', 'core_wp' ) . '</a>';
		$output .= '<a href="' . esc_url( $facebook_url ) . '" class="single-post__social-link single-post__social-link--facebook" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Facebook', 'core_wp' ) . '</a>';
		$output .= '</div>';

		return $output;
	}
}
add_shortcode( 'core_wp_social_share', 'core_wp_social_share_shortcode' );
