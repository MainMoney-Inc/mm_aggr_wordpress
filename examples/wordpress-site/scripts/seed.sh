#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
WP="$ROOT/wordpress"
cd "$ROOT"
if [[ -f .env ]]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
fi

TEST_JSON=false
if [[ "${MM_TEST:-true}" == "true" || "${MM_TEST:-true}" == "1" || "${MM_TEST:-true}" == "yes" ]]; then
  TEST_JSON=true
fi

python3 - "$WP" "$TEST_JSON" <<'PY' | wp option update mm_aggr_settings --path="$WP" --format=json
import json, os, sys
print(json.dumps({
    "client_id": os.environ.get("MM_CLIENT_ID", ""),
    "secret": os.environ.get("MM_API_SECRET", ""),
    "test": sys.argv[2] == "true",
    "base_uri": os.environ.get("MM_BASE_URI", ""),
    "webhook_secret": os.environ.get("MM_WEBHOOK_SECRET", ""),
}))
PY

if ! wp post list --path="$WP" --post_type=page --name=pay --field=ID | grep -q '[0-9]'; then
  wp post create --path="$WP" --post_type=page --post_status=publish --post_title="Pay" --post_name=pay \
    --post_content='[mm_aggr_checkout amount="25.00" currency="USD"]'
fi
if ! wp post list --path="$WP" --post_type=page --name=donate --field=ID | grep -q '[0-9]'; then
  wp post create --path="$WP" --post_type=page --post_status=publish --post_title="Donate" --post_name=donate \
    --post_content='[mm_aggr_checkout]'
fi
wp rewrite structure '/%postname%/' --path="$WP"
echo "Seeded MainMoney settings and Pay/Donate pages."
