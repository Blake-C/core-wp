<?php
/**
 * Title: Midline CTA — Large on Right (Light)
 * Slug: core-wp/midline-cta-large-right-light
 * Categories: core-wp-sections
 * Description: Small descriptor text on the left (4 cols) with a large heading, stat, and CTA on the right (8 cols). Stacks immediately on tablet and mobile. Light mode. Set alignment to "Full Width" for full-bleed background.
 */
?>
<!-- wp:group {"className":"midline-cta midline-cta--light","backgroundColor":"light-gray","layout":{"type":"constrained"}} -->
<div class="wp-block-group midline-cta midline-cta--light has-light-gray-background-color has-background">

	<!-- wp:columns {"isStackedOnMobile":true,"className":"midline-cta__cols midline-cta__cols--stack-tablet"} -->
	<div class="wp-block-columns is-layout-flex midline-cta__cols midline-cta__cols--stack-tablet">

		<!-- wp:column {"width":"33.33%","verticalAlignment":"top"} -->
		<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:33.33%">

			<!-- wp:paragraph {"fontSize":"small","textColor":"dark-gray"} -->
			<p class="has-small-font-size has-dark-gray-color has-text-color">A short descriptor or category label that gives context for what follows.</p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"66.66%"} -->
		<div class="wp-block-column" style="flex-basis:66.66%">

			<!-- wp:heading {"level":2,"textColor":"black"} -->
			<h2 class="wp-block-heading has-black-color has-text-color has-medium-font-size">A Large Headline That Anchors the Right Side</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"midline-cta__stat-number","textColor":"dark-navy","style":{"spacing":{"margin":{"top":"1.5rem"}}}} -->
			<p class="midline-cta__stat-number has-dark-navy-color has-text-color" style="margin-top:1.5rem">30%</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"1.5rem"}}}} -->
			<div class="wp-block-buttons" style="margin-top:1.5rem">
				<!-- wp:button {"backgroundColor":"dark-navy","textColor":"white"} -->
				<div class="wp-block-button"><a class="wp-block-button__link has-dark-navy-background-color has-white-color has-background has-text-color wp-element-button">Get Started</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
