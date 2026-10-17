# Changelog

## Unreleased

- fix: the Idempotency-Key follows the checkout session instead of the reference; a failed payment shows only the error and a Retry button that resubmits with a new reference suffix (`<reference>-r<N>`)
- feat: countries, providers, limits and checkout preferences are cached (24h) and renewed when settings are saved or from the new "Clear cache" button
- feat: the receipt email is pre-filled for logged-in users and can be changed

## 0.1.0

- Licensed under GPL-2.0-or-later.
- First release (Packagist `mainmoney/mm-aggr-wordpress`, zip on GitHub Releases).
