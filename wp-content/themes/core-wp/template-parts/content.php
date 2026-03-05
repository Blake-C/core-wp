<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package core_wp
 */

?>

<?php core_wp_dev_helper( pathinfo( __FILE__, PATHINFO_FILENAME ) ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
			<?php core_wp_posted_on(); ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					'Continue reading %s <span class="meta-nav">&rarr;</span>',
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				the_title( '<span class="show-for-sr">"', '"</span>', false )
			)
		);
		?>

		<?php
		/**
		 * Displays page-links for paginated posts
		 *
		 * @link: https://codex.wordpress.org/Function_Reference/wp_link_pages
		 */
		wp_link_pages(
			array(
				'before' => '<div class="page-links">Pages: ',
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<footer class="entry-footer">
		<?php core_wp_entry_footer(); ?>
	</footer>
</article>
