<?php
/**
 * Plugin Name: MainMoney
 * Description: MainMoney aggregator payments for WordPress.
 * Version: 0.1.0
 * Requires at least: 6.7
 * Requires PHP: 8.2
 * Author: MainMoney SARL
 * License: PolyForm-Noncommercial-1.0.0
 * Text Domain: mm-aggr
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('MM_AGGR_VERSION', '0.1.0');
define('MM_AGGR_FILE', __FILE__);
define('MM_AGGR_DIR', plugin_dir_path(__FILE__));
define('MM_AGGR_URL', plugin_dir_url(__FILE__));

$mmAggrAutoload = MM_AGGR_DIR.'vendor/autoload.php';
if (!is_file($mmAggrAutoload)) {
    add_action('admin_notices', static function (): void {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('MainMoney requires Composer dependencies (mainmoney/mm-aggr-php-sdk). Run composer install in the plugin directory.', 'mm-aggr');
        echo '</p></div>';
    });

    return;
}

require_once $mmAggrAutoload;

add_action('plugins_loaded', static function (): void {
    load_plugin_textdomain('mm-aggr', false, dirname(plugin_basename(MM_AGGR_FILE)).'/languages');
    require_once MM_AGGR_DIR.'includes/class-plugin.php';
    Mm_Aggr_Plugin::instance()->boot();
});
