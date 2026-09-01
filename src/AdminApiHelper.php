<?php

declare(strict_types=1);

namespace MainMoney\WordPressPlugin;

use MainMoney\Aggregator\Exception\ApiException;
use MainMoney\Aggregator\Client;

final class AdminApiHelper
{
    /**
     * @return array{count: int, results: list<array<string, mixed>>}
     */
    public static function normalizeList(array $payload): array
    {
        if (isset($payload['results']) && is_array($payload['results'])) {
            return [
                'count' => (int) ($payload['count'] ?? count($payload['results'])),
                'results' => array_values($payload['results']),
            ];
        }

        if ($payload !== [] && array_is_list($payload)) {
            return [
                'count' => count($payload),
                'results' => $payload,
            ];
        }

        return [
            'count' => 0,
            'results' => [],
        ];
    }

    public static function clientOrNull(): ?Client
    {
        $settings = \Mm_Aggr_Options::load();
        if (!$settings->isConfigured()) {
            return null;
        }

        return ClientFactory::fromSettings($settings);
    }

    /**
     * @return array{ok: true, data: array<string, mixed>}|array{ok: false, message: string}
     */
    public static function safeCall(callable $callback): array
    {
        try {
            $data = $callback();
            if (!is_array($data)) {
                return ['ok' => false, 'message' => 'Unexpected aggregator response'];
            }

            return ['ok' => true, 'data' => $data];
        } catch (ApiException $exception) {
            return ['ok' => false, 'message' => $exception->getMessage()];
        } catch (\Throwable $exception) {
            return ['ok' => false, 'message' => $exception->getMessage()];
        }
    }
}
