<?php

declare(strict_types=1);

use MainMoney\Aggregator\Exception\AggregatorException;
use MainMoney\WordPressPlugin\CheckoutSession;
use MainMoney\WordPressPlugin\ClientFactory;
use MainMoney\WordPressPlugin\ProxyService;
use MainMoney\WordPressPlugin\WebhookService;

final class Mm_Aggr_Rest
{
    public static function register(): void
    {
        add_action('rest_api_init', [self::class, 'routes']);
    }

    public static function routes(): void
    {
        $namespace = 'mm-aggr/v1';
        $proxyGets = ['countries', 'providers', 'match-provider', 'amount-limits', 'checkout-preferences', 'status'];
        foreach ($proxyGets as $route) {
            register_rest_route($namespace, '/'.$route, [
                'methods' => 'GET',
                'callback' => [self::class, 'proxy'],
                'permission_callback' => [self::class, 'requireSession'],
            ]);
        }
        register_rest_route($namespace, '/fees/simulate', [
            'methods' => 'POST',
            'callback' => [self::class, 'proxy'],
            'permission_callback' => [self::class, 'requireSession'],
        ]);
        register_rest_route($namespace, '/deposits', [
            'methods' => 'POST',
            'callback' => [self::class, 'proxy'],
            'permission_callback' => [self::class, 'requireSession'],
        ]);
        register_rest_route($namespace, '/webhooks', [
            'methods' => 'POST',
            'callback' => [self::class, 'webhook'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function requireSession(\WP_REST_Request $request): bool|\WP_Error
    {
        $session = self::sessionFromRequest($request);
        if ($session instanceof \WP_Error) {
            return $session;
        }

        return true;
    }

    public static function proxy(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $session = self::sessionFromRequest($request);
        if ($session instanceof \WP_Error) {
            return $session;
        }
        $settings = Mm_Aggr_Options::load();
        if (!$settings->isConfigured()) {
            return new \WP_Error('mm_aggr_unconfigured', 'MainMoney is not configured', ['status' => 503]);
        }
        $route = trim(str_replace('/mm-aggr/v1/', '', $request->get_route()), '/');
        $service = new ProxyService(ClientFactory::fromSettings($settings));
        $result = $service->handle(
            $request->get_method(),
            $route,
            $request->get_query_params(),
            is_array($request->get_json_params()) ? $request->get_json_params() : [],
            $session,
        );

        return new \WP_REST_Response($result['body'], $result['status']);
    }

    public static function webhook(\WP_REST_Request $request): \WP_REST_Response|\WP_Error
    {
        $settings = Mm_Aggr_Options::load();
        if (!$settings->isConfigured() || $settings->webhookSecret === '') {
            return new \WP_Error('mm_aggr_unconfigured', 'Webhook secret is not configured', ['status' => 503]);
        }
        $raw = $request->get_body();
        $signature = (string) $request->get_header('x-webhook-signature');
        try {
            $payload = (new WebhookService())->verifyAndDecode(
                ClientFactory::fromSettings($settings),
                $raw,
                $signature,
                $settings->webhookSecret,
            );
        } catch (AggregatorException $exception) {
            return new \WP_Error('mm_aggr_webhook', $exception->getMessage(), ['status' => 401]);
        }
        do_action('mm_aggr_webhook_received', $payload);

        return new \WP_REST_Response(['received' => true], 200);
    }

    private static function sessionFromRequest(\WP_REST_Request $request): CheckoutSession|\WP_Error
    {
        $header = (string) $request->get_header('authorization');
        if (!preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return new \WP_Error('mm_aggr_unauthorized', 'Missing checkout session token', ['status' => 401]);
        }
        $session = (new Mm_Aggr_Session_Store())->find($matches[1]);
        if ($session === null) {
            return new \WP_Error('mm_aggr_unauthorized', 'Invalid checkout session', ['status' => 401]);
        }

        return $session;
    }
}
