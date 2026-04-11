<?php
/**
 * Title: Midline CTA — Simple (Light)
 * Slug: core-wp/midline-cta-simple-light
 * Categories: core-wp-sections
 * Description: Heading and CTA on the left with a decorative waveform background on the right. Background column hides on mobile. Light mode. Set alignment to "Full Width" in the block editor for a full-bleed background.
 */
?>
<!-- wp:group {"className":"midline-cta midline-cta--simple midline-cta--light","backgroundColor":"light-gray","layout":{"type":"constrained"}} -->
<div class="wp-block-group midline-cta midline-cta--simple midline-cta--light has-light-gray-background-color has-background">

	<!-- wp:columns {"isStackedOnMobile":true,"className":"midline-cta__cols","style":{"spacing":{"blockGap":"0"}}} -->
	<div class="wp-block-columns is-layout-flex midline-cta__cols" style="gap:0">

		<!-- wp:column {"width":"60%","className":"midline-cta__content-col"} -->
		<div class="wp-block-column midline-cta__content-col" style="flex-basis:60%">

			<!-- wp:heading {"level":2,"textColor":"black"} -->
			<h2 class="wp-block-heading has-black-color has-text-color">Your Compelling Headline Here</h2>
			<!-- /wp:heading -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"1.5rem"}}}} -->
			<div class="wp-block-buttons" style="margin-top:1.5rem">
				<!-- wp:button {"backgroundColor":"dark-navy","textColor":"white"} -->
				<div class="wp-block-button"><a class="wp-block-button__link has-dark-navy-background-color has-white-color has-background has-text-color wp-element-button">Get Started</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"40%","className":"midline-cta__bg-col"} -->
		<div class="wp-block-column midline-cta__bg-col" style="flex-basis:40%"><!-- wp:spacer {"height":"1px"} -->
			<div style="height:1px" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer --></div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
