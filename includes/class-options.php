<?php

declare(strict_types=1);

use MainMoney\WordPressPlugin\Settings;

final class Mm_Aggr_Options
{
    public const OPTION_KEY = 'mm_aggr_settings';

    public static function load(): Settings
    {
        $raw = get_option(self::OPTION_KEY, []);
        if (!is_array($raw)) {
            $raw = [];
        }
        $clientId = isset($raw['client_id']) && is_string($raw['client_id']) ? $raw['client_id'] : '';
        $secret = isset($raw['secret']) && is_string($raw['secret']) ? $raw['secret'] : '';
        $test = !empty($raw['test']);
        $baseUri = isset($raw['base_uri']) && is_string($raw['base_uri']) && trim($raw['base_uri']) !== ''
            ? trim($raw['base_uri'])
            : null;
        $webhookSecret = isset($raw['webhook_secret']) && is_string($raw['webhook_secret']) ? $raw['webhook_secret'] : '';

        return new Settings($clientId, $secret, $test, $baseUri, $webhookSecret);
    }

    /**
     * @param array<string, mixed> $input
     *
     * @return array<string, mixed>
     */
    public static function sanitize(array $input): array
    {
        $existing = get_option(self::OPTION_KEY, []);
        if (!is_array($existing)) {
            $existing = [];
        }
        $secret = isset($input['secret']) && is_string($input['secret']) ? trim($input['secret']) : '';
        if ($secret === '' && isset($existing['secret']) && is_string($existing['secret'])) {
            $secret = $existing['secret'];
        }
        $webhookSecret = isset($input['webhook_secret']) && is_string($input['webhook_secret']) ? trim($input['webhook_secret']) : '';
        if ($webhookSecret === '' && isset($existing['webhook_secret']) && is_string($existing['webhook_secret'])) {
            $webhookSecret = $existing['webhook_secret'];
        }

        return [
            'client_id' => isset($input['client_id']) && is_string($input['client_id']) ? sanitize_text_field($input['client_id']) : '',
            'secret' => $secret,
            'test' => !empty($input['test']) ? '1' : '',
            'base_uri' => isset($input['base_uri']) && is_string($input['base_uri']) ? esc_url_raw(trim($input['base_uri'])) : '',
            'webhook_secret' => $webhookSecret,
        ];
    }
}
