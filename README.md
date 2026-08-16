# MainMoney for WordPress

Accept MainMoney aggregator payments on WordPress. This plugin uses the
official [PHP SDK](https://github.com/MainMoney-Inc/mm_aggr_php_sdk) on the
server and the [JS/TS frontend SDK](https://github.com/MainMoney-Inc/mm_aggr_js_sdk)
in the browser.

For WooCommerce checkouts, install [MainMoney for WooCommerce](https://github.com/MainMoney-Inc/mm_aggr_woocommerce) instead.

## Requirements

- WordPress 6.7 or later
- PHP 8.2 or later
- A merchant application on MM Aggregator

## Install

1. Install the plugin (Composer or zip) into `wp-content/plugins/`.
2. Activate **MainMoney** in WP Admin → Plugins.
3. Enter your aggregator base URL and API credentials in Settings.

Do not put API keys in theme JavaScript.

## License

Copyright (c) 2026 MainMoney SARL. Licensed under the PolyForm Noncommercial
License 1.0.0. Commercial use requires permission from MainMoney SARL.
See [LICENSE](LICENSE).

Want to contribute? See [CONTRIBUTING.md](CONTRIBUTING.md).
