<?php
/**
 * Title: Midline CTA — Stats (Dark)
 * Slug: core-wp/midline-cta-stats-dark
 * Categories: core-wp-sections
 * Description: Heading on the left (8 cols) with a large stat number and supporting text on the right (4 cols). Transitions 8/4 → 6/6 → stacked. Dark navy mode. Set alignment to "Full Width" for full-bleed background.
 */
?>
<!-- wp:group {"className":"midline-cta midline-cta-dark","backgroundColor":"dark-navy","layout":{"type":"constrained"}} -->
<div class="wp-block-group midline-cta midline-cta-dark has-dark-navy-background-color has-background">

	<!-- wp:columns {"isStackedOnMobile":true,"className":"midline-cta-cols midline-cta-cols-responsive"} -->
	<div class="wp-block-columns is-layout-flex midline-cta-cols midline-cta-cols-responsive">

		<!-- wp:column {"width":"66.66%"} -->
		<div class="wp-block-column" style="flex-basis:66.66%">

			<!-- wp:heading {"level":2,"textColor":"white"} -->
			<h2 class="wp-block-heading has-white-color has-text-color">A Headline That Drives the Point Home</h2>
			<!-- /wp:heading -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"33.33%"} -->
		<div class="wp-block-column" style="flex-basis:33.33%">

			<!-- wp:paragraph {"className":"midline-cta-stat-number","textColor":"white"} -->
			<p class="midline-cta-stat-number has-white-color has-text-color">30%</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"textColor":"light-gray"} -->
			<p class="has-light-gray-color has-text-color">Supporting context for the stat. This sentence stays below the number and wraps naturally.</p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
