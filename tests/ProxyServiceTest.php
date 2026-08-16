<?php

declare(strict_types=1);

namespace MainMoney\WordPressPlugin\Tests;

use MainMoney\Aggregator\Http\HttpResponse;
use MainMoney\WordPressPlugin\CheckoutConfig;
use MainMoney\WordPressPlugin\CheckoutSession;
use MainMoney\WordPressPlugin\ClientFactory;
use MainMoney\WordPressPlugin\InMemoryCheckoutSessionStore;
use MainMoney\WordPressPlugin\ProxyService;
use MainMoney\WordPressPlugin\Settings;
use MainMoney\WordPressPlugin\WebhookService;
use PHPUnit\Framework\TestCase;

final class ProxyServiceTest extends TestCase
{
    public function testCountriesProxiesThroughPhpSdk(): void
    {
        $mock = new MockHttpClient();
        $client = ClientFactory::fromSettings($this->settings(), $mock);
        $mock->enqueue($this->tokenResponse(), $this->envelope([['code' => 'CD', 'name' => 'DR Congo']]));
        $session = CheckoutSession::create(amount: '10.00', lockAmount: true);
        $result = (new ProxyService($client))->handle('GET', 'countries', [], [], $session);

        self::assertSame(200, $result['status']);
        self::assertSame('CD', $result['body'][0]['code']);
        self::assertStringContainsString('/manage/general/countries/', $mock->history[1]['uri']);
    }

    public function testLockedDepositOverwritesAmountAndReference(): void
    {
        $mock = new MockHttpClient();
        $client = ClientFactory::fromSettings($this->settings(), $mock);
        $mock->enqueue(
            $this->tokenResponse(),
            $this->envelope(['status' => 'PENDING', 'merchant_reference' => 'WP-LOCKED']),
        );
        $session = new CheckoutSession('tok', 'WP-LOCKED', '25.00', 'USD', true, time() + 60);
        $result = (new ProxyService($client))->handle('POST', 'deposits', [], [
            'provider_code' => 'VODACOM_MPESA_COD',
            'amount' => '999.00',
            'currency' => 'USD',
            'customer_phone' => '243820000000',
            'reference' => 'FORGED',
        ], $session);

        self::assertSame(200, $result['status']);
        $json = $mock->history[1]['options']['json'] ?? [];
        self::assertSame('25.00', $json['amount']);
        self::assertSame('WP-LOCKED', $json['reference']);
        self::assertSame('WP-LOCKED', $mock->history[1]['options']['headers']['Idempotency-Key'] ?? null);
    }

    public function testUnknownPathReturns400(): void
    {
        $mock = new MockHttpClient();
        $client = ClientFactory::fromSettings($this->settings(), $mock);
        $session = CheckoutSession::create();
        $result = (new ProxyService($client))->handle('GET', 'invented', [], [], $session);
        self::assertSame(400, $result['status']);
    }

    public function testCheckoutConfigOmitsSecrets(): void
    {
        $session = CheckoutSession::create(amount: '5.00', lockAmount: true, reference: 'WP-1');
        $config = CheckoutConfig::forSession('https://shop.example/wp-json/mm-aggr/v1', 'https://shop.example/wp-json/mm-aggr/v1/status', $session);
        self::assertArrayNotHasKey('secret', $config);
        self::assertArrayNotHasKey('clientId', $config);
        self::assertSame('Bearer '.$session->token, $config['pollHeaders']['Authorization']);
        self::assertTrue($config['lockAmount']);
    }

    public function testSessionStoreExpires(): void
    {
        $store = new InMemoryCheckoutSessionStore();
        $session = new CheckoutSession('t', 'WP-1', null, null, false, time() - 1);
        $store->save($session);
        self::assertNull($store->find('t'));
    }

    public function testWebhookRejectsBadSignature(): void
    {
        $mock = new MockHttpClient();
        $client = ClientFactory::fromSettings($this->settings(), $mock);
        $this->expectException(\MainMoney\Aggregator\Exception\WebhookSignatureException::class);
        (new WebhookService())->verifyAndDecode($client, '{"status":"SUCCESS"}', 'deadbeef', 'secret');
    }

    public function testWebhookAcceptsValidSignature(): void
    {
        $mock = new MockHttpClient();
        $client = ClientFactory::fromSettings($this->settings(), $mock);
        $raw = '{"merchant_reference":"WP-1","status":"SUCCESS"}';
        $signature = hash_hmac('sha256', $raw, 'secret');
        $decoded = (new WebhookService())->verifyAndDecode($client, $raw, $signature, 'secret');
        self::assertSame('WP-1', $decoded['merchant_reference']);
    }

    private function settings(): Settings
    {
        return new Settings('client-id', 'secret', true, 'https://example.test/api/v1/', 'whsec');
    }

    private function tokenResponse(): HttpResponse
    {
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->format(\DateTimeInterface::ATOM);

        return new HttpResponse(200, json_encode([
            'access_token' => 'tok_1',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'expires_at' => $expiresAt,
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @param array<string, mixed>|list<mixed> $data
     */
    private function envelope(array $data): HttpResponse
    {
        return new HttpResponse(200, json_encode([
            'success' => true,
            'response_code' => 200,
            'response_data' => $data,
            'message' => 'ok',
        ], JSON_THROW_ON_ERROR));
    }
}
