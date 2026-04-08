<?php

namespace Tests\Unit;

use App\Services\GoogleDrive\GoogleDriveClientFactory;
use Google\Client as GoogleClient;
use RuntimeException;
use Tests\TestCase;

class GoogleDriveClientFactoryTest extends TestCase
{
    /** @test */
    public function renueva_el_access_token_usando_unicamente_el_refresh_token(): void
    {
        $client = \Mockery::mock(GoogleClient::class);
        $token = [
            'access_token' => 'fresh-access-token',
            'expires_in' => 3600,
            'created' => time(),
        ];

        $client->shouldReceive('setClientId')->once()->with('client-id');
        $client->shouldReceive('setClientSecret')->once()->with('client-secret');
        $client->shouldReceive('setAccessType')->once()->with('offline');
        $client->shouldReceive('setPrompt')->once()->with('consent');
        $client->shouldReceive('setScopes')->once()->with([\Google\Service\Drive::DRIVE]);
        $client->shouldReceive('setHttpClient')->never();
        $client->shouldReceive('fetchAccessTokenWithRefreshToken')->once()->with('refresh-token')->andReturn($token);
        $client->shouldReceive('setAccessToken')->once()->with($token);

        $factory = new GoogleDriveClientFactory();

        $this->assertSame(
            $client,
            $factory->configureClient($client, [
                'clientId' => 'client-id',
                'clientSecret' => 'client-secret',
                'refreshToken' => 'refresh-token',
            ])
        );
    }

    /** @test */
    public function lanza_un_error_claro_si_google_responde_invalid_grant(): void
    {
        $client = \Mockery::mock(GoogleClient::class);

        $client->shouldReceive('setClientId')->once()->with('client-id');
        $client->shouldReceive('setClientSecret')->once()->with('client-secret');
        $client->shouldReceive('setAccessType')->once()->with('offline');
        $client->shouldReceive('setPrompt')->once()->with('consent');
        $client->shouldReceive('setScopes')->once()->with([\Google\Service\Drive::DRIVE]);
        $client->shouldReceive('setHttpClient')->never();
        $client->shouldReceive('fetchAccessTokenWithRefreshToken')->once()->with('refresh-token')->andReturn([
            'error' => 'invalid_grant',
            'error_description' => 'Token has been expired or revoked.',
        ]);
        $client->shouldReceive('setAccessToken')->never();

        $factory = new GoogleDriveClientFactory();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('refresh token expiró o fue revocado');

        $factory->configureClient($client, [
            'clientId' => 'client-id',
            'clientSecret' => 'client-secret',
            'refreshToken' => 'refresh-token',
        ]);
    }
}
