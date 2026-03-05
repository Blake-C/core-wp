<?php
/**
 * Add responsive container to embeds
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_embed_video_html' ) ) {
	/**
	 * Adds responsive classes to video embeds.
	 *
	 * @method core_wp_embed_video_html
	 * @param  string $html Media HTML.
	 * @param  string $url  The attempted embed URL.
	 * @return string       New media HTML
	 */
	function core_wp_embed_video_html( $html, $url ) {
		$youtube = strpos( $url, 'youtube.com' );
		$vimeo   = strpos( $url, 'vimeo.com' );

		if ( false !== $youtube || false !== $vimeo ) {
			return '<div class="responsive-embed widescreen">' . $html . '</div>';
		} else {
			return $html;
		}
	}
}
add_filter( 'embed_oembed_html', 'core_wp_embed_video_html', 10, 3 );
add_filter( 'video_embed_html', 'core_wp_embed_video_html' ); // Jetpack.
