# CLAUDE.md — core-wp Theme

## Theme type

WordPress **block theme** (Full Site Editing). There is no `header.php` or `footer.php`. Output that would traditionally go in those files is registered via `wp_head` / `wp_footer` hooks in `functions.php`. Example: favicon/icon meta tags are output by `core_wp_site_icons()` hooked to `wp_head` at priority 1.

## Build system

**Package manager:** pnpm (v9). Run all commands with `pnpm run`, never `npm run` (causes "Unknown env config store-dir" warning).

**Build commands run from inside the `cli_tools` Docker container** (or locally if Node/pnpm/PHP are installed).

### Key scripts

| Script | What it does |
|---|---|
| `pnpm run build` | Full build: clean → styles → scripts → images → static assets |
| `pnpm run serve` | browser-sync + file watchers (JS, SCSS, icons, images, HTML) |
| `pnpm run serve:all` | Same as `serve` but also runs per-file PHPCS on save |
| `pnpm run styles` | SCSS → minified CSS (lint → Sass → rename → PostCSS) |
| `pnpm run scripts` | JS → bundled (Prettier → Webpack + Modernizr in parallel) |
| `pnpm run phpcs` | PHPCBF auto-fix then PHPCS lint |
| `pnpm run build:pro` | Production build + copy to `../core-wp-build/` |

Complex pipelines are delegated to POSIX sh scripts in `scripts/`. **All shell scripts use `#!/usr/bin/env sh`** — the Docker container does not have bash.

### browser-sync (`bs-config.cjs`)

Dual-mode: behaviour changes based on `APP_ENV=docker` environment variable.

| | Docker (`pnpm run serve` in container) | Local |
|---|---|---|
| Proxy target | `http://wordpress` | `http://localhost` |
| Port | 3000 (mapped `3000:3000`) | 3010 |
| UI port | 3001 | 3011 |

**CSS injection:** `assets/css/**/*.min.css` changes are injected into the browser without a full page reload. All other asset changes trigger a full reload via `pnpm run reload`.

**WordPress redirect fix:** Docker proxy sets `headers: { host: 'localhost' }` to prevent WordPress canonical URL redirects from stripping the port.

**Port conflict note:** Docker's `ports` directive binds host port 3000 even when the container isn't actively serving. Running browser-sync locally on 3000 would hit Docker's proxy. Use port 3010 locally.

## PHP / PHPCS

- Standards: WordPress Coding Standards + PHPCompatibility (via Composer at `../../vendor/`)
- Config: `phpcs.xml` — parallel=8, cache=`.phpcs.cache` (gitignored)
- Custom sniff: `phpcs/CoreWP/Sniffs/Debug/NoDebugFunctionsSniff.php`
  - Warns if `core_wp_print_pre()` or `core_wp_theme_error_log()` are called outside `utility-functions.php`
  - Both functions are gated by `WP_DEBUG` — they silently no-op in production
  - The `phpcs/` directory is excluded from linting (PHPCS PSR-4 naming conflicts with WordPress conventions)
- PHPCBF exits non-zero when it successfully fixes files — the `phpcs.sh` script intentionally ignores this with `; true`
- Cache behavior: locally PHPCBF only re-scans changed files (fast); in Docker the cache doesn't persist so all 35 files are always scanned

## PHP coding conventions

- Build HTML as **PHP string concatenation**, not by switching in and out of PHP template tags. This applies especially in `functions.php` and `inc/` files.
- Always escape output at the point of use: `esc_url()` for URLs in `href`/`src`, `esc_attr()` for HTML attributes, `esc_html()` for text content.
- Use `esc_url()` (not `esc_html()`) for URL attributes.

## Theme file structure

```
functions.php          — Theme setup, enqueue scripts/styles, wp_head hooks
inc/
  includes.php         — Requires all files in inc/ subdirectories
  utility-functions.php — core_wp_cache_bust(), core_wp_print_pre(), core_wp_theme_error_log()
  components/          — Filters and small functional hooks
  custom-admin-functions/ — Login/admin page customisations
  shortcodes/          — [button], [div], [row], [column], [copyright]
  template-tags/       — Output helpers (posted-on, entry-footer, search meta description)
  classes/             — PHP classes (empty by default)
theme_components/      — Source files (edit these, not assets/)
  sass/                — SCSS source
  js/                  — JS source
  fonts/               — Font files (copied to assets/)
  icons/               — Favicon files (copied to assets/)
  images/              — Image source (optimised to assets/)
assets/                — Compiled output (gitignored, rebuilt on each build)
phpcs/                 — Custom PHPCS sniffs (excluded from linting)
scripts/               — POSIX sh build pipeline scripts
parts/                 — Block template parts (FSE)
templates/             — Block templates (FSE)
patterns/              — Block patterns (content-* for post/page/search/none; hero/cta/feature-grid in Sections category)
```

## Accessibility

- Skip link (`<a class="skip-link screen-reader-text" href="#main-content">`) lives in `parts/header.html`
- All `<main>` landmarks have `id="main-content"` set via the `anchor` block attribute
- `inc/components/skip-link-tabindex.php` injects `tabindex="-1"` and class `skip-link-target` onto the main element via `render_block` filter, making it programmatically focusable
- `theme_components/js/modules/_skip-link.js` hides the skip link after it is activated
- Focus ring for the skip link target is defined in `partials/_generic-styles.scss` (`.skip-link-target:focus-visible`)

## Dependencies of note

- `stylelint` is held at **16.x** — `@wordpress/stylelint-config` requires `^16.8.2` and is not yet compatible with v17.
- `webpack.config.mjs` uses native ESM — no `@babel/register` needed.
- `chokidar-cli` handles file watching (replaced nodemon). Supports `{path}` substitution for per-file PHPCS runs.
