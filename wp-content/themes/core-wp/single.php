<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package core_wp
 */

get_header(); ?>

	<?php core_wp_dev_helper( pathinfo( __FILE__, PATHINFO_FILENAME ) ); ?>

	<div class="row">

		<main class="medium-8 columns" id="content">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'single' ); ?>

				<?php
				/**
				 * Displays the navigation to next/previous post, when applicable.
				 *
				 * @link https://developer.wordpress.org/reference/functions/the_post_navigation/
				 */
				the_post_navigation(
					array(
						'prev_text'          => __( 'Previous', 'core_wp' ),
						'next_text'          => __( 'Next', 'core_wp' ),
						'in_same_term'       => false,
						'taxonomy'           => __( 'post_tag', 'core_wp' ),
						'screen_reader_text' => __( 'Continue Reading', 'core_wp' ),
					)
				);
				?>

				<!-- If comments are open or we have at least one comment, load up the comment template. -->
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<?php comments_template(); ?>
				<?php endif; ?>

			<?php endwhile; ?>
			<!-- End of the loop. -->
		</main><!-- #main -->

		<?php get_sidebar(); ?>

	</div>

<?php get_footer(); ?>
