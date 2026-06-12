# MAINTENANCE.md — Update & Upgrade Runbook

This is the single reference for keeping the project's dependencies and Docker
images current. Point an assistant (or yourself) here and it has everything
needed to update pnpm packages, Composer packages, all Docker images, WordPress,
and the two sibling image repos — including the versioning rules, the changelog
discipline, and the non-obvious gotchas.

**Golden rule:** update the CHANGELOG **before** committing, bump versions per
SemVer, and make the changelog match the actual build artifacts. See
[Versioning & changelog discipline](#versioning--changelog-discipline).

---

## 1. What this project is made of

Three Git repos, all checked out as siblings under `Repositories/`:

| Repo | Path | Produces | Referenced by |
|---|---|---|---|
| `core-wp` | `./` (this repo) | The WordPress site (theme + plugins + compose stack) | — |
| `light-cli` | `../light-cli` | `digitalblake/light-cli` image (build tooling: Node/pnpm/PHP/Composer/WP-CLI) | `compose.yml` `cli_tools`, `docker/wordpress/Dockerfile.prod` builder stages |
| `core-wp-wordpress` | `../core-wp-wordpress` | `digitalblake/core-wp-wordpress` image (security-patched WordPress PHP-FPM) | `compose.yml` `wordpress`, `Dockerfile.prod` stage 3 |

### Images referenced by the compose files

| Image | Where pinned | Update path |
|---|---|---|
| `digitalblake/light-cli:6.x.x` | `compose.yml`, `Dockerfile.prod` (×2 stages) | Rebuild the `light-cli` repo, then bump the tag in both files — [§5](#5-update-the-light-cli-image) |
| `digitalblake/core-wp-wordpress:php8.4-latest` | `compose.yml`, `Dockerfile.prod` (stage 3) | Rolling tag — rebuild the `core-wp-wordpress` repo; reference does **not** change — [§6](#6-update-the-core-wp-wordpress-image--wordpress) |
| `nginx:1.29-alpine` | `compose.yml`, `compose.prod.yml` | Bump tag — [§7](#7-update-the-base-images) |
| `mariadb:11.8` | `compose.yml`, `compose.prod.yml` | Bump tag — [§7](#7-update-the-base-images) |
| `redis:7-alpine` | `compose.yml`, `compose.prod.yml` | Bump tag — [§7](#7-update-the-base-images) |
| `phpmyadmin` | `compose.yml` | Unpinned (latest); dev-only GUI |

### Key container paths (where commands run)

All build/package commands run **inside the `cli_tools` container**:
`docker compose exec -w <path> cli_tools <cmd>`

| Purpose | Container path |
|---|---|
| Theme root (pnpm) | `/home/webdev/www/public_html/wp-content/themes/core-wp` |
| Composer root | `/home/webdev/www/public_html/wp-content` |
| WP core (in `wordpress` container) | `/var/www/html` |

Start the stack first: `docker compose up -d`.

---

## 2. Versioning & changelog discipline

Do this for **every** change, in this order, before committing:

1. **Make the change** and verify it (build/lint/scan — see [§8](#8-verification-checklist)).
2. **Update the changelog** for the repo you touched (see formats below). The
   changelog must describe the **net current state** that will ship — not
   intermediate steps. If you bumped something twice in one unreleased cycle,
   the entry reflects the final version only.
3. **Pick the version** per SemVer (see per-repo rules below).
4. **Commit** — one logical change per commit, never a monolith. Past-tense,
   capitalized subject (`Updated…`, `Fixed…`, `Added…`). No AI attribution.

### Changelog rules (learned the hard way)

- **One section per release.** Never duplicate `### Security` / `### Added` /
  `### Changed` / `### Removed` within a single release block.
- **No stale versions.** If a line says "pnpm 11.1.2" but the tree now ships
  11.6.0, fix the line. The release must match the build artifacts.
- **No redundant entries.** Don't document the same artifact twice (e.g. an
  image bumped to `6.1.0` in one line and `6.2.0` in another) — keep the net.
- **Cite the files** changed at the end of each entry, e.g. `— compose.yml`.

### Per-repo versioning

| Repo | Scheme | Bump rules |
|---|---|---|
| `core-wp` | `CHANGELOG.md` under `## [#.#.#] - Next`; sections Security/Added/Changed/Removed | No release tags cut yet; accumulate under "Next" |
| `light-cli` | Image tag `6.x.x` (semver-ish). Version recorded in `README.md` table (no CHANGELOG) | MINOR for tooling/base refresh; PATCH for a rebuild only |
| `core-wp-wordpress` | Image tag `php{PHP}-{MAJOR}.{MINOR}.{PATCH}`; Keep a Changelog `CHANGELOG.md` | **MAJOR** = PHP line change (8.4→8.5); **MINOR** = new capability / XDebug bump; **PATCH** = security/WordPress rebuild, no Dockerfile change |

---

## 3. Update pnpm packages (theme build toolchain)

Files: `wp-content/themes/core-wp/package.json` (exact-pinned, no carets),
`pnpm-lock.yaml`, `pnpm-workspace.yaml`.

```sh
# 1. See what's outdated
docker compose exec -w /home/webdev/www/public_html/wp-content/themes/core-wp cli_tools pnpm outdated

# 2. Edit package.json to the new exact versions (respect the holds in §9).

# 3. Refresh the lockfile. NOTE: the workspace sets frozenLockfile,
#    so a plain `pnpm install` fails — you must pass --no-frozen-lockfile.
docker compose exec -w /home/webdev/www/public_html/wp-content/themes/core-wp cli_tools pnpm install --no-frozen-lockfile

# 4. Security scan, then build/lint (see §8). Fix and rescan until clean.
```

**`minimumReleaseAge` (7-day) gate:** `pnpm-workspace.yaml` sets
`minimumReleaseAge: 10080`. pnpm will **refuse to install any package version
published less than 7 days ago** — a deliberate Shai-Hulud supply-chain defense.
If `pnpm outdated` shows a newer version but it won't install, check its publish
date; if it's < 7 days old, leave it and revisit after it ages. Do **not** lower
this gate to force a fresh package in.

**Transitive CVE fixes** go in the `overrides:` block of `pnpm-workspace.yaml`
(pnpm v11 reads overrides there, **not** from `package.json`). Example already in
place: `ws: '^8.20.1'`. After editing overrides, delete `pnpm-lock.yaml` and
re-run `pnpm install --no-frozen-lockfile` to force resolution, then diff the
lock to confirm only the intended package changed.

---

## 4. Update Composer packages (PHP tooling + WP plugins)

Files: `wp-content/composer.json` (caret ranges), `composer.lock`.

```sh
# 1. See what's outdated
docker compose exec -w /home/webdev/www/public_html/wp-content cli_tools composer outdated --direct

# 2. Update a package (caret ranges usually allow it without editing composer.json)
docker compose exec -w /home/webdev/www/public_html/wp-content cli_tools composer update wp-plugin/wordpress-seo --no-interaction

# 3. Validate + confirm lock is in sync
docker compose exec -w /home/webdev/www/public_html/wp-content cli_tools composer validate
```

Run a Snyk SCA scan on the composer tree after (see [§8](#8-verification-checklist)).

---

## 5. Update the `light-cli` image

Repo: `../light-cli`. Files: `Dockerfile`, `README.md` (version table = the
record; there is no CHANGELOG here).

What it ships: Alpine base → Node/npm (apk), pnpm (Corepack, pinned), PHP 8.4 +
extensions, Composer (installer fetches latest at build), WP-CLI, browser-sync,
ImageMagick.

```sh
# Check current vs latest for each piece:
docker run --rm digitalblake/light-cli:<current> sh -c 'cat /etc/alpine-release; node -v; pnpm -v; php -v | head -1; composer --version; wp --version --allow-root'
# Alpine latest: https://hub.docker.com/_/alpine/tags   pnpm: npm view pnpm version
# Before bumping the Alpine base, confirm 3.x still ships Node >= the theme's
# engines (package.json "node") and php84:
docker run --rm alpine:<new> sh -c 'apk update >/dev/null 2>&1; apk policy nodejs php84'
```

Edit `Dockerfile` (base image and/or pinned tool versions), update the
`README.md` version table, then build and verify **as the `webdev` user**:

```sh
cd ../light-cli
docker build -t digitalblake/light-cli:<new-tag> .
docker run --rm digitalblake/light-cli:<new-tag> sh -c 'whoami; pnpm -v; node -v; cat /etc/alpine-release; composer --version'
```

**pnpm pin gotcha (Corepack drift):** `corepack enable` will silently fall back
to Corepack's bundled "latest" pnpm unless pinned. The Dockerfile sets
`ENV COREPACK_DEFAULT_TO_LATEST=0` and runs `corepack prepare pnpm@<ver>
--activate` **as the `webdev` runtime user** (not via sudo) so the activated
version actually sticks. Verify `pnpm -v` matches the intended version.

**After publishing a new `light-cli` tag**, bump the references in **this** repo:
`compose.yml` (`cli_tools`) and `docker/wordpress/Dockerfile.prod` (both builder
stages). Recreate the container: `docker compose up -d cli_tools`.

**Keep pnpm in sync with the theme:** the theme's `package.json` `packageManager`
field pins the exact pnpm version Corepack uses inside the project (it overrides
the image default). To move the theme to the image's pnpm, run in the theme dir:
```sh
docker compose exec -w /home/webdev/www/public_html/wp-content/themes/core-wp cli_tools corepack use pnpm@<ver>
```
This rewrites `packageManager` with the correct integrity hash. pnpm 11.x shares
`lockfileVersion 9.0`, so the lockfile itself usually needs no regeneration.

**Release path:** `light-cli` has no CI tag workflow documented — build locally
and `docker push digitalblake/light-cli:<tag>` (and `:latest` if appropriate).

---

## 6. Update the `core-wp-wordpress` image / WordPress

Repo: `../core-wp-wordpress`. Files: `Dockerfile`, `CHANGELOG.md`, `README.md`.

The Dockerfile is `FROM wordpress:php8.4-fpm-alpine` (a **rolling** base) +
`apk upgrade` + `pecl install xdebug-<ver>`. **The WordPress version comes from
the rolling base**, so a plain rebuild pulls whatever WordPress the base
currently ships — no Dockerfile edit needed for a WP bump.

```sh
# Is a newer WordPress available on the base?
docker pull wordpress:php8.4-fpm-alpine
docker run --rm wordpress:php8.4-fpm-alpine sh -c 'grep "wp_version =" /usr/src/wordpress/wp-includes/version.php'
```

Choose the tag per the scheme in [§2](#per-repo-versioning):
- **WordPress-only rebuild** (no Dockerfile change) → **PATCH** (`php8.4-1.0.1`).
- **Also bumping XDebug** (or any Dockerfile capability) → **MINOR** (`php8.4-1.1.0`).

```sh
cd ../core-wp-wordpress
# (edit Dockerfile if bumping xdebug; check latest: pecl xdebug allreleases)
docker build -t digitalblake/core-wp-wordpress:php8.4-latest -t digitalblake/core-wp-wordpress:php8.4-<X.Y.Z> .
docker run --rm digitalblake/core-wp-wordpress:php8.4-latest sh -c 'grep "wp_version =" /usr/src/wordpress/wp-includes/version.php; php -r "echo phpversion(\"xdebug\");"'
```

Update `CHANGELOG.md` (one `### Changed` per release; promote PATCH→MINOR if you
bumped xdebug, and fold both changes into a single section — don't leave a stale
PATCH entry behind).

**Apply WordPress to the running site** by recreating the containers. The
official image's entrypoint syncs core from `/usr/src/wordpress` into the
bind-mounted host `./wordpress/` on recreate, **preserving `wp-content`**:

```sh
docker compose up -d --force-recreate wordpress nginx
docker compose exec -w /home/webdev/www/public_html cli_tools sh -c 'wp core version; wp core update-db --dry-run'
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost/
```

`wp core update-db` should report the DB already at the latest version after a
major WordPress bump; if not, run it without `--dry-run`.

**Release path:** per the repo's README, the publish flow is CI-driven — update
CHANGELOG, commit, push, then `git tag php8.4-<X.Y.Z> && git push origin
php8.4-<X.Y.Z>`; GitHub Actions builds and pushes to Docker Hub.

The compose reference (`php8.4-latest`) is rolling and **does not change**, so no
edit is needed in this repo for a WordPress bump (though note it in this repo's
CHANGELOG since the running environment changed).

---

## 7. Update the base images (nginx / mariadb / redis)

Pinned in `compose.yml` and `compose.prod.yml`. Bump the tag in both files,
recreate, and confirm health. These are scoped/minor by default — check Docker
Hub for the newest patch in the pinned major line and watch Snyk for the image.

```sh
docker compose pull nginx db redis && docker compose up -d
docker compose ps   # all should report (healthy)
```

**nginx healthcheck gotcha:** the dev healthcheck sends `Host: localhost` so
WordPress serves a direct `200` instead of a canonical `301` off `127.0.0.1`;
the prod healthcheck is domain-agnostic (accepts any served `200/301/302`). If
you rewrite a healthcheck, preserve that behavior or the container will report
`unhealthy` even while serving fine.

---

## 8. Verification checklist

Run after **any** dependency or image change (inside `cli_tools`, theme dir):

| Check | Command | Confirms |
|---|---|---|
| Security (deps) | `snyk_sca_scan` on theme + composer trees | no new CVEs; rescan after fixes |
| Frozen install | `pnpm install --frozen-lockfile` | the prod build path resolves |
| Production build | `pnpm run build:prod` | Sass→cssnano + Webpack/Babel pipeline works; minified CSS/JS emitted |
| SCSS lint | `pnpm run styles:lint` | stylelint + Prettier clean |
| PHP lint | `pnpm run phpcs` | WPCS clean |
| Composer | `composer validate` (composer root) | manifest valid, lock in sync |
| Compose | `docker compose [-f compose.prod.yml] config` | YAML parses |
| Health | `docker compose ps` | services `(healthy)`; `curl http://localhost/` → 200 |

Per project policy: run `snyk_code_scan` for modified first-party PHP/JS and
`snyk_sca_scan` when changing dependencies; fix and rescan until clean.

---

## 9. Held-back packages (do not bump without re-checking compatibility)

| Package | Held at | Reason |
|---|---|---|
| `stylelint` | 16.x | `@wordpress/stylelint-config` peer-requires `stylelint ^16.8.2`; not yet v17-compatible |
| `squizlabs/php_codesniffer` | 3.x | WordPress Coding Standards 3.x is not compatible with PHP_CodeSniffer 4.0 |
| Any package fix < 7 days old | current | Blocked by `minimumReleaseAge` (see [§3](#3-update-pnpm-packages-theme-build-toolchain)); revisit after it ages 7 days |

Re-test these on each pass — once the upstream blocker clears, they can move.

---

## 10. Gotchas cheat-sheet

- **pnpm `frozenLockfile`** → regenerate with `pnpm install --no-frozen-lockfile`.
- **pnpm `minimumReleaseAge` (7 days)** → fresh releases are blocked by design.
- **pnpm `overrides`** live in `pnpm-workspace.yaml`, not `package.json` (v11).
- **`packageManager` field** in the theme overrides the image's pnpm; bump with
  `corepack use pnpm@<ver>` to get the right hash.
- **Corepack auto-upgrade** → pin with `COREPACK_DEFAULT_TO_LATEST=0` + activate
  as the `webdev` user in the `light-cli` Dockerfile.
- **WordPress core** is bind-mounted from `./wordpress` and synced by the image
  entrypoint on container recreate; `wp-content` is preserved.
- **nginx healthcheck** must tolerate WordPress canonical redirects (Host header
  in dev, status-tolerant in prod).
- **`cli_tools` paths** differ from the `wordpress` container's `/var/www/html`.
- **npm-registry timeouts** during `docker build` are transient — just retry.

---

## 11. Quick "everything" pass

1. `docker compose up -d`
2. pnpm: `pnpm outdated` → edit `package.json` → `pnpm install --no-frozen-lockfile` ([§3](#3-update-pnpm-packages-theme-build-toolchain))
3. Composer: `composer outdated --direct` → `composer update …` ([§4](#4-update-composer-packages-php-tooling--wp-plugins))
4. `light-cli`: rebuild + bump refs in `compose.yml`/`Dockerfile.prod` ([§5](#5-update-the-light-cli-image))
5. `core-wp-wordpress`/WordPress: rebuild + recreate `wordpress`/`nginx` ([§6](#6-update-the-core-wp-wordpress-image--wordpress))
6. Base images: bump tags in both compose files ([§7](#7-update-the-base-images))
7. Verify everything ([§8](#8-verification-checklist))
8. Update each repo's CHANGELOG/README ([§2](#2-versioning--changelog-discipline))
9. Commit per-feature, then push
