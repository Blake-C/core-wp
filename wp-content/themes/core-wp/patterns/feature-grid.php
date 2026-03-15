<?php
/**
 * Title: Feature Grid
 * Slug: core-wp/feature-grid
 * Categories: core-wp-sections
 * Description: Three-column feature grid with heading and description in each column. Stacks to a single column on mobile.
 */
?>
<!-- wp:columns {"isStackedOnMobile":true} -->
<div class="wp-block-columns is-layout-flex">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"textAlign":"center"} -->
		<h3 class="wp-block-heading has-text-align-center">Feature One</h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">Describe the first key feature or benefit. Keep it concise and focused on value.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"textAlign":"center"} -->
		<h3 class="wp-block-heading has-text-align-center">Feature Two</h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">Describe the second key feature or benefit. Keep it concise and focused on value.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3,"textAlign":"center"} -->
		<h3 class="wp-block-heading has-text-align-center">Feature Three</h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">Describe the third key feature or benefit. Keep it concise and focused on value.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
