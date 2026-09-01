<?php

declare(strict_types=1);

use MainMoney\WordPressPlugin\Settings;

final class Mm_Aggr_Plugin
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void
    {
        require_once MM_AGGR_DIR.'includes/class-options.php';
        require_once MM_AGGR_DIR.'includes/class-session-store.php';
        require_once MM_AGGR_DIR.'includes/class-admin.php';
        require_once MM_AGGR_DIR.'includes/class-admin-transactions.php';
        require_once MM_AGGR_DIR.'includes/class-admin-wallets.php';
        require_once MM_AGGR_DIR.'includes/class-admin-reports.php';
        require_once MM_AGGR_DIR.'includes/class-admin-assets.php';
        require_once MM_AGGR_DIR.'includes/class-rest.php';
        require_once MM_AGGR_DIR.'includes/class-assets.php';
        require_once MM_AGGR_DIR.'includes/class-shortcode.php';
        require_once MM_AGGR_DIR.'includes/class-block.php';

        Mm_Aggr_Admin::register();
        Mm_Aggr_Admin_Assets::register();
        Mm_Aggr_Rest::register();
        Mm_Aggr_Shortcode::register();
        Mm_Aggr_Block::register();

        add_action('admin_notices', [$this, 'maybeNotice']);
    }

    public function maybeNotice(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        if (Mm_Aggr_Options::load()->isConfigured()) {
            return;
        }
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('MainMoney is installed but merchant credentials are missing. Open MainMoney → Settings.', 'mm-aggr');
        echo '</p></div>';
    }

    public static function settings(): Settings
    {
        return Mm_Aggr_Options::load();
    }
}
