# MainMoney for WordPress

[![Packagist](https://img.shields.io/packagist/v/mainmoney/mm-aggr-wordpress)](https://packagist.org/packages/mainmoney/mm-aggr-wordpress) [![License: GPL v2+](https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg)](LICENSE)

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

1. Install the plugin, either:
   - with Composer: `composer require mainmoney/mm-aggr-wordpress` (for Composer-managed sites such as Bedrock, which install `wordpress-plugin` packages into `wp-content/plugins`), or
   - from the zip attached to each [GitHub release](https://github.com/MainMoney-Inc/mm_aggr_wordpress/releases) (upload via Plugins → Add New → Upload; dependencies are bundled).
2. When working from a git checkout, run `composer install` in the plugin directory; the PHP SDK
   ([`mainmoney/mm-aggr-php-sdk`](https://packagist.org/packages/mainmoney/mm-aggr-php-sdk)) comes from Packagist.
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

Copyright (c) 2026 MainMoney SARL. Licensed under the GNU General Public License, version 2 or (at your option) any later version (GPL-2.0-or-later). See [LICENSE](LICENSE).

Want to contribute? See [CONTRIBUTING.md](CONTRIBUTING.md).
