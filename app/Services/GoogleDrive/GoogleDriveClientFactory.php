<?php

namespace App\Services\GoogleDrive;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

class GoogleDriveClientFactory
{
    /**
     * @param array<string, mixed> $config
     */
    public function getGoogleClient(array $config, ?string $certificateBundle = null): GoogleClient
    {
        return $this->configureClient(new GoogleClient(), $config, $certificateBundle);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function configureClient(GoogleClient $client, array $config, ?string $certificateBundle = null): GoogleClient
    {
        $clientId = trim((string) ($config['clientId'] ?? ''));
        $clientSecret = trim((string) ($config['clientSecret'] ?? ''));
        $refreshToken = trim((string) ($config['refreshToken'] ?? ''));

        if ($clientId === '' || $clientSecret === '' || $refreshToken === '') {
            throw new InvalidArgumentException(
                'La configuración de Google Drive requiere GOOGLE_DRIVE_CLIENT_ID, GOOGLE_DRIVE_CLIENT_SECRET y GOOGLE_DRIVE_REFRESH_TOKEN.'
            );
        }

        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes([GoogleDriveService::DRIVE]);

        if ($certificateBundle) {
            $client->setHttpClient(new GuzzleClient([
                'verify' => $certificateBundle,
            ]));
        }

        try {
            $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);
        } catch (\Throwable $exception) {
            Log::error('No se pudo renovar el token de Google Drive usando refresh_token.', [
                'disk' => 'google',
                'error' => $exception->getMessage(),
            ]);

            throw new RuntimeException($this->messageFromThrowable($exception), 0, $exception);
        }

        if (! is_array($token)) {
            Log::error('Google Drive no devolvió un access token válido al renovarlo automáticamente.', [
                'disk' => 'google',
                'response_type' => gettype($token),
            ]);

            throw new RuntimeException('Google Drive no devolvió un access_token válido al intentar renovarlo automáticamente.');
        }

        if (! empty($token['error'])) {
            Log::error('Google Drive rechazó la renovación automática del token.', [
                'disk' => 'google',
                'error' => $token['error'],
                'description' => $token['error_description'] ?? null,
            ]);

            throw new RuntimeException($this->messageFromToken($token));
        }

        if (empty($token['access_token'])) {
            Log::error('Google Drive respondió sin access_token al renovar credenciales.', [
                'disk' => 'google',
                'response_keys' => array_keys($token),
            ]);

            throw new RuntimeException('Google Drive respondió sin un access_token utilizable. Reautoriza la aplicación para generar un nuevo refresh token.');
        }

        $client->setAccessToken($token);

        return $client;
    }

    private function messageFromThrowable(\Throwable $exception): string
    {
        $message = $exception->getMessage();

        if (str_contains($message, 'invalid_grant') || str_contains($message, 'Token has been expired or revoked')) {
            return 'La renovación automática del token de Google Drive falló porque el refresh token expiró o fue revocado. Genera un nuevo GOOGLE_DRIVE_REFRESH_TOKEN.';
        }

        return 'No se pudo renovar automáticamente el token de Google Drive: '.$message;
    }

    /**
     * @param array<string, mixed> $token
     */
    private function messageFromToken(array $token): string
    {
        $error = (string) ($token['error'] ?? 'desconocido');
        $description = (string) ($token['error_description'] ?? $error);

        if ($error === 'invalid_grant' || str_contains($description, 'expired or revoked')) {
            return 'La renovación automática del token de Google Drive falló porque el refresh token expiró o fue revocado. Genera un nuevo GOOGLE_DRIVE_REFRESH_TOKEN.';
        }

        return 'Google Drive rechazó la renovación automática del token: '.$description;
    }
}
