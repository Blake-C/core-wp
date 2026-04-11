/**
 * Prism Code Highlighting
 *
 * Initialises Prism.js syntax highlighting on all code blocks:
 *   - WordPress core/code blocks (.wp-block-code)
 *   - Bare <pre> tags (with or without a <code> child)
 *
 * Features: syntax highlighting, line numbers, copy button, language label.
 *
 * Why the prep step matters
 * ─────────────────────────
 * Prism only processes <code class="language-*"> elements. WordPress code blocks
 * with no language selected have no class on <code>, so Prism — and the toolbar /
 * line-numbers plugins that hook into Prism's pipeline — would silently skip them.
 * We normalise the DOM before calling highlightAll() to ensure every block is
 * processed and gets the toolbar and line numbers.
 *
 * Selector guard: loaded when .wp-block-code or any <pre> is present on the page.
 * See _components.js for the dynamic import pattern.
 */

import Prism from 'prismjs'

// ── Language components ──────────────────────────────────────────────────────
// Core includes: markup, css, clike, javascript.
//
// css-extras MUST come before scss: it calls insertBefore('css', ...) to add
// color tokens. scss uses extend('css', ...) which deep-clones the css grammar
// at import time — if css-extras runs first, scss inherits the color tokens and
// the inline-color plugin works on both languages.
import 'prismjs/components/prism-css-extras.js'
import 'prismjs/components/prism-markup-templating.js'
import 'prismjs/components/prism-php.js'
import 'prismjs/components/prism-bash.js'
import 'prismjs/components/prism-json.js'
import 'prismjs/components/prism-scss.js'
import 'prismjs/components/prism-typescript.js'
import 'prismjs/components/prism-python.js'
import 'prismjs/components/prism-yaml.js'
import 'prismjs/components/prism-sql.js'
import 'prismjs/components/prism-go.js'
import 'prismjs/components/prism-diff.js'

// ── Plugins ──────────────────────────────────────────────────────────────────
// Line numbers: adds .line-numbers-rows inside each <pre class="line-numbers">
import 'prismjs/plugins/line-numbers/prism-line-numbers.js'

// Toolbar: renders the .toolbar overlay (required by show-language and copy-to-clipboard)
import 'prismjs/plugins/toolbar/prism-toolbar.js'

// Show language: reads the language-* class and renders the label in .toolbar
import 'prismjs/plugins/show-language/prism-show-language.js'

// Copy to clipboard: adds a copy button to .toolbar
import 'prismjs/plugins/copy-to-clipboard/prism-copy-to-clipboard.js'

// Inline colour swatches — requires css-extras (already loaded above).
import 'prismjs/plugins/inline-color/prism-inline-color.js'

// ── Register a plain-text fallback grammar ───────────────────────────────────
// Allows Prism to run its full pipeline (toolbar, line-numbers) on blocks that
// have no language selected without applying any token colouring.
Prism.languages.plain = {}

// ── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Resolve the language class to apply to a <code> element.
 *
 * WordPress's "Additional CSS class" field adds classes to the <pre> element.
 * This checks the <pre> for a language-* or lang-* class and returns the
 * correct language-* string, or 'language-plain' as a fallback.
 *
 * Priority:
 *   1. Existing language-* class on <code>
 *   2. language-* class on <pre>
 *   3. lang-* class on <pre> (translated to language-*)
 *   4. Fallback: 'language-plain'
 *
 * @param {HTMLPreElement}  pre
 * @param {HTMLElement}     code
 * @returns {string|null}  Class to add, or null if already correct.
 */
function resolveLanguageClass(pre, code) {
	// <code> already has a language class — nothing to do.
	if (code && code.className.match(/\blanguage-/)) {
		return null
	}

	// Check <pre> for language-* or lang-* class.
	var preClasses = Array.prototype.slice.call(pre.classList)
	for (var i = 0; i < preClasses.length; i++) {
		if (/^language-/.test(preClasses[i])) {
			return preClasses[i]
		}
		if (/^lang-/.test(preClasses[i])) {
			return 'language-' + preClasses[i].replace(/^lang-/, '')
		}
	}

	return 'language-plain'
}

/**
 * Ensure a <pre> element has a <code> child with a language-* class so that
 * Prism and its plugins process it correctly.
 *
 * Cases handled:
 *   1. <pre><code class="language-js">  — already correct, untouched
 *   2. <pre class="language-js"><code> — copy language to <code>
 *   3. <pre class="lang-js"><code>     — translate and copy to <code>
 *   4. <pre><code>                     — add language-plain to <code>
 *   5. <pre>text</pre>                 — wrap in <code class="language-plain">
 *
 * @param {HTMLPreElement} pre
 */
function normaliseCodeBlock(pre) {
	var code = pre.querySelector('code')

	var langClass = resolveLanguageClass(pre, code)

	if (!code) {
		// Bare <pre> with no <code> child — wrap its innerHTML.
		var wrapper = document.createElement('code')
		wrapper.className = langClass || 'language-plain'
		wrapper.innerHTML = pre.innerHTML
		pre.innerHTML = ''
		pre.appendChild(wrapper)
		return
	}

	if (langClass) {
		code.classList.add(langClass)
	}
}

// ── Initialisation ───────────────────────────────────────────────────────────

export function initPrismCode() {
	document.querySelectorAll('.wp-block-code, pre').forEach(function (pre) {
		normaliseCodeBlock(pre)
		pre.classList.add('line-numbers')
	})

	Prism.highlightAll()
}
