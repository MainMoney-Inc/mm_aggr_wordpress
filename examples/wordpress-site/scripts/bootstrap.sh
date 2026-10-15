#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PLUGIN_ROOT="$(cd "$ROOT/../.." && pwd)"
WP="$ROOT/wordpress"
cd "$ROOT"
if [[ -f .env ]]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
fi

if ! command -v wp >/dev/null 2>&1; then
  echo "WP-CLI (wp) is required. https://wp-cli.org/" >&2
  exit 1
fi

mkdir -p "$WP"
if [[ ! -f "$WP/wp-load.php" ]]; then
  wp core download --path="$WP"
fi

if [[ ! -f "$WP/wp-config.php" ]]; then
  wp config create --path="$WP" --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --skip-check
fi

wp plugin install sqlite-database-integration --path="$WP" --force
if [[ -f "$WP/wp-content/plugins/sqlite-database-integration/db.copy" ]]; then
  cp "$WP/wp-content/plugins/sqlite-database-integration/db.copy" "$WP/wp-content/db.php"
fi
wp plugin activate sqlite-database-integration --path="$WP" || true

if ! wp core is-installed --path="$WP" >/dev/null 2>&1; then
  wp core install --path="$WP" \
    --url="http://127.0.0.1:${PORT:-8080}" \
    --title="${WP_TITLE:-MainMoney WordPress Example}" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASSWORD:-admin}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@example.test}" \
    --skip-email
fi

mkdir -p "$WP/wp-content/plugins"
ln -sfn "$PLUGIN_ROOT" "$WP/wp-content/plugins/mm-aggr-wordpress"
(
  cd "$PLUGIN_ROOT"
  if [[ ! -d vendor ]]; then
    composer install
  fi
)
wp plugin activate mm-aggr-wordpress --path="$WP"
echo "WordPress example is bootstrapped in $WP"
