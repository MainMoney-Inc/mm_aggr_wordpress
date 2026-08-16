<?php

declare(strict_types=1);

use MainMoney\WordPressPlugin\CheckoutSession;

final class Mm_Aggr_Shortcode
{
    public static function register(): void
    {
        add_shortcode('mm_aggr_checkout', [self::class, 'render']);
    }

    /**
     * @param array<string, string>|string $atts
     */
    public static function render(array|string $atts): string
    {
        $parsed = shortcode_atts([
            'amount' => '',
            'currency' => '',
            'reference' => '',
        ], is_array($atts) ? $atts : []);
        $amount = is_string($parsed['amount']) ? $parsed['amount'] : '';
        $lockAmount = $amount !== '';
        $session = CheckoutSession::create(
            amount: $amount !== '' ? $amount : null,
            currency: is_string($parsed['currency']) && $parsed['currency'] !== '' ? $parsed['currency'] : null,
            reference: is_string($parsed['reference']) && $parsed['reference'] !== '' ? $parsed['reference'] : null,
            lockAmount: $lockAmount,
        );
        (new Mm_Aggr_Session_Store())->save($session);
        $targetId = 'mm-aggr-checkout-'.wp_unique_id();
        Mm_Aggr_Assets::enqueue($targetId, $session);

        return '<div class="mm-aggr-checkout" id="'.esc_attr($targetId).'"></div>';
    }
}
