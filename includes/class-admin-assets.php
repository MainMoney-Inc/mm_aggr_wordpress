<?php

declare(strict_types=1);

final class Mm_Aggr_Admin_Assets
{
    public static function register(): void
    {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue']);
    }

    public static function enqueue(string $hook): void
    {
        if (!str_contains($hook, 'mm-aggr')) {
            return;
        }

        wp_enqueue_style(
            'mm-aggr-admin',
            MM_AGGR_URL.'assets/css/admin.css',
            [],
            MM_AGGR_VERSION,
        );

        if (str_contains($hook, 'mm-aggr-reports')) {
            wp_enqueue_script(
                'chart-js',
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
                [],
                '4.4.1',
                true,
            );
            wp_enqueue_script(
                'mm-aggr-admin-reports',
                MM_AGGR_URL.'assets/js/admin-reports.js',
                ['chart-js'],
                MM_AGGR_VERSION,
                true,
            );
        }
    }
}
