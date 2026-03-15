<?php
/**
 * Title: Hero Section
 * Slug: core-wp/hero
 * Categories: core-wp-sections
 * Description: Full-width hero with heading, description, and call-to-action button. Replace the cover background by clicking the block and uploading an image.
 */
?>
<!-- wp:cover {"minHeight":500,"minHeightUnit":"px","isDark":false,"layout":{"type":"constrained"}} -->
<div class="wp-block-cover" style="min-height:500px">
	<span aria-hidden="true" class="wp-block-cover__background has-background-dim-50 has-background-dim"></span>
	<div class="wp-block-cover__inner-container">
		<!-- wp:heading {"level":1,"textAlign":"center"} -->
		<h1 class="wp-block-heading has-text-align-center">Your Headline Here</h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">A short description that supports your headline and encourages visitors to take action.</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Get Started</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
</div>
<!-- /wp:cover -->
