# Conventions

- WordPress coding standards where they apply; PHP 8.2+.
- Server calls go through `mainmoney/mm-aggr-php-sdk`. Do not add a second HTTP client.
- Browser UI uses the official JS/TS SDK bundled into `assets/js/checkout.js`.
- REST paths match the JS/TS `DEFAULT_PATHS` convention. Never localize merchant API keys.
