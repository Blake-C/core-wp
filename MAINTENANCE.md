# MAINTENANCE.md: Update and Upgrade Runbook

This runbook covers updating pnpm packages, Composer packages, all Docker images,
WordPress, and the two sibling image repos, along with the versioning rules, the
changelog discipline, and the project-specific gotchas.

Update the CHANGELOG **before** committing, bump versions per
SemVer, and make the changelog match the build artifacts. See
[Versioning & changelog discipline](#versioning--changelog-discipline).

---

## 1. What this project is made of

The project spans three Git repos, checked out side by side under `Repositories/`:

| Repo | Path | Produces | Referenced by |
|---|---|---|---|
| `core-wp` | `./` (this repo) | The WordPress site (theme + plugins + compose stack) | n/a |
| `light-cli` | `../light-cli` | `digitalblake/light-cli` image (build tooling: Node/npm/pnpm/PHP/Composer/WP-CLI) | `compose.yml` `cli_tools`, `docker/wordpress/Dockerfile.prod` builder stages |
| `core-wp-wordpress` | `../core-wp-wordpress` | `digitalblake/core-wp-wordpress` image (security-patched WordPress PHP-FPM) | `compose.yml` `wordpress`, `Dockerfile.prod` stage 3 |

### Images referenced by the compose files

| Image | Where pinned | Update path |
|---|---|---|
| `digitalblake/light-cli:6.x.x` | `compose.yml`, `Dockerfile.prod` (×2 stages) | Rebuild the `light-cli` repo, then bump the tag in both files ([§5](#5-update-the-light-cli-image)) |
| `digitalblake/core-wp-wordpress:php8.4-latest` | `compose.yml`, `Dockerfile.prod` (stage 3) | Rolling tag. Rebuild the `core-wp-wordpress` repo; the reference does **not** change ([§6](#6-update-the-core-wp-wordpress-image--wordpress)) |
| `nginx:1.31-alpine` | `compose.yml`, `compose.prod.yml` | Bump tag ([§7](#7-update-the-base-images)) |
| `mariadb:12.3` | `compose.yml`, `compose.prod.yml` | Bump tag. A major bump runs `mariadb-upgrade` through `MARIADB_AUTO_UPGRADE` ([§7](#7-update-the-base-images)) |
| `redis:8-alpine` | `compose.yml`, `compose.prod.yml` | Bump tag ([§7](#7-update-the-base-images)) |
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

1. **Make the change** and verify it with a build, lint, and scan ([§8](#8-verification-checklist)).
2. **Update the changelog** for the repo you touched (see formats below). The
   changelog describes the **net current state** that will ship, and leaves out
   intermediate steps. If you bumped something twice in one unreleased cycle,
   the entry reflects the final version only.
3. **Pick the version** per SemVer (see per-repo rules below).
4. **Commit** one logical change per commit. Use a past-tense,
   capitalized subject (`Updated…`, `Fixed…`, `Added…`). No AI attribution.

### Changelog rules (learned the hard way)

- **One section per release.** Never duplicate `### Security` / `### Added` /
  `### Changed` / `### Removed` within a single release block.
- **No stale versions.** If a line says "pnpm 11.1.2" but the tree now ships
  11.6.0, fix the line. The release must match the build artifacts.
- **No redundant entries.** Don't document the same artifact twice (e.g. an
  image bumped to `6.1.0` in one line and `6.2.0` in another). Keep only the final version.
- **Cite the files** changed at the end of each entry, e.g. `(compose.yml)`.

### Per-repo versioning

| Repo | Scheme | Bump rules |
|---|---|---|
| `core-wp` | `CHANGELOG.md`, one `## [X.Y.Z] - YYYY-MM-DD` block per change set; sections Security/Added/Changed/Removed | Each change set gets a new version dated the day it is committed. Before 1.0, added capability or a breaking toolchain change raises MINOR, and dependency updates and fixes raise PATCH |
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

# 3. Refresh the lockfile. The workspace sets frozenLockfile, so a plain
#    `pnpm install` fails and you must pass --no-frozen-lockfile.
docker compose exec -w /home/webdev/www/public_html/wp-content/themes/core-wp cli_tools pnpm install --no-frozen-lockfile

# 4. Security scan, then build/lint (see §8). Fix and rescan until clean.
```

**`minimumReleaseAge` (7 days):** `pnpm-workspace.yaml` sets
`minimumReleaseAge: 10080`. pnpm **refuses to install any package version
published less than 7 days ago**, as a defense against supply-chain attacks such as Shai-Hulud.
If `pnpm outdated` shows a newer version but it won't install, check its publish
date; if it's < 7 days old, leave it and revisit after it ages. Do **not** lower
`minimumReleaseAge` to install a newer package sooner.

**Transitive CVE fixes** go in the `overrides:` block of `pnpm-workspace.yaml`.
pnpm v11 and later read overrides there and ignore the `pnpm` field in
`package.json`. After editing overrides, run `pnpm install --no-frozen-lockfile`
on the existing lockfile, then diff the lock to confirm only the intended packages
changed. Do not delete `pnpm-lock.yaml` to apply an override, because without a lockfile
pnpm re-resolves every transitive package to its newest version, which makes a much larger
diff and runs `trustPolicy` against packages the override never touched.

To scope an override to one major line, key it with a range, for example
`'immutable@3': '^3.8.4'`, so a package that depends on a different major is left alone.

**`trustPolicy: no-downgrade`:** pnpm refuses a version whose publisher trust evidence
(trusted publisher, maintainer approval, provenance) is weaker than an earlier release of
the same package, compared by publish date. When pnpm rejects a version this way, check the package on the
registry before changing any config:

```sh
# Publisher, trusted publishing, maintainer approval, and provenance for every version
curl -s https://registry.npmjs.org/<pkg> | node -e 'let s="";process.stdin.on("data",d=>s+=d).on("end",()=>{const j=JSON.parse(s);for(const [v,x] of Object.entries(j.versions)){const u=x._npmUser||{};console.log(v,j.time[v],u.name,"trustedPublisher:",!!u.trustedPublisher,"approver:",!!u.approver,"provenance:",!!(x.dist&&x.dist.attestations))}})'

# Repo, workflow, and commit that built one version (base64 payload in dsseEnvelope)
curl -s https://registry.npmjs.org/-/npm/v1/attestations/<pkg>@<version>
```

If the drop in trust evidence has a harmless cause, add the exact version to `trustPolicyExclude` with a
comment explaining it (see `ua-parser-js@1.0.41`). Otherwise hold the package at the last
version that passes (see §9).

**Unknown settings fail under pnpm 12.** The theme pins pnpm through `packageManager`, so
any key in `pnpm-workspace.yaml` that pnpm 12 does not recognize fails the command with
`ERR_PNPM_UNRECOGNIZED_WORKSPACE_SETTINGS`. Run `pnpm config list` after editing the file.

---

## 4. Update Composer packages (PHP tooling + WP plugins)

Files: `wp-content/composer.json` (caret ranges), `composer.lock`.

```sh
# 1. See what's outdated, and check for advisories
docker compose exec -w /home/webdev/www/public_html/wp-content cli_tools composer outdated --direct
docker compose exec -w /home/webdev/www/public_html/wp-content cli_tools composer audit

# 2. Update a package (caret ranges usually allow it without editing composer.json)
docker compose exec -w /home/webdev/www/public_html/wp-content cli_tools composer update wp-plugin/wordpress-seo --no-interaction

# 3. Validate + confirm lock is in sync
docker compose exec -w /home/webdev/www/public_html/wp-content cli_tools composer validate
```

When an advisory is fixed in a newer release, raise the caret floor in `composer.json`
to the fixed version so a later `composer update` cannot resolve below it. A plugin major
bump (for example `wp-plugin/wordfence ^9.0`) needs a `composer.json` edit; check the
plugin's "Requires at least" and "Requires PHP" values first.

Run a Snyk SCA scan on the composer tree afterward (see [§8](#8-verification-checklist)).

---

## 5. Update the `light-cli` image

Repo: `../light-cli`. Files: `Dockerfile`, `README.md` (the version table is the
release record, because this repo has no CHANGELOG).

The image is built on Alpine and ships Node (apk), npm (pinned global install), pnpm (Corepack,
pinned), PHP 8.4 and extensions, Composer (the installer fetches the latest version at build), WP-CLI,
browser-sync, and ImageMagick.

```sh
# Check current vs latest for each piece:
docker run --rm digitalblake/light-cli:<current> sh -c 'cat /etc/alpine-release; node -v; npm -v; pnpm -v; php -v | head -1; composer --version; wp --version --allow-root'
# Alpine latest: https://hub.docker.com/_/alpine/tags   pnpm: npm view pnpm time   npm: npm view npm time
# Before bumping the Alpine base, confirm 3.x still ships Node >= the theme's
# engines (package.json "node") and php84:
docker run --rm alpine:<new> sh -c 'apk update >/dev/null 2>&1; apk policy nodejs php84'
```

Edit `Dockerfile` (base image and/or pinned tool versions), update the
`README.md` version table, then build and verify **as the `webdev` user**:

```sh
cd ../light-cli
docker build --pull --no-cache -t digitalblake/light-cli:<new-tag> .
docker run --rm digitalblake/light-cli:<new-tag> sh -c 'whoami; npm -v; pnpm -v; node -v; cat /etc/alpine-release; composer --version; pnpm config get store-dir'
```

Pick npm and pnpm versions that are at least 7 days old, the same rule the theme's
`minimumReleaseAge` applies.

**npm gotcha:** Alpine's `npm` apk package lags npm releases and bundles vulnerable
copies of tar, sigstore, and pacote. The Dockerfile uses it only to install a pinned
`npm@<ver>` globally into `/usr/local`, then runs `apk del npm`.

**pnpm pin gotcha (Corepack drift):** by default Corepack looks up the latest
version of the package manager on the npm registry. The Dockerfile sets
`ENV COREPACK_DEFAULT_TO_LATEST=0`, which turns that lookup off, and runs
`corepack prepare pnpm@<ver> --activate` **as the `webdev` runtime user** (not via sudo)
so the activated version is the one the `webdev` user runs. Verify `pnpm -v` matches
the intended version.

**After publishing a new `light-cli` tag**, bump the references in **this** repo:
`compose.yml` (`cli_tools`) and `docker/wordpress/Dockerfile.prod` (both builder
stages). Recreate the container: `docker compose up -d cli_tools`.

**Keep pnpm in sync with the theme:** the theme's `package.json` `packageManager`
field pins the exact pnpm version Corepack uses inside the project (it overrides
the image default). To move the theme to the image's pnpm, run in the theme dir:
```sh
docker compose exec -w /home/webdev/www/public_html/wp-content/themes/core-wp cli_tools corepack use pnpm@<ver>
```
This rewrites `packageManager` with the correct integrity hash. pnpm 12 keeps
`lockfileVersion 9.0`, so an existing lockfile still installs. pnpm 12 also adds a
first YAML document to `pnpm-lock.yaml` that pins pnpm's own binaries by hash.

**Release path:** `light-cli` has no CI workflow. Build locally, then push the
versioned tag and `latest` together, so `latest` never points at an older build:

```sh
docker tag digitalblake/light-cli:<tag> digitalblake/light-cli:latest
docker push digitalblake/light-cli:<tag> && docker push digitalblake/light-cli:latest
```

The published tags are `linux/arm64` only, built on Apple silicon.

---

## 6. Update the `core-wp-wordpress` image / WordPress

Repo: `../core-wp-wordpress`. Files: `Dockerfile`, `CHANGELOG.md`, `README.md`,
`.github/workflows/docker-publish.yml`.

The Dockerfile builds `FROM wordpress:php8.4-fpm-alpine` (a **rolling** base), then
runs `apk upgrade` and `pecl install xdebug-<ver>`. **The WordPress version comes from
the rolling base**, so a plain rebuild pulls whatever WordPress the base
currently ships, and a WP bump needs no Dockerfile edit.

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
docker build --no-cache -t digitalblake/core-wp-wordpress:php8.4-latest -t digitalblake/core-wp-wordpress:php8.4-<X.Y.Z> .
docker run --rm digitalblake/core-wp-wordpress:php8.4-latest sh -c 'grep "wp_version =" /usr/src/wordpress/wp-includes/version.php; php -r "echo phpversion(\"xdebug\");"'
```

Update `CHANGELOG.md` with one `### Changed` section per release. If you also bumped
XDebug, change the PATCH version to a MINOR version and put both changes in that one
section, so no stale PATCH entry is left behind.

**GitHub Actions** in the publish workflow are pinned to release commit SHAs with the
version in a trailing comment. To bump one, resolve the new tag to its commit and
replace the SHA:

```sh
git ls-remote https://github.com/<owner>/<action> "refs/tags/<tag>" "refs/tags/<tag>^{}" | tail -1
```

**Apply WordPress to the running site** with WP-CLI. The image entrypoint copies core
from `/usr/src/wordpress` into the bind-mounted `./wordpress/` only when that directory
has no WordPress install (no `index.php` and no `wp-includes/version.php`), so recreating
the containers leaves an existing install at its current version. This site also has
major core auto-updates enabled (`auto_update_core_major`), so WordPress can update
`./wordpress/` by itself after the new version is released.

```sh
docker compose up -d --force-recreate wordpress nginx
docker compose exec -w /home/webdev/www/public_html cli_tools sh -c 'wp core update --version=<X.Y>; wp core update-db; wp core verify-checksums'
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost/
```

In production, the entrypoint fills the `webroot` named volume from the image only
when the volume is empty, so the same rule applies there.

**Release path:** GitHub Actions publishes the image. A push to `main` publishes
`latest`, and a tag push publishes `php8.4-<X.Y.Z>` and `php8.4-latest`. Update the
CHANGELOG, commit, then push the branch and the tag in one command:

```sh
git tag php8.4-<X.Y.Z>
git push origin main php8.4-<X.Y.Z>
```

Pushing `main` without the tag moves `latest` and leaves `php8.4-latest` on the
previous build, which is what happened when the `php8.4-1.2.1` commit was pushed without its tag. After both runs finish,
confirm on Docker Hub that `latest`, `php8.4-latest`, and `php8.4-<X.Y.Z>` share one digest.

The compose reference (`php8.4-latest`) is rolling and **does not change**, so no
edit is needed in this repo for a WordPress bump (though note it in this repo's
CHANGELOG since the running environment changed).

---

## 7. Update the base images (nginx / mariadb / redis)

The base images are pinned in `compose.yml` and `compose.prod.yml`. Bump the tag in
both files, recreate the containers, and confirm they report healthy. Check Docker Hub for the newest patch in the pinned
line and watch Snyk for the image.

```sh
docker compose pull nginx db redis && docker compose up -d
docker compose ps   # all should report (healthy)
```

**MariaDB major bump:** both compose files set `MARIADB_AUTO_UPGRADE: 1`, so the image
runs `mariadb-upgrade` on the existing data directory when the server version changes.
Back up first, because a system table upgrade can block a later downgrade:

```sh
docker compose exec -T cli_tools sh -c 'cd /home/webdev/www/public_html && wp db export /tmp/pre-upgrade.sql --single-transaction'
docker compose cp cli_tools:/tmp/pre-upgrade.sql <backup-dir>/
docker compose stop db && cp -a data/mysql <backup-dir>/mysql-<old-version>
docker compose up -d db && docker compose logs db | grep -i upgrade
docker compose exec -T cli_tools sh -c 'cd /home/webdev/www/public_html && wp db check'
```

**nginx healthcheck gotcha:** the dev healthcheck sends `Host: localhost`, so
WordPress returns a `200` and does not send a canonical `301` redirect away from `127.0.0.1`.
The prod healthcheck does not depend on the domain and accepts any `200`, `301`, or `302`
response. If you rewrite a healthcheck, keep that behavior, or the container reports
`unhealthy` while it is still serving pages.

---

## 8. Verification checklist

Run after **any** dependency or image change (inside `cli_tools`, theme dir):

| Check | Command | Confirms |
|---|---|---|
| Security (deps) | `snyk test --all-projects --dev` on the theme copy (see note below), and `snyk test --file=composer.lock` in `wp-content` | only the known items in §9; rescan after fixes |
| Security (code) | `snyk code test` in the theme and in `wp-content/plugins/core-wp` | no issues outside `node_modules` |
| Security (images) | Snyk dashboard after publishing | no new OS or app CVEs in the changed images |
| Advisories | `pnpm audit`, `composer audit` | only the known items in §9 |
| Registry signatures | `pnpm audit signatures` | every installed package has a verified signature |
| Frozen install | `pnpm install --frozen-lockfile` | lockfile passes the supply-chain policies |
| Production build | `pnpm run build:prod` | Sass→cssnano + Webpack/Babel pipeline works; minified CSS/JS emitted |
| Prod image | `docker build -f docker/wordpress/Dockerfile.prod .` | the builder stages install and build inside `light-cli` |
| SCSS lint | `pnpm run styles:lint` | stylelint + Prettier clean |
| JS lint | `pnpm exec eslint theme_components/js` | ESLint clean |
| PHP lint | `pnpm run phpcs`, plus phpcs in `wp-content/plugins/core-wp` | WPCS clean |
| Composer | `composer validate` (composer root) | manifest valid, lock in sync |
| Compose | `docker compose [-f compose.prod.yml] config` | YAML parses |
| Health | `docker compose ps` | services `(healthy)`; `curl http://localhost/` → 200 |

**Snyk runs in a container.** No Snyk binary is installed on the host. The `snyk`
shell function from `digitalblake-system-setup` (`snyk/scan.sh`) runs the CLI in the
`digitalblake/snyk` image with the git top level of the current directory mounted
read-only, so the files to scan have to live inside this repo. The container has no
Docker socket and no registry access, so it cannot scan images. Image findings come
from the Snyk dashboard, because the `core-wp-wordpress` workflow runs `snyk monitor` on each
published build and Snyk scans the `light-cli` Dockerfile through its GitHub project. `snyk_mcp_enable` in the project adds the same container as an MCP server.

**Snyk and the pnpm 12 lockfile:** Snyk fails on `pnpm-lock.yaml` with "expected a single
document in the stream" because of the first YAML document pnpm 12 adds. Scan a copy that
holds only the second document. Snyk asks for `--all-projects` when it finds
`pnpm-workspace.yaml`, and it skips devDependencies without `--dev`:

```sh
# from the repo root
mkdir .snyk-theme-scan && cp wp-content/themes/core-wp/{package.json,pnpm-workspace.yaml} .snyk-theme-scan/
awk 'n>=2; /^---$/{n++}' wp-content/themes/core-wp/pnpm-lock.yaml > .snyk-theme-scan/pnpm-lock.yaml
(cd .snyk-theme-scan && snyk test --all-projects --dev)
rm -rf .snyk-theme-scan
```

Project policy is to run `snyk code test` for modified first-party PHP/JS and
`snyk test` when changing dependencies, then fix and rescan until clean.

---

## 9. Held-back packages and known findings

| Package | Held at | Reason |
|---|---|---|
| `cssnano`, `cssnano-preset-advanced` | 8.0.10 | Releases 8.0.3 to 8.0.10 were published with a maintainer approval, and no 9.x release through `cssnano` 9.8.0 and `cssnano-preset-advanced` 9.7.0 (2026-10-12) carries one, so `trustPolicy: no-downgrade` rejects 9.x. Move when a 9.x release carries the approval again |
| `squizlabs/php_codesniffer` | 3.x | WordPress Coding Standards 3.x requires PHP_CodeSniffer `^3.13.5` and does not support 4.0 |
| `phpcompatibility/php-compatibility` | 9.3.5 | 10.x is still alpha |
| `ua-parser-js` | 1.0.41 (in `trustPolicyExclude`) | browser-sync requires `^1`. The MIT 1.x line has no provenance and the AGPL 2.x line does, so 1.x reads as a trust downgrade |
| `braces` (via chokidar and micromatch) | 3.0.3 | `pnpm audit` and Snyk report GHSA-vfj7-8cjw-p6xm (SNYK-JS-BRACES-19963945, stack exhaustion from nested patterns). 3.0.3 is the newest release and no patched version exists. It is used only by the dev file watchers and stylelint |
| `uri-js` (via eslint and ajv 6) | 4.4.1 | Snyk reports SNYK-JS-URIJS-19963963 (high), SNYK-JS-URIJS-20341609, and SNYK-JS-URIJS-19963961. 4.4.1 (2021-01-10) is the final release, and ajv 6.15.0 still depends on it. It is used only by ESLint |
| `undici` inside npm (`light-cli`) | 6.28.0 | npm 11.21.0 and npm 12.2.0 both bundle 6.28.0, so no npm release has the fix yet. pnpm 12.10.1 bundles 6.29.0 |
| Any package fix < 7 days old | current | Blocked by `minimumReleaseAge` (see [§3](#3-update-pnpm-packages-theme-build-toolchain)); revisit after it ages 7 days |

Re-test these on each pass, and update a package once the reason in its row no longer applies.

---

## 10. Gotchas cheat-sheet

- **pnpm `frozenLockfile`** → regenerate with `pnpm install --no-frozen-lockfile`.
- **pnpm `minimumReleaseAge` (7 days)** → releases less than 7 days old are blocked.
- **pnpm `overrides`** live in `pnpm-workspace.yaml`. pnpm v11 and later ignore the `pnpm` field in `package.json`.
- **Overrides on an existing lockfile** → do not delete `pnpm-lock.yaml`, because deleting it re-resolves the whole dependency tree.
- **pnpm 12 unknown settings** → any unrecognized key in `pnpm-workspace.yaml` fails the command.
- **`trustPolicy: no-downgrade`** → inspect the registry trust evidence before excluding or holding a package.
- **pnpm 12 lockfile** has two YAML documents; Snyk needs a copy with only the second one.
- **VS Code after `pnpm install`** → the Prettier extension keeps the old Prettier version loaded and fails with "Cannot find module" once `pnpm install` deletes that version from `node_modules/.pnpm`. Run **Developer: Reload Window**.
- **`Dockerfile.prod` theme stage** must copy `pnpm-workspace.yaml` and `.npmrc` with the lockfile, owned by `webdev`, or the frozen install fails.
- **`packageManager` field** in the theme overrides the image's pnpm; bump with
  `corepack use pnpm@<ver>` to get the right hash.
- **Corepack auto-upgrade** → pin with `COREPACK_DEFAULT_TO_LATEST=0` + activate
  as the `webdev` user in the `light-cli` Dockerfile.
- **WordPress core** is bind-mounted from `./wordpress`. The image entrypoint copies core
  only into an empty directory, so update an existing install with `wp core update`.
- **nginx healthcheck** must tolerate WordPress canonical redirects (Host header
  in dev, status-tolerant in prod).
- **`cli_tools` paths** differ from the `wordpress` container's `/var/www/html`.
- **npm-registry timeouts** during `docker build` are transient; retry the build.
- **Docker Hub push timeouts** on large `light-cli` layers are transient; rerun `docker push`, which skips the layers already uploaded.
- **Versioned and `latest` image tags** are always pushed together. `light-cli:<tag>` is pushed with `light-cli:latest`, and the `core-wp-wordpress` `main` branch is pushed with its `php8.4-<X.Y.Z>` tag. Do not add a `latest` tag to an image that does not already publish one.
- **pnpm overrides and direct pins**: a pnpm override also replaces the theme's own `package.json` pin, so an override floor below the pinned version holds the direct dependency back.

---

## 11. Quick "everything" pass

1. `docker compose up -d`
2. pnpm: `pnpm outdated` → edit `package.json` → `pnpm install --no-frozen-lockfile` ([§3](#3-update-pnpm-packages-theme-build-toolchain))
3. Composer: `composer outdated --direct` + `composer audit` → `composer update …` ([§4](#4-update-composer-packages-php-tooling--wp-plugins))
4. `light-cli`: rebuild + bump refs in `compose.yml`/`Dockerfile.prod` ([§5](#5-update-the-light-cli-image))
5. `core-wp-wordpress`/WordPress: rebuild + recreate `wordpress`/`nginx` ([§6](#6-update-the-core-wp-wordpress-image--wordpress))
6. Base images: bump tags in both compose files ([§7](#7-update-the-base-images))
7. Verify everything ([§8](#8-verification-checklist))
8. Update each repo's CHANGELOG/README ([§2](#2-versioning--changelog-discipline))
9. Commit per-feature, then push
