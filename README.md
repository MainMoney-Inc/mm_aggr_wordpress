# MainMoney for WordPress

Accept MainMoney aggregator payments on WordPress. This plugin uses the
official [PHP SDK](https://github.com/MainMoney-Inc/mm_aggr_php_sdk) on the
server and the [JS/TS frontend SDK](https://github.com/MainMoney-Inc/mm_aggr_js_sdk)
in the browser.

For WooCommerce checkouts, install [MainMoney for WooCommerce](https://github.com/MainMoney-Inc/mm_aggr_woocommerce) instead.

## Requirements

- WordPress 6.7 or later
- PHP 8.2 or later
- Composer
- A merchant application on MM Aggregator

## Install

1. Copy this plugin into `wp-content/plugins/mm-aggr-wordpress` (or install the zip).
2. From the plugin directory run `composer install`. Until Packagist lists the
   PHP SDK, Composer loads it from the sibling `contrib/sdks/php` checkout (or
   clone [mm_aggr_php_sdk](https://github.com/MainMoney-Inc/mm_aggr_php_sdk) next
   to this repo at `../../sdks/php`).
3. Activate **MainMoney** in WP Admin → Plugins.
4. Settings → MainMoney: Client ID, API secret, Test mode, webhook secret.
   Leave Base URI empty unless you override the SDK hosts.
5. In the aggregator admin, set the merchant webhook URL to
   `https://your-site.example/wp-json/mm-aggr/v1/webhooks`.

Do not put API keys in theme JavaScript.

## Checkout

Shortcode (omit `amount` for an open amount; set it to lock the total):

```
[mm_aggr_checkout amount="25.00" currency="USD" reference="ORDER-123"]
```

Or insert the **MainMoney Checkout** block. The wizard talks only to this
site’s REST proxy (`/wp-json/mm-aggr/v1/...`), which calls the PHP SDK.

## Examples

A local WordPress site with this plugin is in [examples/wordpress-site](examples/wordpress-site).

## License

Copyright (c) 2026 MainMoney SARL. Licensed under the PolyForm Noncommercial
License 1.0.0. Commercial use requires permission from MainMoney SARL.
See [LICENSE](LICENSE).

Want to contribute? See [CONTRIBUTING.md](CONTRIBUTING.md).
