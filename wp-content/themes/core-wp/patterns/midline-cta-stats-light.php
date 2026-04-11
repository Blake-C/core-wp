<?php
/**
 * Title: Midline CTA — Stats (Light)
 * Slug: core-wp/midline-cta-stats-light
 * Categories: core-wp-sections
 * Description: Heading on the left (8 cols) with a large stat number and supporting text on the right (4 cols). Transitions 8/4 → 6/6 → stacked. Light mode. Set alignment to "Full Width" for full-bleed background.
 */
?>
<!-- wp:group {"className":"midline-cta midline-cta--light","backgroundColor":"light-gray","layout":{"type":"constrained"}} -->
<div class="wp-block-group midline-cta midline-cta--light has-light-gray-background-color has-background">

	<!-- wp:columns {"isStackedOnMobile":true,"className":"midline-cta__cols midline-cta__cols--responsive"} -->
	<div class="wp-block-columns is-layout-flex midline-cta__cols midline-cta__cols--responsive">

		<!-- wp:column {"width":"66.66%"} -->
		<div class="wp-block-column" style="flex-basis:66.66%">

			<!-- wp:heading {"level":2,"textColor":"black"} -->
			<h2 class="wp-block-heading has-black-color has-text-color">A Headline That Drives the Point Home</h2>
			<!-- /wp:heading -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"33.33%"} -->
		<div class="wp-block-column" style="flex-basis:33.33%">

			<!-- wp:paragraph {"className":"midline-cta__stat-number","textColor":"dark-navy"} -->
			<p class="midline-cta__stat-number has-dark-navy-color has-text-color">30%</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"textColor":"black"} -->
			<p class="has-black-color has-text-color">Supporting context for the stat. This sentence stays below the number and wraps naturally.</p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
