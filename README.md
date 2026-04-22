# Core WP Developer Framework

WordPress starter framework for block theme development with a Docker-first local dev workflow.

---

## Quick Start

```bash
git clone https://github.com/Blake-C/core-wp.git your-project-name
cd your-project-name
rm -rf .git && git init && git add -A
git commit -m "Initial Commit"
git config core.hooksPath .githooks
docker compose up -d
docker compose exec cli_tools zsh
```

Inside the container:

```bash
cd wp-content && composer install
wp-init
```

`wp-init` installs and builds the theme, configures WordPress, sets the homepage and theme, and removes default themes and plugins.

---

## Access

| Resource | URL |
| --- | --- |
| Site | http://localhost |
| phpMyAdmin | http://localhost:8000 |

phpMyAdmin credentials: server `db`, user/password from `.env`.

---

## Build Commands

Run inside the `cli_tools` container (`docker compose exec cli_tools zsh`, then `cd wp-content/themes/core-wp`):

| Command | Description |
| --- | --- |
| `pnpm run build` | Full build — styles, scripts, images, static assets |
| `pnpm run watch` | Watch mode |
| `pnpm run serve` | Watch + Browser-Sync livereload |
| `pnpm run serve:all` | Same as `serve` plus PHP and HTML watching |

---

## Importing an Existing Site

```bash
docker compose up -d
docker compose exec cli_tools zsh
```

1. Copy `plugins/` and `uploads/` into `wp-content/`
2. Import the database via phpMyAdmin at `http://localhost:8000`
3. Run a search-replace for the production URL:
   ```bash
   wp search-replace https://production.com http://localhost --precise --all-tables
   ```
4. Activate the theme and build:
   ```bash
   wp theme activate core-wp
   pnpm install && pnpm run build
   ```

---

## Production Deployment

```bash
cp .env-example .env   # fill in production credentials
chmod 600 .env
./deploy.sh
```

To deploy updates: `git pull && ./deploy.sh`

`deploy.sh` pulls the latest base images, rebuilds the WordPress image, and restarts services.

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
