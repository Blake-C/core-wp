<?php
/**
 * Title: Midline CTA — Simple (Dark)
 * Slug: core-wp/midline-cta-simple-dark
 * Categories: core-wp-sections
 * Description: Heading and CTA on the left with a decorative waveform background on the right. Background column hides on mobile. Dark navy mode. Set alignment to "Full Width" in the block editor for a full-bleed background.
 */
?>
<!-- wp:group {"className":"midline-cta midline-cta-simple midline-cta-dark","backgroundColor":"dark-navy","layout":{"type":"constrained"}} -->
<div class="wp-block-group midline-cta midline-cta-simple midline-cta-dark has-dark-navy-background-color has-background">

	<!-- wp:columns {"isStackedOnMobile":true,"className":"midline-cta-cols","style":{"spacing":{"blockGap":"0"}}} -->
	<div class="wp-block-columns is-layout-flex midline-cta-cols" style="gap:0">

		<!-- wp:column {"width":"60%","className":"midline-cta-content-col"} -->
		<div class="wp-block-column midline-cta-content-col" style="flex-basis:60%">

			<!-- wp:heading {"level":2,"textColor":"white"} -->
			<h2 class="wp-block-heading has-white-color has-text-color">Your Compelling Headline Here</h2>
			<!-- /wp:heading -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"1.5rem"}}}} -->
			<div class="wp-block-buttons" style="margin-top:1.5rem">
				<!-- wp:button {"backgroundColor":"white","textColor":"dark-navy"} -->
				<div class="wp-block-button"><a class="wp-block-button__link has-white-background-color has-dark-navy-color has-background has-text-color wp-element-button">Get Started</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"40%","className":"midline-cta-bg-col"} -->
			<div class="wp-block-column midline-cta-bg-col" style="flex-basis:40%"><!-- wp:spacer {"height":"1px"} -->
			<div style="height:1px" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer --></div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
