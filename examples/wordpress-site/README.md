# WordPress example site

Standalone WordPress 6.7+ site with the MainMoney plugin. Uses SQLite via
`sqlite-database-integration` so you do not need MySQL.

Default port: **8080**.

## Requirements

- PHP 8.2+
- Composer
- [WP-CLI](https://wp-cli.org/)
- yarn (to build the plugin checkout bundle once, if `assets/js/checkout.js` is missing)

## Setup

```bash
cp .env.example .env
# set MM_CLIENT_ID, MM_API_SECRET, MM_WEBHOOK_SECRET
./scripts/bootstrap.sh
./scripts/seed.sh
./scripts/serve.sh
```

Changing `.env` credentials or `MM_BASE_URI` does not update a running site until you run `./scripts/seed.sh` again (or save Settings → MainMoney). The plugin reads the WordPress option, not `.env`.

WordPress core is downloaded into `wordpress/` (gitignored).

Open:

- http://127.0.0.1:8080/pay — locked amount shortcode
- http://127.0.0.1:8080/donate — open amount
- WP Admin: `admin` / `admin` (change in `.env`)

Webhook URL (after a tunnel): `https://your-host.example/wp-json/mm-aggr/v1/webhooks`
