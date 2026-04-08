<?php
/**
 * Title: Midline CTA — Large (Light)
 * Slug: core-wp/midline-cta-large-light
 * Categories: core-wp-sections
 * Description: Large heading on the left (8 cols) with a CTA button on the right (4 cols). Transitions 8/4 → 6/6 → stacked. Light mode. Set alignment to "Full Width" for full-bleed background.
 */
?>
<!-- wp:group {"className":"midline-cta midline-cta-light","backgroundColor":"light-gray","layout":{"type":"constrained"}} -->
<div class="wp-block-group midline-cta midline-cta-light has-light-gray-background-color has-background">

	<!-- wp:columns {"isStackedOnMobile":true,"className":"midline-cta-cols midline-cta-cols-responsive","style":{"spacing":{"blockGap":{"top":"2rem","left":"2rem"}}}} -->
	<div class="wp-block-columns is-layout-flex midline-cta-cols midline-cta-cols-responsive">

		<!-- wp:column {"width":"66.66%","verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%">

			<!-- wp:heading {"level":2,"textColor":"black"} -->
			<h2 class="wp-block-heading has-black-color has-text-color">A Large Headline That Makes an Impact</h2>
			<!-- /wp:heading -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"33.33%","verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33.33%">

			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
			<div class="wp-block-buttons">
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
