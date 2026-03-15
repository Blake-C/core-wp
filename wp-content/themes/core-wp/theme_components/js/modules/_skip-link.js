/**
 * Skip link enhancement.
 *
 * Hides the skip link after it has been activated so it does not
 * remain visible or re-appear on subsequent keyboard navigation.
 */
const skipLink = document.querySelector('.skip-link[href="#wp--skip-link--target"]')

if (skipLink) {
	skipLink.addEventListener('click', function () {
		skipLink.style.display = 'none'
	})
}
