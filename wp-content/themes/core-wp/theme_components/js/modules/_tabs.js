/**
 * Tabs
 *
 * Implements the WAI-ARIA Tabs pattern on desktop and switches to an
 * accordion pattern on mobile when [data-mobile-accordion] is present.
 *
 * Desktop keyboard (tab list, WAI-ARIA automatic activation):
 *   Arrow Right / Left  — move focus + activate next/previous tab (wraps)
 *   Home / End          — move focus + activate first/last tab
 *   Tab                 — move focus into the active panel
 *
 * Mobile keyboard (accordion headers, WAI-ARIA Accordion pattern):
 *   Arrow Down / Up  — move focus to next/previous header (wraps)
 *   Home / End       — move focus to first/last header
 *   Enter / Space    — toggle the focused item (native button behaviour)
 *
 * Media: HTML5 video/audio and YouTube/Vimeo iframes are paused whenever the
 * panel they live in is deactivated or hidden.
 *
 * Scroll: when collapsing a mobile accordion item the clicked header is held at
 * its pre-collapse visual position so the page does not jump.
 */

export function initTabs() {
	document.querySelectorAll('[data-tabs]').forEach(initSingleTabs)
}

function initSingleTabs(tabsEl) {
	var hasMobileAccordion = tabsEl.hasAttribute('data-mobile-accordion')
	var isMobileSingleOpen = tabsEl.hasAttribute('data-mobile-single-open')
	var tabButtons = Array.prototype.slice.call(tabsEl.querySelectorAll('.tabs__nav [role="tab"]'))
	var mobileHeaders = Array.prototype.slice.call(tabsEl.querySelectorAll('.tabs__mobile-header'))

	if (!tabButtons.length) return

	// ── Desktop tab handlers ───────────────────────────────────────────────

	tabButtons.forEach(function (tab, index) {
		tab.addEventListener('click', function () {
			activateTab(tabButtons, tab)
			if (hasMobileAccordion) {
				syncMobileToActiveTab(tabButtons, mobileHeaders)
			}
		})

		tab.addEventListener('keydown', function (e) {
			var target

			switch (e.key) {
				case 'ArrowRight':
					e.preventDefault()
					target = tabButtons[index + 1] !== undefined ? tabButtons[index + 1] : tabButtons[0]
					activateTab(tabButtons, target)
					if (hasMobileAccordion) syncMobileToActiveTab(tabButtons, mobileHeaders)
					target.focus()
					break
				case 'ArrowLeft':
					e.preventDefault()
					target =
						tabButtons[index - 1] !== undefined ? tabButtons[index - 1] : tabButtons[tabButtons.length - 1]
					activateTab(tabButtons, target)
					if (hasMobileAccordion) syncMobileToActiveTab(tabButtons, mobileHeaders)
					target.focus()
					break
				case 'Home':
					e.preventDefault()
					activateTab(tabButtons, tabButtons[0])
					if (hasMobileAccordion) syncMobileToActiveTab(tabButtons, mobileHeaders)
					tabButtons[0].focus()
					break
				case 'End':
					e.preventDefault()
					target = tabButtons[tabButtons.length - 1]
					activateTab(tabButtons, target)
					if (hasMobileAccordion) syncMobileToActiveTab(tabButtons, mobileHeaders)
					target.focus()
					break
				default:
					break
			}
		})
	})

	// ── Mobile accordion handlers ──────────────────────────────────────────

	if (hasMobileAccordion && mobileHeaders.length) {
		mobileHeaders.forEach(function (header, index) {
			header.addEventListener('click', function () {
				toggleMobileItem(header, isMobileSingleOpen, mobileHeaders)
				syncTabsToActiveMobileHeader(tabButtons, mobileHeaders)
			})

			header.addEventListener('keydown', function (e) {
				var target

				switch (e.key) {
					case 'ArrowDown':
						e.preventDefault()
						target = mobileHeaders[index + 1] !== undefined ? mobileHeaders[index + 1] : mobileHeaders[0]
						target.focus()
						break
					case 'ArrowUp':
						e.preventDefault()
						target =
							mobileHeaders[index - 1] !== undefined
								? mobileHeaders[index - 1]
								: mobileHeaders[mobileHeaders.length - 1]
						target.focus()
						break
					case 'Home':
						e.preventDefault()
						mobileHeaders[0].focus()
						break
					case 'End':
						e.preventDefault()
						mobileHeaders[mobileHeaders.length - 1].focus()
						break
					default:
						break
				}
			})
		})

		// ── Media query mode switching ─────────────────────────────────────
		// Breakpoint mirrors helpers/_settings.scss 'medium': 640 px.

		var mql = window.matchMedia('(max-width: 639px)')

		function onMediaChange(e) {
			if (e.matches) {
				enterMobileMode(tabButtons, mobileHeaders)
			} else {
				enterDesktopMode(tabButtons, mobileHeaders)
			}
		}

		onMediaChange(mql)

		if (typeof mql.addEventListener === 'function') {
			mql.addEventListener('change', onMediaChange)
		} else {
			mql.addListener(onMediaChange) // Safari < 14 fallback
		}
	}
}

// ── Mode transitions ───────────────────────────────────────────────────────

/**
 * Switch to desktop tab mode.
 *
 * Uses the last-active mobile header's index as the active tab. Panels that
 * are being hidden have their media paused first.
 */
function enterDesktopMode(tabButtons, mobileHeaders) {
	var activeIndex = 0
	mobileHeaders.forEach(function (header, i) {
		if (header.getAttribute('aria-expanded') === 'true') {
			activeIndex = i
		}
	})

	tabButtons.forEach(function (tab, i) {
		var isActive = i === activeIndex
		var panel = getPanelForTab(tab)

		tab.setAttribute('aria-selected', isActive ? 'true' : 'false')
		tab.setAttribute('tabindex', isActive ? '0' : '-1')
		tab.classList.toggle('tabs__tab--active', isActive)

		if (!panel) return

		panel.classList.toggle('tabs__panel--active', isActive)

		if (isActive) {
			panel.removeAttribute('hidden')
			// Ensure content wrapper is visible if present.
			var content = panel.querySelector('.tabs__panel-content')
			if (content) content.removeAttribute('hidden')
		} else {
			pauseMediaInElement(panel)
			panel.setAttribute('hidden', '')
		}
	})
}

/**
 * Switch to mobile accordion mode.
 *
 * All panel wrappers become visible; content visibility is managed by
 * mobile header aria-expanded state. Syncs to the currently active desktop tab.
 */
function enterMobileMode(tabButtons, mobileHeaders) {
	var activeIndex = 0
	tabButtons.forEach(function (tab, i) {
		if (tab.getAttribute('aria-selected') === 'true') {
			activeIndex = i
		}
	})

	// Reveal all panel wrappers — they are accordion item containers on mobile.
	tabButtons.forEach(function (tab) {
		var panel = getPanelForTab(tab)
		if (panel) panel.removeAttribute('hidden')
	})

	mobileHeaders.forEach(function (header, i) {
		var isActive = i === activeIndex
		var content = getContentForMobileHeader(header)

		header.setAttribute('aria-expanded', isActive ? 'true' : 'false')

		if (!content) return

		if (isActive) {
			content.removeAttribute('hidden')
		} else {
			content.setAttribute('hidden', '')
		}
	})
}

// ── Core activation ────────────────────────────────────────────────────────

/**
 * Activate a desktop tab, pausing media in all panels being hidden.
 */
function activateTab(tabButtons, activeTab) {
	tabButtons.forEach(function (tab) {
		var isActive = tab === activeTab
		var panel = getPanelForTab(tab)

		tab.setAttribute('aria-selected', isActive ? 'true' : 'false')
		tab.setAttribute('tabindex', isActive ? '0' : '-1')
		tab.classList.toggle('tabs__tab--active', isActive)

		if (!panel) return

		panel.classList.toggle('tabs__panel--active', isActive)

		if (isActive) {
			panel.removeAttribute('hidden')
		} else {
			pauseMediaInElement(panel)
			panel.setAttribute('hidden', '')
		}
	})
}

/**
 * Toggle a single mobile accordion item.
 *
 * Handles single-open mode (closes all other items first), pauses media in
 * content being hidden, and restores the clicked header's scroll position
 * after any layout shift caused by collapsing content.
 *
 * @param {HTMLElement}   header         The mobile header button clicked.
 * @param {boolean}       isSingleOpen   Close other items when opening.
 * @param {HTMLElement[]} allHeaders     All mobile headers in this instance.
 */
function toggleMobileItem(header, isSingleOpen, allHeaders) {
	var isExpanded = header.getAttribute('aria-expanded') === 'true'

	// Capture header's viewport position before any DOM change.
	// Used to restore scroll after content collapses.
	var headerTopBefore = header.getBoundingClientRect().top

	// Close all other items when single-open mode is active and this item is opening.
	if (isSingleOpen && !isExpanded) {
		allHeaders.forEach(function (other) {
			if (other === header) return
			if (other.getAttribute('aria-expanded') !== 'true') return

			var otherContent = getContentForMobileHeader(other)
			other.setAttribute('aria-expanded', 'false')

			if (otherContent) {
				pauseMediaInElement(otherContent)
				otherContent.setAttribute('hidden', '')
			}
		})
	}

	// Toggle this item.
	var content = getContentForMobileHeader(header)
	header.setAttribute('aria-expanded', isExpanded ? 'false' : 'true')

	if (content) {
		if (isExpanded) {
			pauseMediaInElement(content)
			content.setAttribute('hidden', '')
		} else {
			content.removeAttribute('hidden')
		}
	}

	// Scroll correction: getBoundingClientRect() forces a synchronous reflow so
	// this reads the post-collapse position immediately. scrollBy() then nudges
	// the viewport so the header stays at its original visual position.
	var headerTopAfter = header.getBoundingClientRect().top
	var shift = headerTopBefore - headerTopAfter

	if (Math.abs(shift) >= 1) {
		window.scrollBy(0, shift)
	}
}

// ── Sync helpers ───────────────────────────────────────────────────────────

/**
 * After a desktop tab click, sync mobile headers to match.
 */
function syncMobileToActiveTab(tabButtons, mobileHeaders) {
	tabButtons.forEach(function (tab, i) {
		var isActive = tab.getAttribute('aria-selected') === 'true'
		var header = mobileHeaders[i]
		if (!header) return

		var content = getContentForMobileHeader(header)
		header.setAttribute('aria-expanded', isActive ? 'true' : 'false')

		if (content) {
			if (isActive) {
				content.removeAttribute('hidden')
			} else {
				pauseMediaInElement(content)
				content.setAttribute('hidden', '')
			}
		}
	})
}

/**
 * After a mobile header click, sync the desktop tab selection.
 * The most-recently expanded header determines the active tab.
 */
function syncTabsToActiveMobileHeader(tabButtons, mobileHeaders) {
	var activeIndex = -1

	mobileHeaders.forEach(function (header, i) {
		if (header.getAttribute('aria-expanded') === 'true') {
			activeIndex = i
		}
	})

	if (activeIndex < 0) return

	tabButtons.forEach(function (tab, i) {
		var isActive = i === activeIndex
		var panel = getPanelForTab(tab)

		tab.setAttribute('aria-selected', isActive ? 'true' : 'false')
		tab.setAttribute('tabindex', isActive ? '0' : '-1')
		tab.classList.toggle('tabs__tab--active', isActive)

		if (panel) panel.classList.toggle('tabs__panel--active', isActive)
	})
}

// ── Media helpers ──────────────────────────────────────────────────────────

/**
 * Pause all HTML5 media and postMessage-compatible iframe embeds inside `el`.
 *
 * YouTube requires enablejsapi=1 on the iframe src; Vimeo always accepts the
 * postMessage. Errors from cross-origin frames are silently swallowed.
 *
 * @param {HTMLElement} el  Container element to search within.
 */
function pauseMediaInElement(el) {
	if (!el) return

	var mediaEls = el.querySelectorAll('video, audio')
	for (var m = 0; m < mediaEls.length; m++) {
		if (!mediaEls[m].paused) mediaEls[m].pause()
	}

	var iframes = el.querySelectorAll('iframe')
	for (var f = 0; f < iframes.length; f++) {
		var src = iframes[f].src || ''
		var win = iframes[f].contentWindow
		if (!win) continue

		try {
			if (src.indexOf('youtube.com') !== -1 || src.indexOf('youtu.be') !== -1) {
				win.postMessage(
					JSON.stringify({ event: 'command', func: 'pauseVideo', args: [] }),
					'https://www.youtube.com'
				)
			} else if (src.indexOf('vimeo.com') !== -1) {
				win.postMessage(JSON.stringify({ method: 'pause' }), 'https://player.vimeo.com')
			}
		} catch {
			// Cross-origin contentWindow access denied — nothing to do.
		}
	}
}

// ── DOM helpers ────────────────────────────────────────────────────────────

function getPanelForTab(tab) {
	var panelId = tab.getAttribute('aria-controls')
	return panelId ? document.getElementById(panelId) : null
}

function getContentForMobileHeader(header) {
	var contentId = header.getAttribute('aria-controls')
	return contentId ? document.getElementById(contentId) : null
}
