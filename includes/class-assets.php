<?php

declare(strict_types=1);

use MainMoney\WordPressPlugin\CheckoutConfig;
use MainMoney\WordPressPlugin\CheckoutSession;

final class Mm_Aggr_Assets
{
    public static function enqueue(string $targetId, CheckoutSession $session): void
    {
        $script = MM_AGGR_URL.'assets/js/checkout.js';
        $style = MM_AGGR_URL.'assets/js/checkout.css';
        wp_enqueue_style('mm-aggr-checkout', $style, [], MM_AGGR_VERSION);
        wp_enqueue_script('mm-aggr-checkout', $script, [], MM_AGGR_VERSION, true);
        $config = CheckoutConfig::forSession(
            rest_url('mm-aggr/v1'),
            rest_url('mm-aggr/v1/status'),
            $session,
        );
        $config['targetId'] = $targetId;
        $config['logoUrl'] = MM_AGGR_URL.'assets/js/main_money_square.png';
        wp_add_inline_script(
            'mm-aggr-checkout',
            'window.mmAggrCheckouts = window.mmAggrCheckouts || []; window.mmAggrCheckouts.push('.wp_json_encode($config).');',
            'before',
        );
    }
}
