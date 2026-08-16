<?php

declare(strict_types=1);

final class Mm_Aggr_Block
{
    public static function register(): void
    {
        add_action('init', [self::class, 'block']);
    }

    public static function block(): void
    {
        register_block_type(MM_AGGR_DIR.'blocks/checkout', [
            'render_callback' => [self::class, 'render'],
        ]);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public static function render(array $attributes): string
    {
        $atts = [
            'amount' => isset($attributes['amount']) && is_string($attributes['amount']) ? $attributes['amount'] : '',
            'currency' => isset($attributes['currency']) && is_string($attributes['currency']) ? $attributes['currency'] : '',
            'reference' => isset($attributes['reference']) && is_string($attributes['reference']) ? $attributes['reference'] : '',
        ];

        return Mm_Aggr_Shortcode::render($atts);
    }
}
