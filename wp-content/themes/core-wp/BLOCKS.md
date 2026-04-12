# Custom Blocks — core-wp Theme

All custom blocks are registered under the `core-wp` namespace and grouped in the **Core WP** category in the block inserter. They are server-side rendered via PHP render callbacks located in `inc/blocks/<block-name>/block.php`. Each block also has a `block.json` metadata file and an `editor.js` file that registers the edit UI.

---

## Table of Contents

- [Accordion](#accordion)
- [Accordion Item](#accordion-item)
- [Tabs](#tabs)
- [Tab Item](#tab-item)
- [Copyright](#copyright)
- [Related Posts](#related-posts)
- [Social Share](#social-share)

---

## Accordion

**Block name:** `core-wp/accordion`
**Location:** [`inc/blocks/accordion/`](inc/blocks/accordion/)

A collapsible accordion container. Add **Accordion Item** child blocks to build each row. The block supports three layout modes and an optional single-open constraint.

### Settings (Inspector Controls)

| Setting | Type | Default | Description |
|---|---|---|---|
| Block layout | Radio | `full` | `full` — spans the full column width. `float` — floats alongside other content. |
| Float direction | Radio | `left` | Visible only when layout is `float`. Sets the CSS float to `left` or `right`. |
| 50/50 split with image | Toggle | `false` | Visible only when layout is `full`. Renders the accordion on the left half and a swappable image panel on the right. The image displayed is taken from the first Accordion Item that has an image set. |
| One item open at a time | Toggle | `false` | When enabled, opening one item automatically closes all others. Always forced on in split layout. |

### CSS classes

| Class | When applied |
|---|---|
| `accordion` | Always present on the wrapper `<div>` |
| `accordion--float-left` / `accordion--float-right` | Layout is `float` |
| `accordion--split` | Layout is `full` and split layout is enabled |

### Data attributes

| Attribute | When present |
|---|---|
| `data-accordion` | Always — used by JS to initialize the component |
| `data-single-open="true"` | Single-open mode is active |

---

## Accordion Item

**Block name:** `core-wp/accordion-item`
**Location:** [`inc/blocks/accordion-item/`](inc/blocks/accordion-item/)
**Constraint:** Must be used inside an **Accordion** block (`parent: core-wp/accordion`).

A single collapsible row within an Accordion. Renders an accessible `<button>` / `<div[role="region"]>` pair with unique coordinated IDs. Inner blocks provide the rich content area (paragraphs, headings, lists, images, quotes).

### Settings (Inspector Controls)

| Setting | Type | Default | Description |
|---|---|---|---|
| Open by default | Toggle | `false` | Renders the panel expanded on page load (`aria-expanded="true"`, no `hidden` attribute). |
| Image (split layout) | Media picker | — | Available in a collapsible panel. Sets the image shown in the right-hand image panel when the parent accordion has split layout enabled. The image URL and alt text are stored as block attributes and passed to the parent render via `data-image-url` / `data-image-alt`. |

### Allowed inner blocks

`core/paragraph`, `core/heading`, `core/list`, `core/image`, `core/quote`

### Accessibility notes

- The trigger is a `<button>` with `aria-expanded` and `aria-controls` pointing to the panel.
- The panel has `role="region"` and `aria-labelledby` pointing back to the button.
- Unique IDs are generated server-side via `wp_unique_id()` on every render.

---

## Tabs

**Block name:** `core-wp/tabs`
**Location:** [`inc/blocks/tabs/`](inc/blocks/tabs/)

A tabbed-content container. Add **Tab Item** child blocks to define each tab. The render callback bypasses the pre-rendered `$content` string and iterates `$block->inner_blocks` directly so it can build both the `<ul role="tablist">` nav and the tab panels in a single pass, assigning coordinated ARIA IDs to each button/panel pair.

### Settings (Inspector Controls)

| Setting | Type | Default | Description |
|---|---|---|---|
| Convert to accordion on mobile | Toggle | `false` | Below 640 px the tab navigation is hidden and each panel gains a `<button class="tabs__mobile-header">` header, turning the component into a collapsible accordion. JS uses `matchMedia` to toggle between the two behaviors. |
| One item open at a time (mobile) | Toggle | `false` | Only visible when mobile accordion is enabled. Opening one panel automatically closes the others on mobile. |

### Data attributes

| Attribute | When present |
|---|---|
| `data-tabs` | Always — used by JS to initialize the component |
| `data-mobile-accordion` | Mobile accordion mode is enabled |
| `data-mobile-single-open` | Mobile single-open mode is also enabled |

### Accessibility notes

- Tab nav is a `<ul role="tablist">` with `<li role="presentation">` items.
- Each tab button has `role="tab"`, `aria-selected`, `aria-controls`, and a managed `tabindex` (`0` for the active tab, `-1` for others).
- Each panel has `role="tabpanel"`, `aria-labelledby`, `tabindex="0"`, and `hidden` when inactive.
- Mobile accordion headers use `aria-expanded` and `aria-controls` independent of the tab panel ARIA attributes.

---

## Tab Item

**Block name:** `core-wp/tab-item`
**Location:** [`inc/blocks/tab-item/`](inc/blocks/tab-item/)
**Constraint:** Must be used inside a **Tabs** block (`parent: core-wp/tabs`).

A single tab within the Tabs block. Stores a `title` attribute and an inner blocks content area. There is **no PHP render callback** on this block — the parent Tabs render callback reads `$block->inner_blocks` directly and builds the full nav + panel output. The `save` function persists inner block HTML to `post_content` so `->render()` works at query time.

### Attributes

| Attribute | Type | Default | Description |
|---|---|---|---|
| `title` | string | `""` | The label shown on the tab button and (when mobile accordion is active) in the mobile accordion header. |

### Allowed inner blocks

`core/paragraph`, `core/heading`, `core/list`, `core/image`, `core/quote`, `core/video`, `core/embed`

---

## Copyright

**Block name:** `core-wp/copyright`
**Location:** [`inc/blocks/copyright/`](inc/blocks/copyright/)

Outputs a dynamic, server-side rendered copyright line. The year is always generated at render time via `gmdate('Y')` so it never goes stale. Commonly placed in the footer template part.

### Settings (Inspector Controls)

| Setting | Type | Default | Description |
|---|---|---|---|
| Prefix text | Text | `""` | Optional text rendered before the `©` symbol (e.g. `Copyright`). |
| Show site name | Toggle | `true` | Appends the WordPress site title (from `get_bloginfo('name')`) after the year. |
| Suffix text | Text | `""` | Optional text rendered after the year and site name (e.g. `All rights reserved.`). |

### Output example

```
© 2025 My Site Name All rights reserved.
```

Wrapped in `<p class="site-copyright">`.

---

## Related Posts

**Block name:** `core-wp/related-posts`
**Location:** [`inc/blocks/related-posts/`](inc/blocks/related-posts/)

Server-side rendered block intended for single post templates. Queries the 3 most recent posts that share at least one category with the current post, excluding the current post itself. Renders nothing if there is no current post or no matching categories.

### Behavior

- Uses `WP_Query` with `category__in`, `post__not_in`, ordered by date descending, limited to 3 results.
- Each card shows the **featured image** (falls back to `assets/images/related-post-placeholder.webp` if none is set), **post tags** as pill spans, and the **post title** as a linked heading.
- The entire card is wrapped in an `<a>` pointing to the post permalink.
- Calls `wp_reset_postdata()` after the loop.

### No configurable attributes

This block has no user-facing settings — it is fully automatic and context-driven.

### Output structure

```html
<section class="single-post__related">
  <h2 class="single-post__related-heading">Related Stories</h2>
  <hr class="single-post__related-divider" />
  <div class="post-grid">
    <a href="..." class="post-card">
      <article>
        <figure class="post-card__image">...</figure>
        <div class="post-card__tags">
          <span class="post-card__tag">Tag Name</span>
        </div>
        <h3 class="post-card__title">Post Title</h3>
      </article>
    </a>
    <!-- repeated up to 3 times -->
  </div>
</section>
```

---

## Social Share

**Block name:** `core-wp/social-share`
**Location:** [`inc/blocks/social-share/`](inc/blocks/social-share/)

Server-side rendered block that outputs social sharing links for the current post. Intended for single post templates. Supports LinkedIn, X (Twitter), and Facebook with individual enable/disable toggles, custom link text, and an unlimited number of additional custom links.

All share links open in a new tab with `rel="noopener noreferrer"`. URLs are built from `get_permalink()` and `get_the_title()` using `rawurlencode()`.

### Settings (Inspector Controls)

| Panel | Setting | Type | Default | Description |
|---|---|---|---|---|
| Label | Share label text | Text | `Share:` | The prefix label rendered before the share links. |
| LinkedIn | Enable LinkedIn | Toggle | `true` | Show or hide the LinkedIn share link. |
| LinkedIn | Link text | Text | `LinkedIn` | Visible when LinkedIn is enabled. |
| X (Twitter) | Enable X (Twitter) | Toggle | `true` | Show or hide the X share link. |
| X (Twitter) | Link text | Text | `X (Twitter)` | Visible when X is enabled. |
| Facebook | Enable Facebook | Toggle | `true` | Show or hide the Facebook share link. |
| Facebook | Link text | Text | `Facebook` | Visible when Facebook is enabled. |
| Custom Links | Add Link | Repeater | `[]` | Add arbitrary share links with custom text and URL. Only links with both `text` and `url` set are rendered. |

### Share URL formats

| Network | Share URL pattern |
|---|---|
| LinkedIn | `https://www.linkedin.com/sharing/share-offsite/?url={encoded_url}` |
| X (Twitter) | `https://twitter.com/intent/tweet?url={encoded_url}&text={encoded_title}` |
| Facebook | `https://www.facebook.com/sharer/sharer.php?u={encoded_url}` |

### Output structure

```html
<div class="single-post__social-links">
  <span class="single-post__social-label">Share:</span>
  <a class="single-post__social-link single-post__social-link--linkedin" ...>LinkedIn</a>
  <a class="single-post__social-link single-post__social-link--twitter" ...>X (Twitter)</a>
  <a class="single-post__social-link single-post__social-link--facebook" ...>Facebook</a>
  <!-- custom links get class single-post__social-link--custom -->
</div>
```

---

## Adding a New Block

1. Create a directory under `inc/blocks/<block-name>/`.
2. Add `block.json`, `block.php`, and `editor.js` — follow the pattern used by existing blocks.
3. Register the block in `block.php` with an `add_action('init', ...)` call.
4. Require the `block.php` file from `inc/blocks/` — check how existing blocks are loaded (typically from `inc/includes.php` or `functions.php`).
5. Document the new block in this file.
