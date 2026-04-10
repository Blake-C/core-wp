<?php
/**
 * Social Share block registration and render callback.
 *
 * Server-side rendered block that outputs social share links for the current
 * post. Supports LinkedIn, X (Twitter), and Facebook with configurable labels,
 * individual enable/disable toggles, and an arbitrary number of custom links.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_social_share_render' ) ) {
	/**
	 * Render callback for the core-wp/social-share block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Inner block content (unused).
	 * @param WP_Block $block      Block instance (unused).
	 * @return string              Social share links HTML, or empty string if nothing is enabled.
	 */
	function core_wp_social_share_render( $attributes, $content, $block ) {
		$share_label      = isset( $attributes['shareLabel'] ) ? $attributes['shareLabel'] : 'Share:';
		$linkedin_enabled = isset( $attributes['linkedinEnabled'] ) ? (bool) $attributes['linkedinEnabled'] : true;
		$linkedin_text    = isset( $attributes['linkedinText'] ) ? $attributes['linkedinText'] : 'LinkedIn';
		$twitter_enabled  = isset( $attributes['twitterEnabled'] ) ? (bool) $attributes['twitterEnabled'] : true;
		$twitter_text     = isset( $attributes['twitterText'] ) ? $attributes['twitterText'] : 'X (Twitter)';
		$facebook_enabled = isset( $attributes['facebookEnabled'] ) ? (bool) $attributes['facebookEnabled'] : true;
		$facebook_text    = isset( $attributes['facebookText'] ) ? $attributes['facebookText'] : 'Facebook';
		$custom_links     = isset( $attributes['customLinks'] ) && is_array( $attributes['customLinks'] )
			? $attributes['customLinks']
			: array();

		// Filter custom links to only include those with both text and URL set.
		$valid_custom_links = array_filter(
			$custom_links,
			function ( $link ) {
				return ! empty( $link['text'] ) && ! empty( $link['url'] );
			}
		);

		$has_any = $linkedin_enabled || $twitter_enabled || $facebook_enabled || ! empty( $valid_custom_links );

		if ( ! $has_any ) {
			return '';
		}

		$url   = rawurlencode( (string) get_permalink() );
		$title = rawurlencode( (string) get_the_title() );

		$output  = '<div class="single-post__social-links">';
		$output .= '<span class="single-post__social-label">' . esc_html( $share_label ) . '</span>';

		if ( $linkedin_enabled ) {
			$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url;
			$output      .= '<a href="' . esc_url( $linkedin_url ) . '" class="single-post__social-link single-post__social-link--linkedin" target="_blank" rel="noopener noreferrer">' . esc_html( $linkedin_text ) . '</a>';
		}

		if ( $twitter_enabled ) {
			$twitter_url = 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title;
			$output     .= '<a href="' . esc_url( $twitter_url ) . '" class="single-post__social-link single-post__social-link--twitter" target="_blank" rel="noopener noreferrer">' . esc_html( $twitter_text ) . '</a>';
		}

		if ( $facebook_enabled ) {
			$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
			$output      .= '<a href="' . esc_url( $facebook_url ) . '" class="single-post__social-link single-post__social-link--facebook" target="_blank" rel="noopener noreferrer">' . esc_html( $facebook_text ) . '</a>';
		}

		foreach ( $valid_custom_links as $link ) {
			$output .= '<a href="' . esc_url( $link['url'] ) . '" class="single-post__social-link single-post__social-link--custom" target="_blank" rel="noopener noreferrer">' . esc_html( $link['text'] ) . '</a>';
		}

		$output .= '</div>';

		return $output;
	}
}

/**
 * Register the core-wp/social-share block.
 */
function core_wp_register_social_share_block() {
	wp_register_script(
		'core-wp-social-share-editor',
		get_template_directory_uri() . '/inc/blocks/social-share/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		'1.0.0',
		true
	);

	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'core_wp_social_share_render',
		)
	);
}
add_action( 'init', 'core_wp_register_social_share_block' );
