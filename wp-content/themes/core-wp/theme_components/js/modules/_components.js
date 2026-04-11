/**
 * Custom JS Components
 *
 * This file replaces Foundation's component loader. Add vanilla JS (or jQuery)
 * component modules here, using the same async chunk pattern so each component
 * is only loaded when its selector is present on the page.
 *
 * Pattern
 * -------
 * 1. Write the component as a named export in its own file under modules/.
 * 2. Wrap the dynamic import in a guard that checks for the selector first.
 * 3. Webpack will split it into a separate bundle chunk automatically because
 *    of the dynamic import() call.
 *
 * Example — lazy-load a custom accordion only when [data-accordion] exists:
 *
 *   async function loadAccordion() {
 *     const { initAccordion } = await import(
 *       /* webpackChunkName: "accordion" *\/
 *       './accordion.js'
 *     )
 *     initAccordion()
 *   }
 *
 *   if (document.querySelector('[data-accordion]')) {
 *     loadAccordion()
 *   }
 *
 * The chunk will be output as assets/js/accordion.js and loaded on demand.
 * No changes to webpack.config.mjs or scripts-list.js are needed for chunks.
 */

async function loadAccordion() {
	const { initAccordion } = await import(
		/* webpackChunkName: "accordion" */
		'./_accordion.js'
	)
	initAccordion()
}

if (document.querySelector('[data-accordion]')) {
	loadAccordion()
}

async function loadTabs() {
	const { initTabs } = await import(
		/* webpackChunkName: "tabs" */
		'./_tabs.js'
	)
	initTabs()
}

if (document.querySelector('[data-tabs]')) {
	loadTabs()
}

async function loadPrismCode() {
	const { initPrismCode } = await import(
		/* webpackChunkName: "prism-code" */
		'./_prism-code.js'
	)
	initPrismCode()
}

if (document.querySelector('.wp-block-code, pre')) {
	loadPrismCode()
}
