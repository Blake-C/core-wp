/**
 * Accordion
 *
 * Manages toggle behaviour, keyboard navigation (WAI-ARIA Accordion Pattern),
 * and image-panel swapping for the core-wp/accordion block.
 *
 * Keyboard support:
 *   Enter / Space  — toggle focused item
 *   Arrow Down     — move focus to next header (wraps)
 *   Arrow Up       — move focus to previous header (wraps)
 *   Home           — move focus to first header
 *   End            — move focus to last header
 */

export function initAccordion() {
	document.querySelectorAll('[data-accordion]').forEach(initSingleAccordion)
}

function initSingleAccordion(accordion) {
	var buttons = Array.prototype.slice.call(accordion.querySelectorAll('.accordion__header'))
	var imagePanel = accordion.querySelector('.accordion__image-panel')
	var imageEl = imagePanel ? imagePanel.querySelector('.accordion__image') : null
	var isSplit = accordion.classList.contains('accordion--split')
	var isSingleOpen = accordion.getAttribute('data-single-open') === 'true'

	if (!buttons.length) return

	buttons.forEach(function (button, index) {
		button.addEventListener('click', function () {
			var isExpanded = button.getAttribute('aria-expanded') === 'true'
			var opening = !isExpanded

			// Close all other items when single-open mode is active.
			if (isSingleOpen && opening) {
				buttons.forEach(function (other) {
					if (other !== button) {
						toggleItem(other, false)
					}
				})
			}

			toggleItem(button, opening)

			// Update split image whenever any item is opened.
			if (isSplit) {
				if (opening) {
					updateSplitImage(button.closest('.accordion__item'), imageEl, imagePanel)
				}
			}
		})

		button.addEventListener('keydown', function (e) {
			var next, prev

			switch (e.key) {
				case 'ArrowDown':
					e.preventDefault()
					next = buttons[index + 1] !== undefined ? buttons[index + 1] : buttons[0]
					next.focus()
					break
				case 'ArrowUp':
					e.preventDefault()
					prev = buttons[index - 1] !== undefined ? buttons[index - 1] : buttons[buttons.length - 1]
					prev.focus()
					break
				case 'Home':
					e.preventDefault()
					buttons[0].focus()
					break
				case 'End':
					e.preventDefault()
					buttons[buttons.length - 1].focus()
					break
				default:
					break
			}
		})
	})

	// Initialise image panel on page load for split layout.
	if (isSplit && imageEl) {
		var initialItem = null

		// Prefer the first item that is open by default.
		buttons.forEach(function (button) {
			if (!initialItem && button.getAttribute('aria-expanded') === 'true') {
				initialItem = button.closest('.accordion__item')
			}
		})

		// Fall back to the first item.
		if (!initialItem) {
			initialItem = accordion.querySelector('.accordion__item')
		}

		if (initialItem) {
			updateSplitImage(initialItem, imageEl, imagePanel)
		}
	}
}

/**
 * Toggle a single accordion item open or closed.
 *
 * @param {HTMLElement} button  The header button element.
 * @param {boolean}     expand  True to open, false to close.
 */
function toggleItem(button, expand) {
	var panelId = button.getAttribute('aria-controls')
	var panel = panelId ? document.getElementById(panelId) : null

	if (!panel) return

	button.setAttribute('aria-expanded', expand ? 'true' : 'false')

	if (expand) {
		panel.removeAttribute('hidden')
	} else {
		panel.setAttribute('hidden', '')
	}
}

/**
 * Update the shared image panel to reflect the active accordion item.
 *
 * @param {HTMLElement}      item        The active .accordion__item element.
 * @param {HTMLImageElement} imageEl     The <img> inside the panel.
 * @param {HTMLElement}      imagePanel  The panel wrapper element.
 */
function updateSplitImage(item, imageEl, imagePanel) {
	if (!item || !imageEl || !imagePanel) return

	var url = item.getAttribute('data-image-url') || ''
	var alt = item.getAttribute('data-image-alt') || ''

	if (url) {
		imageEl.src = url
		imageEl.alt = alt
		imagePanel.hidden = false
	} else {
		imagePanel.hidden = true
	}
}
