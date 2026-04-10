<?php
/**
 * Related Posts block registration and render callback.
 *
 * Server-side rendered block that outputs a Related Stories section for the
 * current post. Queries the 3 most recent posts from the same categories,
 * excluding the current post. Each card shows the featured image, post tags
 * (as pills), and a linked post title.
 *
 * @package core_wp
 */

if ( ! function_exists( 'core_wp_related_posts_render' ) ) {
	/**
	 * Render callback for the core-wp/related-posts block.
	 *
	 * @param array    $attributes Block attributes (unused).
	 * @param string   $content    Inner block content (unused).
	 * @param WP_Block $block      Block instance (unused).
	 * @return string              Related posts HTML, or empty string if none found.
	 */
	function core_wp_related_posts_render( $attributes, $content, $block ) {
		$post = get_post();

		if ( ! $post ) {
			return '';
		}

		$category_ids = wp_get_post_categories( $post->ID, array( 'fields' => 'ids' ) );

		if ( empty( $category_ids ) ) {
			return '';
		}

		$related_query = new WP_Query(
			array(
				'category__in'   => $category_ids,
				'order'          => 'DESC',
				'orderby'        => 'date',
				'post__not_in'   => array( $post->ID ),
				'post_type'      => 'post',
				'posts_per_page' => 3,
			)
		);

		if ( ! $related_query->have_posts() ) {
			return '';
		}

		$output  = '<section class="single-post__related">';
		$output .= '<h2 class="single-post__related-heading">' . esc_html__( 'Related Stories', 'core_wp' ) . '</h2>';
		$output .= '<hr class="single-post__related-divider" />';
		$output .= '<div class="post-grid">';

		while ( $related_query->have_posts() ) {
			$related_query->the_post();

			$related_id = get_the_ID();
			$post_url   = get_permalink();
			$post_title = get_the_title();
			$tags       = get_the_terms( $related_id, 'post_tag' );

			$output .= '<a href="' . esc_url( $post_url ) . '" class="post-card">';

			$output .= '<article>';

			$output .= '<figure class="post-card__image">';

			if ( has_post_thumbnail() ) {
				$output .= get_the_post_thumbnail( $related_id, 'large' );
			} else {
				$fallback_url = get_template_directory_uri() . '/assets/images/related-post-placeholder.webp';
				$output      .= '<img src="' . esc_url( $fallback_url ) . '" alt="" width="800" height="600" />';
			}

			$output .= '</figure>';

			if ( $tags && ! is_wp_error( $tags ) ) {
				$output .= '<div class="post-card__tags">';
				foreach ( $tags as $tag ) {
					$output .= '<span class="post-card__tag">' . esc_html( $tag->name ) . '</span>';
				}
				$output .= '</div>';
			}

			$output .= '<h3 class="post-card__title">' . esc_html( $post_title ) . '</h3>';

			$output .= '</article>';
			$output .= '</a>';
		}

		wp_reset_postdata();

		$output .= '</div>';
		$output .= '</section>';

		return $output;
	}
}

/**
 * Register the core-wp/related-posts block.
 */
function core_wp_register_related_posts_block() {
	wp_register_script(
		'core-wp-related-posts-editor',
		get_template_directory_uri() . '/inc/blocks/related-posts/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n' ),
		'1.0.0',
		true
	);

	register_block_type(
		__DIR__,
		array(
			'render_callback' => 'core_wp_related_posts_render',
		)
	);
}
add_action( 'init', 'core_wp_register_related_posts_block' );
