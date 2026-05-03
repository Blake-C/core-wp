# CLAUDE.md — core-wp Plugin

## What this is

The project's custom plugin. Functionality that belongs to the site but not to the theme lives here — primarily custom post types and taxonomies.

## File structure

```
core-wp.php               — Plugin entry point; defines CORE_WP_FILES constant
custom-post-types/        — One file per post type / taxonomy
  sample-post-type.php    — Starter CPT (rename/replace for real post types)
phpcs.xml                 — PHPCS config for this plugin
```

## Conventions

- Text domain: `core_wp`
- Function prefix: `core_wp_`
- All functions must be wrapped in `if ( ! function_exists() )` guards.
- Follow WordPress Coding Standards (WPCS) for all PHP.
- Always escape output: `esc_url()` for URLs, `esc_attr()` for attributes, `esc_html()` for text.
- Each new post type gets its own file in `custom-post-types/`.
- Run PHPCS before committing: `pnpm run phpcs` from the theme root runs the theme's scanner; run `php ../../vendor/bin/phpcs --standard=phpcs.xml --basepath=./` from this directory to lint the plugin directly.

## Security scanning

- Always run `snyk_code_scan` for new or modified PHP in this plugin.
- Run `snyk_sca_scan` when adding or upgrading Composer dependencies.
